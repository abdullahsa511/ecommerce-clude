<?php

declare(strict_types=1);

namespace App\Core\Models\Menu;

use App\Core\Models\Base\Model;

class Menu extends Model
{
    public int $menu_id;
    public string $name;
    public string $slug;
    public ?string $type = null;
    public ?int $site_id = null;
    public function __construct() 
    {
        parent::__construct();
    }

    /**
     * Define relationship with MenuItem model
     * 
     * @return array{join: string, select: string, model: object, tableAlias: string, class: string, type: string, columns: array}
     */
    public function menuItems(): array
    {
        return $this->hasMany(MenuItem::class, 'menu_id');
    }

    /**
     * Define relationship with MenuItem model including content
     * 
     * @return array{join: string, select: string, model: object, tableAlias: string, class: string, type: string, columns: array}
     */
    public function menuItemsWithContent(): array
    {
        $this->with(['menuItemContent']);
        return $this->hasMany(MenuItem::class, 'menu_id');
    }

    /**
     * Define relationship with MenuItem model including content and children
     * 
     * @return array{join: string, select: string, model: object, tableAlias: string, class: string, type: string, columns: array}
     */
    public function menuItemsWithContentAndChildren(): array
    {
        $this->with(['menuItemContent', 'children.menuItemContent']);
        $this->where('parent_id', '=', 0);
        $this->orderBy('sort_order');
        return $this->hasMany(MenuItem::class, 'menu_id');
    }

    /**
     * Get menu items in hierarchical structure
     * 
     * @param int $language_id
     * @return array
     */
    public function getHierarchicalItems(int $language_id): array
    {
        $this->with(['menuItemContent', 'children.menuItemContent']);
        $this->where('parent_id', '=', 0);
        $this->orderBy('sort_order');
        $items = $this->findAll();
        return $this->buildHierarchy($items);
    }

    /**
     * Build hierarchical structure from flat array
     * 
     * @param array $items
     * @param int $parentId
     * @return array
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
} 