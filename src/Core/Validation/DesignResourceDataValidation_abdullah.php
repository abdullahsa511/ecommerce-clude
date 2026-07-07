<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;

class DesignResourceDataValidation extends Validation
{
    public stdClass $design_resource;
    public stdClass $media;

    public function __construct(array $data, string $type, array $requiredFields = [], array $textFields = [], array $existingData = ["designResourceIds" => [], 'designResourceTitles' => []])
    {
        $this->rawData = $data;
        parent::__construct($requiredFields, $textFields);
        $this->design_resource = new stdClass();
        $this->media = new stdClass();

        $imagePath = '/media/Products/image/';

        match ($type) {
            'finishes' => $this->media->path = '/media/design-resource/finishes/',
            'textiles' => $this->media->path = '/media/design-resource/textiles/',
            default => $this->media->path = '/media/Products/image/',
        };
        if(in_array($type, ['finishes', 'textiles'])){
            $imagePath = $this->media->path;
        }
        

        // DESIGN RESOURCE TABLE
        if(isset($data['media_id']))$this->design_resource->media_id = $this->validateInteger($data['media_id'], 'media_id');
        if(isset($data['title']))$this->design_resource->title = $this->validateString($data['title'], 'title', 191);
        if(isset($data['description']))$this->design_resource->description = $this->validateText($data['description'], 'description');
        if(isset($data['resource_type']))$this->design_resource->resource_type = $this->validateString($data['resource_type'], 'resource_type', 191);
        if(isset($data['link_text']))$this->design_resource->link_text = $this->validateString($data['link_text'], 'link_text', 191);
        if(isset($data['grade']))$this->design_resource->grade = $this->validateString($data['grade'], 'grade', 191);
        if(isset($data['slug']))$this->design_resource->slug = $this->validateSlug($data['slug'], 'slug', ['title', 'resource_type']);
        if(isset($data['img']))$this->design_resource->img = isset($data['img']) ? $this->validateJson($data['img'], 'img', $imagePath) : json_encode([]);
        if(isset($data['img2']))$this->design_resource->img2 = isset($data['img2']) ? $this->validateJson($data['img2'], 'img2', $imagePath) : json_encode([]);
        if(isset($data['brand']))$this->design_resource->brand = $this->validateString($data['brand'], 'brand', 255);
        if(isset($data['type']))$this->design_resource->type = $this->validateString($data['type'], 'type', 191);
        if(isset($data['title']))$this->design_resource->slug = $this->validateSlug($data['title'], 'title');
        // if(isset($data['is_featured']))$this->design_resource->is_featured = $this->validateBoolean($data['is_featured'], 'is_featured');
        
        if(isset($existingData['designResourceTitles'][$data['title']])){
            $this->isExistingData = true;
            $this->design_resource->design_resource_id = $existingData['designResourceTitles'][$data['title']];
        }else if(isset($data['design_resource_id'])) {
            $this->design_resource->design_resource_id = $this->validateInteger($data['design_resource_id'], 'design_resource_id', 0);
            if(isset($existingData['designResourceIds'][$data['design_resource_id']])){
                $this->isExistingData = true;
            }
        }
    }

    public function toArray(): array
    {
        return [
            'design_resource' => (array)$this->design_resource,
        ];
    }
}
