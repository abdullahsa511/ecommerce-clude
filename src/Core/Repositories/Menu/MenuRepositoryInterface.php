<?php

declare(strict_types=1);

namespace App\Core\Repositories\Menu;

use App\Core\Models\Menu\Menu;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface MenuRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get a single menu by ID
     * 
     * @param int $menu_id Menu ID
     * @param int $language_id Language ID
     * @param int $site_id Site ID
     * @return Menu|null
     */
    public function getMenu(int $menu_id, int $language_id, int $site_id): ?Menu;

    

    /**
     * Get all menus with pagination
     * 
     * @param int|null $language_id Language ID
     * @param int|null $site_id Site ID
     * @param string|null $type Menu type
     * @param int $start Start offset
     * @param int $limit Number of records to return
     * @return array{data: array, total: int}
     */
    public function getAll(?int $language_id = null, ?int $site_id = null, ?string $type = null, int $start = 0, int $limit = 10): array;

    /**
     * Get menu items
     * 
     * @param int|null $menu_id Menu ID
     * @param int $language_id Language ID
     * @param int|null $site_id Site ID
     * @param string|null $slug Menu slug
     * @param int $start Start offset
     * @param int $limit Number of records to return
     * @return array
     */
    public function get(?int $menu_id = null, int $language_id, ?int $site_id = null, ?string $slug = null, int $start = 0, int $limit = 10): array;

    /**
     * Get menu items in all languages
     * 
     * @param int|null $menu_id Menu ID
     * @param int|null $site_id Site ID
     * @param string|null $search Search term
     * @param int $start Start offset
     * @param int $limit Number of records to return
     * @return array{data: array, total: int}
     */
    public function getMenuAllLanguages(?int $menu_id = null, ?int $site_id = null, ?string $search = null, int $start = 0, int $limit = 10): array;

    /**
     * Edit menu item
     * 
     * @param int $menu_item_id Menu item ID
     * @param array $menu_item Menu item data
     * @return int
     */
    public function editMenuItem(int $menu_item_id, array $menu_item): int;

    /**
     * Add new menu item
     * 
     * @param array $menu_item Menu item data
     * @return array{menu_item_id: int, content_id: int}
     */
    public function addMenuItem(array $menu_item): array;

    /**
     * Update menu items (reorder)
     * 
     * @param array $menu_items Array of menu items with new order
     * @return int
     */
    public function updateMenuItems(array $menu_items): int;

    /**
     * Delete menu item and all its children recursively
     * 
     * @param int $menu_item_id Menu item ID
     * @return bool
     */
    public function deleteMenuItemRecursive(int $menu_item_id): bool;

    /**
     * Delete menu item and its immediate children
     * 
     * @param int $menu_item_id Menu item ID
     * @return bool
     */
    public function deleteMenuItem(int $menu_item_id): bool;
} 