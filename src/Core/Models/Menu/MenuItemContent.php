<?php

declare(strict_types=1);

namespace App\Core\Models\Menu;

use App\Core\Models\Base\Model;

class MenuItemContent extends Model
{
    public int $menu_item_id;
    public int $language_id;
    public string $name;
    public string $slug;
    public string $content;

    public function __construct() 
    {
        parent::__construct();
    }
} 