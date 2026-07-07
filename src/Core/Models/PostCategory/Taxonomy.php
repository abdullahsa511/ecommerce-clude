<?php

declare(strict_types=1);

namespace App\Core\Models\PostCategory;

use App\Core\Models\Site\Site;
use App\Core\Models\Base\Model;
use App\Core\Models\Post\Post;
use App\Core\Models\PostCategory\TaxonomyContent;

class Taxonomy extends Model
{
    // Core properties
    public int $taxonomy_id;
    public string $name;
    public string $post_type;
    public string $type;
    public int $site_id;


    public function taxonomyContent()
    {
        return $this->hasMany(TaxonomyContent::class, 'taxonomy_id');
    }

    /**
     * Get all taxonomy items in this taxonomy
     */
    public function taxonomyItem()
    {
        return $this->belongsTo(TaxonomyItem::class, 'taxonomy_id');
    }

    /**
     * Get the site this taxonomy belongs to
     */
    public function site()
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    /**
     * Get all posts associated with this taxonomy
     */
    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_taxonomy', 'taxonomy_id', 'post_id');
    }

    /**
     * Check if this is a category taxonomy
     */
    public function isCategory(): bool
    {
        return $this->type === 'categories';
    }

    /**
     * Check if this is a tag taxonomy
     */
    public function isTag(): bool
    {
        return $this->type === 'tags';
    }

    /**
     * Get the display name for this taxonomy
     */
    public function getDisplayName(): string
    {
        return ucfirst($this->type);
    }

    public function __construct() 
    {
        parent::__construct();
    }
} 