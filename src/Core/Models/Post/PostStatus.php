<?php

declare(strict_types=1);

namespace App\Core\Models\Post;

use App\Core\Models\Base\Model;

class PostStatus extends Model
{
    public int $post_status_id;
    public int $language_id;
    public string $name;

    public function __construct() 
    {
        parent::__construct();
    }
} 