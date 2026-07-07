<?php

declare(strict_types=1);

namespace App\Core\Models\Cart;

use App\Core\Models\Base\Model;

class CouponProduct extends Model
{
    public int $coupon_product_id;
    public int $coupon_id;
    public int $product_id;

    public function __construct()
    {
        parent::__construct();
    }
} 