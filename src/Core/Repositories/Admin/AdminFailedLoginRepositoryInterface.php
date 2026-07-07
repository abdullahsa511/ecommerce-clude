<?php

declare(strict_types=1);

namespace App\Core\Repositories\Admin;

use App\Core\Models\Admin\AdminFailedLogin;

interface AdminFailedLoginRepositoryInterface
{
    /**
     * Get all failed login attempts with pagination and filtering
     * 
     * @param int $start Starting offset
     * @param int $limit Number of records per page
     * @param int|null $admin_id Filter by admin ID
     * @param int|null $count Filter by count
     * @param string|null $updated_at Filter by update date
     * @return array{data: array, total: int}
     */
    public function getAll(int $start = 0, int $limit = 10, ?int $admin_id = null, ?int $count = null, ?string $updated_at = null): array;

    /**
     * Get failed login attempt by various criteria
     * 
     * @param int|null $admin_id Filter by admin ID
     * @param string|null $updated_at Filter by update date
     * @param int|null $count Filter by count
     * @param string|null $username Filter by admin username
     * @param string|null $email Filter by admin email
     * @param int|null $status Filter by admin status
     * @param int|null $role_id Filter by admin role ID
     * @return AdminFailedLogin|null
     */
    public function get(?int $admin_id = null, ?string $updated_at = null, ?int $count = null, ?string $username = null, ?string $email = null, ?int $status = null, ?int $role_id = null): ?AdminFailedLogin;

    /**
     * Log a failed login attempt
     * 
     * @param string $last_ip IP address
     * @param string $updated_at Update timestamp
     * @param int|null $admin_id Admin ID
     * @param string|null $username Admin username
     * @return AdminFailedLogin|null
     */
    public function logFailed(string $last_ip, string $updated_at, ?int $admin_id = null, ?string $username = null): ?AdminFailedLogin;

    /**
     * Add new failed login record
     * 
     * @param array $data Failed login data
     * @return AdminFailedLogin|null
     */
    public function add(array $data): ?AdminFailedLogin;

    /**
     * Update failed login record
     * 
     * @param int $admin_id Admin ID
     * @param array $data Failed login data
     * @return bool
     */
    public function edit(int $admin_id, array $data): bool;

    /**
     * Purge failed login records by criteria.
     *
     * @param array<int> $admin_ids Array of admin IDs
     * @param string|null $updated_at Filter by update date
     * @param int|null $count Filter by count
     * @return bool
     */
    public function purgeFailedLogins(array $admin_ids, ?string $updated_at = null, ?int $count = null): bool;
} 