<?php

namespace App\Core\Repositories\User;

use App\Core\Models\User\UserFailedLogin;
use App\Core\Models\Base\Model;
use App\Core\Repositories\Base\BaseRepository;
use PDO;

class UserFailedLoginRepository extends BaseRepository implements UserFailedLoginRepositoryInterface
{
    protected Model $model;

    public function __construct(UserFailedLogin $model) 
    {
        parent::__construct($model);
        $this->model = $model;
    }

    /**
     * Get all failed login entries with optional filtering and pagination
     * 
     * @param int|null $start
     * @param int|null $limit
     * @param int|null $userId
     * @param int|null $count
     * @param string|null $updatedAt
     * @return array
     */
    public function getAll(
        ?int $start = null,
        ?int $limit = null,
        ?int $userId = null,
        ?int $count = null,
        ?string $updatedAt = null
    ): array {
        $query = $this->model->select(['user_failed_login.*']);

        if ($userId !== null) {
            $query->where('user_id', '=', $userId);
        }

        if ($count !== null) {
            $query->where('count', '>', $count);
        }

        if ($updatedAt !== null) {
            $query->where('updated_at', '=', $updatedAt);
        }

        $query->orderBy('user_id')
              ->orderBy('updated_at');

        if ($start !== null) {
            $query->offset($start);
        }

        if ($limit !== null) {
            $query->limit($limit);
        }

        $results = $query->findAll();
        $totalRecords = $query->countAll();

        return [
            'data' => $results,
            'total' => $totalRecords
        ];
    }

    /**
     * Get user failed login information with various filters
     * 
     * @param int|null $userId
     * @param string|null $updatedAt
     * @param int|null $count
     * @param string|null $username
     * @param string|null $email
     * @param int|null $status
     * @param int|null $roleId
     * @return array|null
     */
    public function get(
        ?int $userId = null,
        ?string $updatedAt = null,
        ?int $count = null,
        ?string $username = null,
        ?string $email = null,
        ?int $status = null,
        ?int $roleId = null
    ): ?array {
        $query = $this->model->select(['user_failed_login.*'])
            ->join('user', 'user_id');

        if ($userId !== null) {
            $query->where('user_failed_login.user_id', '=', $userId);
        }

        if ($count !== null) {
            $query->where('user_failed_login.count', '>', $count);
        }

        if ($updatedAt !== null) {
            $query->where('user_failed_login.updated_at', '=', $updatedAt);
        }

        if ($username !== null) {
            $query->where('user.username', '=', $username);
        }

        if ($email !== null) {
            $query->where('user.email', '=', $email);
        }

        if ($status !== null) {
            $query->where('user.status', '=', $status);
        }

        if ($roleId !== null) {
            $query->where('user.role_id', '=', $roleId);
        }

        $query->limit(1);
        $results = $query->findAll();
        return !empty($results) ? $results[0] : null;
    }

    /**
     * Log a failed login attempt
     * 
     * @param int|null $userId
     * @param string|null $username
     * @param string $updatedAt
     * @param string $lastIp
     * @return int
     */
    public function logFailed(
        ?int $userId,
        ?string $username,
        string $updatedAt,
        string $lastIp
    ): int {
        // First check if user exists and is active
        $userQuery = $this->model->select(['user.user_id'])
            ->join('user', 'user_id')
            ->where('user.status', '=', 1);

        if ($userId !== null) {
            $userQuery->where('user.user_id', '=', $userId);
        }

        if ($username !== null) {
            $userQuery->where('user.username', '=', $username);
        }

        $userQuery->limit(1);
        $user = $userQuery->findAll();

        if (empty($user)) {
            return 0;
        }

        // Insert or update failed login attempt using upsert
        $data = [
            'user_id' => $user[0]['user_id'],
            'updated_at' => $updatedAt,
            'last_ip' => $lastIp,
            'count' => 1
        ];

        $result = $this->model->upsert([$data], ['user_id']);
        return $result ? $user[0]['user_id'] : 0;
    }

    
} 