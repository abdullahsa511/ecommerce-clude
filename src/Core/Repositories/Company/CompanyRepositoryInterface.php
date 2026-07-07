<?php

declare(strict_types=1);

namespace App\Core\Repositories\Company;

use App\Core\Models\Company\Company;

interface CompanyRepositoryInterface
{
    /**
     * Get all companies with pagination and filtering
     * 
     * @param int $start Starting offset
     * @param int $limit Number of records per page
     * @param string|null $search Search term for company name
     * @return array{data: array, total: int}
     */
    public function getAll(
        int $start = 0,
        int $limit = 10,
        ?string $search = null
    ): array;

    /**
     * Get company by ID
     * 
     * @param int $company_id Company ID
     * @return Company|null
     */
    public function get(int $company_id): ?Company;

    /**
     * Add new company
     * 
     * @param array $company Company data
     * @return Company|null
     */
    public function add(array $company): ?Company;

    /**
     * Update company
     * 
     * @param int $company_id Company ID
     * @param array $company Company data
     * @return bool
     */
    public function edit(int $company_id, array $company): bool;

    /**
     * Delete company (soft delete)
     * 
     * @param int $company_id Company ID
     * @return bool
     */
    public function delete(int $company_id): bool;

    /**
     * Get all companies
     * 
     * @return array
     */
    public function getAllCompanies(): array;

    /**
     * Get company by ID
     * 
     * @param int $id Company ID
     * @return array
     */
    public function getCompanyById(int $id): array;

    /**
     * Find company by code
     * 
     * @param string $code Company code
     * @param int|null $exclude_id Company ID to exclude from search
     * @return Company|null
     */
    public function findByCode(string $code, ?int $exclude_id = null): ?Company;

    /**
     * Import companies from CSV file
     * 
     * @param string $csv_file Path to CSV file
     * @return array
     */
    public function importCompanies(string $csv_file): array;

    /**
     * Create company
     * 
     * @param array $data Company data
     * @return array
     */
    public function createCompany(array $data): array;

    /**
     * Update company
     * 
     * @param array $data Company data
     * @param int $id Company ID
     * @return array
     */
    public function updateCompany(array $data, int $id): array;

    /**
     * Delete company (soft delete)
     * 
     * @param int $id Company ID
     * @return bool
     */
    public function deleteCompany(int $id): bool;

    /**
     * Delete multiple companies (soft delete)
     * 
     * @param array $company_ids Array of company IDs
     * @return bool
     */
    public function deleteMultipleCompanies(array $company_ids): bool;
} 