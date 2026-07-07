<?php

declare(strict_types=1);

namespace App\Core\Models\Menu;

use App\Core\Models\Base\Model;

class MenuToSite extends Model
{
    public int $menu_id;
    public int $site_id;

    public function __construct() 
    {
        parent::__construct();
    }
} 