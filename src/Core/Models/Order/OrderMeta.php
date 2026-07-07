<?php

declare(strict_types=1);

namespace App\Core\Models\Order;

use App\Core\Models\Base\Model;

class OrderMeta extends Model
{
    public int $meta_id;
    public int $order_id;
    public ?string $key;
    public ?string $value;

    public function __construct() 
    {
        parent::__construct();
    }
} 