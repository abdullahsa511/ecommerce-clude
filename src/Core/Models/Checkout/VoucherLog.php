<?php

declare(strict_types=1);

namespace App\Core\Models\Checkout;

use App\Core\Models\Base\Model;

class VoucherLog extends Model
{
    public int $voucher_log_id;
    public int $voucher_id;
    public int $order_id;
    public float $credit;
    public string $created_at;

    public function __construct() 
    {
        parent::__construct();
    }
} 