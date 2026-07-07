<?php

namespace App\Core\Models\Component;

use stdClass;

class ComponentItemData {
    public mixed  $component_item_id;
    public mixed  $property_name;
    public mixed  $title;
    public mixed  $subtitle;
    public mixed  $description;
    public mixed  $component_id;
    public mixed  $model;
    public mixed  $item_count = 1;
    public bool|null  $is_recent;
    public bool|null  $is_featured;
    public mixed  $fields;
    public mixed  $related_models;
    public mixed  $link_text;

    public function __construct(array $data, bool $convertForDb = false)
    {
        if(isset($data['component_item_id']))$this->component_item_id = $data['component_item_id'];
        if(isset($data['property_name']))$this->property_name = $data['property_name'];
        if(isset($data['title']))$this->title = $data['title'];
        if(isset($data['subtitle']))$this->subtitle = $data['subtitle'];
        if(isset($data['description']))$this->description = $data['description'];
        if(isset($data['component_id']))$this->component_id = $data['component_id'];
        if(isset($data['model']))$this->model = $data['model'];
        if(isset($data['item_count']))$this->item_count = $data['item_count'];
        if(array_key_exists('is_recent', $data))$this->is_recent = $data['is_recent'];
        if(array_key_exists('is_featured', $data))$this->is_featured = $data['is_featured'];
        if(isset($data['fields'])){
            if($convertForDb){
                $this->fields = isset($data['fields']) && is_array($data['fields']) ? json_encode($data['fields']) : null;
            }else{
                $this->fields = isset($data['fields']) ? json_decode($data['fields'], true) : null;
            }
        }
        if(isset($data['related_models'])){
            if($convertForDb){
                $this->related_models = isset($data['related_models']) && is_array($data['related_models']) ? json_encode($data['related_models']) : null;
            }else{
                $this->related_models = isset($data['related_models']) ? json_decode($data['related_models'], true) : null;
            }
        }
        if(isset($data['link_text']))$this->link_text = $data['link_text'];
    }

    public function toArray(): array
    {
        $data = [];
        if(isset($this->component_item_id)) $data['component_item_id'] = $this->component_item_id;
        if(isset($this->property_name)) $data['property_name'] = $this->property_name;
        if(isset($this->title)) $data['title'] = $this->title;
        if(isset($this->subtitle)) $data['subtitle'] = $this->subtitle;
        if(isset($this->description)) $data['description'] = $this->description;
        if(isset($this->component_id)) $data['component_id'] = $this->component_id;
        if(isset($this->model)) $data['model'] = $this->model;
        if(isset($this->item_count)) $data['item_count'] = $this->item_count;
        if(isset($this->is_recent)) $data['is_recent'] = $this->is_recent ? 1 : 0;
        if(isset($this->is_featured)) $data['is_featured'] = $this->is_featured ? 1 : 0;
        if(isset($this->fields)) $data['fields'] = $this->fields;
        if(isset($this->related_models)) $data['related_models'] = $this->related_models;
        if(isset($this->link_text)) $data['link_text'] = $this->link_text;
        return $data;
    }
}