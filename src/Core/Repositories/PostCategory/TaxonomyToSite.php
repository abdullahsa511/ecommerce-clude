<?php

declare(strict_types=1);

namespace App\Core\Models\PostCategory;

use App\Core\Models\Base\Model;

class TaxonomyToSite extends Model
{
    public int $taxonomy_item_id;
    public int $site_id;

    public function __construct()
    {}
} 