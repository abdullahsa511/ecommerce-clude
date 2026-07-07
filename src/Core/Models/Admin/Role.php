<?php

declare(strict_types=1);

namespace App\Core\Models\Admin;

use App\Core\Models\Base\Model;
use function App\Core\System\utils\session;

class Role extends Model
{
    public int $role_id;
    public string $name;
    public string $display_name;
    public string $permissions;
    private static string $namespace = 'role';

    public function __construct()
    {
        parent::__construct();
    }
} 