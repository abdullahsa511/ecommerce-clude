<?php

declare(strict_types=1);

namespace App\Core\Models\Post;

use App\Core\Models\Base\Model;

class PostTag extends Model
{
    // Core properties
    public int $post_tag_id;
    public string $name = '';
    public string $slug = '';
    public string $description = '';
    public ?string $image = null;
    public int $status;
    public ?int $post_id;


    public function __construct() 
    {
        parent::__construct();
    }
} 

class PostaTagData{
    public int $post_tag_id;
    public string $name = '';
    public string $slug = '';
    public string $description = '';
    public ?string $image = null;
    public int $status;
    public ?int $post_id;

    public function __construct(array $data) 
    {
        if(isset($data['post_id'])) $this->post_id = $data['post_id'];
        if(isset($data['name'])) $this->name = $data['name'];
        if(isset($data['slug'])) $this->slug = $data['slug'];
        if(isset($data['description'])) $this->description = $data['description'];
        if(isset($data['image'])) $this->image = json_encode($data['image']);
        if(isset($data['status'])) $this->status = (int)$data['status'];
    }


    public function toArray(): array
    {
        $data = [];
        if(isset($this->post_tag_id)) $data ['post_tag_id'] = $this->post_tag_id;
        if(isset($this->name)) $data ['name'] = $this->name;
        if(isset($this->slug)) $data ['slug'] = $this->slug;
        if(isset($this->description)) $data ['description'] = $this->description;
        if(isset($this->image)) $data ['image'] = $this->image;
        if(isset($this->status)) $data ['status'] = $this->status;
        if(isset($this->post_id)) $data ['post_id'] = $this->post_id;
        return $data;
    }
}