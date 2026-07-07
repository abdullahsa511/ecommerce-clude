<?php

declare(strict_types=1);

namespace App\Core\Repositories\Type;

use PDO;
use App\Core\Models\Type\Type;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Validation\TypeDataValidation;
use League\Csv\Reader;

class TypeRepository extends BaseRepository implements TypeRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'type', Type::class);
    }

    public function getTypes(): array
    {
        $this->model->clearQuery();
        $data = $this->model
            ->select(['type_id', 'type', 'sort_order'])
            ->orderBy('type_id', 'DESC')
            ->whereNull('deleted_at')
            ->findAll(false);
        return $data;
    }

    public function getTypeById($id)
    {
        $this->model->clearQuery();
        $item = $this->model->where('type_id', '=', $id)->first();
        return $item ? (array) $item->data : [];
    }

    public function findTypeByName(string $name)
    {
        $this->model->clearQuery();
        return $this->model->where('type', '=', $name)
            ->select(['type_id as id', 'type'])
            ->first();
    }

    public function createType(array $data): array
    {
        $this->model->clearQuery();
        $payload = [
            'type' => $data['type'] ?? '',
            'sort_order' => $data['sort_order'] ?? 0,
        ];

        try {
            $this->db->beginTransaction();
            $obj = $this->model->create($payload);
            $insertedId = $obj->type_id ?? null;
            $result = (array)$obj->data;
            $this->db->commit();

            return (array) $this->getTypeById($insertedId);
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to create type: " . $e->getMessage());
        }
    }

    public function updateType(array $data, $id): array
    {
        $this->model->clearQuery();
        $item = $this->model->where('type_id', '=', $id)->first();
        if (!$item) return [];

        try {
            $this->db->beginTransaction();
            $item->clearQuery();
            $item->update($data);
            $this->db->commit();
            return $this->getTypeById($id);
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to update type: " . $e->getMessage());
        }
    }

    public function deleteType(int $type_id): bool
    {
        try {
            $this->db->beginTransaction();
            $this->model->clearQuery();
            $type = $this->model->where('type_id', '=', $type_id)->first();
            if ($type) {
                $type->update(['deleted_at' => date('Y-m-d H:i:s')]);
            }
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to delete type: " . $e->getMessage());
        }
    }

    /**
     * importTypes (CSV)
     * Very similar to importOptions but simplified because no content table.
     */
    public function importTypes(string $csv_file): array
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

        $showFrontendValidData = [];
        $existingData = [];
        $showFrontendExistingData = [];
        $invalid = [];
        $updated = [];
        $processed = [];
        $existingMap = $this->model->select(['type_id', 'type', 'sort_order'])->findAll(false);
        $existingMap = array_column($existingMap, 'type_id', 'type');
        $existingGroupIds = array_values($existingMap);

        $existingDataMaps = [
            'typeContentMap' => $existingMap,
            'typeIds' => $existingGroupIds,

        ];

        foreach ($records as $offset => $record) {
            try {
                $record = $this->prepareRecord($record, $defaultFields);
                $validator = new TypeDataValidation($record, $existingDataMaps);

                $validated = $validator->validate();
                if ($validated === false) {
                    $invalid[] = [
                        'row' => $offset + 2, // +2 because CSV row count starts at 1 and includes header
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
                    $existingData[] = (array) $validated->type;
                    $showFrontendExistingData[] = $record;
                } else {
                    $validData[] = (array) $validated->type;
                    $contentData = (array) $validated->type;
                    $showFrontendValidData[] = $contentData;
                    // unset($validData['type_id']); 
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
            // if (count($existingData) > 0) {
            //     $this->model->upsert($existingData, ['type_id']);
            // }
            if (count($validData) > 0) {
                $this->model->insert($validData);
            }
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update types: " . $e->getMessage());
        }

        return [
            'success' => true,
            'total_records' => iterator_count($records),
            'valid_records' => count($showFrontendValidData),
            'valid_data' => $showFrontendValidData,
            'invalid_records' => count($invalid),
            'updated_records' => count($showFrontendExistingData),
            'updated_data' => $showFrontendExistingData,
            'duplicated_records' => count($updated),
            'duplicated_data' => $updated,
            'types' => [
                'inserted_count' => count($validData),
                'valid_data' => $validData
            ],

            'invalid_data' => $invalid,

            'summary' => [
                'success_rate' => iterator_count($records) > 0
                    ? round((count($validData) / iterator_count($records)) * 100, 2) . '%'
                    : '0%',
                'type_processed' => count($validData),
                'content_records_created' => $validData,
                'errors' => count($invalid),
            ],
            // 'language_map' => array_flip($languageMap)
        ];
    }

    private function getDefaultFields(array $headers): array
    {
        $defaultFields = [];
        foreach ($headers as $header) {
            $defaultFields[$header] = null;
        }
        $defaultFields['sort_order'] = 1;
        return $defaultFields;
    }

    private function prepareRecord(array $record, array $defaultFields): array
    {
        return isset($record['type_id']) && $record['type_id'] ? $record : array_merge($defaultFields, $record);
    }
}
