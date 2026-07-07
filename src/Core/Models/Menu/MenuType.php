<?php

declare(strict_types=1);

namespace App\Core\Models\Menu;

use App\Core\Models\Base\Model;

class MenuType extends Model
{
    public int $menu_type_id;
    public string $code;
    public string $created_at;
    public string $updated_at;

    /**
     * Define relationship with MenuItem model
     */
    public function menuItem()
    {
        return $this->hasMany(MenuItem::class, 'type', 'code');
    }

    /**
     * Define relationship with MenuTypeContent model
     */
    public function menuTypeContent()
    {
        return $this->hasMany(MenuTypeContent::class, 'menu_type_id');
    }

    public function __construct() 
    {
        parent::__construct();
    }
} 