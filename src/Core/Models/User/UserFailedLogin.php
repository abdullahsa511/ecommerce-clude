<?php

declare(strict_types=1);

namespace App\Core\Models\User;

use App\Core\Models\Base\Model;

class UserFailedLogin extends Model
{
    public int $user_id;
    public int $count;
    public string $last_ip;
    public string $updated_at;

    public function __construct() 
    {
        parent::__construct();
    }
} 