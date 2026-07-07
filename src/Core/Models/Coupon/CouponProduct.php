<?php

declare(strict_types=1);

namespace App\Core\Models\Coupon;

use App\Core\Models\Base\Model;
use App\Core\Models\Product\Product;

class CouponProduct extends Model
{
    protected string $table = 'coupon_product';
    // protected string $tableAlias = 'cp';

    protected int $coupon_product_id;
    protected int $coupon_id;
    protected int $product_id;
    protected string $created_at;
    protected string $updated_at;

    public function __construct() 
    {
        parent::__construct();
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class, 'coupon_id', 'coupon_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}

class CouponProductData
{
    public ?int $coupon_product_id;
    public ?int $coupon_id;
    public ?int $product_id;

    public function __construct(array $data = [])
    {
        $this->coupon_product_id = $data['coupon_product_id'] ?? null;
        $this->coupon_id = $data['coupon_id'] ?? null;
        $this->product_id = $data['product_id'] ?? null;
    }

    public function toArray(): array
    {
        return [
            'coupon_product_id' => $this->coupon_product_id,
            'coupon_id' => $this->coupon_id,
            'product_id' => $this->product_id
        ];
    }
} 