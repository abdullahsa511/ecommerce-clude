<?php

declare(strict_types=1);

namespace App\Core\Models\Component;

use App\Core\Models\Base\Model;
use function App\Core\System\utils\session;

class ComponentItem extends Model
{
    public int $component_item_id;
    public int $component_id;
    public string $property_name;
    public ?string $model;
    public ?string $item_count;
    public bool|string|int|null $is_recent;
    public bool|string|int|null $is_featured;
    public string $fields;
    public ?string $related_models;
    public ?string $description;
    public ?string $title;
    public ?string $subtitle;
    public ?string $link_text;

    public function __construct() 
    {
        parent::__construct();
    }


}
