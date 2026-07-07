<?php

namespace App\Core\Repositories\Admin;

use App\Core\Models\Admin\AdminRole;
use App\Core\Repositories\Base\BaseRepository;
use PDO;
use League\Csv\Reader;
use Exception;
use App\Core\Validation\RoleDataValidation;

class AdminRoleRepository extends BaseRepository implements AdminRoleRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'admin_role', AdminRole::class);
    }

    /**
     * Get all roles with optional pagination
     * 
     * @param int|null $start
     * @param int|null $limit 
     * @return array
     */
    public function getAll(?int $start = null, ?int $limit = null): array
    {
        $query = $this->model->select(['role.*']);

        if ($start !== null) {
            $query->offset($start);
        }

        if ($limit !== null) {
            $query->limit($limit);
        }

        $results = $query->findAll();
        $totalRecords = $query->countAll();

        return [
            'data' => $results,
            'total' => $totalRecords
        ];
    }

    /**
     * Get a single role by ID
     * 
     * @param int $roleId
     * @return array|null
     */
    public function get(int $roleId): ?array
    {
        $result = $this->model->find($roleId);
        return $result ? $result->findAll() : null;
    }
   
    // import admins
    public function importRoles(string $csv_file): array
    {
        $reader = Reader::createFromPath($csv_file, 'r');
        $reader->setHeaderOffset(0);
        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }
        $defaultFields = $this->getDefaultFields($headers);
        $requiredFields = [
            'name',
            'display_name',
            'permissions',
        ];
        $records = $reader->getRecords();
        $validData = [];
        $invalid = [];
        $processed = [];
        $updated = [];
        $duplicate = [];
        $existingData = [];

        // fetch existing data
        $existingMap = $this->model->select(['role_id', 'name'])->findAll(false);
        $existingMap = array_column($existingMap, 'role_id', 'name');
        $existingDataMaps = [
            'roleIds' => $existingMap,
        ];

        foreach ($records as $offset => $record) {
            $record = $this->prepareRecord($record, $defaultFields);
            $validator = new RoleDataValidation($record, $requiredFields, array_keys($defaultFields), $existingDataMaps);
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

            // if duplicate in the CSV (compare names against already-collected valid rows)
            $name = $record['name'] ?? null;
            if ($name !== null) {
                $namesInValidData = array_column($validData, 'name');
                if (in_array($name, $namesInValidData, true)) {
                    $duplicate[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'reason' => 'duplicate in CSV'
                    ];
                    continue;
                }
            }

            if (in_array($validator->toArray()['data']['name'], $processed, true)) {
                $updated[] = [
                    'row' => $offset + 2,
                    'data' => $validator->toArray()['data'],
                    'reason' => 'duplicate in database'
                ];
                continue;
            }
            if ($validator->isExistingData) {
                $existingData[] = $record;
                $updated[] = [
                    'row' => $offset + 2,
                    'data' => $record,
                    'reason' => 'existing in database'
                ];
                continue;
            } else {
                $validData[] = $validator->toArray()['data'];
            }
            $processed[] = $unique;
        }
        try {
            $this->db->beginTransaction();
            // if (count($existingData) > 0) {
            //     $this->model->upsert($existingData, ['tax_type_id']);
            // }
            if (count($validData) > 0) {
                $this->model->insert($validData);
            }
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to import tax types: " . $e->getMessage());
        }

        return [
            'success' => true,
            'total_records' => iterator_count($records),
            'inserted' => count($validData),
            'valid_records' => count($validData),
            'valid_data' =>  $validData,
            'invalid_records' => count($invalid),
            'invalid_data' => $invalid,
            'updated_records' => count($updated),
            'updated_data' => $updated,
            'duplicated_records' => count($duplicate),
            'duplicated_data' => $duplicate,
            // 'existing_records' => count($existingData),
            // 'existing_data' => $existingData,
            'processed_records' => count($processed),
            'processed_data' => $processed,
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
        $defaultFields['name'] = 'role_name';
        $defaultFields['display_name'] = 'Role Display Name';
        $defaultFields['permissions'] = '';

        return $defaultFields;
    }

    private function prepareRecord(array $record, array $defaultFields): array
    {
        return isset($record['role_id']) && $record['role_id'] ? $record : array_merge($defaultFields, $record);
    }

    
} 