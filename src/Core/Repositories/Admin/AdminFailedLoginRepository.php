<?php

declare(strict_types=1);

namespace App\Core\Repositories\Admin;

use App\Core\Models\Admin\Admin;
use App\Core\Models\Admin\AdminFailedLogin;
use App\Core\Repositories\Base\BaseRepository;
use PDO;

class AdminFailedLoginRepository extends BaseRepository implements AdminFailedLoginRepositoryInterface
{
    protected Admin $admin;

    public function __construct(PDO $db, Admin $admin)
    {
        parent::__construct($db, 'admin_failed_login', AdminFailedLogin::class);
        $this->admin = $admin;
        $this->admin->setDb($db);
    }

    /**
     * Get all failed login attempts with pagination and filtering
     */
    public function getAll(int $start = 0, int $limit = 10, ?int $admin_id = null, ?int $count = null, ?string $updated_at = null): array
    {
        $query = $this->model->select(['*']);

        if ($admin_id !== null) {
            $query->where('admin_id', '=', $admin_id);
        }

        if ($count !== null) {
            $query->where('count', '>', $count);
        }

        if ($updated_at !== null) {
            $query->where('updated_at', '=', $updated_at);
        }

        $query->orderBy('admin_id')
              ->orderBy('updated_at')
              ->limit($limit)
              ->offset($start);

        $data = $query->findAll();
        $total = $query->countAll();

        return [
            'data' => $data,
            'total' => $total
        ];
    }

    /**
     * Get failed login attempt by various criteria
     */
    public function get(?int $admin_id = null, ?string $updated_at = null, ?int $count = null, ?string $username = null, ?string $email = null, ?int $status = null, ?int $role_id = null): ?AdminFailedLogin
    {
        $query = $this->model->select(['admin_failed_login.*'])
                            ->join('admin', 'admin_failed_login.admin_id', '=', 'admin.admin_id');

        if ($admin_id !== null) {
            $query->where('admin_failed_login.admin_id', '=', $admin_id);
        }

        if ($count !== null) {
            $query->where('admin_failed_login.count', '>', $count);
        }

        if ($updated_at !== null) {
            $query->where('admin_failed_login.updated_at', '=', $updated_at);
        }

        if ($username !== null) {
            $query->where('admin.username', '=', $username);
        }

        if ($email !== null) {
            $query->where('admin.email', '=', $email);
        }

        if ($status !== null) {
            $query->where('admin.status', '=', $status);
        }

        if ($role_id !== null) {
            $query->where('admin.role_id', '=', $role_id);
        }

        $result = $query->findAll();
        return !empty($result) ? $this->model->set($result[0]) : null;
    }

    /**
     * Log a failed login attempt
     */
    public function logFailed(string $last_ip, string $updated_at, ?int $admin_id = null, ?string $username = null): ?AdminFailedLogin
    {
        // First find the admin
        $adminQuery = $this->admin->where('status', '=', 1);

        if ($admin_id !== null) {
            $adminQuery->where('admin_id', '=', $admin_id);
        }

        if ($username !== null) {
            $adminQuery->where('username', '=', $username);
        }

        $adminResult = $adminQuery->findAll();

        if (empty($adminResult)) {
            return null;
        }

        $admin = $adminResult[0];

        // Try to find existing failed login record
        $existingRecord = $this->model->where('admin_id', '=', $admin['admin_id'])->findAll();

        if (!empty($existingRecord)) {
            // Update existing record
            $data = [
                'count' => $existingRecord[0]['count'] + 1,
                'last_ip' => $last_ip,
                'updated_at' => $updated_at
            ];
            $this->model->update($admin['admin_id'], $data);
            return $this->model->set($data);
        } else {
            // Create new record
            $data = [
                'admin_id' => $admin['admin_id'],
                'count' => 1,
                'last_ip' => $last_ip,
                'updated_at' => $updated_at
            ];
            return $this->model->create($data);
        }
    }

    /**
     * Add new failed login record
     */
    public function add(array $data): ?AdminFailedLogin
    {
        return $this->model->create($data);
    }

    /**
     * Update failed login record
     */
    public function edit(int $admin_id, array $data): bool
    {
        $this->model->clearQuery();
        $record = $this->model->where('admin_id', '=', $admin_id)->first();
        if (!$record) {
            return false;
        }

        return (bool) $record->update($data);
    }

    /**
     * Delete failed login records
     */
    public function purgeFailedLogins(array $admin_ids, ?string $updated_at = null, ?int $count = null): bool
    {
        $query = $this->model->join('admin', 'admin_failed_login.admin_id', '=', 'admin.admin_id', 'INNER');

        if (!empty($admin_ids)) {
            $query->whereIn('admin_failed_login.admin_id', $admin_ids);
        }

        if ($updated_at !== null) {
            $query->where('admin_failed_login.updated_at', '=', $updated_at);
        }

        if ($count !== null) {
            $query->where('admin_failed_login.count', '=', $count);
        }

        $sql = $query->getQuery();
        $sql = str_replace('SELECT *', 'DELETE admin_failed_login', $sql);
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($query->getParams());
        } catch (\PDOException $e) {
            throw new \PDOException("Delete operation failed: " . $e->getMessage());
        }
    }
} 