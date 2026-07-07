<?php

declare(strict_types=1);

namespace App\Core\Repositories\Tax;

use App\Core\Models\Tax\TaxType;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Validation\TaxTypeDataValidation;
use League\Csv\Reader;
use PDO;

class TaxTypeRepository extends BaseRepository implements TaxTypeRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'tax_type', TaxType::class);
    }


    public function getAll(): array
    {
        $this->model->orderBy('tax_type_id', 'desc');
        return $this->findAll();
    }

    public function findByName(string $name): ?TaxType
    {
        $this->model->where('name', '=', $name);
        $this->model->limit(1);

        $results = $this->model->findAll();
        return !empty($results['items']) ? $results['items'][0] : null;
    }

    public function findByContent(string $content): ?TaxType
    {
        $this->model->where('content', '=', $content);
        $this->model->limit(1);

        $results = $this->model->findAll();
        return !empty($results['items']) ? $results['items'][0] : null;
    }

    public function importTaxTypes(string $csv_file): array
    {
        $reader = Reader::createFromPath($csv_file, 'r');
        $reader->setHeaderOffset(0);
        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }
        $defaultFields = $this->getDefaultFields($headers);
        $records = $reader->getRecords();
        $validData = [];
        $invalid = [];
        $processed = [];
        $updated = [];
        $duplicate = [];
        $existingData = [];

        // fetch existing data
        $existingMap = $this->model->select(['tax_type_id', 'name'])->findAll(false);
        $existingMap = array_column($existingMap, 'tax_type_id', 'name');

        foreach ($records as $offset => $record) {
            $record = $this->prepareRecord($record, $defaultFields);
            $validator = new TaxTypeDataValidation($record, [], [], $existingMap);
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
        $defaultFields['content'] = null;
        return $defaultFields;
    }

    private function prepareRecord(array $record, array $defaultFields): array
    {
        return isset($record['tax_type_id']) && $record['tax_type_id'] ? $record : array_merge($defaultFields, $record);
    }

    public function isNameExists(string $name):bool
    {
        $this->model->where('name', '=', $name);
        $this->model->limit(1);
        $results = $this->model->first();
        if(isset($results->tax_type_id) && $results->tax_type_id > 0){
            return true;
        }
        return false;
    }
}
