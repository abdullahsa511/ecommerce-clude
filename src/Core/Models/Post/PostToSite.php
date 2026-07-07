<?php

declare(strict_types=1);

namespace App\Core\Models\Post;

use App\Core\Models\Base\Model;

class PostToSite extends Model
{
    public int $post_id;
    public int $site_id;
    
    public function __construct()
    {}
   
}
