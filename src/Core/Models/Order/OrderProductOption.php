<?php

declare(strict_types=1);

namespace App\Core\Models\Order;

use App\Core\Models\Base\Model;

class OrderProductOption extends Model
{
    public int $order_product_option_id;
    public int $order_id;
    public int $order_product_id;
    public int $product_option_id;
    public int $product_option_value_id;
    public string $option;
    public string $name;
    public float $price;
    public string $type;

    /**
     * Define relationship with Order model
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    /**
     * Define relationship with OrderProduct model
     */
    public function orderProduct()
    {
        return $this->belongsTo(OrderProduct::class, 'order_product_id');
    }

    public function __construct() 
    {
        parent::__construct();
    }
} 