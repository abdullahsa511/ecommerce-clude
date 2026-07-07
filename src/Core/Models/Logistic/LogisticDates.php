<?php

declare(strict_types=1);

namespace App\Core\Models\Logistic;

use App\Core\Models\Base\Model;

class LogisticDates extends Model
{
    public int $logistic_dates_id;
    public int $orders_install_date_id;
    public string $uuid;
    public int $order_id;
    public int $logistic_types_id;
    public int $user_id;
    public int $logistic_statuses_id;
    public string $date;
    public int $sort_order;
    public int $mins;    
    public int $drive_mins;
    public int $drive_kms;
    public string $time_pref;
    public bool $calc;
    public string $expected_start;
    public string $expected_end;
    public string $actual_start;
    public string $actual_end;
    public int $actual_mins;
    public string $customer_name;
    public bool $time_block;
    public string $address;
    public float $latitude;
    public float $longitude;
    public bool $send_email;
    public bool $email_confirmed;
    public bool $email_alerted;
    public bool $load_up;
    public float $actual_cost;
    public bool $actual_cost_updated;
    public string $notes;
    public string $deleted_at;
    public string $created_at;
    public string $updated_at;

    
    protected string $table = 'logistic_dates';
    protected string $primaryKey = 'logistic_dates_id';
    
    public function __construct() 
    {
        parent::__construct();
    }
} 