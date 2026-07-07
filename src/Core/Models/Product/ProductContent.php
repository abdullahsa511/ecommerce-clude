<?php

declare(strict_types=1);

namespace App\Core\Models\Product;

use App\Core\Models\Base\Model;

class ProductContent extends Model
{
    public int $product_id;
    public int $language_id;
    public string $name;
    public string $slug;
    public string $content;
    public string $title;
    public string $tag_line;
    public ?string $rules;
    public string $tag;
    public string $meta_title;
    public string $meta_description;
    public string $meta_keywords;

    public function __construct() 
    {
        parent::__construct();
    }
} 