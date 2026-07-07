<?php

declare(strict_types=1);

namespace App\Core\Models\Admin;

use App\Core\Models\Base\Model;
use function App\Core\System\utils\session;

class AdminPasswordReset extends Model
{
    public string $email;
    public string $token;
    public ?string $created_at;
    private static string $namespace = 'admin_password_reset';

    public function __construct()
    {
        parent::__construct();
    }
} 