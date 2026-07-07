<?php

declare(strict_types=1);

namespace App\Core\Models\Product;

use App\Core\Models\Base\Model;

class ProductImage extends Model
{
    public int $product_image_id;
    public int $product_id;
    public string $image;
    public int $sort_order;
    public string $created_at;
    public string $updated_at;

    public function __construct() 
    {
        parent::__construct();
    }
} 