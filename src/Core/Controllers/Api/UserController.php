<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Http\ApiController;
use App\Core\Repositories\UserRepositoryInterface;
use App\Core\Repositories\User\UserGroupRepositoryInterface;
use App\Core\Repositories\Role\ModelHasRoleRepositoryInterface;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Exceptions\ValidationException;
use PDO;
use App\Core\Repositories\Media\MediaRepositoryInterface;
use Exception;

use function App\Core\System\utils\generateUuidV4;
use function App\Core\System\utils\uuidToBin;
use function App\Core\System\utils\binToUuid;

class UserController extends ApiController
{
    private UserRepositoryInterface $userRepository;
    private UserGroupRepositoryInterface $userGroupRepository;
    private ModelHasRoleRepositoryInterface $modelHasRoleRepository;
    private MediaRepositoryInterface $mediaRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        UserGroupRepositoryInterface $userGroupRepository,
        ModelHasRoleRepositoryInterface $modelHasRoleRepository,
        MediaRepositoryInterface $mediaRepository,
    ) {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->userGroupRepository = $userGroupRepository;
        $this->modelHasRoleRepository = $modelHasRoleRepository;
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * Get all subscriptions.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $users = $this->userRepository->findUsers();
        $users = $this->sanitizeUsersForJson($users);

        return $this->renderResponse($users);
    }

    private function sanitizeUsersForJson(array $users): array
    {
        foreach ($users as &$user) {
            if (isset($user['uuid']) && is_string($user['uuid']) && !preg_match('//u', $user['uuid'])) {
                $user['uuid'] = binToUuid($user['uuid']);
            }
        }

        return $users;
    }

    /**
     * Get a subscription by ID.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function show(Request $request, $id): Response
    {
        $user = $this->userRepository->findWithRoles((int)$id);
        if (!$user) {
            return $this->renderError(404, 'User not found');
        }

        $user['avatar'] = [
            [
                'user_image_id' => $id,
                'image' => $user['avatar'],
                'size' => 256,
                'type' => 'image/jpeg',
                'objectURL' => $user['avatar'], // $data->image
                'status' => [
                    'name' => 'Uploaded',
                    'severity' => 'success'
                ],
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];

        return $this->renderResponse($user);
    }

    /**
     * Create a new subscription.
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        $data = $request->validate([
            'user_group_id' => 'required|integer',
            'site_id' => 'required|integer',
            'username' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'password' => 'required|string',
            'email' => 'required|string|email',
            'phone_number' => 'required|string',
            'url' => 'required|string',
            'display_name' => 'required|string',
            'bio' => 'string|nullable',
            'subscribe' => 'required|integer',
            'status' => 'required|integer',
            'token' => 'required|string',
            'userRole' => 'array|nullable',
        ]);

        try {
            if ($data instanceof Response) {
                return $data;
            }

            // Extract userRole data before creating user
            $userRoles = $data['userRole'] ?? null;
            $data['uuid'] = uuidToBin(generateUuidV4()); // generate uuid for user
            unset($data['avatar']); // Remove avatar from user data
            unset($data['userRole']); // Remove userRole from user data
            // check if email already exists
            if ($this->userRepository->existsByEmail($data['email'])) {
                return $this->renderError(422, 'Validation error. Email is already in use.', ['email' => ['The  ' . $data['email'] . ' is already in use.']]);
            }

            // Create the user
            $user = $this->userRepository->create($data);
            $userId = $user->data->user_id;

            if (!$user) {
                return $this->renderError(500, 'Failed to create user');
            }

            // Handle userRole if provided
            if (isset($userRoles) && !empty($userRoles)) {
                foreach ($userRoles as &$userRole) {
                    $userRole['model_id'] = $userId;
                }
                // Upsert the role to model_has_role table
                $roleAssigned = $this->modelHasRoleRepository->upsertRole($userRoles);
                if (!$roleAssigned) {
                    return $this->renderError(500, 'Failed to update user role');
                }
            }

            return $this->renderResponse($user->data);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Failed to create user: ' . $e->getMessage());
        }
    }

    /**
     * Update a subscription.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id): Response
    {
        $data = $request->validate([
            'user_group_id' => 'integer|nullable',
            'site_id' => 'integer|nullable',
            'username' => 'required|string',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'password' => 'string|nullable',
            'email' => 'required|string|email',
            'phone_number' => 'required|string|nullable',
            'url' => 'required|string',
            'display_name' => 'string|nullable',
            'bio' => 'string|nullable',
            'subscribe' => 'integer|nullable',
            'status' => 'integer|nullable',
            'token' => 'string|nullable',
            'userRole' => 'array|nullable',
        ]);
        if ($data instanceof Response) {
            return $data;
        }

        $existingUser = $this->userRepository->find((int)$id);
        if (!$existingUser) {
            return $this->renderError(404, 'User not found');
        }

        // Extract userRole data before updating user
        $userRoles = $data['userRole'] ?? null;
        unset($data['userRole']); // Remove userRole from user data
        unset($data['avatar']); // Remove avatar from user data

        $user = $this->userRepository->update((int) $id, $data);
        if (!$user) {
            return $this->renderError(500, 'Failed to update user');
        }
        if (isset($userRoles) && !empty($userRoles)) {
            $userRoles = array_filter($userRoles, function($role){
                return !empty($role['role_id']);
            });

            if (count($userRoles) > 0) {
                // Upsert the role to model_has_role table
                $roleAssigned = $this->modelHasRoleRepository->upsertRole($userRoles, (int)$id);
                if (!$roleAssigned) {
                    return $this->renderError(500, 'Failed to update user role');
                }
            }
        }

        return $this->renderResponse($user->data);
    }

    /**
     * Delete a subscription.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function delete(Request $request, $id): Response
    {
        // Remove user roles first
        $this->modelHasRoleRepository->removeAllRoles((int)$id, 'user');

        // Then delete the user
        // $this->userRepository->delete((int) $id);
        $this->userRepository->deleteUser((int) $id);
        return $this->renderResponse(['message' => 'User deleted successfully']);
    }

    /**
     * Get all user groups.
     *
     * @param Request $request
     * @return Response
     */
    public function userGroupIndex(Request $request): Response
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
    public function userGroupShow(Request $request, $id): Response
    {
        $userGroup = $this->userGroupRepository->find((int)$id);
        if (!$userGroup) {
            return $this->renderError(404, 'User group not found');
        }
        return $this->renderResponse($userGroup->data);
    }

    /**
     * Create a new user group.
     *
     * @param Request $request
     * @return Response
     */
    public function userGroupCreate(Request $request): Response
    {
        try {
            $data = $request->validate([
                'name' => 'required|string',
                'sort_order' => 'integer|nullable',
                'status' => 'required|integer|in:0,1',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $userGroup = $this->userGroupRepository->create($data);
        return $this->renderResponse($userGroup->data);
    }

    /**
     * Update a user group.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function userGroupUpdate(Request $request, $id): Response
    {
        try {
            $data = $request->validate([
                'name' => 'string|nullable',
                'trial_status' => 'integer|in:0,1|nullable',
                'status' => 'integer|in:0,1|nullable',
                'sort_order' => 'integer|nullable',
                'language_id' => 'integer|nullable',
                'name' => 'string|nullable',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $existingUserGroup = $this->userGroupRepository->find((int)$id);
        if (!$existingUserGroup) {
            return $this->renderError(404, 'User group not found');
        }

        $userGroup = $this->userGroupRepository->update((int)$id, $data);
        if (!$userGroup) {
            return $this->renderError(500, 'Failed to update user group');
        }

        return $this->renderResponse($userGroup->data);
    }

    /**
     * Delete a user group.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function userGroupDelete(Request $request, $id): Response
    {
        $existingUserGroup = $this->userGroupRepository->find((int)$id);
        if (!$existingUserGroup) {
            return $this->renderError(404, 'User group not found');
        }

        if (!$this->userGroupRepository->delete((int)$id)) {
            return $this->renderError(500, 'Failed to delete user group');
        }

        return $this->renderResponse(['message' => 'User group deleted successfully']);
    }

    /**
     * Import users from CSV file
     *
     * @param Request $request
     * @return Response
     */
    public function import(Request $request): Response
    {
        // Debug request data
        error_log('Request Method: ' . $_SERVER['REQUEST_METHOD']);
        error_log('Content Type: ' . ($_SERVER['CONTENT_TYPE'] ?? 'Not set'));

        // Get raw input and parse it
        $rawInput = file_get_contents('php://input');
        error_log('Raw Input Length: ' . strlen($rawInput));
        error_log('Raw Input Preview: ' . substr($rawInput, 0, 1000));

        // Debug PHP superglobals
        error_log('$_FILES: ' . print_r($_FILES, true));
        error_log('$_POST: ' . print_r($_POST, true));
        error_log('$_REQUEST: ' . print_r($_REQUEST, true));

        // Check if file was uploaded with key 'csv_file'
        if (!isset($_FILES['csv_file'])) {
            return $this->renderError(400, 'No file uploaded. Please upload a CSV file with key name "csv_file"');
        }

        $file = $_FILES['csv_file'];

        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return $this->renderError(400, 'File upload error: ' . $file['error']);
        }

        // Check if file is a CSV
        $fileType = mime_content_type($file['tmp_name']);
        if ($fileType !== 'text/csv' && $fileType !== 'text/plain') {
            return $this->renderError(400, 'Invalid file type. Please upload a CSV file');
        }

        // Read CSV file
        $csvData = [];
        $handle = fopen($file['tmp_name'], 'r');
        if ($handle !== false) {
            // Read headers
            $headers = fgetcsv($handle);
            if ($headers === false) {
                fclose($handle);
                return $this->renderError(400, 'Invalid CSV format');
            }

            // Read data rows
            while (($data = fgetcsv($handle)) !== false) {
                $row = array_combine($headers, $data);
                $csvData[] = [
                    'user_group_id' => (int)$row['user_group_id'],
                    'site_id' => (int)$row['site_id'],
                    'username' => $row['username'],
                    'first_name' => $row['first_name'],
                    'last_name' => $row['last_name'],
                    'password' => password_hash($row['password'], PASSWORD_DEFAULT),
                    'email' => $row['email'],
                    'phone_number' => $row['phone_number'],
                    'url' => $row['url'],
                    'display_name' => $row['display_name'],
                    'avatar' => $row['avatar'],
                    'bio' => $row['bio'] ?? null,
                    'subscribe' => (int)$row['subscribe'],
                    'status' => (int)$row['status'],
                    'token' => md5(uniqid($row['email'], true))
                ];
            }
            fclose($handle);
        }

        // return $this->renderResponse($csvData);

        if (empty($csvData)) {
            return $this->renderError(400, 'No data found in CSV file');
        }

        // Use import to insert/update users
        $uniqueKeys = ['user_id']; // Use user_id as unique identifier
        $success = $this->userRepository->import($csvData, $uniqueKeys);

        if (!$success) {
            return $this->renderError(500, 'Failed to import users');
        }

        return $this->renderResponse([
            'message' => 'Users imported successfully',
            'debug_info' => [
                'file_name' => $file['name'],
                'file_size' => $file['size'],
                'file_type' => $fileType,
                'rows_processed' => count($csvData)
            ]
        ]);
    }

    /**
     * Export users to CSV file
     *
     * @param Request $request
     * @return Response
     */
    public function export(Request $request): Response
    {
        try {
            // Get all users
            $users = $this->userRepository->findAll();

            if (empty($users)) {
                return $this->renderError(404, 'No users found to export');
            }

            // Create CSV content
            $csvContent = '';

            // Add headers
            $headers = [
                'user_id',
                'user_group_id',
                'site_id',
                'username',
                'first_name',
                'last_name',
                'email',
                'phone_number',
                'url',
                'display_name',
                'avatar',
                'bio',
                'subscribe',
                'status'
            ];
            $csvContent .= implode(',', $headers) . "\n";

            // Add data rows
            foreach ($users as $user) {
                $row = [
                    $user['user_id'] ?? '',
                    $user['user_group_id'] ?? '',
                    $user['site_id'] ?? '',
                    $user['username'] ?? '',
                    $user['first_name'] ?? '',
                    $user['last_name'] ?? '',
                    $user['email'] ?? '',
                    $user['phone_number'] ?? '',
                    $user['url'] ?? '',
                    $user['display_name'] ?? '',
                    $user['avatar'] ?? '',
                    $user['bio'] ?? '',
                    $user['subscribe'] ?? '',
                    $user['status'] ?? ''
                ];

                // Escape and quote values
                $row = array_map(function ($value) {
                    if ($value === null) return '""';
                    return '"' . str_replace('"', '""', (string)$value) . '"';
                }, $row);

                $csvContent .= implode(',', $row) . "\n";
            }

            // Get the project root directory
            $projectRoot = dirname(dirname(dirname(dirname(__DIR__))));
            $exportDir = $projectRoot . '/storage/exports';

            // Create export directory if it doesn't exist
            if (!file_exists($exportDir)) {
                if (!mkdir($exportDir, 0777, true)) {
                    return $this->renderError(500, 'Failed to create export directory');
                }
            }

            // Check if directory is writable
            if (!is_writable($exportDir)) {
                return $this->renderError(500, 'Export directory is not writable');
            }

            // Generate filename with timestamp
            $filename = 'users_export_' . date('Y-m-d_H-i-s') . '.csv';
            $filepath = $exportDir . '/' . $filename;

            // Save CSV content to file
            $bytesWritten = file_put_contents($filepath, $csvContent);
            if ($bytesWritten === false) {
                return $this->renderError(500, 'Failed to write export file');
            }

            // Verify file was created
            if (!file_exists($filepath)) {
                return $this->renderError(500, 'Export file was not created');
            }

            // Get file size
            $fileSize = filesize($filepath);
            if ($fileSize === false) {
                return $this->renderError(500, 'Failed to get file size');
            }

            // Set headers for file download
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Pragma: no-cache');
            header('Expires: 0');
            header('Content-Length: ' . $fileSize);
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type, Authorization');
            header('Access-Control-Max-Age: 86400');

            // Output file content
            readfile($filepath);
            exit;
        } catch (\Exception $e) {
            error_log('Export error: ' . $e->getMessage());
            return $this->renderError(500, 'Failed to export users: ' . $e->getMessage());
        }
    }

    public function customerSearch(Request $request): Response
    {
        $customers = $this->userRepository->customerSearch($request->input('search'));
        return $this->renderResponse($customers);
    }

    // 
    public function importUsers(Request $request): Response
    {
        $csv_file = $request->file('csv_file');

        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';

        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->userRepository->importUsers($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }

    public function upload(Request $request, int $user_id): Response
    {
        // Set default size
        $size = [
            'width' => 400,
            'height' => 420,
        ];

        $folder = 'media/users/';
        if ($request->files() || isset($_FILES['files'])) {
            $files = $request->files() ?? $_FILES['files'];

            if (!count($files)) {
                return $this->renderError(422, 'No files uploaded');
            }
            $data = [
                'files' => $files,
                'upload_dir' => $request->input('upload_dir', $folder)
            ];

            $result = $this->mediaRepository->upload($data, $size, $folder);
            if (!$result) {
                return $this->renderError(500, 'Failed to upload media');
            }

            $this->userRepository->updateUserImage($result['files'], $user_id);
            return $this->renderResponse($result);
        }

        return $this->renderError(422, 'No files uploaded');
    }

    // delete vendor image
    public function deleteImage(Request $request, int $user_id): Response
    {
        $deleted = $this->userRepository->deleteUserImage($user_id);
        return $this->renderResponse(['deleted' => $deleted]);
    }

    public function createRequest(Request $request): Response
    {
        $name = $request->input('name');
        $description = $request->input('description');
        $attachments = $request->file('attachments');
        $attachments_path = $attachments['tmp_name'] ?? $attachments['name'] ?? '';
        // if (empty($attachments_path)) {
        //     return $this->renderError(400, 'No attachments uploaded or file path not found');
        // }
        $result = $this->userRepository->createRequest($name, $description, $attachments_path);

        return $this->renderResponse(['status' => 200, 'data' => ['message' => 'Request created successfully']]);
    }

    public function contactSalesGetInTouch(Request $request): Response
    {
        $data = $request->all();
        $result = $this->userRepository->contactSalesGetInTouch($data);
        return $this->renderResponse($result);
    }

    // send-email-verification
    public function sendEmailVerification(Request $request): Response
    {
        $data = $request->all();
        if (empty($data['email'])) {
            return $this->renderError(400, 'Email is required');
        }
        try {
            $result = $this->userRepository->sendEmailVerification($data['email']);
        } catch (Exception $e) {
            // return $this->renderError(500, $e->getMessage());
        }
        return $this->renderResponse($result);
    }
}
