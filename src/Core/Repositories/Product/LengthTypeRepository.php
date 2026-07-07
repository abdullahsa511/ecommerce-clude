<?php

declare(strict_types=1);

namespace App\Core\Repositories\Product;

use App\Core\Models\Product\LengthType;
use App\Core\Models\Product\LengthTypeContent;
use App\Core\Repositories\Base\BaseRepository;
use PDO;
use App\Core\Models\Base\Model;
use App\Core\Validation\LengthTypeDataValidation;
use League\Csv\Reader;

// use App\Core\Repositories\ValidationCSVFileRepository;

class LengthTypeRepository extends BaseRepository implements LengthTypeRepositoryInterface
{
    private LengthTypeContent $lengthTypeContent;

    public function __construct(PDO $db, LengthTypeContent $lengthTypeContent)
    {
        parent::__construct($db, 'length_type', LengthType::class);
        $this->lengthTypeContent = $lengthTypeContent;
        $this->lengthTypeContent->setDb($db);
    }

    /**
     * Get all length types with content for a specific language
     * 
     * @param int $languageId Language ID
     * @param int $start Pagination start
     * @param int $limit Pagination limit
     * @return array{items: array, total: int}
     */
    public function getAll(?int $languageId = null, int $start = 0, int $limit = 10): array
    {
        $query = $this->model->with(['lengthTypeContent']);

        if ($languageId !== null) {
            $query->where('length_type_content.language_id', '=', $languageId);
        }

        // Add pagination
        if ($limit !== null) {
            $query->limit($limit);
        }
        if ($start !== null) {
            $query->offset($start);
        }

        // Get results
        $results = $query->findAll() ?? [];
        $total = $query->countAll();
        // $perPage = $limit ?? $this->model->limitValue;

        return $results;
        // return [
        //     'items' => collect($results),
        //     'total' => $total,
        //     "total_pages" => (int)ceil($total / $perPage),
        //     "current_page" => (int)($start / $perPage + 1),
        //     "per_page" => $perPage
        // ];
    }

    /**
     * Get a specific length type with content
     * 
     * @param int $lengthTypeId Length type ID
     * @param int|null $languageId Optional language ID
     * @return LengthType|null
     */
    public function get(int $lengthTypeId, ?int $languageId = null): ?LengthType
    {
        $query = $this->model;

        // Join with length_type_content
        $query->join(
            'length_type_content',
            'length_type.length_type_id',
            '=',
            'length_type_content.length_type_id',
            'LEFT'
        );

        // Add length_type_id filter
        $query->where('length_type.length_type_id', '=', $lengthTypeId);

        // Add language filter if provided
        if ($languageId !== null) {
            $query->where('length_type_content.language_id', '=', $languageId);
        }

        $result = $query->findAll();
        return !empty($result) ? $this->model->set($result[0]) : null;
    }

    public function findAll(): array //this->model = length_type
    {
        $results = $this->model->with(['lengthTypeContent'])->whereNull('length_type.deleted_at')->findAll();
        foreach ($results as &$result) {
            if (isset($result['length_type_content_data'])) {
                $result['length_type_content_data'] = json_decode($result['length_type_content_data'], true);
            }
        }

        return $results ?? [];
    }

    public function find(int $id): ?object
    {
        $result = $this->model->with(['lengthTypeContent'])->find($id);
        if ($result && isset($result->length_type_content_data)) {
            $result->length_type_content_data = json_decode($result->length_type_content_data, true);
        }

        return $result;
    }

    public function createLenthType(array $data): array
    {
        $response = [];
        try {
            $this->db->beginTransaction();
            $lengthType = $this->model->create([
                'value' => $data['value'],
                'code' => $data['code'] ?? null,
            ]);
            $data['length_type_id'] = $lengthType->length_type_id;
            // $unit = (string) ($data['unit'] ?? '');
            // if (mb_strlen($unit) > 4) {
            //     throw new \InvalidArgumentException(sprintf("Value for 'unit' is too long (%d). Maximum allowed is 4 characters.", mb_strlen($unit)));
            // }

            $lengthTypeContentCreated = $this->lengthTypeContent->create([
                'length_type_id' => $data['length_type_id'],
                'language_id' => $data['language_id'] ?? 1,
                'name' => $data['name'],
                'unit' => $data['unit'],
            ]);
            $response = (array) $lengthType->data;
            if ($lengthTypeContentCreated) {
                $lengthTypeContentdata = $this->lengthTypeContent->where('length_type_id', '=', $lengthType->length_type_id)
                    ->where('language_id', '=', $data['language_id'])->first();
                $response['lengthTypeContent'] = (array) $lengthTypeContentdata->data;
            }
            $this->db->commit();
            return $response;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update variants: " . $e->getMessage());
        }
    }


    public function updateLengthType(int $id, array $data): array
    {
        $this->db->beginTransaction();
        try {
            // Update length_type value
            $this->model->clearQuery();
            $lengthType = $this->model->where('length_type_id', '=', $id)->first();
            if (!$lengthType) {
                $this->db->rollBack();
                return [];
            }

            // Only update fields that are set
            $updateFields = [];
            if (isset($data['value'])) {
                $updateFields['value'] = $data['value'];
            }
            if (!empty($updateFields)) {
                $lengthType->clearQuery();
                $lengthType->update($updateFields);


                // content data 

                $languageId = $data['language_id'] ?? 1;

                // Find existing content row for this length_type_id and language_id
                $this->lengthTypeContent->clearQuery();
                $existingContent = $this->lengthTypeContent
                    ->where('length_type_id', '=', $id)
                    ->where('language_id', '=', $languageId)
                    ->first();

                $upsertContent = [
                    'length_type_id' => $id,
                    'language_id' => $languageId,
                    'name'         => $data['name'] ?? null,
                    'unit'         => $data['unit'] ?? null,
                ];

                if ($existingContent) {
                    // Update existing
                    $existingContent->clearQuery();
                    $existingContent->update($upsertContent);
                } else {
                    // Insert new
                    $this->lengthTypeContent->insert([$upsertContent]);
                }
            }
            // Retrieve the updated length type including content
            $updated = $this->model->with(['lengthTypeContent'])->find($id);
            if ($updated && isset($updated->length_type_content_data)) {
                $updated->length_type_content_data = json_decode($updated->length_type_content_data, true);
            }

            $this->db->commit();
            return $updated ? (array) $updated->data : [];
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update variants: " . $e->getMessage());
        }
    }

    public function deleteLengthType(int $lengthTypeId): ?LengthType
    {
        try {
            $this->db->beginTransaction();

            $this->model->clearQuery();
            $lengthType = $this->model->where('length_type_id', '=', $lengthTypeId)->first();
            if (!$lengthType) {
                return null;
            }
            $lengthType->update(['deleted_at' => date('Y-m-d H:i:s')]);

            $this->db->commit();
            return $lengthType;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to delete length type: " . $e->getMessage());
        }
    }

    // import data
    public function importCSVs(string $csv_file): array
    {
        $reader = Reader::createFromPath($csv_file, 'r');
        $reader->setHeaderOffset(0);
        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }
        $defaultFields = $this->getDefaultFields($headers);
        $records = $reader->getRecords();

        $validData = [
            'length_type' => [],
            'length_type_content' => [],
        ];
        $showFrontendValidData = ['length_type' => []];
        $existingData = [];
        $showFrontendExistingData = [];
        $invalid = [];
        $updated = [];
        $processed = [];
        $existingGroupMap = $this->model->select(['length_type_id', 'code'])->findAll(false);
        $existingGroupMap = array_column($existingGroupMap, 'length_type_id', 'code');
        $existingGroupIds = array_values($existingGroupMap);

        $existingDataMaps = [
            'lengthTypeMap' => $existingGroupMap,
            'lengthTypeIds' => $existingGroupIds,
        ];

        foreach ($records as $offset => $record) {
            try {
                // Merge defaults with record (ensures required fields exist)
                $record = $this->prepareRecord($record, $defaultFields);
                

               
                $validator = new LengthTypeDataValidation($record, $existingDataMaps);
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

                $unique = $validator->getLengthTypeUniqueIdentifier();

                // Skip if product has already been processed
                if (in_array($unique, $processed, true)) {
                    $updated[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'identifier' => $unique
                    ];
                    continue;
                }
                if ($validated->isExistingData) {
                    $existingData[] = (array) $validated->lengthType;
                    $showFrontendExistingData[] = $record;
                } else {
                    $validData['length_type'][] = (array) ['code' => $validated->lengthType->name, 'value' => $validated->lengthType->value]; // insert data 
                    $validData['length_type_content'][] = (array) ['name' => $validated->lengthType->name, 'unit' => $validated->lengthType->unit, 'language_id' => 1];



                    $contentData = (array) $validated->lengthType;

                    $showFrontendValidData['length_type'][] = $contentData;
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
        try {
            $this->db->beginTransaction();

            if (count($validData['length_type']) > 0) {
                $this->model->upsert($validData['length_type'], ['code']);
                $lengthTypeCodes = array_column($validData['length_type'], 'code');
                $this->model->clearQuery();
                $this->model->softDelete(false);

                $lengthTypeData = $this->model->whereIn('code', $lengthTypeCodes)->select(['length_type_id', 'code','value'])->findAll(false);
                $lengthTypeData = array_column($lengthTypeData, 'length_type_id', 'code');
            }

            if(count($validData['length_type_content']) > 0){
                foreach($validData['length_type_content'] as &$content){
                    $content['length_type_id'] = $lengthTypeData[$content['name']];
                    // unset($content['code']);
                }
                $this->lengthTypeContent->upsert($validData['length_type_content'], ['length_type_id', 'language_id']);
            }

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update length types: " . $e->getMessage());
        }
        return [
            'success' => true,
            'total_records' => iterator_count($records),
            'valid_records' => count($validData['length_type']),
            'valid_data' => $showFrontendValidData['length_type'],
            'invalid_records' => count($invalid),
            'updated_records' => count($showFrontendExistingData),
            'updated_data' => $showFrontendExistingData,
            'duplicated_records' => count($updated),
            'duplicated_data' => $updated,
            'lengthTypes' => [
                'inserted_count' => count($validData['length_type']),
                'valid_data' => $validData['length_type']
            ],
            'lengthTypes' => [
                'inserted_count' => count($showFrontendValidData['length_type']),
                'valid_data' => $showFrontendValidData['length_type']
            ],
            'invalid_data' => $invalid,

            'summary' => [
                'success_rate' => iterator_count($records) > 0
                    ? round((count($validData['length_type']) / iterator_count($records)) * 100, 2) . '%'
                    : '0%',
                'lengthType_processed' => count($validData['length_type']),
                'content_records_created' => $validData['length_type'],
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
        $defaultFields['language_id'] = 1;
        $defaultFields['length_type_id'] = null;

        return $defaultFields;
    }
    private function prepareRecord(array $record, array $defaultFields): array
    {
        return isset($record['length_type_id']) && $record['length_type_id'] ? $record : array_merge($defaultFields, $record);
    }

   
}
