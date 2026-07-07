<?php

declare(strict_types=1);

namespace App\Core\Models\Component;

use App\Core\Models\Base\Model;

class Component extends Model
{
    public ?int $component_id;
    public ?string $name;
    public ?string $section_title;
    public ?string $section_subtitle;
    public ?string $section_link;
    public ?string $title;
    public ?string $subtitle;
    public ?string $description;
    public ?string $image;
    public ?string $mobile_banner;
    public mixed $images;
    public mixed $links;
    public mixed $buttons;
    public ?string $template;
    public ?string $banner_way_points;

        
    public function __construct() 
    {
        parent::__construct();
    }

    public function items()
    {
        return $this->hasMany(ComponentItem::class, 'component_id', 'component_id');
    }
    public function metaProperties()
    {
        return $this->hasMany(ComponentMeta::class, 'component_id', 'component_id');
    }

}
