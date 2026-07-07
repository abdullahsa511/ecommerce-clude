<?php

declare(strict_types=1);

namespace App\Core\Models\Subscription;

use App\Core\Models\Base\Model;

class SubscriptionStatus extends Model
{
    public int $subscription_status_id;
    public int $language_id;
    public string $name;
    public string $created_at;
    public string $updated_at;

    public function __construct() 
    {
        parent::__construct();
    }
} 