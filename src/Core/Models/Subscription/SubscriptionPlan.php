<?php

declare(strict_types=1);

namespace App\Core\Models\Subscription;


use App\Core\Models\Base\Model;


class SubscriptionPlan extends Model
{
    // Core subscription plan properties
    public int $subscription_plan_id;
    public string $period;
    public int $length;
    public int $cycle;
    public string $trial_period;
    public int $trial_length;
    public int $trial_cycle;
    public int $trial_status;
    public int $status;
    public int $sort_order = 0;


    public function subscriptionPlanContent()
    {
        return $this->hasOne(SubscriptionPlanContent::class, 'subscription_plan_id', 'subscription_plan_id');
    }

    /**
     * Check if the subscription plan is active
     */
    public function isActive(): bool
    {
        return $this->status === 1;
    }

    /**
     * Check if the subscription plan has trial period
     */
    public function hasTrial(): bool
    {
        return $this->trial_status === 1;
    }

    /**
     * Get the trial period in days
     */
    public function getTrialPeriodInDays(): int
    {
        if (!$this->hasTrial()) {
            return 0;
        }

        $multiplier = match($this->trial_period) {
            'day' => 1,
            'week' => 7,
            'month' => 30,
            'year' => 365,
            default => 0
        };

        return $this->trial_length * $multiplier;
    }

    /**
     * Get the subscription period in days
     */
    public function getPeriodInDays(): int
    {
        $multiplier = match($this->period) {
            'day' => 1,
            'week' => 7,
            'month' => 30,
            'year' => 365,
            default => 0
        };

        return $this->length * $multiplier;
    }

    /**
     * Get the total subscription duration in days
     */
    public function getTotalDurationInDays(): int
    {
        return $this->getPeriodInDays() * $this->cycle;
    }

    public function __construct() 
    {
        parent::__construct();
    }
} 