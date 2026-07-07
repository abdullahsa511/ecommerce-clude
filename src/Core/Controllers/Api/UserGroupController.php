<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Http\ApiController;
use App\Core\Repositories\User\UserGroupRepositoryInterface;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Exceptions\ValidationException;
use App\Core\Models\User\UserGroupData;
use App\Core\Models\User\UserGroupResponse;

class UserGroupController extends ApiController
{
    private UserGroupRepositoryInterface $userGroupRepository;

    public function __construct(
        UserGroupRepositoryInterface $userGroupRepository,
    )
    {
        parent::__construct();
        $this->userGroupRepository = $userGroupRepository;
    }

    /**
     * Get all user groups.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $userGroups = $this->userGroupRepository->findAll();
        return $this->renderResponse($userGroups);
    }

    /**
     * Get a user group by ID.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function show(Request $request, $id): Response
    {
        $userGroup = $this->userGroupRepository->find((int)$id);
        if(!$userGroup){
            return $this->renderError(404, 'User group not found');
        }
        $response = new UserGroupResponse($userGroup->data);
        return $this->renderResponse($response);
    }

    /**
     * Create a new user group.
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        try {
            $userGroup = $request->input('userGroup');
            $inputData = $userGroup['userGroupContent'];
             $request->validate([
                'name' => 'required|string',
                'content' => 'required|string',
            ], $inputData);
            
            $userGroupData = new UserGroupData($userGroup);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }
        $userGroup = $this->userGroupRepository->create($userGroupData->toArray());
        if(!$userGroup){
            return $this->renderError(500, 'Failed to create user group');
        }
        $userGroup = new UserGroupResponse($userGroup->data);
        return $this->renderResponse($userGroup);
    }

    /**
     * Update a user group.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, int $id): Response
    {
        // return $this->renderResponse("You hit it");
        try {
            $userGroup = $request->input('userGroup');
            $inputData = $userGroup['userGroupContent'];
            $request->validate([
                'name' => 'required|string',
                'content' => 'required|string',
            ], $inputData);
            
            $userGroupData = new UserGroupData($userGroup);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $userGroup = $this->userGroupRepository->update((int)$id, $userGroupData->toArray());
        
        if(!$userGroup){
            return $this->renderError(500, 'Failed to update user group');
        }

        $userGroup = new UserGroupResponse($userGroup->data);
        return $this->renderResponse($userGroup);
    }

    /**
     * Delete a user group.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function delete(Request $request, $id): Response
    {
        $existingUserGroup = $this->userGroupRepository->find((int)$id);

        if (!$this->userGroupRepository->delete((int)$id)) {
            return $this->renderError(500, 'Failed to delete user group');
        }

        return $this->renderResponse(['message' => 'User group deleted successfully']);
    }

    public function importUserGroups(Request $request): Response
    {
        $csv_file = $request->file('csv_file');
        
        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';
        
        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->userGroupRepository->importUserGroups($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }
} 