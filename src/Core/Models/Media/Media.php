<?php

declare(strict_types=1);

namespace App\Core\Models\Media;

use App\Core\Models\Base\Model;

class Media extends Model
{
    public int $media_id;
    public string|array|null $file;
    public string $type;
    public ?string $meta;
    public ?int $parent_id;
    public ?int $folder_id;
    public ?string $name;
    public ?string $created_at;
    public ?string $updated_at;
    public ?int $product_id;
    public ?int $project_id;
    public ?int $project_section_id;
    public ?int $post_id;
    public ?int $showroom_id;
    public ?string $path;
    public ?int $design_resource_id;
    public ?string $product_code;


    /**
     * Define relationship with MediaContent model
     */
    public function mediaContent()
    {
        return $this->hasMany(MediaContent::class, 'media_id');
    }

    public function __construct() 
    {
        parent::__construct();
    }
} 