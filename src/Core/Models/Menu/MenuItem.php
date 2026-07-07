<?php

declare(strict_types=1);

namespace App\Core\Models\Menu;

use App\Core\Models\Base\Model;

class MenuItem extends Model
{
    public int $menu_item_id;
    public int $menu_id;
    public string $type;
    public string $url;
    public int $parent_id;
    public ?int $item_id;
    public string $options;
    public int $sort_order;
    public int $status;

    /**
     * Define relationship with Menu model
     */
    public function menu()
    {
        return $this->hasOne(Menu::class, 'menu_id');
    }

    /**
     * Define relationship with MenuItemContent model
     */
    public function menuItemContent()
    {
        return $this->hasMany(MenuItemContent::class, 'menu_item_id');
    }

    /**
     * Define relationship with MenuItemMeta model
     */
    public function menuItemMeta()
    {
        return $this->hasMany(MenuItemMeta::class, 'menu_item_id');
    }

    /**
     * Define self-referential relationship for parent-child
     */
    public function parent()
    {
        return $this->hasOne(MenuItem::class, 'parent_id');
    }

    /**
     * Define self-referential relationship for parent-child
     */
    public function children()
    {
        return $this->hasMany(MenuItem::class, 'parent_id');
    }

    public function __construct() 
    {
        parent::__construct();
    }
} 