<?php

declare(strict_types=1);

namespace App\Core\Models\Order;

use App\Core\Models\Base\Model;

class OrderShipment extends Model
{
    public int $order_shipment_id;
    public int $order_id;
    public string $shipping_method;
    public string $tracking_number;
    public string $created_at;

    public function __construct() 
    {
        parent::__construct();
    }
} 