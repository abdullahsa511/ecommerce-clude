<?php

declare(strict_types=1);

namespace App\Core\Repositories\Option;

use App\Core\Http\Response;
use App\Core\Models\Base\Model;
use App\Core\Models\Localisation\Language;
use App\Core\Models\Option\Option;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Models\Option\OptionContent;
use App\Core\Models\Type\Type;
use App\Core\Validation\OptionDataValidation;
use League\Csv\Reader;
use PDO;

class OptionRepository extends BaseRepository implements OptionRepositoryInterface
{
    protected Model $model;
    protected PDO $db;
    private Type $type;
    private Language $language;
    private OptionContent $optionContent;
    // private OptionGroup $optionGroup;
    // private OptionGroupContent $optionGroupContent;

    public function __construct(
        PDO $db,
        OptionContent $optionContent,
        Type $type,
        Language $language,
        // OptionGroup $optionGroup,
        // OptionGroupContent $optionGroupContent
    ) {
        $this->db = $db;
        $this->model = new Option();
        $this->model->setDb($db);

        $this->type = $type;
        $this->type->setDb($db);

        $this->optionContent = $optionContent;
        $this->optionContent->setDb($db);

        $this->language = $language;
        $this->language->setDb($db);

        // $this->optionGroup = $optionGroup;
        // $this->optionGroup->setDb($db);

        // $this->optionGroupContent = $optionGroupContent;
        // $this->optionGroupContent->setDb($db);
    }


    /**
     * {@inheritDoc}
     */
    public function getAll(int $language_id, int $start = 0, int $limit = 10): array
    {
        $this->model->select([
            'o.option_id',
            'o.sort_order',
            'o.status'
        ])
            ->with(['optionDescription' => function ($query) use ($language_id) {
                $query->where('language_id', '=', $language_id);
            }])
            ->limit($limit)
            ->offset($start);

        return [
            'data' => $this->model->findAll(),
            'total' => $this->model->countAll()
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function get(int $option_id, int $language_id): ?Option
    {
        /** @var Option|null */
        return $this->model->select([
            'o.option_id',
            'o.sort_order',
            'o.status'
        ])
            ->with(['optionDescription' => function ($query) use ($language_id) {
                $query->where('language_id', '=', $language_id);
            }])
            ->find($option_id);
    }

    /**
     * {@inheritDoc}
     */
    public function add(array $option): int
    {
        // First insert the main option data
        $optionData = [
            'sort_order' => $option['sort_order'] ?? 0,
            'status' => $option['status'] ?? 1
        ];

        $newOption = $this->model->create($optionData);
        $option_id = $newOption->getId();

        // Then insert the option descriptions
        if (isset($option['option_description'])) {
            $descriptions = [];
            foreach ($option['option_description'] as $language_id => $description) {
                $descriptions[] = [
                    'option_id' => $option_id,
                    'language_id' => $language_id,
                    'name' => $description['name'],
                    'value' => $description['value'] ?? ''
                ];
            }

            // Insert all descriptions at once
            $this->db->beginTransaction();
            try {
                $stmt = $this->db->prepare(
                    "INSERT INTO option_description (option_id, language_id, name, value) 
                     VALUES (:option_id, :language_id, :name, :value)"
                );

                foreach ($descriptions as $desc) {
                    $stmt->execute($desc);
                }

                $this->db->commit();
            } catch (\Exception $e) {
                $this->db->rollBack();
                throw $e;
            }
        }

        return $option_id;
    }

    /**
     * {@inheritDoc}
     */
    public function edit(int $option_id, array $option): bool
    {
        // Update main option data
        $optionData = [
            'sort_order' => $option['sort_order'] ?? 0,
            'status' => $option['status'] ?? 1
        ];

        $success = $this->model->update($optionData) !== null;

        // Update option descriptions
        if (isset($option['option_description']) && $success) {
            $this->db->beginTransaction();
            try {
                // Delete existing descriptions
                $stmt = $this->db->prepare(
                    "DELETE FROM option_description WHERE option_id = ?"
                );
                $stmt->execute([$option_id]);

                // Insert new descriptions
                $stmt = $this->db->prepare(
                    "INSERT INTO option_description (option_id, language_id, name, value) 
                     VALUES (:option_id, :language_id, :name, :value)"
                );

                foreach ($option['option_description'] as $language_id => $description) {
                    $stmt->execute([
                        'option_id' => $option_id,
                        'language_id' => $language_id,
                        'name' => $description['name'],
                        'value' => $description['value'] ?? ''
                    ]);
                }

                $this->db->commit();
                return true;
            } catch (\Exception $e) {
                $this->db->rollBack();
                throw $e;
            }
        }

        return $success;
    }

    /** -----------------------------------
     * option related all transcation
     * 
     * ------------------------------------
     */
    public function getAllOptions(): array
    {
        $query = $this->optionContent
            ->select([
                'option.option_id',
                'option.type_id',
                'option.type',
                'option.sort_order',
                'option_content.name',
                'option_content.language_id',
                'language.name as language_name',
                'language.code',
            ])
            ->Join('option', 'option_content.option_id', '=', 'option.option_id')
            ->Join('language', 'option_content.language_id', '=', 'language.language_id')
            // ->groupBy('option_content.option')
            ->whereNotNull('option.option_id')
            ->whereNull('option.deleted_at')
            ->orderBy('option.option_id', 'DESC');

        // 🔹 Execute query
        $options = $query->findAll(false);
        $optionData = array_map(function ($item) {
            // Return formatted structure
            return [
                'option_id' => (int)($item['option_id'] ?? 0),
                'type_id' => (int)($item['type_id'] ?? 0),
                'type' => ($item['type'] ?? 0),
                'sort_order' => (int)($item['sort_order'] ?? 0),
                'content' => [
                    'option_id' => $item['option_id'] ?? null,
                    'name' => $item['name'] ?? null,
                    'language_name' => $item['language_name'] ?? '',
                    'language_code' => $item['code'] ?? '',
                ],
            ];
        }, $options);
        return $optionData;
    }

    // attribute group 
    public function getAllOptionTypes(): array
    {
        // $this->type->clearQuery();
        $data = $this->type->select(['type_id as id', 'type as name'])->findAll();
        return $data;
    }

    public function getOptionById($id)
    {
        // $query = $this->optionContent
        //     ->select([
        //         '`option`.option_id',
        //         '`option`.type_id',
        //         '`option`.`type`',
        //         '`option`.`sort_order`',
        //         '`option_content`.name',
        //         'option_content.language_id',
        //     ])
        //     ->join('`option`', 'option_content.option_id', '=', '`option`.option_id')
        //     ->where('option_content.option_id', '=', $id)
        //     ->orderBy('`option`.option_id', 'DESC')
        //     ->first();
        $option = $this->model->where('option_id', '=', $id)->with(['content'])->first();
        if ($option && $option->data->content) {
            $option->data->content = (array) json_decode($option->data->content);
            $option->data->content = array_find(
                $option->data->content,
                fn($item) => isset($item->language_id) && $item->language_id == 1
            );
        }

        // $item = $this->model->where('option_id', '=', $id)->first();
        // if (!$item) {
        //     return [];
        // }
        // $content = $this->optionContent->where('option_id', '=', $id)->first();
        // $item->content = $content->data;
        return $option->data;
    }

    public function findByName(string $name): ?Option
    {
        return $this->model
            ->Join('option_content', 'option.option_id', '=', 'option_content.option_id')
            ->where('option_content.name', '=', $name)
            ->select(['option_content.option_id as id', 'option_content.name', 'option.sort_order'])
            ->first();
    }

    public function findByCode(string $code, ?int $id = null): ?Option
    {
        $query = $this->model
            ->join('option_content', 'option.option_id', '=', 'option_content.option_id')
            ->where('`option`.code', '=', $code);

        if ($id !== null) {
            $query->whereNotIn('`option`.option_id', [(int)$id]); // cast to int here
        }

        return $query
            ->select(['option_content.option_id as id', 'option_content.name', '`option`.sort_order'])
            ->first();
    }

    public function createOptions($data): array
    {
        $response = [];
        $code = str_replace(' ', '-', strtolower($data['content']['name']));
        $option = [
            'type_id' => $data['type_id'] ?? 1,
            'type' => $data['type'] ?? 1,
            'sort_order' => $data['sort_order'] ?? 1,
            'code' =>  $code,
        ];
        $languageId = $data['content']['language_id'] ?? 1;
        $optionContent = [
            'name' => $data['content']['name'] ?? 1,
            'language_id' => $data['content']['language_id'] ?? 1,
        ];

        try {
            $this->db->beginTransaction();
            $this->model->clearQuery();
            $optionObj = $this->model->create($option);
            $optionId = $data['option_id'] = $optionObj->option_id ?? null;
            $response = (array) $optionObj->data;

            if (isset($data['content']) && $optionId) {
                $optionContent['option_id'] = (int) $optionId;
                $this->optionContent->clearQuery();
                $this->optionContent->insert([$optionContent]);
                // $this->optionContent->clearQuery();
                $content = $this->optionContent->where('option_id', '=', $optionId)->where('language_id', '=', $languageId)->first();
                $response['content'] = (array) $content->data;
            }

            $this->db->commit();
            return (array) $response;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update options: " . $e->getMessage());
        }
    }
    /**
     * update options
     * 
     * @param array $data ['option' => [], 'content' => []]
     * @param int $id option_id
     * @return array
     */

    public function updateOptions($data, $id): array
    {
        $this->model->clearQuery();
        $option = $this->model->where('option_id', '=', $id)->first();
        $content = $this->optionContent->where('option_id', '=', $id)->first();


        try {
            $this->db->beginTransaction();

            if (!$content || !$option) {
                return [];
            }

            if (!empty($data['option'])) {
                $option->clearQuery();
                $option->update($data['option']);
            }

            if (!empty($data['content'])) {
                $content->clearQuery();
                $content->upsert([$data['content']], ['option_id', 'language_id']);
            }

            $this->db->commit();
            return (array) $this->getOptionById($id);
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update options: " . $e->getMessage());
        }
    }

    // delete option
    public function deleteOptions(int $option_id): bool
    {
        try {
            $this->db->beginTransaction();

            $this->model->clearQuery();
            $option = $this->model->where('option_id', '=', $option_id)->first();
            if ($option) {
                $option->update(['deleted_at' => date('Y-m-d H:i:s')]);
            }
            // if ($option) {
            //     $this->optionContent->clearQuery();
            //     $optionContent = $this->optionContent->where('option_id', '=', $option_id)->first();
            //     if ($optionContent) {
            //         $optionContent->update(['deleted_at' => date('Y-m-d H:i:s')]);
            //     } else {
            //         throw new Exception("Error: option content something wrong", 1);
            //     }
            // }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to delete options: " . $e->getMessage());
        }
    }

    public function deleteMultipleOptions(array $option_ids): bool
    {
        try {
            $this->db->beginTransaction();

            $options = $this->model->select(['option_id'])->whereIn('option_id', $option_ids)->findAll();
            $optionIds = array_column($options, 'option_id');
            if ($optionIds) {
                $this->model->deleteMultiple($optionIds);
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to delete options: " . $e->getMessage());
        }
    }

    // import data
    public function importOptions(string $csv_file): array
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
            'option_data' => [],
            'option_content_data' => [],
        ];
        $existingData = [];
        $showFrontendExistingData = [];
        $showFrontendValidData = ['option_content_data' => []];
        $invalid = [];
        $updated = [];
        $processed = [];
        $existingGroupMap = $this->model->select(['option_id', 'code'])->findAll(false);
        $existingGroupMap = array_column($existingGroupMap, 'option_id', 'code');
        $existingGroupIds = array_values($existingGroupMap);
        $languageMap = $this->language->select(['language_id', 'code'])->findAll(false);
        $languageMap = array_column($languageMap, 'language_id', 'code');
        // type map
        $typeMap = $this->type->select(['type_id', 'type'])->findAll(false);
        $typeMap = array_column($typeMap, 'type_id', 'type');
        $existingDataMaps = [
            'optionContentMap' => $existingGroupMap,
            'optionIds' => $existingGroupIds,
            'languageMap' => $languageMap,
            'typeMap' => $typeMap,
        ];

        foreach ($records as $offset => $record) {
            try {
                // Merge defaults with record (ensures required fields exist)
                $record = $this->prepareRecord($record, $defaultFields);
                // $record['language_id'] = 1;
                $validator = new OptionDataValidation($record, $existingDataMaps);
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
                if ($validated->isExistingData) {
                    $existingData[] = (array) $validated->option_content;
                    $showFrontendExistingData[] = $record;
                } else {
                    $validData['option_data'][] = (array) $validated->option;
                    $validData['option_content_data'][] = (array) $validated->option_content;
                    // show frontend 
                    $contentData = (array) $validated->option_content;
                    $contentData['type'] = $record['type'];
                    $contentData['language_code'] = $record['language_code'];
                    $contentData['sort_order'] = $record['sort_order'];
                    $showFrontendValidData['option_content_data'][] = $contentData;
                    // end
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

        // $result = $this->optionAndContentInsertorUpdate($validData, $languageMap);
        try {
            $this->db->beginTransaction();
            if (count($existingData) > 0) {
                $this->optionContent->upsert($existingData, ['option_id', 'language_id']);
            }
            if (count($validData['option_data']) > 0) {
                $this->model->upsert($validData['option_data'], ['code', 'type']);
                $optionCodes = array_column($validData['option_data'], 'code');
                $this->model->clearQuery();
                $this->model->softDelete(false);
                $optionData = $this->model->whereIn('code', $optionCodes)->select(['option_id', 'code'])->findAll(false);
                $optionData = array_column($optionData, 'option_id', 'code');
            }
            if (count($validData['option_content_data']) > 0) {
                foreach ($validData['option_content_data'] as &$content) {
                    $content['option_id'] = $optionData[$content['code']];
                    unset($content['code']);
                }
                $this->optionContent->upsert($validData['option_content_data'], ['name', 'language_id']);
            }
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update attribute groups: " . $e->getMessage());
        }

        return [
            'success' => true,
            'total_records' => iterator_count($records),
            'valid_records' => count($showFrontendValidData['option_content_data']),
            'valid_data' => $showFrontendValidData['option_content_data'],
            'invalid_records' => count($invalid),
            'updated_records' => count($showFrontendExistingData),
            'updated_data' => $showFrontendExistingData,
            'duplicated_records' => count($updated),
            'duplicated_data' => $updated,
            'options' => [
                'inserted_count' => count($validData['option_data']),
                'valid_data' => $validData['option_data']
            ],
            'option_contents' => [
                'inserted_count' => count($validData['option_content_data']),
                'valid_data' => $validData['option_content_data']
            ],
            'invalid_data' => $invalid,

            'summary' => [
                'success_rate' => iterator_count($records) > 0
                    ? round((count($validData['option_content_data']) / iterator_count($records)) * 100, 2) . '%'
                    : '0%',
                'attribute_processed' => count($validData['option_content_data']),
                'content_records_created' => $validData['option_content_data'],
                'errors' => count($invalid),
            ],
            'language_map' => array_flip($languageMap)
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
        $defaultFields['option_group_id'] = 1;

        return $defaultFields;
    }

    private function prepareRecord(array $record, array $defaultFields): array
    {
        return isset($record['option_id']) && $record['option_id'] ? $record : array_merge($defaultFields, $record);
    }

    // here start type
    /**
     * Get all types
     * returns array of rows
     */
    public function getTypes(): array
    {
        $this->type->clearQuery();
        $data = $this->type
            ->select(['type_id', 'type', 'sort_order'])
            ->whereNull('deleted_at')
            ->orderBy('type_id', 'DESC')
            ->findAll(false);
        return $data;
    }

    public function getTypeById($id)
    {
        $this->type->clearQuery();
        $item = $this->type->where('type_id', '=', $id)->first();
        return $item ? (array) $item->data : [];
    }

    public function findTypeByName(string $name)
    {
        $this->type->clearQuery();
        return $this->type->where('type', '=', $name)
            ->select(['type_id as id', 'type'])
            ->first();
    }

    public function createType(array $data): array
    {
        $this->type->clearQuery();
        $payload = [
            'type' => $data['type'] ?? '',
            'sort_order' => $data['sort_order'] ?? 0,
        ];

        try {
            $this->db->beginTransaction();
            $obj = $this->type->create($payload);
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
        $this->type->clearQuery();
        $item = $this->type->where('type_id', '=', $id)->first();
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
            $this->type->clearQuery();
            $type = $this->type->where('type_id', '=', $type_id)->first();
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

        $records = $reader->getRecords();
        $validData = [];
        $invalid = [];
        $processed = [];
        $updated = [];
        $existingMap = $this->type->select(['type_id', 'type'])->findAll(false);
        $existingMap = array_column($existingMap, 'type_id', 'type');

        foreach ($records as $offset => $record) {
            $rowNum = $offset + 2;
            $typeName = trim($record['type'] ?? '');
            $sortOrder = isset($record['sort_order']) ? (int)$record['sort_order'] : 0;
            $unique = $typeName;

            if ($typeName === '') {
                $invalid[] = ['row' => $rowNum, 'errors' => ['type' => 'required']];
                continue;
            }

            if (isset($existingMap[$typeName])) {
                $updated[] = [
                    'row' => $offset + 2,
                    'data' => $record,
                    'identifier' => $unique
                ];
                continue;
            }

            if (in_array(strtolower($typeName), $processed, true)) {
                $invalid[] = [
                    'row' => $rowNum,
                    'errors' => ['duplicate' => 'duplicate in CSV']
                ];
                continue;
            }

            $validData[] = ['type' => $typeName, 'sort_order' => $sortOrder];
            $processed[] = strtolower($typeName);
        }

        try {
            $this->db->beginTransaction();
            if (!empty($validData)) {
                $this->type->insert($validData);
            }
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to import types: " . $e->getMessage());
        }

        return [
            'success' => true,
            'total_records' => iterator_count($records),
            'inserted' => count($validData),
            'invalid' => $invalid,
            'updated' => $updated,
            'valid_data' => $validData,
            'processed_updates' => $processed,
        ];
    }
}
