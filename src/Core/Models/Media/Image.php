<?php

declare(strict_types=1);

namespace App\Core\Models\Media;


class Image
{
    public ?int $project_id;
    public ?int $post_id;
    public ?int $project_image_id;
    public ?int $post_image_id;
    public ?int $product_id;
    public ?int $product_image_id;
    public ?int $product_certificate_id;
    public string $image;
    public string $name;
    public ?string $path;
    public string $description;
    public int|string $size;
    public string $type;
    public string $objectURL;
    public ImageStatus|string|array $status;
    public int $sort_order;
    public string|null $created_at;

    public function __construct(array $data)
    {
        $this->project_id = $data['project_id']??null;
        $this->post_id = $data['post_id']??null;
        $this->product_certificate_id = $data['product_certificate_id']??null;
        $this->image = $data['image']??'';
        $this->name = $data['name']??'';
        $this->path = $data['path']??'';
        $this->description = $data['description']??'';
        $this->size = $data['size']??0;
        $this->type = $data['type']??'';
        $this->objectURL = $data['objectURL']??'';
        $this->status = isset($data['status'])?new ImageStatus($data['status']):['severity' => 'pending'];
        $this->sort_order = $data['sort_order']??0;
        $this->created_at = $data['created_at'] ?? null;
    }

    public function toArray(): array
    {
        $array = [
            'image_link' => $this->image,
            'image' => json_encode([
                'image' => $this->image,
                'objectURL' => $this->objectURL,
                'name' => $this->name,
                'description' => $this->description,
                'size' => $this->size,
                'type' => $this->type,
            ]),
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at,
            'status' => json_encode($this->status),
        ];
        if(isset($this->project_id)) $array['project_id'] = $this->project_id;
        if(isset($this->post_id)) $array['post_id'] = $this->post_id;
        if(isset($this->project_image_id)) $array['project_image_id'] = $this->project_image_id;
        if(isset($this->product_certificate_id)) $array['product_certificate_id'] = $this->product_certificate_id;
        if(isset($this->post_image_id)) $array['post_image_id'] = $this->post_image_id;
        return $array;
    }
}

class ImageStatus {
    public string $name;
    public string $severity;

    public function __construct(array $data)
    {
        if(isset($data['name'])) $this->name = $data['name'];
        if(isset($data['severity'])) $this->severity = $data['severity'];
    }
}