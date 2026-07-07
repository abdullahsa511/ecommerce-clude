<?php

declare(strict_types=1);

namespace App\Core\Models\Cart;

use App\Core\Models\Base\Model;

class Coupon extends Model
{
    public int $coupon_id;
    public string $name;
    public string $code;
    public string $type;
    public float $discount;
    public float $total;
    public int $limit;
    public string $limit_user;
    public int $logged_in;
    public int $free_shipping;
    public int $status;
    public ?string $from_date;
    public ?string $to_date;
    public string $created_at;
    public string $updated_at;

    
    public function __construct() 
    {
        parent::__construct();
    }
    /**
     * Define relationship with CouponProduct model
     */
    public function couponProduct()
    {
        return $this->hasMany(CouponProduct::class, 'coupon_id');
    }

    /**
     * Define relationship with CouponTaxonomy model
     */
    public function couponTaxonomy()
    {
        return $this->hasMany(CouponTaxonomy::class, 'coupon_id');
    }

    /**
     * Define relationship with CouponLog model
     */
    public function couponLog()
    {
        return $this->hasMany(CouponLog::class, 'coupon_id');
    }
} 