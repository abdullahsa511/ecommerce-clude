<?php

declare(strict_types=1);

namespace App\Core\Models\User;

use App\Core\Models\Base\Model;

class UserPoints extends Model
{
    public int $user_points_id;
    public int $user_id;
    public int $order_id;
    public int $points;
    public string $description;
    public string $created_at;
    public string $updated_at;

    public function __construct() 
    {
        parent::__construct();
    }
} 