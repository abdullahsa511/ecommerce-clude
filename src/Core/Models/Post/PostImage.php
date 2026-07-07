<?php

declare(strict_types=1);

namespace App\Core\Models\Post;

use App\Core\Models\Base\Model;

class PostImage extends Model
{
    public int $post_image_id;
    public int $post_id;
    public string $image_link;
    public string $image;
    public int $sort_order;
    public string $status;
    public string $way_points;

    public function __construct() 
    {
        parent::__construct();
    }
} 