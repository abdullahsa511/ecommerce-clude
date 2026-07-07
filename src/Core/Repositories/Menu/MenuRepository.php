<?php

declare(strict_types=1);

namespace App\Core\Repositories\Menu;

use App\Core\Models\Menu\Menu;
use App\Core\Models\Menu\MenuItem;
use App\Core\Models\Menu\MenuItemContent;
use App\Core\Repositories\Base\BaseRepository;
use PDO;

class MenuRepository extends BaseRepository implements MenuRepositoryInterface
{
    protected MenuItem $menuItem;
    protected MenuItemContent $menuItemContent;

    public function __construct(PDO $db, Menu $menu, MenuItem $menuItem, MenuItemContent $menuItemContent)
    {
        parent::__construct($db, 'menu', Menu::class);
        $this->menuItem = $menuItem;
        $this->menuItemContent = $menuItemContent;
        $this->menuItem->setDb($db);
        $this->menuItemContent->setDb($db);
    }

    public function getMenu(int $menu_id, int $language_id, int $site_id): ?Menu
    {
        $query = $this->model->with(['menuItemsWithContent'])
            ->where('menu_id', '=', $menu_id)
            ->where('site_id', '=', $site_id)
            ->limit(1);

        $result = $query->findAll();
        return !empty($result) ? $this->model->set($result[0]) : null;
    }

    

    public function getAll(?int $language_id = null, ?int $site_id = null, ?string $type = null, int $start = 0, int $limit = 10): array
    {
        $query = $this->model->with(['menuItemsWithContent']);

        if ($site_id !== null) {
            $query->where('site_id', '=', $site_id);
        }

        if ($type !== null) {
            $query->where('type', '=', $type);
        }

        $query->orderBy('menu_id')
              ->limit($limit)
              ->offset($start);

        $data = $query->findAll();
        $total = $query->countAll();

        return [
            'data' => $data,
            'total' => $total
        ];
    }

    public function get(?int $menu_id = null, int $language_id, ?int $site_id = null, ?string $slug = null, int $start = 0, int $limit = 10): array
    {
        if ($slug !== null) {
            $menu = $this->model->where('slug', '=', $slug)->limit(1)->findAll();
            if (!empty($menu)) {
                $menu_id = $menu[0]['menu_id'];
            }
        }

        if ($menu_id === null) {
            return [];
        }

        $query = $this->menuItem->with(['menuItemContent', 'children.menuItemContent'])
            ->where('menu_id', '=', $menu_id)
            ->where('parent_id', '=', 0)
            ->orderBy('sort_order')
            ->orderBy('menu_item_id');

        $items = $query->findAll();
        return $this->buildHierarchy($items);
    }

    public function getMenuAllLanguages(?int $menu_id = null, ?int $site_id = null, ?string $search = null, int $start = 0, int $limit = 10): array
    {
        $query = $this->menuItem->with(['menuItemContent', 'children.menuItemContent']);

        if ($menu_id !== null) {
            $query->where('menu_id', '=', $menu_id);
        }

        if ($search !== null) {
            $query->join('menu_item_content', 'menu_item.menu_item_id', '=', 'menu_item_content.menu_item_id')
                  ->where('menu_item_content.name', 'LIKE', "%{$search}%");
        }

        $query->where('parent_id', '=', 0)
              ->orderBy('sort_order')
              ->orderBy('menu_item_id');

        if ($limit > 0) {
            $query->limit($limit)->offset($start);
        }

        $data = $query->findAll();
        $total = $query->countAll();

        return [
            'data' => $this->buildHierarchy($data),
            'total' => $total
        ];
    }

    /**
     * Build hierarchical structure from flat array
     */
    protected function buildHierarchy(array $items, int $parentId = 0): array
    {
        $branch = [];
        
        foreach ($items as $item) {
            if ($item['parent_id'] == $parentId) {
                $children = $this->buildHierarchy($items, $item['menu_item_id']);
                if ($children) {
                    $item['children'] = $children;
                }
                $branch[] = $item;
            }
        }
        
        return $branch;
    }

    public function editMenuItem(int $menu_item_id, array $menu_item): int
    {
        try {
            $this->db->beginTransaction();

            if (isset($menu_item['menu_item_content'])) {
                foreach ($menu_item['menu_item_content'] as $content) {
                    $content['menu_item_id'] = $menu_item_id;
                    $this->menuItemContent->upsert([$content], ['menu_item_id', 'language_id']);
                }
                unset($menu_item['menu_item_content']);
            }

            $this->menuItem->update($menu_item);

            $this->db->commit();
            return $menu_item_id;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \RuntimeException('Failed to edit menu item: ' . $e->getMessage());
        }
    }

    public function addMenuItem(array $menu_item): array
    {
        try {
            $this->db->beginTransaction();

            $content = $menu_item['menu_item_content'] ?? [];
            unset($menu_item['menu_item_content']);

            $result = $this->menuItem->create($menu_item);
            $menu_item_id = $result->getId();

            $content_id = 0;
            if (!empty($content)) {
                foreach ($content as $langContent) {
                    $langContent['menu_item_id'] = $menu_item_id;
                    $contentResult = $this->menuItemContent->create($langContent);
                    $content_id = $contentResult->getId();
                }
            }

            $this->db->commit();
            return [
                'menu_item_id' => $menu_item_id,
                'content_id' => $content_id
            ];
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \RuntimeException('Failed to add menu item: ' . $e->getMessage());
        }
    }

    public function updateMenuItems(array $menu_items): int
    {
        try {
            $this->db->beginTransaction();
            $count = 0;

            foreach ($menu_items as $item) {
                if (isset($item['menu_item_id'])) {
                    $this->menuItem->update($item['menu_item_id'], $item);
                    $count++;
                }
            }

            $this->db->commit();
            return $count;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \RuntimeException('Failed to update menu items: ' . $e->getMessage());
        }
    }

    public function deleteMenuItemRecursive(int $menu_item_id): bool
    {
        try {
            $this->db->beginTransaction();

            $menuItem = $this->menuItem->with(['children'])->find($menu_item_id);
            if (!$menuItem) {
                return false;
            }

            $itemsToDelete = $this->collectChildrenIds($menuItem);
            $itemsToDelete[] = $menu_item_id;

            // Delete content first
            $this->menuItemContent->deleteMultiple($itemsToDelete);
            // Then delete items
            $this->menuItem->deleteMultiple($itemsToDelete);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \RuntimeException('Failed to delete menu item recursively: ' . $e->getMessage());
        }
    }

    protected function collectChildrenIds($menuItem, array &$ids = []): array
    {
        if (!empty($menuItem['children'])) {
            foreach ($menuItem['children'] as $child) {
                $ids[] = $child['menu_item_id'];
                $this->collectChildrenIds($child, $ids);
            }
        }
        return $ids;
    }

    public function deleteMenuItem(int $menu_item_id): bool
    {
        try {
            $this->db->beginTransaction();

            $menuItem = $this->menuItem->with(['children'])->find($menu_item_id);
            if (!$menuItem) {
                return false;
            }

            $childIds = array_column($menuItem['children'] ?? [], 'menu_item_id');
            $allIds = array_merge([$menu_item_id], $childIds);

            // Delete content first
            $this->menuItemContent->deleteMultiple($allIds);
            // Then delete items
            $this->menuItem->deleteMultiple($allIds);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \RuntimeException('Failed to delete menu item: ' . $e->getMessage());
        }
    }
} 