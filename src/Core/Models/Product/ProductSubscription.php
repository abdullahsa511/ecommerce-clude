<?php

declare(strict_types=1);

namespace App\Core\Models\Product;

use App\Core\Models\Base\Model;
use App\Core\Models\Subscription\SubscriptionPlan;
use App\Core\Models\User\UserGroup;

class ProductSubscription extends Model
{
    protected string $table = 'product_subscription';
    protected string $tableAlias = 'ps';
    protected string $primaryKey = 'product_subscription_id';
    protected array $fillable = [
        'product_id',
        'subscription_plan_id',
        'user_group_id',
        'price',
        'trial_price',
        'created_at',
        'updated_at'
    ];

    /**
     * Product Subscription ID
     */
    public int $product_subscription_id;

    /**
     * Product ID
     */
    public int $product_id;

    /**
     * Subscription Plan ID
     */
    public int $subscription_plan_id;

    /**
     * User Group ID
     */
    public int $user_group_id;

    /**
     * Price
     */
    public float $price;

    /**
     * Trial Price
     */
    public float $trial_price;

    /**
     * Created at
     */
    public string $created_at;

    /**
     * Updated at
     */
    public string $updated_at;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Define relationship with Product model
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Define relationship with SubscriptionPlan model
     */
    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    /**
     * Define relationship with UserGroup model
     */
    public function userGroup()
    {
        return $this->belongsTo(UserGroup::class, 'user_group_id');
    }
} 