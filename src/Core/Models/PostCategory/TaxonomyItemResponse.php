<?php

declare(strict_types=1);

namespace App\Core\Models\PostCategory;

use stdClass;

class TaxonomyItemResponse
{
    public ?int $tag_id;
    public ?string $thumbnail;
    public ?array $image;
    public ?string $name;
    public ?string $slug;
    public ?string $description;
    public ?int $sort_order;
    public ?int $status;
    public ?string $template;
    public ?int $parent_id;
    public ?int $taxonomy_id;
    public ?string $date_added;
    public ?string $date_updated;
    public ?string $meta_title;
    public ?string $meta_description;
    public ?string $meta_keywords;
    public ?string $link;

    public function __construct(stdClass $data) 
    {
        $this->tag_id = $data->taxonomy_item_id ?? null;
        $this->taxonomy_id = $data->taxonomy_id ?? null;
        
        // Handle image/thumbnail
        if (isset($data->image) && !empty($data->image)) {
            if (is_string($data->image)) {
                $imageData = json_decode($data->image, true);
                $this->thumbnail = $imageData['src'] ?? $data->image;
            } else {
                $this->thumbnail = $data->image;
            }
        } else {
            $this->thumbnail = '';
        }
        // Normalizes and decodes the image field (handles double-encoded JSON as well)
        if (isset($data->image) && !empty($data->image)) {
            if (is_string($data->image)) {
                // Remove extra quotes if present (double-encoded JSON as string)
                $decoded = json_decode($data->image, true);

                if (is_string($decoded)) {
                    // Still a string, decode one more layer
                    $decoded = json_decode($decoded, true);
                }

                // Ensure $decoded is array or object at this point
                if ($decoded !== null) {
                    $this->image = $decoded;
                } else {
                    // fallback to raw string
                    $this->image = null;
                }
            } else {
                $this->image = null;
            }
            } else {
                $this->image = null;
            }
        
        $this->template = $data->template ?? '';
        $this->parent_id = $data->parent_id ?? null;
        $this->sort_order = $data->sort_order ?? 0;
        $this->status = $data->status ?? 1;
        $this->meta_title = $data->meta_title ?? '';
        $this->meta_description = $data->meta_description ?? '';
        $this->meta_keywords = $data->meta_keywords ?? '';
        $this->link = $data->link ?? '';
        
        // Handle content data
        if (isset($data->taxonomyItemContent)) {
            if (is_string($data->taxonomyItemContent)) {
                // Handle JSON string
                $contentData = json_decode($data->taxonomyItemContent, true);
                if ($contentData && is_array($contentData)) {
                    $this->name = $contentData['name'] ?? '';
                    $this->slug = $contentData['slug'] ?? '';
                    $this->description = $contentData['content'] ?? '';
                    $this->meta_title = $contentData['meta_title'] ?? '';
                    $this->meta_description = $contentData['meta_description'] ?? '';
                    $this->meta_keywords = $contentData['meta_keywords'] ?? '';
                    $this->link = $contentData['link'] ?? '';
                }
            } elseif (is_array($data->taxonomyItemContent) && !empty($data->taxonomyItemContent)) {
                $content = $data->taxonomyItemContent[0]; // Get first content record
                $this->name = $content['name'] ?? '';
                $this->slug = $content['slug'] ?? '';
                $this->description = $content['content'] ?? '';
                $this->meta_title = $content['meta_title'] ?? '';
                $this->meta_description = $content['meta_description'] ?? '';
                $this->meta_keywords = $content['meta_keywords'] ?? '';
                $this->link = $content['link'] ?? '';
            } elseif (is_object($data->taxonomyItemContent)) {
                $this->name = $data->taxonomyItemContent->name ?? '';
                $this->slug = $data->taxonomyItemContent->slug ?? '';
                $this->description = $data->taxonomyItemContent->content ?? '';
                $this->meta_title = $data->taxonomyItemContent->meta_title ?? '';
                $this->meta_description = $data->taxonomyItemContent->meta_description ?? '';
                $this->meta_keywords = $data->taxonomyItemContent->meta_keywords ?? '';
                $this->link = $data->taxonomyItemContent->link ?? '';
            }
        } else {
            // Direct fields from joined query
            $this->name = $data->name ?? '';
            $this->slug = $data->slug ?? '';
            $this->description = (string)($data->taxonomyItemContent ?? $data->description ?? '');
        }
        
        $this->date_added = $data->created_at ?? date('c');
        $this->date_updated = $data->updated_at ?? date('c');
    }
}
