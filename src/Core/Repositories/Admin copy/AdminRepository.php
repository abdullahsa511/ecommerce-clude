<?php

declare(strict_types=1);

namespace App\Core\Repositories\Admin;

use App\Core\Models\Admin\Admin;
use App\Core\Models\Role\Role;
use App\Core\Repositories\Base\BaseRepository;
use PDO;

class AdminRepository extends BaseRepository implements AdminRepositoryInterface
{
    protected Role $role;
    public function __construct(PDO $db, Role $role)
    {
        parent::__construct($db, 'admin', Admin::class);
        $this->role = $role;
        $this->role->setDb($db);
    }

    /**
     * Find admin by username
     */
    public function findByUsername(string $username): ?Admin
    {
        $result = $this->model
            ->findBy(['username' => $username]);
        return $result[0]?$this->model->set($result[0]):null;
    }

    /**
     * Find admin by email
     */
    public function findByEmail(string $email): ?Admin
    {
        $result = $this->model
            ->findBy(['email' => $email]);
        return $result[0]?$this->model->set($result[0]):null;
    }

    /**
     * Get all admins with pagination and filtering
     * 
     * @param int $start Starting offset
     * @param int $limit Number of records per page
     * @param int|null $status Filter by status
     * @param string|null $search Search term
     * @param string|null $email Filter by email
     * @param string|null $phone_number Filter by phone number
     * @return array{data: array, total: int}
     */
    public function getAll(int $start = 0, int $limit = 10, ?int $status = null, ?string $search = null, ?string $email = null, ?string $phone_number = null): array
    {
        $query = $this->model->select(['*']);

        if ($status !== null) {
            $query->where('status', '=', $status);
        }

        if ($email !== null) {
            $query->where('email', '=', $email);
        }

        if ($phone_number !== null) {
            $query->where('phone_number', '=', $phone_number);
        }

        if ($search !== null) {
            $query->where('username', 'LIKE', "%{$search}%")
                  ->orWhere('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%");
        }

        $query->orderBy('status', 'DESC')
              ->orderBy('admin_id')
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
     * Get admin by various criteria
     * 
     * @param string|null $username
     * @param string|null $email
     * @param string|null $token
     * @param int|null $admin_id
     * @param int|null $status
     * @param int|null $role_id
     * @return Admin|null
     */
    public function get(?string $username = null, ?string $email = null, ?string $token = null, ?int $admin_id = null, ?int $status = null, ?int $role_id = null): ?Admin
    {
        $query = $this->model->select(['admin.*', 'role.name as role', 'role.permissions'])
                            ->join('role', 'admin.role_id', '=', 'role.role_id');

        if ($username !== null) {
            $query->where('admin.username', '=', $username);
        }

        if ($email !== null) {
            $query->where('admin.email', '=', $email);
        }

        if ($admin_id !== null) {
            $query->where('admin.admin_id', '=', $admin_id);
        }

        if ($status !== null) {
            $query->where('admin.status', '=', $status);
        }

        if ($token !== null) {
            $query->where('admin.token', '=', $token);
        }

        if ($role_id !== null) {
            $query->where('admin.role_id', '=', $role_id);
        }

        $result = $query->findAll();
        return !empty($result) ? $this->model->set($result[0]) : null;
    }

    /**
     * Add new admin
     * 
     * @param array $admin Admin data
     * @return Admin|null
     */
    public function add(array $admin): ?Admin
    {
        return $this->model->create($admin);
    }

    /**
     * Update admin
     * 
     * @param array $admin Admin data to update
     * @param string|null $username Filter by username
     * @param string|null $email Filter by email
     * @param int|null $admin_id Filter by admin_id
     * @param int|null $role_id Filter by role_id
     * @return bool
     */
    public function edit(array $admin, ?string $username = null, ?string $email = null, ?int $admin_id = null, ?int $role_id = null): bool
    {
        $query = $this->model;

        if ($username !== null) {
            $query->where('username', '=', $username);
        }

        if ($email !== null) {
            $query->where('email', '=', $email);
        }

        if ($admin_id !== null) {
            $query->where('admin_id', '=', $admin_id);
        }

        if ($role_id !== null) {
            $query->where('role_id', '=', $role_id);
        }

        return $query->update($admin_id ?? 0, $admin);
    }


    /**
     * Set admin role
     * 
     * @param int $admin_id Admin ID
     * @param string|null $role Role name
     * @param int|null $role_id Role ID
     * @return bool
     */
    public function setRole(int $admin_id, ?string $role = null, ?int $role_id = null): bool
    {
        $data = [];
        
        if ($role_id !== null) {
            $data['role_id'] = $role_id;
        } elseif ($role !== null) {
            // Get role_id from role name
            $roleData = $this->role->where('name', '=', $role)->findAll();
            if (!empty($roleData)) {
                $data['role_id'] = $roleData[0]['role_id'];
            }
        }

        if (empty($data)) {
            return false;
        }

        return $this->model->update($admin_id, $data);
    }
} 