<?php

declare(strict_types=1);

namespace App\Core\Models\Subscription;

use App\Core\Models\Base\Model;

class Subscription extends Model
{
    public int $subscription_id;
    public int $order_id;
    public string $email;
    public int $order_product_id;
    public int $site_id;
    public int $user_id;
    // public int $payment_address_id;
    public string $payment_method;
    // public int $shipping_address_id;
    public string $shipping_method;
    public int $product_id;
    public int $quantity;
    // public int $subscription_plan_id;
    public float $price;
    public string $period;
    public int $cycle;
    public int $length;
    public int $left;
    public float $trial_price;
    public string $trial_period;
    public int $trial_cycle;
    public int $trial_length;
    public int $trial_left;
    public int $trial_status;
    public string $date_next;
    public int $subscription_status_id;
    public string $notes;
    public string $created_at;
    public string $updated_at;

    public function __construct() 
    {
        parent::__construct();
    }

    public function subscriptionPlanContent()
    {
        return $this->hasOne(SubscriptionPlanContent::class, 'subscription_id', 'subscription_id');
    }
} 