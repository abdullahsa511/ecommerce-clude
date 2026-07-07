<?php

declare(strict_types=1);

namespace App\Core\Models\Checkout;

use App\Core\Models\Base\Model;

class Voucher extends Model
{
    public int $voucher_id;
    public int $order_id;
    public string $code;
    public string $from_name;
    public string $from_email;
    public string $to_name;
    public string $to_email;
    public string $message;
    public float $credit;
    public int $status;
    public string $created_at;
        
    public function __construct() 
    {
        parent::__construct();
    }


    /**
     * Define relationship with VoucherLog model
     */
    public function voucherLog()
    {
        return $this->hasMany(VoucherLog::class, 'voucher_id');
    }
} 