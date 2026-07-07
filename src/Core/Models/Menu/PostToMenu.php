<?php

declare(strict_types=1);

namespace App\Core\Models\Menu;

use App\Core\Models\Base\Model;

class PostToMenu extends Model
{
    public int $post_id;
    public int $menu_id;

    public function __construct() 
    {
        parent::__construct();
    }
} 