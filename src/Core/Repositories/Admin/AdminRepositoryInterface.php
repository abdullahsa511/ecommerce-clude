<?php

declare(strict_types=1);

namespace App\Core\Repositories\Admin;

use App\Core\Models\Admin\Admin;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface AdminRepositoryInterface extends BaseRepositoryInterface
{
    public function findByUsername(string $username): ?Admin;
    public function findByEmail(string $email): ?Admin;
    public function getSalesTeam(string $location, array $fields, int $limit = 4): array;
    public function importAdmins(string $csv_file): array;
    public function updateAdminImage(array $data, int $admin_id): bool;
    public function deleteAdminImage(int $admin_id): bool;
} 