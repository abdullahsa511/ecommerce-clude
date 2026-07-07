<?php

declare(strict_types=1);

namespace App\Core\Models\Role;

use App\Core\Models\Base\Model;

class Role extends Model
{
    public int $role_id;
    public string $name;
    public string $display_name;
    public string $permissions;

    public function __construct()
    {
        parent::__construct();
    }
} 