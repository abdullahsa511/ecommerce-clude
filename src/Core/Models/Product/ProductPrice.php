<?php

declare(strict_types=1);

namespace App\Core\Models\Product;

use App\Core\Models\Base\Model;
use App\Core\Models\User\CustomerSegment;

class ProductPrice extends Model
{
    public int $product_price_id;
    public int $product_id;
    public int $customer_segment_id;
    public string $price;

    // protected string $primaryKey = 'attribute_id';

    public function __construct() 
    {
        parent::__construct();
    }
    public function customerSegment()
    {
        return $this->belongsTo(CustomerSegment::class, 'customer_segment_id');
    }
} 