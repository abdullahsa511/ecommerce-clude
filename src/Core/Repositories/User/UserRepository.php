<?php

declare(strict_types=1);

namespace App\Core\Repositories;

use App\Core\Models\User;
use App\Core\Models\UsersAuthScope;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Validation\UserDataValidation;
use League\Csv\Reader;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\UserEntityInterface;
use PDO;
use Exception;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected UsersAuthScope $usersAuthScope;

    public function __construct(PDO $db, UsersAuthScope $usersAuthScope)
    {
        parent::__construct($db, 'user', User::class);

        $this->usersAuthScope = $usersAuthScope;
        $this->usersAuthScope->setDb($db);
    }

    public function findByEmail(string $email): ?User
    {
        $model = $this->model->where('email', '=', $email);

        $users = $model->executeQuery($model->getQuery());

        if (!empty($users)) {
            $users = $model->set($users[0]);
            return $users;
        }
        return null;
    }

    public function existsByEmail(string $email): bool
    {
        return $this->model->where('email', '=', $email)->first() ? true : false;
    }

    public function getUserEntityByUserCredentials(
        string $username,
        string $password,
        string $grantType,
        ClientEntityInterface $clientEntity
    ): ?UserEntityInterface {
        // 1. Look up the user record by username (e.g., email) in your data source
        $user = $this->findByEmail($username);
        if (!$user) {
            // User not found
            return null;
        }

        // 2. Verify the password
        // Assuming $user->getPassword() returns a hashed password
        if (!password_verify($password, $user->getPassword())) {
            // Password mismatch
            return null;
        }

        // 3. Return an instance of UserEntityInterface
        // We'll create an anonymous class implementing the interface
        // and set the identifier to the user's ID.
        return new class($user->user_id) implements UserEntityInterface {
            use EntityTrait;

            public function __construct(private readonly int $id)
            {
                // Use the trait's setIdentifier method
                $this->setIdentifier((string) $id);
            }
        };
    }

    /**
     * Get the scopes for a user by left joining the scopes table.
     *
     * @param int $id
     * @return array<string> List of scope names
     */
    public function getUserScopes(int $id): array
    {
        // $query = "
        //     SELECT us.scopes 
        //     FROM users_auth_scopes us
        //     WHERE us.user_id = :id
        // ";

        // $statement = $this->db->prepare($query);
        // $statement->bindParam(':id', $id, PDO::PARAM_INT);
        // $statement->execute();

        // $result = $statement->fetch(PDO::FETCH_COLUMN);

        // return $result ? json_decode($result, true) : [];

        $result = $this->usersAuthScope->select(['scopes'])->where('user_id', '=', $id)->findAll();

        return $result;
    }

    /**
     * Get all users with their roles
     *
     * @return array
     */
    public function findUsers(): array
    {
        $users = $this->model->findAll();
        $this->model->clearQuery();

        // Get all user roles with user_id included
        $userRoles = $this->model
            ->select(['user.user_id', 'role.role_id', 'role.name', 'role.display_name'])
            ->join('model_has_role', 'model_has_role.model_id', '=', 'user.user_id')
            ->join('role', 'role.role_id', '=', 'model_has_role.role_id')
            ->where('model_has_role.model_type', '=', 'user')
            ->findAll();

        // Group roles by user_id
        $rolesByUserId = [];
        foreach ($userRoles as $role) {
            $userId = $role['user_id'];
            if (!isset($rolesByUserId[$userId])) {
                $rolesByUserId[$userId] = [];
            }
            $rolesByUserId[$userId][] = [
                'role_id' => $role['role_id'],
                'name' => $role['name'],
                'display_name' => $role['display_name']
            ];
        }

        // Add roles to each user
        foreach ($users as &$user) {
            $userId = $user['user_id'];
            $user['userRole'] = $rolesByUserId[$userId] ?? [];
        }

        return $users;
    }

    /**
     * Insert or update multiple users
     * 
     * @param array $data Array of user records to insert/update
     * @param array $uniqueKeys The unique keys to check for existing records
     * @return int|false The number of affected rows or false on failure
     */
    public function import(array $data, array $uniqueKeys): int|false
    {
        try {
            return $this->model->upsert($data, $uniqueKeys);
        } catch (\Exception $e) {
            error_log('Error importing users: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Search for customers by name or email
     * 
     * @param string $search The search query
     * @return array The list of customers
     */
    public function customerSearch(string $search): array
    {
        $query = "
            SELECT 
                u.*,
                GROUP_CONCAT(r.name) as role_names,
                GROUP_CONCAT(r.display_name) as role_display_names,
                GROUP_CONCAT(r.role_id) as role_ids
            FROM user u
            LEFT JOIN model_has_role mhr ON mhr.model_id = u.user_id AND mhr.model_type = 'user'
            LEFT JOIN role r ON r.role_id = mhr.role_id
            WHERE u.first_name LIKE :search 
               OR u.last_name LIKE :search 
               OR u.email LIKE :search 
               OR u.username LIKE :search
            GROUP BY u.user_id
            ORDER BY u.user_id DESC
        ";

        $statement = $this->db->prepare($query);
        $searchTerm = "%{$search}%";
        $statement->bindParam(':search', $searchTerm, PDO::PARAM_STR);
        $statement->execute();
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);

        // Process results to format role data
        foreach ($results as &$result) {
            // Convert role data from concatenated strings to arrays
            if (!empty($result['role_names'])) {
                $roleNames = explode(',', $result['role_names']);
                $roleDisplayNames = explode(',', $result['role_display_names']);
                $roleIds = explode(',', $result['role_ids']);

                $roles = [];
                for ($i = 0; $i < count($roleNames); $i++) {
                    $roles[] = [
                        'role_id' => (int)$roleIds[$i],
                        'name' => $roleNames[$i],
                        'display_name' => $roleDisplayNames[$i]
                    ];
                }
                $result['roles'] = $roles;
            } else {
                $result['roles'] = [];
            }

            // Remove the concatenated role fields
            unset($result['role_names'], $result['role_display_names'], $result['role_ids']);
        }

        return $results;
    }

    /**
     * Find a specific user by ID with their roles
     *
     * @param int $id
     * @return array|null
     */
    public function findWithRoles(int $id): ?array
    {
        // Get the specific user
        $user = $this->model->where('user_id', '=', $id)->first();
        if (!$user) {
            return null;
        }

        $this->model->clearQuery();

        // Get user roles for this specific user
        $userRoles = $this->model
            ->select(['user.user_id', 'role.role_id', 'role.name', 'role.display_name'])
            ->join('model_has_role', 'model_has_role.model_id', '=', 'user.user_id')
            ->join('role', 'role.role_id', '=', 'model_has_role.role_id')
            ->where('model_has_role.model_type', '=', 'user')
            ->where('user.user_id', '=', $id)
            ->findAll();

        // Convert user object to array and add roles
        $userData = $user->data;
        $userArray = (array) $userData;

        // Add roles to user data
        $userArray['userRole'] = [];
        foreach ($userRoles as $role) {
            $userArray['userRole'][] = [
                'role_id' => $role['role_id'],
                'name' => $role['name'],
                'display_name' => $role['display_name']
            ];
        }

        return $userArray;
    }

    // import data
    public function importUsers(string $csv_file): array
    {
        $reader = Reader::createFromPath($csv_file, 'r');
        $reader->setHeaderOffset(0);
        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }
        $defaultFields = $this->getDefaultFields($headers);
        $requiredFields = [
            'email',
            'username',
            'first_name',
            'last_name',
            'password',
            'phone_number',
            'url',
            'display_name',
            'avatar',
            'token',
        ];
        $records = $reader->getRecords();

        $validData = [];
        $showData = [];
        $invalid = [];
        $updated = [];
        $processed = [];
        $userGroupMap = $this->userGroup->select(['user_group_id', 'name'])->limit(0)->findAll(false);
        $userGroupMap = array_column($userGroupMap, 'user_group_id', 'name');
        $existingUsersMap = $this->model->select(['user_id', 'email'])->limit(0)->findAll(false);
        $existingUsersMap = array_column($existingUsersMap, 'user_id', 'email');

        $existingDataMaps = [
            'userGroupMap' => $userGroupMap,
            'userMap' => $existingUsersMap,
        ];

        foreach ($records as $offset => $record) {
            try {
                $record = $this->prepareRecord($record, $defaultFields);
                $validator = new UserDataValidation($record, $requiredFields, array_keys($defaultFields), $existingDataMaps);
                $validated = $validator->validate();

                if ($validated === false) {
                    $invalid[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => $validator->getErrors()
                    ];
                    continue;
                }

                $unique = $validator->getUniqueIdentifier();

                if (in_array($unique, $processed, true)) {
                    $updated[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'identifier' => $unique
                    ];
                    continue;
                }
                if ($validated->isExistingData) {
                    $updated[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'identifier' => $unique
                    ];
                } else {
                    $validData[] = (array) $validated->user;
                    $showData[] = $record;
                }
                $processed[] = $unique;
            } catch (Exception $e) {
                // Capture any runtime exception per record
                $invalid[] = [
                    'row' => $offset + 2,
                    'data' => $record,
                    'errors' => ['processing_error' => $e->getMessage()]
                ];
                continue;
            }
        }

        try {
            $this->db->beginTransaction();
            if (count($validData) > 0) {
                $this->model->upsert($validData, ['email']);
            }
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update users: " . $e->getMessage());
        }

        return [
            'success' => true,
            'total_records' => iterator_count($records),
            'valid_records' => count($validData),
            'valid_data' => $showData,
            'invalid_records' => count($invalid),
            'updated_records' => count($updated),
            'updated_data' => $updated,
            'users' => [
                'inserted_count' => count($validData),
                'valid_data' => $validData
            ],
            'invalid_data' => $invalid,
            'summary' => [
                'success_rate' => iterator_count($records) > 0
                    ? round((count($validData) / iterator_count($records)) * 100, 2) . '%'
                    : '0%',
                'user_processed' => count($validData),
                'user_records_created' => $validData,
                'errors' => count($invalid),
            ],
        ];
    }

    private function getDefaultFields(array $headers): array
    {
        $defaultFields = [];
        // Initialize all CSV headers as null by default
        foreach ($headers as $header) {
            $defaultFields[$header] = null;
        }

        // Set default values for required fields
        $defaultFields['user_group_id'] = 1;
        $defaultFields['site_id'] = 1;
        $defaultFields['status'] = 1;
        $defaultFields['subscribe'] = 0;
        $defaultFields['token'] = md5(uniqid($defaultFields['username'], true));
        $defaultFields['created_at'] = date('Y-m-d H:i:s');
        $defaultFields['updated_at'] = date('Y-m-d H:i:s');
        return $defaultFields;
    }

    private function prepareRecord(array $record, array $defaultFields): array
    {
        return isset($record['user_id']) && $record['user_id'] ? $record : array_merge($defaultFields, $record);
    }

    /**
     * Minimal stub required by interface — return authentication summary.
     *
     * @return array
     */
    public function userAuth(): array
    {
        // Stub implementation to satisfy interface; real logic lives elsewhere.
        return [];
    }

    /**
     * Minimal stub to update a user's image.
     *
     * @param array $data
     * @param int $user_id
     * @return bool
     */
    public function updateUserImage(array $data, int $user_id): bool
    {
        // Stub implementation; return false to indicate not implemented.
        return false;
    }

    /**
     * Minimal stub to delete a user's image.
     *
     * @param int $user_id
     * @return bool
     */
    public function deleteUserImage(int $user_id): bool
    {
        // Stub implementation; return false to indicate not implemented.
        return false;
    }

    /**
     * Minimal stub to create a request.
     *
     * @param string $name
     * @param string $description
     * @param string $attachments_path
     * @return bool
     */
    public function createRequest(string $name, string $description, string $attachments_path): bool
    {
        // Stub implementation; return false to indicate not implemented.
        return false;
    }

     public function deleteUser(int $id):bool
    {

         try {
            $this->db->beginTransaction();

            $this->model->clearQuery();
            $user = $this->model->where('user_id', '=', $id)->first();
            if (!$user) {
                return false;
            }
            $user->update(['deleted_at' => date('Y-m-d H:i:s')]);
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to delete user: " . $e->getMessage());
        }
    }


    public function createUser(array $data)
    {
        try {
            $this->db->beginTransaction();
            $user = $this->model->create($data);
        if (!$user) {
            return false;
        }
        return $user;
        } catch (\Exception $e) {
            throw new \Exception("Failed to create user: " . $e->getMessage());
        }
    }
}
