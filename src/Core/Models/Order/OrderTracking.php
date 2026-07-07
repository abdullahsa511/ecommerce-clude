<?php

declare(strict_types=1);

namespace App\Core\Models\Order;

use App\Core\Models\Base\Model;
use App\Core\Models\Order\OrderStatus;

use stdClass;

class OrderTracking extends Model
{
    protected string $table = 'order_tracking';

    protected int $order_tracking_id;
    protected int $order_id;
    protected int $order_status_id;
    protected ?string $comment;
    protected string $created_at;
    protected string $updated_at;


    public function __construct() 
    {
        parent::__construct();
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    public function Status()
    {
        return $this->belongsTo(OrderStatus::class, 'order_status_id', 'order_status_id');
    }
} 

