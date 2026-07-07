<?php

declare(strict_types=1);

namespace App\Core\Repositories\Product;

use App\Core\Models\Product\WeightType;
use App\Core\Models\Product\WeightTypeContent;
use App\Core\Repositories\Base\BaseRepository;
use PDO;
use App\Core\Models\Base\Model;
use App\Core\Validation\WeightTypeDataValidation;
use League\Csv\Reader;

class WeightTypeRepository extends BaseRepository implements WeightTypeRepositoryInterface
{
    private WeightTypeContent $weightTypeContent;

    public function __construct(PDO $db, WeightTypeContent $weightTypeContent)
    {
        parent::__construct($db, 'weight_type', WeightType::class);
        $this->weightTypeContent = $weightTypeContent;
        $this->weightTypeContent->setDb($db);
    }

    /**
     * Get all weight types with content for a specific language
     * 
     * @param int $languageId Language ID
     * @param int $start Pagination start
     * @param int $limit Pagination limit
     * @return array{items: array, total: int}
     */
    public function getAll(?int $languageId = null, int $start = 0, int $limit = 10): array
    {
        $query = $this->model->with(['weightTypeContent']);

        if ($languageId !== null) {
            $query->where('weight_type_content.language_id', '=', $languageId);
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
     * Get a specific weight type with content
     * 
     * @param int $weightTypeId Weight type ID
     * @param int|null $languageId Optional language ID
     * @return WeightType|null
     */
    public function get(int $weightTypeId, ?int $languageId = null): ?WeightType
    {
        $query = $this->model;

        // Join with weight_type_content
        $query->join(
            'weight_type_content',
            'weight_type.weight_type_id',
            '=',
            'weight_type_content.weight_type_id',
            'LEFT'
        );

        // Add weight_type_id filter
        $query->where('weight_type.weight_type_id', '=', $weightTypeId);

        // Add language filter if provided
        if ($languageId !== null) {
            $query->where('weight_type_content.language_id', '=', $languageId);
        }

        $result = $query->findAll();
        return !empty($result) ? $this->model->set($result[0]) : null;
    }

    public function findAll(): array //this->model = weight_type
    {
        $results = $this->model->with(['weightTypeContent'])->whereNull('weight_type.deleted_at')->findAll();
        foreach ($results as &$result) {
            if (isset($result['weight_type_content_data'])) {
                $result['weight_type_content_data'] = json_decode($result['weight_type_content_data'], true);
            }
        }

        return $results ?? [];
    }

    public function find(int $id): ?object
    {
        $result = $this->model->with(['weightTypeContent'])->find($id);
        if ($result && isset($result->weight_type_content_data)) {
            $result->weight_type_content_data = json_decode($result->weight_type_content_data, true);
        }

        return $result;
    }

    public function createWeightType(array $data): array
    {
        $response = [];
        try {
            $this->db->beginTransaction();
            $weightType = $this->model->create([
                'value' => $data['value'],
                'code' => $data['code'] ?? null,
            ]);
            $data['weight_type_id'] = $weightType->weight_type_id;
            // $unit = (string) ($data['unit'] ?? '');
            // if (mb_strlen($unit) > 4) {
            //     throw new \InvalidArgumentException(sprintf("Value for 'unit' is too long (%d). Maximum allowed is 4 characters.", mb_strlen($unit)));
            // }

            $weightTypeContentCreated = $this->weightTypeContent->create([
                'weight_type_id' => $data['weight_type_id'],
                'language_id' => $data['language_id'] ?? 1,
                'name' => $data['name'],
                'unit' => $data['unit'],
            ]);
            $response = (array) $weightType->data;
            if ($weightTypeContentCreated) {
                $weightTypeContentdata = $this->weightTypeContent->where('weight_type_id', '=', $weightType->weight_type_id)
                    ->where('language_id', '=', $data['language_id'])->first();
                $response['weightTypeContent'] = (array) $weightTypeContentdata->data;
            }
            $this->db->commit();
            return $response;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update variants: " . $e->getMessage());
        }
    }


    public function updateWeightType(int $id, array $data): array
    {
        $this->db->beginTransaction();
        try {
            // Update weight_type value
            $this->model->clearQuery();
            $weightType = $this->model->where('weight_type_id', '=', $id)->first();
            if (!$weightType) {
                $this->db->rollBack();
                return [];
            }

            // Only update fields that are set
            $updateFields = [];
            if (isset($data['value'])) {
                $updateFields['value'] = $data['value'];
            }
            if (!empty($updateFields)) {
                $weightType->clearQuery();
                $weightType->update($updateFields);


                // content data 

                $languageId = $data['language_id'] ?? 1;

                // Find existing content row for this weight_type_id and language_id
                $this->weightTypeContent->clearQuery();
                $existingContent = $this->weightTypeContent
                    ->where('weight_type_id', '=', $id)
                    ->where('language_id', '=', $languageId)
                    ->first();

                $upsertContent = [
                    'weight_type_id' => $id,
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
                    $this->weightTypeContent->insert([$upsertContent]);
                }
            }
            // Retrieve the updated weight type including content
            $updated = $this->model->with(['weightTypeContent'])->find($id);
            if ($updated && isset($updated->weight_type_content_data)) {
                $updated->weight_type_content_data = json_decode($updated->weight_type_content_data, true);
            }

            $this->db->commit();
            return $updated ? (array) $updated->data : [];
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update variants: " . $e->getMessage());
        }
    }

    public function deleteWeightType(int $weightTypeId): ?WeightType
    {
        try {
            $this->db->beginTransaction();

            $this->model->clearQuery();
            $weightType = $this->model->where('weight_type_id', '=', $weightTypeId)->first();
            if (!$weightType) {
                return null;
            }
            $weightType->update(['deleted_at' => date('Y-m-d H:i:s')]);

            $this->db->commit();
            return $weightType;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to delete weight type: " . $e->getMessage());
        }
    }

    // weight type import 
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
            'weight_type' => [],
            'weight_type_content' => [],
        ];
        $showFrontendValidData = ['weight_type' => []];
        $existingData = [];
        $showFrontendExistingData = [];
        $invalid = [];
        $updated = [];
        $processed = [];
        $existingGroupMap = $this->model->select(['weight_type_id', 'code'])->findAll(false);
        $existingGroupMap = array_column($existingGroupMap, 'weight_type_id', 'code');
        $existingGroupIds = array_values($existingGroupMap);

        $existingDataMaps = [
            'weightTypeMap' => $existingGroupMap,
            'weightTypeIds' => $existingGroupIds,
        ];

        foreach ($records as $offset => $record) {
            try {
                // Merge defaults with record (ensures required fields exist)
                $record = $this->prepareRecord($record, $defaultFields);
                

               
                $validator = new WeightTypeDataValidation($record, $existingDataMaps);
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

                $unique = $validator->getWeightTypeUniqueIdentifier();

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
                    $existingData[] = (array) $validated->weightType;
                    $showFrontendExistingData[] = $record;
                } else {
                    $validData['weight_type'][] = (array) ['code' => $validated->weightType->name, 'value' => $validated->weightType->value]; // insert data 
                    $validData['weight_type_content'][] = (array) ['name' => $validated->weightType->name, 'unit' => $validated->weightType->unit, 'language_id' => 1];



                    $contentData = (array) $validated->weightType;

                    $showFrontendValidData['weight_type'][] = $contentData;
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

            if (count($validData['weight_type']) > 0) {
                $this->model->upsert($validData['weight_type'], ['code']);
                $weightTypeCodes = array_column($validData['weight_type'], 'code');
                $this->model->clearQuery();
                $this->model->softDelete(false);

                $weightTypeData = $this->model->whereIn('code', $weightTypeCodes)->select(['weight_type_id', 'code','value'])->findAll(false);
                $weightTypeData = array_column($weightTypeData, 'weight_type_id', 'code');
            }

            if(count($validData['weight_type_content']) > 0){
                foreach($validData['weight_type_content'] as &$content){
                    $content['weight_type_id'] = $weightTypeData[$content['name']];
                    // unset($content['code']);
                }
                $this->weightTypeContent->upsert($validData['weight_type_content'], ['weight_type_id', 'language_id']);
            }

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update weight types: " . $e->getMessage());
        }
        return [
            'success' => true,
            'total_records' => iterator_count($records),
            'valid_records' => count($validData['weight_type']),
            'valid_data' => $showFrontendValidData['weight_type'],
            'invalid_records' => count($invalid),
            'updated_records' => count($showFrontendExistingData),
            'updated_data' => $showFrontendExistingData,
            'duplicated_records' => count($updated),
            'duplicated_data' => $updated,
            'weightTypes' => [
                'inserted_count' => count($validData['weight_type']),
                'valid_data' => $validData['weight_type']
            ],
            'weightTypes' => [
                'inserted_count' => count($showFrontendValidData['weight_type']),
                'valid_data' => $showFrontendValidData['weight_type']
            ],
            'invalid_data' => $invalid,

            'summary' => [
                'success_rate' => iterator_count($records) > 0
                    ? round((count($validData['weight_type']) / iterator_count($records)) * 100, 2) . '%'
                    : '0%',
                'weightType_processed' => count($validData['weight_type']),
                'content_records_created' => $validData['weight_type'],
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
        $defaultFields['weight_type_id'] = null;

        return $defaultFields;
    }
    private function prepareRecord(array $record, array $defaultFields): array
    {
        return isset($record['weight_type_id']) && $record['weight_type_id'] ? $record : array_merge($defaultFields, $record);
    }


    

    // import data
    // public function importCSVs(string $csv_file, $formType): array
    // {
    //     $reader = Reader::createFromPath($csv_file, 'r');
    //     $reader->setHeaderOffset(0);
    //     $headers = $reader->getHeader();
    //     if (empty($headers)) {
    //         throw new Exception("CSV file has no headers");
    //     }
    //     $defaultFields = $this->getDefaultFields($headers);
    //     $records = $reader->getRecords();

    //     $validData = [
    //         'post_tag_data' => [],
    //     ];
    //     $showFrontendValidData = ['post_tag_data' => []];
    //     $existingData = [];
    //     $showFrontendExistingData = [];
    //     $invalid = [];
    //     $updated = [];
    //     $processed = [];
    //     $existingGroupMap = $this->model->select(['post_tag_id', 'name', 'slug', 'description', 'status', 'image', 'post_id'])->findAll(false);
    //     $existingGroupMap = array_column($existingGroupMap, 'post_tag_id', 'name');
    //     $existingGroupIds = array_values($existingGroupMap);

    //     $existingDataMaps = [
    //         'postTagMap' => $existingGroupMap,
    //         'postTagIds' => $existingGroupIds,
    //     ];

    //     foreach ($records as $offset => $record) {
    //         try {
    //             // Merge defaults with record (ensures required fields exist)
    //             $record = $this->prepareRecord($record, $defaultFields);
    //             if (empty($record['slug'])) {
    //                 $record['slug'] = $this->createSlug($record['name']);
    //                 $record['status'] = 1;
    //             }

    //             $mediaPaths = [
    //                 'image_path' => '/media/post-tag/image/',
    //             ];
    //             $validator = new PostTagDataValidation($record, $mediaPaths, $existingDataMaps);
    //             $validated = $validator->validate();

    //             // If validation fails, store record and error info in $invalid
    //             if ($validated === false) {
    //                 $invalid[] = [
    //                     'row' => $offset + 2, // +2 because CSV row count starts at 1 and includes header
    //                     'data' => $record,
    //                     'errors' => $validator->getErrors()
    //                 ];
    //                 continue;
    //             }

    //             $unique = $validator->getPostTagUniqueIdentifier();

    //             // Skip if product has already been processed
    //             if (in_array($unique, $processed, true)) {
    //                 $updated[] = [
    //                     'row' => $offset + 2,
    //                     'data' => $record,
    //                     'identifier' => $unique
    //                 ];
    //                 continue;
    //             }
    //             if ($validated->isExistingData) {
    //                 $existingData[] = (array) $validated->post_tag;
    //                 $showFrontendExistingData[] = $record;
    //             } else {
    //                 $validData['post_tag_data'][] = (array) $validated->post_tag;
    //                 $contentData = (array) $validated->post_tag;
    //                 $showFrontendValidData['post_tag_data'][] = $contentData;
    //             }
    //             $processed[] = $unique;
    //         } catch (Exception $e) {
    //             // Capture any runtime exception per record
    //             $invalid[] = [
    //                 'row' => $offset + 2,
    //                 'data' => $record,
    //                 'errors' => ['processing_error' => $e->getMessage()]
    //             ];
    //             continue;
    //         }
    //     }

    //     // $result = $this->attributeGroupAndContentInsertorUpdate($validData, $languageMap);
    //     try {
    //         $this->db->beginTransaction();
    //         if (count($existingData) > 0) {
    //             $this->model->upsert($existingData, ['post_tag_id']);
    //         }
    //         if (count($validData['post_tag_data']) > 0) {
    //             $this->model->upsert($validData['post_tag_data'], ['name']);
    //             $postTagNames = array_column($validData['post_tag_data'], 'name');
    //             $this->model->clearQuery();
    //             $this->model->softDelete(false);

    //             // next day 
    //             $postTagData = $this->model->whereIn('name', $postTagNames)->select(['post_tag_id', 'name', 'description', 'image', 'post_id'])->findAll(false);
    //             $postTagData = array_column($postTagData, 'post_tag_id', 'name');
    //         }
    //         if (count($validData['post_tag_data']) > 0) {
    //             foreach ($validData['post_tag_data'] as &$content) {
    //                 $content['post_tag_id'] = $postTagData[$content['name']];
    //                 // unset($content['code']);
    //             }
    //             $this->model->upsert($validData['post_tag_data'], ['name', 'post_id']);
    //         }
    //         $this->db->commit();
    //     } catch (\Exception $e) {
    //         $this->db->rollBack();
    //         throw new \Exception("Failed to insert/update post tag groups: " . $e->getMessage());
    //     }

    //     return [
    //         'success' => true,
    //         'total_records' => iterator_count($records),
    //         'valid_records' => count($validData['post_tag_data']),
    //         'valid_data' => $showFrontendValidData['post_tag_data'],
    //         'invalid_records' => count($invalid),
    //         'updated_records' => count($showFrontendExistingData),
    //         'updated_data' => $showFrontendExistingData,
    //         'duplicated_records' => count($updated),
    //         'duplicated_data' => $updated,
    //         'postTags' => [
    //             'inserted_count' => count($validData['post_tag_data']),
    //             'valid_data' => $validData['post_tag_data']
    //         ],
    //         'post_tags' => [
    //             'inserted_count' => count($showFrontendValidData['post_tag_data']),
    //             'valid_data' => $showFrontendValidData['post_tag_data']
    //         ],
    //         'invalid_data' => $invalid,

    //         'summary' => [
    //             'success_rate' => iterator_count($records) > 0
    //                 ? round((count($validData['post_tag_data']) / iterator_count($records)) * 100, 2) . '%'
    //                 : '0%',
    //             'posttag_processed' => count($validData['post_tag_data']),
    //             'content_records_created' => $validData['post_tag_data'],
    //             'errors' => count($invalid),
    //         ],

    //     ];
    // }

    // private function getDefaultFields(array $headers): array
    // {
    //     $defaultFields = [];
    //     // Initialize all CSV headers as null by default
    //     foreach ($headers as $header) {
    //         $defaultFields[$header] = null;
    //     }

    //     // Set default values for required fields
    //     $defaultFields['language_id'] = 1;
    //     $defaultFields['post_tag_id'] = null;

    //     return $defaultFields;
    // }
    // private function prepareRecord(array $record, array $defaultFields): array
    // {
    //     return isset($record['post_tag_id']) && $record['post_tag_id'] ? $record : array_merge($defaultFields, $record);
    // }

    // private function createSlug($string)
    // {
    //     // Convert to lowercase
    //     $slug = strtolower($string);

    //     // Remove any non-alphanumeric characters except spaces
    //     $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);

    //     // Replace multiple spaces or hyphens with a single hyphen
    //     $slug = preg_replace('/[\s-]+/', '-', $slug);

    //     // Trim hyphens from the beginning and end
    //     $slug = trim($slug, '-');

    //     return $slug;
    // }
}
