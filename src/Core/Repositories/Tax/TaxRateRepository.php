<?php

declare(strict_types=1);

namespace App\Core\Repositories\Tax;

use App\Core\Models\Tax\TaxRate;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Validation\TaxRateDataValidation;
use PDO;
use League\Csv\Reader;
use Exception;

class TaxRateRepository extends BaseRepository implements TaxRateRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'tax_rate', TaxRate::class);
    }


    public function getAll(): array
    {
        $this->model->orderBy('name', 'ASC');
        return $this->findAll();
    }

    public function findByRegionGroup(int $regionGroupId): array
    {
        $this->model->where('region_group_id', '=', (string)$regionGroupId);
        $this->model->orderBy('name', 'ASC');
        return $this->findAll();
    }

    public function findByName(string $name): ?TaxRate
    {
        $this->model->where('name', '=', $name);
        $this->model->limit(1);
        
        $results = $this->model->findAll();
        return !empty($results['items']) ? $results['items'][0] : null;
    }

    public function findByType(string $type): array
    {
        $this->model->where('type', '=', $type);
        $this->model->orderBy('name', 'ASC');
        return $this->findAll();
    }

    // import admins
    public function importTaxRates(string $csv_file): array
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
            'region_group_id',
            'rate',
            'type',
        ];
        $records = $reader->getRecords();
        $validData = [];
        $invalid = [];
        $processed = [];
        $updated = [];
        $duplicate = [];
        $existingData = [];

        // fetch existing data
        $existingMap = $this->model->select(['tax_rate_id', 'name'])->findAll(false);
        $existingMap = array_column($existingMap, 'tax_rate_id', 'name');
        $existingDataMaps = [
            'taxRateIds' => $existingMap,
        ];

        foreach ($records as $offset => $record) {
            $record = $this->prepareRecord($record, $defaultFields);
            $validator = new TaxRateDataValidation($record, $requiredFields, array_keys($defaultFields), $existingDataMaps);
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
            //     $this->model->upsert($existingData, ['tax_rate_id']);
            // }
            if (count($validData) > 0) {
                $this->model->insert($validData);
            }
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to import tax rates: " . $e->getMessage());
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
        $defaultFields['name'] = 'Tax Rate Name';
        $defaultFields['region_group_id'] = 1;
        $defaultFields['rate'] = 0.0000;
        $defaultFields['type'] = 'P';

        return $defaultFields;
    }

    private function prepareRecord(array $record, array $defaultFields): array
    {
        return isset($record['admin_id']) && $record['admin_id'] ? $record : array_merge($defaultFields, $record);
    }

    public function isNameExists(string $name, ?int $id = 0):bool
    {
        $this->model->where('name', '=', $name);
        $this->model->limit(1);
        if($id > 0){
            $this->model->where('tax_rate_id', '!=', $id);
        }
        $results = $this->model->first();
        if(isset($results->tax_rate_id) && $results->tax_rate_id > 0){
            return true;
        }
        return false;
    }
} 