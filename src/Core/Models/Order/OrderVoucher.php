<?php

declare(strict_types=1);

namespace App\Core\Models\Order;

use App\Core\Models\Base\Model;
use App\Core\Models\Checkout\Voucher;

class OrderVoucher extends Model
{
    public int $order_voucher_id;
    public int $order_id;
    public int $voucher_id;
    public string $content;
    public string $voucher;
    public string $from_name;
    public string $from_email;
    public string $to_name;
    public string $to_email;
    public string $message;
    public float $amount;

    public function __construct() 
    {
        parent::__construct();
    }

    public function voucher()
    {
        return $this->hasOne(Voucher::class, 'voucher_id');
    }
} 