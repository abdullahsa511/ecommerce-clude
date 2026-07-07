<?php

declare(strict_types=1);

namespace App\Core\Models\PostCategory;

use App\Core\Models\Base\Model;

class PostToTaxonomyItem extends Model
{
    public int $post_id;
    public int $taxonomy_item_id;

    public function __construct()
    {}
} 