<?php

declare(strict_types=1);

namespace App\Core\Models\PostCategory;

use function App\Core\System\utils\makeSlug;
use DateTime;

class TaxonomyItemData 
{
    public ?int $taxonomy_item_id;
    public int $taxonomy_id;
    public ?string $image;
    public string $name;
    public string $taxonomy_item_code;
    public ?string $label_name;
    public string $template;
    public ?int $parent_id;
    public ?int $item_id;
    public ?int $sort_order;
    public ?int $status;
    
    public TaxonomyItemContentData $content;

    public function __construct(array $data) 
    {
        if(isset($data['tag_id'])) $this->taxonomy_item_id = $data['tag_id'];
        $this->taxonomy_id = $data['taxonomy_id'] ?? 2; // Default to product tags taxonomy
        
        // Handle image/thumbnail
        if(isset($data['image'])) {
            $this->image = is_string($data['image']) ? $data['image'] : json_encode($data['image']);
        } elseif(isset($data['thumbnail'])) {
            $this->image = !empty($data['thumbnail']) ? json_encode(['src' => $data['thumbnail']]) : null;
        } else {
            $this->image = null;
        }
        $this->name = $data['name'] ?? '';
        $this->taxonomy_item_code = makeSlug($data['slug']) ?? '';
        $this->label_name = $data['label_name'] ?? null;
        $this->template = $data['template'] ?? '';
        $this->parent_id = $data['parent_id'] ?? null;
        $this->item_id = $data['item_id'] ?? null;
        $this->sort_order = $data['sort_order'] ?? 0;
        $this->name = $data['name'] ?? '';
        $this->status = $data['status'] ?? 1;
        
        // Handle content data
        if(isset($data['content']) && is_array($data['content'])) {
            $this->content = new TaxonomyItemContentData($data['content']);
        } else {
            // Create content from direct fields
            $contentData = [
                'language_id' => $data['language_id'] ?? 1,
                'name' => $data['name'] ?? '',
                'slug' => makeSlug($data['slug']) ?? '',
                'content' => $data['content'] ?? '',
                'meta_title' => $data['meta_title'] ?? '',
                'meta_description' => $data['meta_description'] ?? '',
                'meta_keywords' => $data['meta_keywords'] ?? '',
                'link' => $data['link'] ?? '',
                'products_link' => $data['products_link'] ?? '',
            ];
            $this->content = new TaxonomyItemContentData($contentData);
        }
    }

    public function toArray(): array
    {
        return [
            // 'taxonomy_item_id' => $this->taxonomy_item_id,
            'taxonomy_id' => $this->taxonomy_id,
            'image' => $this->image,
            'name' => $this->name,
            'taxonomy_item_code' => $this->taxonomy_item_code,
            'label_name' => $this->label_name,
            'template' => $this->template,
            'parent_id' => $this->parent_id,
            'item_id' => $this->item_id,
            'sort_order' => $this->sort_order,
            'status' => $this->status,
        ];
    }
}

class TaxonomyItemContentData 
{
    public int $language_id;
    public string $name;
    public string $slug;
    public string $content;
    public string $meta_title;
    public string $meta_description;
    public string $meta_keywords;
    public string $link;
    public string $products_link;

    public function __construct(array $data)
    {
        $this->language_id = $data['language_id'] ?? 1;
        $this->name = $data['name'] ?? '';
        $this->slug = makeSlug($data['slug']) ?? '';
        $this->content = $data['content'] ?? $data['description'] ?? '';
        $this->meta_title = $data['meta_title'] ?? '';
        $this->meta_description = $data['meta_description'] ?? '';
        $this->meta_keywords = $data['meta_keywords'] ?? '';
        $this->link = $data['link'] ?? '';
        $this->products_link = $data['products_link']?? '';
    }

    public function toArray(): array
    {
        return [
            'language_id' => $this->language_id,
            'name' => $this->name,
            'slug' => makeSlug($this->slug),
            'content' => $this->content,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'link' => $this->link,
            'products_link' => $this->products_link,
        ];
    }
}
