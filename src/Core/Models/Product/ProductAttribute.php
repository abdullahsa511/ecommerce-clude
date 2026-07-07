<?php

declare(strict_types=1);

namespace App\Core\Models\Product;

use App\Core\Models\Base\Model;

class ProductAttribute extends Model
{
    public int $product_id;
    public int $attribute_id;
    public int $language_id;
    public string $value;
    public int $attribute_group_id;
    public int $sort_order;
    public string $name;
    public string $description;
    public string $metadata;
    public string $type;
    public string $image;

    // protected string $primaryKey = 'attribute_id';

    public function __construct() 
    {
        parent::__construct();
    }
} 