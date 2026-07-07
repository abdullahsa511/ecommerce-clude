<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\Admin\AdminRoleRepositoryInterface;

class RoleController extends ApiController
{
    private AdminRoleRepositoryInterface $roleRepository;

    public function __construct(
        AdminRoleRepositoryInterface $roleRepository,
    )
    {
        parent::__construct();
        $this->roleRepository = $roleRepository;
    }

    /**
     * Get all roles.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $roles = $this->roleRepository->findAll();
        return $this->renderResponse($roles);
    }

    /**
     * Get a role by ID.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function show(Request $request, $id): Response
    {
        $role = $this->roleRepository->find((int)$id);
        if(!$role){
            return $this->renderError(404, 'Role not found');
        }
        return $this->renderResponse($role->data);
    }

    /**
     * Create a new role.
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        try {
            $data = $request->validate([
                'name' => 'required|string',
                'display_name' => 'required|string',
                'permissions' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $role = $this->roleRepository->create($data);
        return $this->renderResponse($role->data);
    }

    /**
     * Update a role.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id): Response
    {
        try {
            $data = $request->validate([
                'name' => 'required|string',
                'display_name' => 'string|nullable',
                'permissions' => 'string|nullable',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $existingRole = $this->roleRepository->find((int)$id);
        if (!$existingRole) {
            return $this->renderError(404, 'Role not found');
        }

        $role = $this->roleRepository->update((int) $id, $data);
        if (!$role) {
            return $this->renderError(500, 'Failed to update role');
        }
        
        return $this->renderResponse($role->data);
    }

    /**
     * Delete a role.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function delete(Request $request, $id): Response
    {
        $this->roleRepository->delete((int) $id);
        return $this->renderResponse(['message' => 'Role deleted successfully']);
    }

    // import roles
    public function importRoles(Request $request): Response
    {
        $csv_file = $request->file('csv_file');
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';
        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }
        try {
            $result = $this->roleRepository->importRoles($csv_file_path);
            return $this->renderResponse($result);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }
} 