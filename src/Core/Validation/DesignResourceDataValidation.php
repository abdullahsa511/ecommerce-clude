<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;

class DesignResourceDataValidation extends Validation
{
    public stdClass $product_resource; // single data for product resource
    public stdClass $design_resource; // single data for design resource
    public stdClass $media; // single data for media
    public stdClass $resource_document; // multiple data for resource document

    public function __construct(array $data, string $type, array $requiredFields = [], array $textFields = [], array $productData = [])
    {
        $this->rawData = $data;
        parent::__construct($requiredFields, $textFields);
        $this->product_resource = new stdClass();
        $this->design_resource = new stdClass();
        $this->media = new stdClass();
        $this->resource_document = new stdClass();

        $imagePath = '/media/Products/image/';
        match ($type) {
            'finishes' => $this->media->path = '/media/design-resource/finishes/',
            'textiles' => $this->media->path = '/media/design-resource/textiles/',
            'models' => $this->media->path = '/media/design-resource/models/',
            'documents' => $this->media->path = '/media/design-resource/documents/',
             default => $this->media->path = '/media/Products/image/',
        };
        if(in_array($type, ['finishes', 'textiles'])){
            $imagePath = $this->media->path;
        }
        
        // DESIGN RESOURCE TABLE
        $title = isset($data['title']) ? $data['title'] : null;
        if (!isset($title) || empty($title)) {
            $this->addError('title', 'Title is required');
        }
        $fileName = in_array($type, ['documents', 'models']) ? $data['file_name'] ?? null : $data['img'] ?? null;

        $imageUrl = isset($data['image_url']) ?  $this->media->path . '400X400/' . $data['image_url'] : null;
        $imageThumbUrl = isset($data['image_thumb_url']) ?  $this->media->path . '258X258/' . $data['image_thumb_url'] : null;

        if(isset($data['media_id']))$this->design_resource->media_id = $this->validateInteger($data['media_id'], 'media_id');
        if(isset($title))$this->design_resource->title = $this->validateString($title, 'title', 191);
        if(isset($data['description']))$this->design_resource->description = $this->validateText($data['description'], 'description');
        if(isset($data['resource_type']))$this->design_resource->resource_type = $type;
        if(isset($data['link_text']))$this->design_resource->link_text = $this->validateString($data['link_text'], 'link_text', 191);
        if(isset($data['grade']))$this->design_resource->grade = $this->validateString($data['grade'], 'grade', 191);
        if(isset($data['slug']))$this->design_resource->slug = $this->validateSlug($data['slug'], 'slug');
        if(isset($data['image_url']))$this->design_resource->img = isset($data['image_url']) ? $this->validateJson($data['image_url'], 'img', $imagePath) : json_encode([]);
        if(isset($data['img2']))$this->design_resource->img2 = isset($data['img2']) ? $this->validateJson($data['img2'], 'img2', $imagePath) : json_encode([]);
        if(isset($data['brand']))$this->design_resource->brand = $this->validateString($data['brand'], 'brand', 255);
        if(isset($data['type']))$this->design_resource->type = $this->validateString($data['type'], 'type', 191);
        if(isset($data['tag']))$this->design_resource->tag = $this->validateString($data['tag'], 'tag', 191);
        if(isset($title)) $this->design_resource->slug = $this->validateSlug($title, 'title');
        if(isset($data['img'])) $this->design_resource->media_path = $this->media->path . $fileName;
        if(isset($imageUrl))$this->design_resource->image_url = $imageUrl;
        if(isset($imageThumbUrl))$this->design_resource->image_thumb_url = $imageThumbUrl;
        if(isset($data['sort_order']))$this->design_resource->sort_order = $this->validateInteger($data['sort_order'], 'sort_order');
        // if(isset($data['is_featured']))$this->design_resource->is_featured = $this->validateBoolean($data['is_featured'], 'is_featured');

        if(in_array($type, ['documents', 'models'])){
            // product resource data
            $this->product_resource->resource_title = $this->design_resource->title;
            $this->product_resource->resource_type = $this->design_resource->resource_type;
            if(isset($productData[$data['product_code']])){
                $this->product_resource->product_id = $productData[$data['product_code']];
            }else{
                $this->addError('product_code', 'Product code not found');
            }
            // resource document data
            $this->resource_document->name = $fileName;
            $this->resource_document->format = $data['file_type'] ?? null;
            $this->resource_document->url = $this->media->path . $fileName;
            $this->resource_document->design_resource_title = $this->design_resource->title;
        }

        // media data
        $this->media->name = $title ?? null;
        $this->media->type = $type == 'documents' ? 'document' : 'image';
        $this->media->path = $this->media->path . $fileName;
        $this->media->file = $this->validateJson($fileName, 'file', $this->media->path);
    }

    public function toArray(): array
    {
        return [
            'design_resource' => (array)$this->design_resource,
        ];
    }
}
