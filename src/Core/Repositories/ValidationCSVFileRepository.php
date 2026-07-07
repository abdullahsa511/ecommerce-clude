<?php

declare(strict_types=1);

namespace App\Core\Repositories;

use App\Core\Repositories\Base\BaseRepository;
use App\Core\Validation\StatusDataValidation;
use League\Csv\Reader;
use PDO;

class ValidationCSVFileRepository extends BaseRepository
{
    public function __construct(PDO $db, string $table, string $entityClass)
    {
        parent::__construct($db, $table, $entityClass);
    }
    public function importStatuses(string $csv_file, $primaryKey): array
    {
        $query = $this->model;
        $reader = Reader::createFromPath($csv_file, 'r');
        $reader->setHeaderOffset(0);
        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }
        $defaultFields = $this->getDefaultFields($headers, $primaryKey);
        $fields = array_keys($defaultFields);
        $records = $reader->getRecords();

        $validData = [];
        $existingData = [];
        $invalid = [];
        $updated = [];
        $processed = [];
        $existingStatusesMap = $this->query->select([$primaryKey, 'name'])->findAll(false);
        $existingStatusesMap = array_column($existingStatusesMap, $primaryKey, 'name');
        $existingStatusesIds = array_values($existingStatusesMap);
        $languageMap = $this->language->select(['language_id', 'code'])->findAll(false);
        $languageMap = array_column($languageMap, 'language_id', 'code');
        $existingDataMaps = [
            'statusesMap' => $existingStatusesMap,
            'statusesIds' => $existingStatusesIds,
            'languageMap' => $languageMap,
        ];

        foreach ($records as $offset => $record) {
            try {
                // Merge defaults with record (ensures required fields exist)
                $record = $this->prepareRecord($record, $defaultFields);
                // $record['language_id'] = 1;
                $validator = new StatusDataValidation($record, ['name'], $fields, $primaryKey, $existingDataMaps);
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

                // Skip if product has already been processed
                if (in_array($unique, $processed, true)) {
                    $updated[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'identifier' => $unique
                    ];
                    continue;
                }
                $status = $validated->toArray();
                if($validated->isExistingData){
                    $existingData[] = $status;
                }else{
                    $validData[] = $status;
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

        // $result = $this->attributeGroupAndContentInsertorUpdate($validData, $languageMap);
        try{
            $this->db->beginTransaction();
            if(count($existingData) > 0){
                $query->upsert($existingData, [$primaryKey, 'language_id']);
            }
            if(count($validData) > 0){
                $this->model->upsert($validData, ['name', 'language_id']);
                $this->model->clearQuery();
                $this->model->softDelete(false);
            }
            $this->db->commit();
        }catch(\Exception $e){
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update attribute groups: " . $e->getMessage());
        }

        return [
            'success' => true,
            'total_records' => iterator_count($records),
            'valid_records' => count($validData),
            'valid_data' => $validData,
            'invalid_records' => count($invalid),
            'updated_records' => count($existingData),
            'updated_data' => $existingData,
            'duplicated_records' => count($updated),
            'duplicated_data' => $updated,
            'statuses' => [
                'inserted_count' => count($validData),
                'valid_data' => $validData
            ],
            'invalid_data' => $invalid,
            
            'summary' => [
                'success_rate' => iterator_count($records) > 0
                    ? round((count($validData) / iterator_count($records)) * 100, 2) . '%'
                    : '0%',
                'status_processed' => count($validData),
                'status_records_created' => $validData,
                'errors' => count($invalid),
            ],
            'language_map' => array_flip($languageMap)
        ];
    }

    private function getDefaultFields(array $headers, $primaryKey): array
    {
        $defaultFields = [];
        // Initialize all CSV headers as null by default
        foreach ($headers as $header) {
            $defaultFields[$header] = null;
        }

        // Set default values for required fields
        $defaultFields['language_id'] = 1;
        $defaultFields[$primaryKey] = null;

        return $defaultFields;
    }
}