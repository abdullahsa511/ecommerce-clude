<?php

declare(strict_types=1);

namespace App\Core\Models\Menu;

use App\Core\Models\Base\Model;

class MenuItemMeta extends Model
{
    public int $menu_item_meta_id;
    public int $menu_item_id;
    public ?string $key;
    public ?string $value;

    public function __construct() 
    {
        parent::__construct();
    }
} 