<?php

declare(strict_types=1);

namespace App\Core\Models\Cart;

use App\Core\Models\Base\Model;

class CouponLog extends Model
{
    public int $coupon_log_id;
    public int $coupon_id;
    public int $order_id;
    public int $user_id;
    public float $discount;
    public string $created_at;

    public function __construct()
    {
        parent::__construct();
    }
} 