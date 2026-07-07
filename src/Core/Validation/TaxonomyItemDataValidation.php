<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;

class TaxonomyItemDataValidation extends Validation
{
    // public bool $isValidData = true;
    // public array $errors = [];
    // public array $rawData = [];

    public stdClass $taxonomyItem;
    public stdClass $content;

    public function __construct(array $data)
    {
        $this->taxonomyItem = new stdClass();
        $this->content = new stdClass();

        $this->rawData = $data;

        $image_path = '/media/categories/banner/';
        $slider_image_path = '/media/categories/slider/';


        //If taxonomy_item_id is set then use the existing taxonomy_item_id and update only the field exist in csv
        //  else create a new one and make sure all the requied fields will have proper value

        if(isset($data['taxonomy_item_id'])) $this->taxonomyItem->taxonomy_item_id =  $this->validateInteger($data['taxonomy_item_id'], 'taxonomy_item_id') ?? null;
        if(isset($data['category_name']))$this->taxonomyItem->name = $this->validateString($data['category_name'], 'name', 191) ?? '';
        if(isset($data['parent_id']))$this->taxonomyItem->parent_id =  $this->validateInteger($data['parent_id'], 'parent_id') ?? null;
        if($this->taxonomyItem->parent_id === 0) $this->taxonomyItem->parent_id = null;
        
        if(isset($data['slug'])) $this->taxonomyItem->taxonomy_item_code =  $this->validateString($data['slug'], 'taxonomy_item_code', 191) ?? '';
        if(isset($data['taxonomy_id'])) $this->taxonomyItem->taxonomy_id =  $this->validateInteger($data['taxonomy_id'], 'taxonomy_id', 1, true) ?? null;
        if(isset($data['item_id'])) $this->taxonomyItem->item_id =  ($this->validateInteger($data['item_id'], 'item_id') ?? null);
        if(isset($data['sort_order'])) $this->taxonomyItem->sort_order =  ($this->validateInteger($data['sort_order'], 'sort_order', 0) ?? 0) ?? 0;
        if(isset($data['status'])) $this->taxonomyItem->status =  $this->validateInteger($data['status'], 'status', 0) ?? 0;
        
        // strings
        if(isset($data['template'])) $this->taxonomyItem->template =  $this->validateString($data['template'], 'template', 1000) ?? '';
        else $this->taxonomyItem->template = '';
        if(isset($data['color'])) $this->taxonomyItem->color =  $this->validateString($data['color'], 'color', 1000) ?? null;
        
        // JSON
        if(isset($data['category_banner']) && $data['category_banner']) $this->taxonomyItem->image = $this->validateJson($image_path . $data['category_banner'], 'category_banner') ?? null;
        if(isset($data['category_slider_image']) && $data['category_slider_image']) $this->taxonomyItem->slider_image = $this->validateJson($slider_image_path . $data['category_slider_image'], 'slider_image') ?? null;
        if(isset($data['label'])) $this->taxonomyItem->label_name = $this->validateString($data['label'], 'label', 191) ?? null;
         // booleans/flags as ints
        if(isset($data['is_featured'])) $this->taxonomyItem->is_featured =  ($this->validateInteger($data['is_featured'], 'is_featured', 0) ?? 0) ?? 0;

        // content strings
        if(isset($data['language_id'])) $this->content->language_id =  ($this->validateInteger($data['language_id'], 'language_id', 1) ?? 1) ?? 1;
        if(isset($data['name'])) $this->content->name =  $this->validateString($data['name'], 'name', 191) ?? '';
        if(isset($data['slug'])) $this->content->slug =  $this->generateSlugFromName($data['slug'], 'slug') ?? '';
        if(isset($data['content'])) $this->content->content =  $this->validateString($data['content'], 'content', 191) ?? '';
        if(isset($data['meta_title'])) $this->content->meta_title =  $this->validateString($data['meta_title'], 'meta_title', 191) ?? '';
        if(isset($data['meta_keywords'])) $this->content->meta_keywords =  $this->validateString($data['meta_keywords'], 'meta_keywords', 191) ?? '';
        if(isset($data['meta_description'])) $this->content->meta_description = $this->validateString($data['meta_description'], 'meta_description', 191) ?? '';
        // if(isset($data['meta_description'])) $this->content->content =  $this->validateString($data['meta_description'], 'content', 1000) ?? '';
        if(isset($data['link'])) $this->content->link =  $this->validateString($data['link'], 'link', 191) ?? '';
        if(isset($data['products_link'])) $this->content->products_link =  $this->validateString($data['products_link'], 'products_link', 191) ?? '';
    }

    /**
     * Generate slug from name if not provided
     */
    public function toArray(): array
    {
        return [
            'taxonomyItem' => (array) $this->taxonomyItem,
            'content' => (array) $this->content
        ];
    }

}


