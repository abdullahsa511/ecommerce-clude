<?php

declare(strict_types=1);

namespace App\Core\Models\Subscription;

use App\Core\Models\Base\Model;

class SubscriptionPlanContent extends Model
{
    public int $subscription_plan_id;
    public int $language_id;
    public string $name;


    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function __construct() 
    {
        parent::__construct();
    }

    public function getPrimaryKey(): string
    {
        return 'subscription_plan_id';
    }
} 