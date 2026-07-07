<?php

declare(strict_types=1);

namespace App\Core\Models\PostCategory;

use App\Core\Models\Base\Model;

class TaxonomyItemContent extends Model
{
    public int $taxonomy_item_id;
    public int $language_id;
    public string $name;
    public string $slug;
    public string $content;
    public string $meta_title;
    public string $meta_description;
    public string $meta_keywords;

    /**
     * Get the taxonomy item this content belongs to
     */
    public function getTaxonomyItem(): array
    {
        return $this->belongsTo(TaxonomyItem::class, 'taxonomy_item_id');
    }

    public function __construct() 
    {
        parent::__construct();
    }
} 