<?php

declare(strict_types=1);

namespace App\Core\Models\Subscription;

use App\Core\Models\Base\Model;

class SubscriptionLog extends Model
{
    public int $subscription_log_id;
    public int $subscription_id;
    public int $subscription_status_id;
    public string $comment;
    public string $created_at;
    public string $updated_at;

    public function __construct() 
    {
        parent::__construct();
    }
} 