<?php

declare(strict_types=1);

namespace App\Core\Models\PostCategory;

use App\Core\Models\Base\Model;
use App\Core\Models\TaxonomyItem;

class TaxonomyItemMeta extends Model
{
    public int $meta_id;
    public int $taxonomy_item_id;
    public string $key;
    public string $value;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get the taxonomy item this meta belongs to
     */
    public function taxonomyItem()
    {
        return $this->belongsTo(TaxonomyItem::class, 'taxonomy_item_id');
    }
} 