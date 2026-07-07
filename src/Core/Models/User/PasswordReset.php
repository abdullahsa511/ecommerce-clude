<?php

declare(strict_types=1);

namespace App\Core\Models\User;

use App\Core\Models\Base\Model;

class PasswordReset extends Model
{
    public string $email;
    public string $token;
    public string $created_at;

    public function __construct() 
    {
        parent::__construct();
    }
} 