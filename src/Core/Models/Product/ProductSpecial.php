<?php

declare(strict_types=1);

namespace App\Core\Models\Product;

use App\Core\Models\Base\Model;

class ProductSpecial extends Model
{
    public int $product_special_id;
    public int $product_id;
    public int $customer_group_id;
    public int $priority;
    public float $price;
    public string $date_start;
    public string $date_end;
    public string $created_at;
    public string $updated_at;

    public function __construct() 
    {
        parent::__construct();
    }
} 