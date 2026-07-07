<?php

namespace App\Core\Models\Component;

use stdClass;

class ComponentData {
    public ?int $component_id;
    public ?string $name;
    public ?string $section_title;
    public ?string $section_subtitle;
    public ?string $section_link;
    public ?string $title;
    public ?string $subtitle;
    public ?string $description;
    public array|string|null $image;
    public array|string|null $mobile_banner;
    public array|string|null $images;
    public array |string|null $links;
    public array|string|null $buttons;
    public ?string $template;
    public ?stdClass $properties;
    public array|string|null $items;
    public array $banner_way_points = [];
    public array|string|null $options;

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}