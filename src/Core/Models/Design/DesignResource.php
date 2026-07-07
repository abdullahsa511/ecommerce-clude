<?php

declare(strict_types=1);

namespace App\Core\Models\Design;


use App\Core\Models\Base\Model;

class DesignResource extends Model
{
    protected string $table = 'design_resource';
    protected string $primaryKey = 'design_resource_id';

    public int $design_resource_id;
    public ?string $img;
    public ?string $title;
    public ?string $description;
    public ?string $resource_type;
    public ?string $link_text;
    public ?string $grade;
    public ?string $slug;
    public ?string $type;
    public ?string $tag;
    public ?string $brand;
    public ?string $img2;
    public ?string $is_featured;
    public ?string $image_url;
    public ?string $image_thumb_url;

    public function design_resource_documents() 
    {
        return $this->hasMany(DesignResourceDocument::class, 'design_resource_id', 'design_resource_id');
    }
    
}
