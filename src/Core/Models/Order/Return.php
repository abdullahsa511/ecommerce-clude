<?php

declare(strict_types=1);

namespace App\Core\Models\Order;

use App\Core\Models\Base\Model;
use App\Core\Models\Product\Product;

class ReturnObject extends Model
{
    protected string $table = 'return';
    
    public int $return_id;
    public int $order_id;
    public string $customer_order_id;
    public int $product_id;
    public int $user_id;
    public string $first_name;
    public string $last_name;
    public string $email;
    public string $phone_number;
    public string $product;
    public string $model;
    public int $quantity;
    public int $opened;
    public int $return_reason_id;
    public int $return_resolution_id;
    public int $return_status_id;
    public string $note;
    public string $date_ordered;
    public string $created_at;
    public string $updated_at;

    /**
     * Define relationship with ReturnProduct model
     */
    public function product()
    {
        return $this->hasOne(Product::class, 'product_id');
    }

    /**
     * Define relationship with ReturnReason model
     */
    public function returnReason()
    {
        return $this->belongsTo(ReturnReason::class, 'return_reason_id');
    }

    /**
     * Define relationship with ReturnResolution model
     */
    public function returnResolution()
    {
        return $this->belongsTo(ReturnResolution::class, 'return_resolution_id');
    }

    /**
     * Define relationship with ReturnStatus model
     */
    public function returnStatus()
    {
        return $this->belongsTo(ReturnStatus::class, 'return_status_id');
    }

    /**
     * Define relationship with Order model
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function __construct() 
    {
        parent::__construct();
    }
}