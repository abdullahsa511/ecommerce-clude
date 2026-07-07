<?php

declare(strict_types=1);

namespace App\Core\Models\Order;

use App\Core\Models\Base\Model;

class OrderTotal extends Model
{
    public int $order_total_id;
    public int $order_id;
    public string $key;
    public string $title;
    public float $value;
    public int $sort_order;

    public function __construct() 
    {
        parent::__construct();
    }
} 