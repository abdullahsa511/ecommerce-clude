<?php

declare(strict_types=1);

namespace App\Core\Models\Subscription;

use App\Core\Models\Base\Model;

class OrderSubscription extends Model
{
    public int $order_subscription_id;
    public int $order_id;
    public int $subscription_id;
    public int $subscription_plan_id;
    public int $subscription_status_id;
    public string $trial_start;
    public string $trial_end;
    public string $start_date;
    public string $end_date;
    public int $trial_remaining;
    public int $remaining;
    public string $created_at;
    public string $updated_at;

    public function __construct() 
    {
        parent::__construct();
    }
} 