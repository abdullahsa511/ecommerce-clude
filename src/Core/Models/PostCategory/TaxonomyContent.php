<?php

declare(strict_types=1);

namespace App\Core\Models\PostCategory;

use App\Core\Models\Base\Model;

class TaxonomyContent extends Model
{
    public int $taxonomy_id;
    public int $language_id;
    public string $name;
    public string $slug;
    public string $content;

    public function __construct()
    {}
} 