<?php

declare(strict_types=1);

namespace App\Core\Models\Order;

use App\Core\Models\Base\Model;

class OrderProduct extends Model
{
    public int $order_product_id;
    public int $order_id;
    public int $product_id;
    public string $name;
    public string $model;
    public int $quantity;
    public float $price;
    public float $total;
    public float $tax;
    public int $points;

    /**
     * Define relationship with Order model
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Define relationship with OrderProductOption model
     */
    public function orderProductOptions()
    {
        return $this->hasMany(OrderProductOption::class, 'order_product_id');
    }

    public function __construct() 
    {
        parent::__construct();
    }
} 