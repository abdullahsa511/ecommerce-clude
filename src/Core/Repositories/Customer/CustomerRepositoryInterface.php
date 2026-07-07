<?php

declare(strict_types=1);

namespace App\Core\Repositories\Customer;

use App\Core\Models\Customer\Customer;

interface CustomerRepositoryInterface
{
    /**
     * Get all customers with pagination and filtering
     * 
     * @param int $start Starting offset
     * @param int $limit Number of records per page
     * @param string|null $search Search term for customer name
     * @return array{data: array, total: int}
     */
    public function getAll(
        int $start = 0,
        int $limit = 10,
        ?string $search = null
    ): array;

    /**
     * Get customer by ID
     * 
     * @param int $customer_id Customer ID
     * @return Customer|null
     */
    public function get(int $customer_id): ?Customer;

    /**
     * Add new customer
     * 
     * @param array $customer Customer data
     * @return Customer|null
     */
    public function add(array $customer): ?Customer;

    /**
     * Update customer
     * 
     * @param int $customer_id Customer ID
     * @param array $customer Customer data
     * @return bool
     */
    public function edit(int $customer_id, array $customer): bool;

    /**
     * Delete customer (soft delete)
     * 
     * @param int $customer_id Customer ID
     * @return bool
     */
    public function delete(int $customer_id): bool;

    /**
     * Get all customers
     * 
     * @return array
     */
    public function getAllCustomers(): array;
    public function searchCustomers(string $search): array;

    /**
     * Get customer by ID
     * 
     * @param int $id Customer ID
     * @return array
     */
    public function getCustomerById(int $id): array;

    /**
     * Find customer by org code
     * 
     * @param string $code Organization code
     * @param int|null $exclude_id Customer ID to exclude from search
     * @return Customer|null
     */
    public function findByOrgCode(string $code, ?int $exclude_id = null): ?Customer;

    /**
     * Import customers from CSV file
     * 
     * @param string $csv_file Path to CSV file
     * @return array
     */
    public function importCustomers(string $csv_file): array;

    /**
     * Create customer
     * 
     * @param array $data Customer data
     * @return array
     */
    public function createCustomer(array $data): array;

    /**
     * Update customer
     * 
     * @param array $data Customer data
     * @param int $id Customer ID
     * @return array
     */
    public function updateCustomer(array $data, int $id): array;

    /**
     * Delete customer (soft delete)
     * 
     * @param int $id Customer ID
     * @return bool
     */
    public function deleteCustomer(int $id): bool;

    /**
     * Delete multiple customers (soft delete)
     * 
     * @param array $customer_ids Array of customer IDs
     * @return bool
     */
    public function deleteMultipleCustomers(array $customer_ids): bool;
    public function findByUserId(int $user_id): array;
    public function checkExistingCustomer(string $email): array;
    public function sendEmailVerification(string $email): array;
    public function verifyEmail(string $email, string $otpCode): array;
    public function getCustomerInfo(string $email,?string $name = null): array;
    public function getCustomerUserInfoByCustomerId(int $customer_id): array;
} 