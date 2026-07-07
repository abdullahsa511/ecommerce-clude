<?php

declare(strict_types=1);

namespace App\Core\Repositories;

use App\Core\Models\Customer\Customer;
use App\Core\Models\Role\ModelHasRole;
use App\Core\Models\User;
use App\Core\Models\User\UserGroupContent;
use App\Core\Models\UsersAuthScope;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Validation\UserDataValidation;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\UserEntityInterface;
use PDO;
use League\Csv\Reader;
use Exception;

use function App\Core\System\utils\uuidToBin;
use function App\Core\System\utils\generateUuidV4;

// use App\Core\Repositories\Customer\CustomerRepositoryInterface;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected UsersAuthScope $usersAuthScope;
    protected UserGroupContent $userGroupContent;
    protected ModelHasRole $modelHasRole;
    protected Customer $customer;



    public function __construct(PDO $db, UsersAuthScope $usersAuthScope, UserGroupContent $userGroupContent, ModelHasRole $modelHasRole, Customer $customer)
    {
        parent::__construct($db, 'user', User::class);

        $this->usersAuthScope = $usersAuthScope;
        $this->usersAuthScope->setDb($db);
        $this->userGroupContent = $userGroupContent;
        $this->userGroupContent->setDb($db);
        $this->modelHasRole = $modelHasRole;
        $this->modelHasRole->setDb($db);
        $this->customer = $customer;
        $this->customer->setDb($db);
    }

    public function findByEmail(string $email): ?User
    {
        $model = $this->model
        ->join('customer', 'customer.user_id', '=', 'user.user_id')
        ->select([
            'user.*',
            'customer.customer_id'
        ])
        ->where('user.email', '=', $email);

        $users = $model->executeQuery($model->getQuery());

        if (!empty($users)) {
            $users = $model->set($users[0]);
            return $users;
        }
        return null;
    }
    // find user by user id
    public function findByUserId(int $user_id): ?User
    {
        $model = $this->model
        ->where('user_id', '=', $user_id);

        $user = $model->first();
        return $user instanceof User ? $user : null;
    }

    public function findByEmailSimple(string $email): ?User
    {
        $this->model->clearQuery();
        $user = $this->model
        ->join('customer', 'customer.user_id', '=', 'user.user_id')
        ->select([
            'user.*',
            'customer.customer_id as customer_id'
        ])
        ->where('email', '=', trim($email))->first();

        return $user instanceof User ? $user : null;
    }

    public function getUserEntityByUserCredentials(
        string $username,
        string $password,
        string $grantType,
        ClientEntityInterface $clientEntity
    ): ?UserEntityInterface {
        if ($grantType === 'internal_sso') {
            $expected = $_ENV['OAUTH_INTERNAL_TOKEN_SECRET'] ?? '';
            if ($expected === '' || !hash_equals($expected, $password)) {
                return null;
            }
            $userId = (int) $username;
            if ($userId < 1) {
                return null;
            }
            $user = $this->find($userId);
            if (!$user) {
                return null;
            }
            return new class((int) $user->user_id) implements UserEntityInterface {
                use EntityTrait;

                public function __construct(private readonly int $id)
                {
                    $this->setIdentifier((string) $id);
                }
            };
        }

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
     * Insert or update multiple users
     * 
     * @param array $data Array of user records to insert/update
     * @param array $uniqueKeys The unique keys to check for existing records
     * @return int|false The number of affected rows or false on failure
     */
    public function import(array $data, array $uniqueKeys): int|false
    {
        return $this->model->upsert($data, $uniqueKeys);
    }

    public function customerSearch(string $search): array
    {
        $result = $this->model
            ->select(['user.user_id as customer_id', 'first_name', 'last_name', 'email', 'CONCAT(user.first_name, " ", user.last_name) as name'])
            ->where('first_name', 'like', '%' . $search . '%')
            ->orWhere('last_name', 'like', '%' . $search . '%')
            ->orWhere('email', 'like', '%' . $search . '%')
            ->orWhere('username', 'like', '%' . $search . '%');

        $result = $result->findAll();
        return $result;
    }

    public function findUsers(): array
    {
        $users = $this->model->whereNull('user.deleted_at')->findAll();
        $this->model->clearQuery();

        // Get all user roles with user_id included
        $userRoles = $this->model
            ->select(['user.user_id', 'role.role_id', 'role.name', 'role.display_name'])
            ->join('model_has_role', 'model_has_role.model_id', '=', 'user.user_id')
            ->join('role', 'role.role_id', '=', 'model_has_role.role_id')
            ->where('model_has_role.model_type', '=', 'user')
            ->whereNull('user.deleted_at')
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

    public function findWithRoles(int $id): ?array
    {
        // Get the specific user
        $user = $this->model->where('user_id', '=', $id)->first();
        if (!$user) {
            return null;
        }
        $userData = $user->data;
        $userArray = (array) $userData;

        $this->modelHasRole->clearQuery();
        $userRoles = $this->modelHasRole
                    ->select(['role_id'])
                    ->where('model_id', '=', $id)
                    ->where('model_type', '=', 'user')
                    ->findAll(false);

        $userRoles = array_column($userRoles, 'role_id');
        $userArray['roles'] = $userRoles;
        return $userArray;
    }

    public function getSalesTeamComponentData(array $param = [])
    {
        $query = $this->model
            ->select([
                'user.user_id',
                'user.first_name',
                'user.last_name',
                'user.email',
                'user.image',
                'user.location',
                'user.position',
                'role.name as role_name',
                'role.display_name as role_display_name'
            ])
            ->join('model_has_role', 'model_has_role.model_id', '=', 'user.user_id')
            ->join('role', 'role.role_id', '=', 'model_has_role.role_id')
            ->where('model_has_role.model_type', '=', 'user')
            ->whereIn('role.name', ['sales_executive', 'director', 'project_manager', 'senior_sales_executive'])
            ->where('user.status', '=', 1);

        if (isset($param['item_count']) && $param['item_count'] > 0) {
            $query->limit($param['item_count']);
        }

        $query->orderBy('user.location', 'ASC')
            ->orderBy('user.first_name', 'ASC');

        $results = $query->findAll();

        // Group users by location
        $groupedUsers = [];
        foreach ($results as $user) {
            $location = $user['location'] ?? 'Sydney'; // Default to Sydney if no location
            if (!isset($groupedUsers[$location])) {
                $groupedUsers[$location] = [];
            }

            $imageData = json_decode($user['image'] ?? '{}', true);
            $imageUrl = $imageData['objectURL'] ?? $imageData['url'] ?? '/img/contact/member-' . (count($groupedUsers[$location]) % 8) . '.jpg';

            $groupedUsers[$location][] = [
                'memberImage' => $imageUrl,
                'memberName' => $user['first_name'] . ' ' . $user['last_name'],
                'memberPosition' => $user['position'] ?? $user['role_display_name'] ?? 'Sales Executive'
            ];
        }

        // Format the final structure
        $items = [];
        foreach ($groupedUsers as $location => $teamData) {
            $items[] = [
                'itemName' => $location,
                'teamData' => $teamData
            ];
        }

        // If no results found, return default structure
        if (empty($items)) {
            $items = [
                [
                    "itemName" => "Sydney",
                    "teamData" => [
                        [
                            'memberImage' => '/img/contact/member-0.jpg',
                            'memberName' => 'Devon Lane',
                            'memberPosition' => 'Director'
                        ],
                        [
                            'memberImage' => '/img/contact/member-1.jpg',
                            'memberName' => 'Jane Doe',
                            'memberPosition' => 'Senior Sales Executive'
                        ],
                        [
                            'memberImage' => '/img/contact/member-2.jpg',
                            'memberName' => 'Devon Lane',
                            'memberPosition' => 'Sales Executive'
                        ],
                        [
                            'memberImage' => '/img/contact/member-3.jpeg',
                            'memberName' => 'Jane Doe',
                            'memberPosition' => 'Sales Executive'
                        ]
                    ]
                ],
                [
                    "itemName" => "Melbourne",
                    "teamData" => [
                        [
                            'memberImage' => '/img/contact/member-4.jpg',
                            'memberName' => 'Devon Lane',
                            'memberPosition' => 'Project Manager'
                        ],
                        [
                            'memberImage' => '/img/contact/member-5.jpg',
                            'memberName' => 'Jane Doe',
                            'memberPosition' => 'Project Manager'
                        ],
                        [
                            'memberImage' => '/img/contact/member-6.jpg',
                            'memberName' => 'Devon Lane',
                            'memberPosition' => 'Sales Executive'
                        ],
                        [
                            'memberImage' => '/img/contact/member-7.jpg',
                            'memberName' => 'Jane Doe',
                            'memberPosition' => 'Sales Executive'
                        ]
                    ]
                ]
            ];
        }

        return [
            'sectionTitle' => 'Connect with our sales team',
            'sectionSubtitle' => 'Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.',
            'items' => $items
        ];
    }
    public function existsByEmail(string $email): bool
    {
        return $this->model->where('email', '=', $email)->first() ? true : false;
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
        $userGroupMap = $this->userGroupContent->select(['user_group_id', 'name'])->limit(0)->findAll(false);
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
            'inserted_count' => count($validData),
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
        $defaultFields['display_name'] = 'User';
        $defaultFields['token'] = md5((string)(round(microtime(true) * 1000000)));
        $defaultFields['bio'] = 'Life is what happens when you\'re busy making other plans.';
        $defaultFields['avatar'] = 'default-avatar.jpg';
        $defaultFields['created_at'] = date('Y-m-d H:i:s');
        $defaultFields['updated_at'] = date('Y-m-d H:i:s');
        return $defaultFields;
    }

    private function prepareRecord(array $record, array $defaultFields): array
    {
        return isset($record['user_id']) && $record['user_id'] ? $record : array_merge($defaultFields, $record);
    }

    public function updateUserImage(array $data, int $user_id): bool
    {
        $user = $this->model->where('user_id', '=', $user_id)->first();
        if (!$user) {
            return false; // user not found
        }

        $dataobj = $data;
        $image = $dataobj[0]['objectURL'];

        $this->db->beginTransaction();
        try {
            // UPDATE `user` SET `image` = $img WHERE `user`.`user_id` = $user_id
            $user->update(['avatar' => $image]);
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // delete vendor image
    public function deleteUserImage(int $user_id): bool
    {
        $this->model->clearQuery();
        $user = $this->model->where('user_id', '=', $user_id)->first();
        if (!$user) {
            return false; // user not found
        }
        $user->update(['avatar' => '']);
        return true;
    }

    public function createRequest(string $name, string $description, string $attachments_path): bool
    {
        $user_id = 1;
        
        $request = $this->model->create([
            'name' => $name,
            'description' => $description,
            'attachments' => $attachments_path,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'status' => 1,
            'user_id' => $user_id,
        ]);
        return $request ? true : false;
    }

    // userAuth
    public function userAuth(): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    
        $sessionData = $_SESSION['user_data'] ?? $_SESSION['user'] ?? null;
        if (!$sessionData) {
            return [];
        }
    
        return is_array($sessionData) ? $sessionData : (array) $sessionData;
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

    public function loginUser(array $data): object|false
    {
        $user = $this->model->where('email', '=', $data['email'])->first();
        if (!$user) {
            return false;
        }
        // if (!password_verify($data['password'], $user->password)) {
        //     return false;
        // }
        return $user;
    }

    public function createUser(array $data): object|false
    {
        try {
            $this->db->beginTransaction();

            // Map form fields to user table columns
            $name = trim($data['name'] ?? $data['first_name'] ?? '');
            $parts = explode(' ', $name, 2);
            $email = trim($data['email'] ?? '');

            $userData = array_merge($data, [
                'uuid' => uuidToBin(generateUuidV4()),
                'user_group_id' => 1,
                'site_id' => 1,
                'status' => 1,
                'subscribe' => 0,
                'username' => $data['username'] ?? str_replace([' ', '@'], ['-', '-at-'], strtolower($email)),
                'email' => $email,
                'first_name' => $parts[0] ?? '',
                'last_name' => $parts[1] ?? '',
                'display_name' => $data['display_name'] ?? $name,
                'token' => md5((string)(round(microtime(true) * 1000000))),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            unset($userData['csrf_token'], $userData['name']);
            // Hash plain password
            if (isset($userData['password']) && !str_starts_with((string)$userData['password'], '$')) {
                $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
            }

            $user = $this->model->create($userData);

            $customer = $this->customer->where('user_id', '=', $user->user_id)->first();
            if (empty($customer)) {
                $customerData = [
                    'user_id' => $user->user_id, 
                    'company_id' => 1,
                    'uuid' => bin2hex(random_bytes(16)),
                    'organisation_id' => 1, 
                    'org_code' => 'ORG-' . $user->user_id, 
                    'name' => $data['name'], 
                    'gmail_Id' => $data['email'],
                    'company_name' => $data['companyName']??$data['name']
                ];
                $customer = $this->customer->create($customerData);
            }
            
            $this->db->commit();

            return $user ?: false;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to create user: " . $e->getMessage());
        }
    }
    public function updateUserProfile(array $data)
    {
        try {
            $this->db->beginTransaction();

            $user_id = (int) $data['user_id'];

            $updateUser = [
                'first_name'   => (string) ($data['first_name'] ?? ''),
                'last_name'    => (string) ($data['last_name'] ?? ''),
                'display_name' => (string) ($data['display_name'] ?? ''),
                'phone_number' => (string) ($data['phone'] ?? ''),
                'designation' => (string) ($data['designation'] ?? ''),
                'notify_orders' => isset($data['notify_orders']) ? 1 : 0,
                'notify_quotes' => isset($data['notify_quotes']) ? 1 : 0,
                'subscribe'     => isset($data['notify_product_news']) ? 1 : 0,
            ];

            if (!empty($data['email'])) {
                $updateUser['email'] = (string) $data['email'];
            }

            $updateCustomer = [
                'company_name'      => (string) ($data['company'] ?? ''),
                'billing_address_1' => (string) ($data['street_address'] ?? ''),
                'billing_city'      => (string) ($data['suburb'] ?? ''),
                'billing_region'    => (string) ($data['state'] ?? ''),
                'billing_post_code' => (string) ($data['postcode'] ?? ''),
            ];

            $this->model->clearQuery();
            $user = $this->model->where('user_id', '=', $user_id)->first();

            if (!$user) {
                throw new \Exception('User not found');
            }

            $user->update($updateUser);

            $this->customer->clearQuery();
            $customer = $this->customer->where('user_id', '=', $user_id)->first();

            if (!$customer) {
                throw new \Exception('Customer not found');
            }

            $customer->update($updateCustomer);

            $this->db->commit();

            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception('Failed to update user: ' . $e->getMessage());
        }
    }



}
