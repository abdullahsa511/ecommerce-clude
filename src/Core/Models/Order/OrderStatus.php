<?php

declare(strict_types=1);

namespace App\Core\Models\Order;

use App\Core\Models\Base\Model;

class OrderStatus extends Model
{
    public int $order_status_id;
    public int $language_id;
    public string $name;

    public function __construct() 
    {
        parent::__construct();
    }
} 