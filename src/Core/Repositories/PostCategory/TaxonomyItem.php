<?php

declare(strict_types=1);

namespace App\Core\Models\PostCategory;

use App\Core\Models\Base\Model;
use App\Core\Models\Post\Post;
use App\Core\Repositories\PostCategory\TaxonomyItemRepository;

class TaxonomyItem extends Model
{
    // Core properties
    public int $taxonomy_item_id;
    public int $taxonomy_id;
    public string $image = '';
    public string $slider_image = '';
    public ?string $label = null;
    public string $template = '';
    public int $parent_id = 0;
    public ?int $item_id = null;
    public int $sort_order = 0;
    public int $status = 0;
    public array|string|null $banner_way_points;
    public ?string $products_link = null;
    public ?string $link = null;
    public ?string $content = null;
    public ?string $meta_title = null;
    public ?string $meta_description = null;
    public ?string $meta_keywords = null;
    public ?string $slug = null;
    public ?string $taxonomy_item_code = null;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get the parent taxonomy item
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Get all child taxonomy items
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Get the taxonomy this item belongs to
     */
    public function taxonomy()
    {
        return $this->belongsTo(Taxonomy::class, 'taxonomy_id');
    }

    /**
     * Get all posts associated with this taxonomy item
     */
    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_to_taxonomy_item', 
            'taxonomy_item_id', 
            'post_id', 
            'taxonomy_item_id', 
            'post_id'
        );
    }

    /**
     * Get the thumbnail URL for display
     */
    public function getThumbnail(?int $width = null, ?int $height = null): string
    {
        return !empty($this->image) ? $this->image : '/assets/images/default-taxonomy-thumbnail.jpg';
    }

    /**
     * Check if this item is active
     */
    public function isActive(): bool
    {
        return $this->status === 1;
    }

    /**
     * Get the full path of this item (including parent names)
     */
    public function getPath(): string
    {
        global $container;
        $path = [$this->getName()];
        $repository = $container->get(TaxonomyItemRepository::class);
        
        $parentId = $this->parent_id;
        while ($parentId > 0) {
            $parent = $repository->find($parentId);
            if (!$parent) break;
            
            array_unshift($path, $parent->getName());
            $parentId = $parent->parent_id;
        }
        
        return implode(' > ', $path);
    }

    /**
     * Get the name of this taxonomy item
     */
    public function getName(): string
    {
        // This would need to be implemented based on your content structure
        // For example, if you have a taxonomy_item_content table
        return '';
    }
} 