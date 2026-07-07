<?php

declare(strict_types=1);

namespace App\Core\Models\Subscription;

use App\Core\Models\Base\Model;

class ProductSubscription extends Model
{
    public int $product_id;
    public int $subscription_plan_id;
    public string $created_at;
    public string $updated_at;

    public function __construct() 
    {
        parent::__construct();
    }
} 