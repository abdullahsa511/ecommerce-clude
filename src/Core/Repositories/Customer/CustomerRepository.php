<?php

declare(strict_types=1);

namespace App\Core\Repositories\Customer;

use App\Core\Exceptions\ValidationException;
use App\Core\Models\Customer\Customer;
use App\Core\Models\Geoip\Country;
use App\Core\Models\User\UserAddress;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Repositories\Email\EmailRepositoryInterface;
use App\Core\Repositories\UserRepositoryInterface;
use App\Core\Validation\CustomerDataValidation;
use App\Core\Models\User;
use PDO;
use League\Csv\Reader;
use Exception;
use function App\Core\System\utils\uuidToBin;
use function App\Core\System\utils\generateUuidV4;


class CustomerRepository extends BaseRepository implements CustomerRepositoryInterface
{
    private User $user;
    private Country $country;
    private UserAddress $userAddress;
    private UserRepositoryInterface $userRepository;
    private EmailRepositoryInterface $emailRepository;

    public function __construct(
        PDO $db,
        Country $country,
        UserAddress $userAddress,
        User $user,
        UserRepositoryInterface $userRepository,
        EmailRepositoryInterface $emailRepository
    ) {
        parent::__construct($db, 'customer', Customer::class);
        $this->country = $country;
        $this->userAddress = $userAddress;
        $this->country->setDb($db);
        $this->userAddress->setDb($db);
        $this->user = $user;
        $this->user->setDb($db);
        $this->userRepository = $userRepository;
        $this->emailRepository = $emailRepository;
    }

    /**
     * Get all customers with pagination and filtering
     */
    public function getAll(int $start = 0, int $limit = 10, ?string $search = null): array
    {
        $this->model->clearQuery();
        $query = $this->model->select(['*'])->whereNull('deleted_at');

        // Search by customer name
        if ($search !== null) {
            $query->whereLike('name', $search);
        }

        // Apply pagination
        $query->orderBy('customer_id', 'DESC')
            ->limit($limit)
            ->offset($start);

        $data = $query->findAll(false);
        $total = $this->model->countAll();

        return [
            'data' => $data,
            'total' => $total
        ];
    }

    /**
     * Get customer by ID
     */
    public function get(int $customer_id): ?Customer
    {
        $this->model->clearQuery();
        return $this->model->where('customer_id', '=', $customer_id)
            ->whereNull('deleted_at')
            ->first();
    }
    public function getByEmail(string $email): ?Customer
    {
        $this->model->clearQuery();
        return $this->model->where('gmail_Id', '=', $email)
            ->whereNull('deleted_at')
            ->first();
    }

    /**
     * Add new customer
     */
    public function add(array $customer): ?Customer
    {
        try {
            $this->db->beginTransaction();
            $result = $this->model->create($customer);
            $this->db->commit();
            return $result;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return null;
        }
    }

    /**
     * Update customer
     */
    public function edit(int $customer_id, array $customer): bool
    {
        try {
            $this->db->beginTransaction();
            $result = $this->update($customer_id, $customer);
            $this->db->commit();
            return $result !== null;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Delete customer (soft delete)
     */
    public function delete(int $customer_id): bool
    {
        try {
            $this->db->beginTransaction();
            $this->model->clearQuery();
            $customer = $this->model->where('customer_id', '=', $customer_id)->first();
            if ($customer) {
                $customer->update(['deleted_at' => date('Y-m-d H:i:s')]);
            }
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Get all customers
     */
    public function getAllCustomers(): array
    {
        $this->model->clearQuery();
        $data = $this->model
            ->select(['customer.*', 'customer.name as first_name', 'customer.gmail_Id as email', 'customer.phone as phone_number', 'CONCAT(user.first_name, " ", user.last_name) as username', 'user.user_id','user.user_group_id', 'user_group_content.name as user_group_name'])
            ->join('user', 'user.user_id', '=', 'customer.user_id')
            ->join('user_group_content', 'user_group_content.user_group_id', '=', 'user.user_group_id')
            ->whereNull('deleted_at')
            ->orderBy('customer_id', 'DESC')
            ->findAll(false);
        return $data;
    }

    public function getCustomerUserInfoByCustomerId(int $customer_id): array
    {
        $this->model->clearQuery();
        $data = $this->model
            ->select(['customer.*', 'customer.name as first_name', 'customer.gmail_Id as email', 'customer.phone as phone_number', 'CONCAT(user.first_name, " ", user.last_name) as username', 'user.user_id','user.user_group_id', 'user_group_content.name as user_group_name'])
            ->join('user', 'user.user_id', '=', 'customer.user_id')
            ->join('user_group_content', 'user_group_content.user_group_id', '=', 'user.user_group_id')
            ->whereNull('deleted_at')
            ->where('customer_id', '=', $customer_id)
            ->first();
        return $data ? (array) $data->data : [];
    }

    public function searchCustomers(string $search): array
    {
        $this->model->clearQuery();
        $query = $this->model->select(['customer.*', 'customer.name as first_name', 'customer.gmail_Id as email', 'customer.phone as phone_number', 'CONCAT(user.first_name, " ", user.last_name) as username', 'user.user_id','user.user_group_id'])
        ->with(['user']);
        if($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }
        $results = $query->orderBy('customer.customer_id', 'DESC')
        ->limit(50)
        ->findAll(false);
        
        $data = [];
        foreach($results as $result) {
            $this->userAddress->clearQuery();
            $user_id = (int) ($result['user_id'] ?? 0);

            if ($user_id > 0) {
                $billingAddress = $this->userAddress
                    ->where('user_id', '=', $user_id)
                    ->where('is_billing', '=', 1)
                    ->first();

                $this->userAddress->clearQuery();
                $shippingAddress = $this->userAddress
                    ->where('user_id', '=', $user_id)
                    ->where('is_shipping', '=', 1)
                    ->first();
            } else {
                $billingAddress = null;
                $shippingAddress = null;
            }

            $billingAddress = isset($billingAddress->data) ? $billingAddress->data : null;
            $shippingAddress = isset($shippingAddress->data) ? $shippingAddress->data : null;
            $data[] = [
                'customer' => $result,
                'user' => json_decode($result['user']),
                'billingAddress' => isset($billingAddress) ? $billingAddress : null,
                'shippingAddress' => isset($shippingAddress) ? $shippingAddress : null
            ];
        }
        return $data;
    }

    /**
     * Get customer by ID
     */
    public function getCustomerById(int $id): array
    {
        $this->model->clearQuery();
        $item = $this->model->where('customer_id', '=', $id)->first();
        return $item ? (array) $item->data : [];
    }

    /**
     * Find customer by org code
     */
    public function findByOrgCode(string $code, ?int $exclude_id = null): ?Customer
    {
        $this->model->clearQuery();
        $query = $this->model->where('org_code', '=', $code)
            ->whereNull('deleted_at');
        
        if ($exclude_id !== null) {
            $query->where('customer_id', '!=', $exclude_id);
        }
        
        return $query->first();
    }

    /**
     * Create customer
     */
    public function createCustomer(array $data): array
    {
        $this->model->clearQuery();
        
        // Get existing data for validation
        $existingMap = $this->model->select(['customer_id', 'org_code'])->findAll(false);
        $existingMap = array_column($existingMap, 'customer_id', 'org_code');
        $existingCustomerIds = array_values($existingMap);

        // Get company IDs for validation
        $companyModel = new \App\Core\Models\Company\Company();
        $companyModel->setDb($this->db);
        $companies = $companyModel->select(['company_id'])->findAll(false);
        $companyIds = array_column($companies, 'company_id');

        $existingDataMaps = [
            'customerMap' => $existingMap,
            'customerIds' => $existingCustomerIds,
            'companyIds' => $companyIds,
        ];

        // Validate data
        $validator = new CustomerDataValidation($data, [], [], $existingDataMaps);
        $validated = $validator->validate();

        if ($validated === false) {
            throw new ValidationException($validator->getErrors(true));
        }

        $payload = (array) $validator->customer;
        unset($payload['customer_id']); // Remove ID if present

        try {
            // $this->db->beginTransaction();
            $obj = $this->model->create($payload);
            $insertedId = $obj->customer_id ?? null;
            $result = (array)$obj->data;
            // $this->db->commit();

            return $insertedId ? $this->getCustomerById($insertedId) : [];
        } catch (\Exception $e) {
            // $this->db->rollBack();
            throw new \Exception("Failed to create customer: " . $e->getMessage());
        }
    }

    /**
     * Update customer
     */
    public function updateCustomer(array $data, int $id): array
    {
        $this->model->clearQuery();
        $item = $this->model->where('customer_id', '=', $id)->first();
        if (!$item) {
            return [];
        }

        // Get existing data for validation
        $existingMap = $this->model->select(['customer_id', 'org_code'])->findAll(false);
        $existingMap = array_column($existingMap, 'customer_id', 'org_code');
        $existingCustomerIds = array_values($existingMap);

        // Get company IDs for validation
        $companyModel = new \App\Core\Models\Company\Company();
        $companyModel->setDb($this->db);
        $companies = $companyModel->select(['company_id'])->findAll(false);
        $companyIds = array_column($companies, 'company_id');

        $existingDataMaps = [
            'customerMap' => $existingMap,
            'customerIds' => $existingCustomerIds,
            'companyIds' => $companyIds,
        ];

        // Add customer_id to data for validation
        $data['customer_id'] = $id;

        // Validate data
        $validator = new CustomerDataValidation($data, [], [], $existingDataMaps);
        $validated = $validator->validate();

        if ($validated === false) {
            throw new ValidationException($validator->getErrors(true));
        }

        $updateData = (array) $validator->customer;
        unset($updateData['customer_id']); // Remove ID from update data

        try {
            $this->db->beginTransaction();
            $item->clearQuery();
            $item->update($updateData);
            $this->db->commit();
            return $this->getCustomerById($id);
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to update customer: " . $e->getMessage());
        }
    }

    /**
     * Delete customer (soft delete)
     */
    public function deleteCustomer(int $id): bool
    {
        try {
            $this->db->beginTransaction();
            $this->model->clearQuery();
            $customer = $this->model->where('customer_id', '=', $id)->first();
            if ($customer) {
                $customer->update(['deleted_at' => date('Y-m-d H:i:s')]);
            }
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to delete customer: " . $e->getMessage());
        }
    }

    /**
     * Delete multiple customers (soft delete)
     */
    public function deleteMultipleCustomers(array $customer_ids): bool
    {
        try {
            $this->db->beginTransaction();
            $this->model->clearQuery();
            $customers = $this->model->select(['customer_id'])
                ->whereIn('customer_id', $customer_ids)
                ->findAll();
            
            $deletedAt = date('Y-m-d H:i:s');
            foreach ($customers as $customer) {
                $customer->update(['deleted_at' => $deletedAt]);
            }
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to delete customers: " . $e->getMessage());
        }
    }

    /**
     * Import customers from CSV file
     */
    public function importCustomers(string $csv_file): array
    {
        $reader = Reader::createFromPath($csv_file, 'r');
        $reader->setHeaderOffset(0);
        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new \Exception("CSV file has no headers");
        }

        $defaultFields = $this->getDefaultFields($headers);
        $records = $reader->getRecords();

        $custoemr = [];
        $users = [];
        $billingAddress = [];
        $shippingAddress = [];
        $existingData = [];
        $invalid = [];
        $updated = [];
        $processed = [];

        $existingMap = $this->model->select(['customer_id', 'org_code', 'name'])->findAll(false);
        $existingMap = array_column($existingMap, 'customer_id', 'org_code');
        $existingCustomerIds = array_values($existingMap);

        // Get company IDs for validation
        $companyModel = new \App\Core\Models\Company\Company();
        $companyModel->setDb($this->db);
        $companies = $companyModel->select(['company_id'])->findAll(false);
        $companyIds = array_column($companies, 'company_id');
        $countries = $this->country->select(['country_id', 'name'])->findAll(false);
        $countryIds = array_column($countries, 'country_id', 'name');
        $userMaps = $this->user->select(['user_id', 'email'])->limit(0)->findAll(false);
        $userMaps = array_column($userMaps, 'user_id', 'email');

        $existingDataMaps = [
            'customerMap' => $existingMap,
            'customerIds' => $existingCustomerIds,
            'companyIds' => $companyIds,
            'countryIds' => $countryIds,
            'userIds' => $userMaps,
        ];

        foreach ($records as $offset => $record) {
            try {
                // Merge defaults with record (ensures required fields exist)
                $record = $this->prepareRecord($record, $defaultFields);
                $validator = new CustomerDataValidation($record, [], [], $existingDataMaps);
                $validated = $validator->validate();

                // If validation fails, store record and error info in $invalid
                if ($validated === false) {
                    $invalid[] = [
                        'row' => $offset + 2, // +2 because CSV row count starts at 1 and includes header
                        'data' => $record,
                        'errors' => $validator->getErrors()
                    ];
                    continue;
                }

                $unique = $validator->getUniqueIdentifier();

                if ($validator->isExistingData) {
                    $toUpdate = (array) $validator->customer;
                    $existingData[] = $toUpdate;
                } else {
                    $custoemr[] = (array) $validator->customer;
                    $billingAddress[] = (array) $validator->billingAddress;
                    $shippingAddress[] = (array) $validator->shippingAddress;
                    $users[] = (array) $validator->user;
                }

                // Skip if customer has already been processed
                if (in_array($unique, $processed, true)) {
                    $updated[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'identifier' => $unique
                    ];
                    continue;
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
            // insert customer
            if (count($custoemr) > 0) {
                $this->model->upsert($custoemr, ['uuid']);
            }
            if (count($users) > 0) {
                $this->user->upsert($users, ['email']);
            }
            // insert user address for billing and shipping address
            if (count($custoemr) > 0 && count($billingAddress) > 0 && count($shippingAddress) > 0) {
                // get customer id by uuid
                $customerEmails = array_column($users, 'email');
                $customerIds = $this->user->select(['user_id', 'email'])
                ->whereIn('email', $customerEmails)->limit(0)->findAll();
                // map customer id by uuid
                $userIds = array_column($customerIds, 'user_id', 'email');
                $billing = [];
                $shipping = [];
                foreach ($billingAddress as &$billAdds) {
                    if(isset($userIds[$billAdds['email']])) {
                        $billAdds['user_id'] = $userIds[$billAdds['email']];
                        unset($billAdds['email']);
                        $billing[] = $billAdds;
                    }
                }
                foreach ($shippingAddress as &$shipAdds) {
                    if(isset($userIds[$shipAdds['email']])) {
                        $shipAdds['user_id'] = $userIds[$shipAdds['email']];
                        unset($shipAdds['email']);
                        $shipping[] = $shipAdds;
                    }
                }
                if (count($billing) > 0) {
                    $this->userAddress->upsert($billing, ['user_id', 'is_billing', 'is_shipping']);
                }
                if (count($shipping) > 0) {
                    $this->userAddress->upsert($shipping, ['user_id', 'is_billing', 'is_shipping']);
                }
            }

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update customers: " . $e->getMessage());
        }

        return [
            'success' => true,
            'total_records' => iterator_count($records),
            'valid_records' => count($custoemr),
            'valid_data' => $custoemr,
            'invalid_records' => count($invalid),
            'updated_records' => count($existingData),
            'updated_data' => $existingData,
            'duplicated_records' => count($updated),
            'duplicated_data' => $updated,
            'customers' => [
                'inserted_count' => count($custoemr),
                'valid_data' => $custoemr
            ],
            'invalid_data' => $invalid,
            'summary' => [
                'success_rate' => iterator_count($records) > 0
                    ? round((count($custoemr) / iterator_count($records)) * 100, 2) . '%'
                    : '0%',
                'customers_processed' => count($custoemr),
                'errors' => count($invalid),
            ],
        ];
    }

    /**
     * Get default fields for CSV import
     */
    private function getDefaultFields(array $headers): array
    {
        $defaultFields = [];
        foreach ($headers as $header) {
            $defaultFields[$header] = null;
        }
        $defaultFields['rating'] = 0.00;
        $defaultFields['segment_id'] = 1;
        $defaultFields['term_id'] = 1;
        $defaultFields['credit_limit'] = 0.00;
        $defaultFields['caution_bad_payer'] = 0;
        $defaultFields['is_active'] = 1;
        $defaultFields['default_price_list'] = 1;
        $defaultFields['deposit_percentage'] = 0.00;
        $defaultFields['gst'] = 0.00;
        $defaultFields['is_gmail_lead'] = 0;
        return $defaultFields;
    }

    /**
     * Prepare record for processing
     */
    private function prepareRecord(array $record, array $defaultFields): array
    {
        return isset($record['customer_id']) && $record['customer_id'] 
            ? $record 
            : array_merge($defaultFields, $record);
    }

    // find customer by user id
    public function findByUserId(int $user_id): array
    {
        $this->model->clearQuery();
        $customer = $this->model->where('user_id', '=', $user_id)->first();
        return $customer ? (array) $customer->data : [];
    }

    // find customer by email
    public function checkExistingCustomer(string $email): array
    {
        $user = $this->userRepository->findByEmail($email);
        if (!$user) {
            return ['success' => false, 'message' => 'User not found', 'data' => []];
        }

        // CHECK IF CUSTOMER EXISTS
        $customer = $this->model->where('user_id', '=', $user->user_id)->first();
        if (!$customer) {
            return ['success' => false, 'message' => 'Customer not found', 'data' => []];
        }
        $customer = (array) $customer->data;
        $customer['email'] = $user->data->email;
        return ['success' => true, 'data' => $customer];
    }

    // send email verification
    public function sendEmailVerification(string $email): array
    {
        $subject = 'Verification Code with Krost';
        try {
            $user = $this->userRepository->findByEmail($email);
            if (!$user) {
                return ['status' => 404, 'success' => false, 'message' => 'User not found'];
            }
            $otpData = [
                'otp_code' => str_pad(strval(random_int(100000, 999999)), 6, '0', STR_PAD_LEFT),
                'otp_created_at' => date('Y-m-d H:i:s'),
                'otp_expiry_time' => date('Y-m-d H:i:s', strtotime('+10 minutes'))
            ];
           $updatedUser = $this->userRepository->update((int) $user->data->user_id, $otpData);
           $customer = $this->model->where('user_id', '=', $user->data->user_id)->first();

           if (!$updatedUser) {
             return ['status' => 500, 'success' => false, 'message' => 'Failed to update user'];
           }

           $context = [
                'subject'        => $subject,
                'client_name'    => $email,
                'otp'            => (string) $otpData['otp_code'],
                'otp_formatted'  => implode(' ', str_split((string) $otpData['otp_code'])),
                'expiry_minutes' => 10
            ];
        
            $loaderPath = ROOT_DIR . '/src/themes/landing/src/emailtemplate';
            try {
                $sendEmail = $this->emailRepository->sendEmail(
                    $email,
                    $subject,
                    'OTP Verification',
                    $context,
                    $loaderPath,
                    'otp-verification.html.twig',
                    null,
                    true
                );
            } catch (\Exception $e) {
                throw new \Exception("Failed to send email verification: " . $e->getMessage());
            }
           // send email in the background
          
           if (!$sendEmail) {
             return ['status' => 500, 'success' => false, 'message' => 'Failed to send email'];
           }

           return ['status' => 200, 'success' => true, 
           'message' => 'OTP sent successfully', 
           'customer' => [
            'uuid' => $customer->data->uuid,
            'customer_id' => $customer->data->customer_id,
            'name' => $customer->data->name,
            'email' => $user->data->email,
            'phone' => isset($user->data->phone_number) ? $user->data->phone_number : '01849XXXXXXX',
            'org_code' => $customer->data->org_code,
            'is_verified' =>  0,
            // 'otp' => $otpData['otp_code']
           ]];

        } catch (\Exception $e) {
            throw new \Exception("Failed to send email verification: " . $e->getMessage());
        }
       
    }

    public function getCustomerInfo(string $email, ?string $name = null): array
    {
        // CHECK IF USER EXISTS
        $user = $this->userRepository->findByEmail($email);
        if (!$user) {
            $userData = [
                'user_group_id' => 1,
                'username' => str_replace(' ', '-', strtolower(trim($name ?? ''))), 
                'password' => '123456', 
                'email' => $email, 
                'uuid' => uuidToBin(generateUuidV4()),
                'phone_number' => '01849XXXXXXX'
            ];
            // CREATE THE USER
            $user = $this->userRepository->create($userData);
        }

        // CHECK IF CUSTOMER EXISTS
        $customer = $this->findByUserId($user->user_id);
        if (empty($customer)) {
            $customerData = [
                'user_id' => $user->user_id, 
                'organisation_id' => 1, 
                'org_code' => 'ORG-' . $user->user_id, 
                'name' => $name ?? $email, 
                'gmail_Id' => $email,
                'company_name' => ''
            ];
            // CREATE THE CUSTOMER
            $customer = $this->createCustomer($customerData);
        }
        return [
            'user_id' => $user->user_id,
            'customer_id' => $customer['customer_id']
        ];
    }

    /**
     * Sends OTP via EmailRepository (Symfony Mailer; set MAILER_DSN and MAIL_FROM_* in .env).
     */
    private function sendEmailVerificationEmail(string $email, string $otpCode, ?string $subject = 'OTP Verification with Krost'): bool
    {
        // return $this->emailRepository->sendEmail(
        //     $email,
        //     'Email Verification',
        //     'Your OTP code is: ' . $otpCode,
        //     null,
        //     null,
        //     null
        // );

        $context = [
            'subject'        => $subject,
            'client_name'    => $email,
            'otp'            => (string) $otpCode,
            'otp_formatted'  => implode(' ', str_split((string) $otpCode)),
            'expiry_minutes' => 10,
        ];
        
        $loaderPath = ROOT_DIR . '/src/themes/landing/src/emailtemplate';
        try {
            return $this->emailRepository->sendEmail(
                $email,
                $subject,
                'OTP Verification',
                $context,
                $loaderPath,
                'otp-verification.html.twig',
                null,
                true
            );
        } catch (\Exception $e) {
            throw new \Exception("Failed to send email verification: " . $e->getMessage());
        }
    }

    // otp verification by email
    public function verifyEmail(string $email, string $otpCode): array
    {
        try {
            $user = $this->userRepository->findByEmail($email);
            if (!$user) {
                return ['status' => 404, 'success' => false, 'message' => 'User not found'];
            }
            if ($user->data->otp_code !== $otpCode) {
                return ['status' => 400, 'success' => false, 'message' => 'Invalid OTP code'];
            }
            if ($user->data->otp_expiry_time < date('Y-m-d H:i:s')) {
                return ['status' => 400, 'success' => false, 'message' => 'OTP expired'];
            }

            $updatedUser = $this->userRepository->update(
                (int) $user->data->user_id, 
                [
                    'otp_code' => '', 
                    'is_verified' => 1,
                    // 'otp_created_at' => '', 
                    // 'otp_expiry_time' => ''
                ]
            );
            $customer = $this->model->where('user_id', '=', $user->data->user_id)->first();
            if (!$customer->customer_id) {
                return ['status' => 500, 'success' => false, 'message' => 'Failed to update customer'];
            }
            $updatedCustomer = $this->model->update(
                [
                    'is_verified' => 1,
                ]
            );
            if (!$updatedCustomer) {
                return ['status' => 500, 'success' => false, 'message' => 'Failed to update customer'];
            }
            if (!$updatedUser) {
                return ['status' => 500, 'success' => false, 'message' => 'Failed to update user'];
            }
            // Extract relevant customer fields
            $customerData = [
                'customer_id' => $customer->data->customer_id ?? null,
                'user_id' => $customer->data->user_id ?? null,
                'email' => $userData->data->email ?? null,
                'name' => $customer->data->name ?? null,
                'is_verified' => $customer->data->is_verified ?? null,
                'uuid' => $customer->data->uuid ?? null
            ];
            $userData = [
                'avatar' => $user->data->avatar ?? null,
                'email' => $user->data->email ?? null,
                'first_name' => $user->data->first_name ?? null,
                'last_name' => $user->data->last_name ?? null,
                'is_verified' => $user->data->is_verified ?? null,
                'otp_code' => $user->data->otp_code ?? null,
                'otp_expiry_time' => $user->data->otp_expiry_time ?? null,
                'user_id' => $user->data->user_id ?? null,
                'username' => $user->data->username ?? null,
            ];
            return ['status' => 200, 'success' => true, 'message' => 'Email verified successfully', 'user' => $userData, 'customer' => $customerData];
        } catch (\Exception $e) {
            return ['status' => 500, 'success' => false, 'message' => 'Failed to verify email: ' . $e->getMessage()];
        }
    }
    
}
