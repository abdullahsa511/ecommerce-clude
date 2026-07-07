<?php

declare(strict_types=1);

namespace App\Core\Repositories\Company;

use App\Core\Models\Company\Company;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Validation\CompanyDataValidation;
use App\Core\Exceptions\ValidationException;
use App\Core\Repositories\Product\VendorRepositoryInterface;
use PDO;
use League\Csv\Reader;
use Exception;

class CompanyRepository extends BaseRepository implements CompanyRepositoryInterface
{
    private VendorRepositoryInterface $vendorRepository;
    public function __construct(PDO $db, VendorRepositoryInterface $vendorRepository)
    {
        parent::__construct($db, 'company', Company::class);
        $this->vendorRepository = $vendorRepository;
    }

    /**
     * Get all companies with pagination and filtering
     */
    public function getAll(int $start = 0, int $limit = 10, ?string $search = null): array
    {
        $this->model->clearQuery();
        $query = $this->model->select(['*'])->whereNull('deleted_at');

        // Search by company name
        if ($search !== null) {
            $query->whereLike('company_name', $search);
        }

        // Apply pagination
        $query->orderBy('sort_order', 'ASC')
            ->orderBy('company_id', 'DESC')
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
     * Get company by ID
     */
    public function get(int $company_id): ?Company
    {
        $this->model->clearQuery();
        return $this->model->where('company_id', '=', $company_id)
            ->whereNull('deleted_at')
            ->first();
    }

    /**
     * Add new company
     */
    public function add(array $company): ?Company
    {
        try {
            $this->db->beginTransaction();
            $result = $this->model->create($company);
            $this->db->commit();
            return $result;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return null;
        }
    }

    /**
     * Update company
     */
    public function edit(int $company_id, array $company): bool
    {
        try {
            $this->db->beginTransaction();
            $result = $this->update($company_id, $company);
            $this->db->commit();
            return $result !== null;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Delete company (soft delete)
     */
    public function delete(int $company_id): bool
    {
        try {
            $this->db->beginTransaction();
            $this->model->clearQuery();
            $company = $this->model->where('company_id', '=', $company_id)->first();
            if ($company) {
                $company->update(['deleted_at' => date('Y-m-d H:i:s')]);
            }
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * Get all companies
     */
    public function getAllCompanies(): array
    {
        $this->model->clearQuery();
        $data = $this->model
            ->select(['*'])
            ->whereNull('deleted_at')
            ->orderBy('sort_order', 'ASC')
            ->orderBy('company_id', 'DESC')
            ->findAll(false);
        return $data;
    }

    /**
     * Get company by ID
     */
    public function getCompanyById(int $id): array
    {
        $this->model->clearQuery();
        $item = $this->model->where('company_id', '=', $id)->first();
        $vendor = $this->vendorRepository->getVendorById((int) $item->vendor_id);
        $item->vendor_name = $vendor['name'] ?? '';
        return $item ? (array) $item->data : [];
    }

    /**
     * Find company by code
     */
    public function findByCode(string $code, ?int $exclude_id = null): ?Company
    {
        $this->model->clearQuery();
        $query = $this->model->where('company_code', '=', $code)
            ->whereNull('deleted_at');

        if ($exclude_id !== null) {
            $query->where('company_id', '!=', $exclude_id);
        }

        return $query->first();
    }

    /**
     * Create company
     */
    public function createCompany(array $data): array
    {
        $this->model->clearQuery();

        // Get existing data for validation
        $existingMap = $this->model->select(['company_id', 'company_code'])->findAll(false);
        $existingMap = array_column($existingMap, 'company_id', 'company_code');
        $existingCompanyIds = array_values($existingMap);

        $existingDataMaps = [
            'companyMap' => $existingMap,
            'companyIds' => $existingCompanyIds,
        ];

        // Validate data
        $validator = new CompanyDataValidation($data, [], [], $existingDataMaps);
        $validated = $validator->validate();

        if ($validated === false) {
            // throw new \Exception("Validation failed: " . json_encode($validator->getErrors()));
            throw new ValidationException($validator->getErrors(true));
        }

        $payload = (array) $validator->company;
        unset($payload['company_id']); // Remove ID if present

        try {
            $this->db->beginTransaction();
            $obj = $this->model->create($payload);
            $insertedId = $obj->company_id ?? null;
            $result = (array)$obj->data;
            $this->db->commit();

            return $insertedId ? $this->getCompanyById($insertedId) : [];
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to create company: " . $e->getMessage());
        }
    }

    /**
     * Update company
     */
    public function updateCompany(array $data, int $id): array
    {
        $this->model->clearQuery();
        $item = $this->model->where('company_id', '=', $id)->first();
        if (!$item) {
            return [];
        }

        // Get existing data for validation
        $existingMap = $this->model->select(['company_id', 'company_code'])->findAll(false);
        $existingMap = array_column($existingMap, 'company_id', 'company_code');
        $existingCompanyIds = array_values($existingMap);

        $existingDataMaps = [
            'companyMap' => $existingMap,
            'companyIds' => $existingCompanyIds,
        ];

        // Add company_id to data for validation
        $data['company_id'] = $id;

        // Validate data
        $validator = new CompanyDataValidation($data, [], [], $existingDataMaps);
        $validated = $validator->validate();

        if ($validated === false) {
            throw new ValidationException($validator->getErrors(true));
        }

        $updateData = (array) $validator->company;
        unset($updateData['company_id']); // Remove ID from update data

        try {
            $this->db->beginTransaction();
            $item->clearQuery();
            $item->update($updateData);
            $this->db->commit();
            return $this->getCompanyById($id);
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to update company: " . $e->getMessage());
        }
    }

    /**
     * Delete company (soft delete)
     */
    public function deleteCompany(int $id): bool
    {
        try {
            $this->db->beginTransaction();
            $this->model->clearQuery();
            $company = $this->model->where('company_id', '=', $id)->first();
            if ($company) {
                $company->update(['deleted_at' => date('Y-m-d H:i:s')]);
            }
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to delete company: " . $e->getMessage());
        }
    }

    /**
     * Delete multiple companies (soft delete)
     */
    public function deleteMultipleCompanies(array $company_ids): bool
    {
        try {
            $this->db->beginTransaction();
            $this->model->clearQuery();
            $companies = $this->model->select(['company_id'])
                ->whereIn('company_id', $company_ids)
                ->findAll();

            $deletedAt = date('Y-m-d H:i:s');
            foreach ($companies as $company) {
                $company->update(['deleted_at' => $deletedAt]);
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to delete companies: " . $e->getMessage());
        }
    }

    /**
     * Import companies from CSV file
     */
    public function importCompanies(string $csv_file): array
    {
        $reader = Reader::createFromPath($csv_file, 'r');
        $reader->setHeaderOffset(0);
        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new \Exception("CSV file has no headers");
        }

        $defaultFields = $this->getDefaultFields($headers);
        $records = $reader->getRecords();

        $validData = [];
        $existingData = [];
        $invalid = [];
        $updated = [];
        $processed = [];

        $existingMap = $this->model->select(['company_id', 'company_code', 'company_name'])->findAll(false);
        $existingMap = array_column($existingMap, 'company_id', 'company_code');
        $existingCompanyIds = array_values($existingMap);

        $existingDataMaps = [
            'companyMap' => $existingMap,
            'companyIds' => $existingCompanyIds,
        ];

        foreach ($records as $offset => $record) {
            try {
                // Merge defaults with record (ensures required fields exist)
                $record = $this->prepareRecord($record, $defaultFields);

                $validator = new CompanyDataValidation($record, [], [], $existingDataMaps);
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

                 // Skip if company has already been processed
                 if (in_array($unique, $processed, true)) {
                    $updated[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'identifier' => $unique
                    ];
                    continue;
                }

                if($validated->isExistingData){
                    $updated[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'identifier' => $unique
                    ];
                    continue;
                }else{
                    $validData[] = (array) $validated->company;
                }

                // if ($validator->isExistingData) {
                //     $toUpdate = (array) $validator->company;
                //     $existingData[] = $toUpdate;
                // } else {
                //     $validData[] = (array) $validator->company;
                // }

               
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

            if (count($updated) > 0) {
                // Update existing companies
                foreach ($updated as $data) {
                    $companyId = $data['company_id'] ?? null;
                    if ($companyId) {
                        unset($data['company_id']); // Remove ID from update data
                        $company = $this->model->where('company_id', '=', $companyId)->first();
                        if ($company) {
                            $company->update($data);
                        }
                        // $this->updateCompany($data, $companyId);
                    }
                }
            }

            if (count($validData) > 0) {
                // Remove company_id from new records if present
                foreach ($validData as &$data) {
                    unset($data['company_id']);
                }
                $this->model->insert($validData);
            }

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update companies: " . $e->getMessage());
        }

        return [
            'success' => true,
            'total_records' => iterator_count($records),
            'valid_records' => count($validData),
            'valid_data' => $validData,
            'inserted_count' => count($validData),
            'inserted_data' => $validData,
            'invalid_records' => count($invalid),
            'updated_records' => count($updated),
            'updated_data' => $updated,
            'duplicated_records' => count($updated),
            'duplicated_data' => $updated,
            'companies' => [
                'inserted_count' => count($validData),
                'valid_data' => $validData
            ],
            'invalid_data' => $invalid,
            'summary' => [
                'success_rate' => iterator_count($records) > 0
                    ? round((count($validData) / iterator_count($records)) * 100, 2) . '%'
                    : '0%',
                'companies_processed' => count($validData),
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
        $defaultFields['sort_order'] = 0;
        return $defaultFields;
    }

    /**
     * Prepare record for processing
     */
    private function prepareRecord(array $record, array $defaultFields): array
    {
        return isset($record['company_id']) && $record['company_id']
            ? $record
            : array_merge($defaultFields, $record);
    }
}
