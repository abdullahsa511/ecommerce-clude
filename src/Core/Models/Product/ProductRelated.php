<?php

declare(strict_types=1);

namespace App\Core\Models\Product;

use App\Core\Models\Base\Model;

class ProductRelated extends Model
{
    public int $product_id;
    public int $product_related_id;

    public function __construct() 
    {
        parent::__construct();
    }
} 