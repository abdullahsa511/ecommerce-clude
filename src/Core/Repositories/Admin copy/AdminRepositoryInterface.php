<?php

declare(strict_types=1);

namespace App\Core\Repositories\Admin;

use App\Core\Models\Admin\Admin;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface AdminRepositoryInterface extends BaseRepositoryInterface
{
    public function findByUsername(string $username): ?Admin;
    public function findByEmail(string $email): ?Admin;
} 