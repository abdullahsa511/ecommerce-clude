<?php

declare(strict_types=1);

namespace App\Core\Repositories\Attribute;

use App\Core\Models\Attribute\Attribute;
use App\Core\Models\Attribute\AttributeContent;
use App\Core\Models\Attribute\AttributeGroup;
use App\Core\Models\Attribute\AttributeGroupContent;
use App\Core\Models\Localisation\Language;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Utilities\Debug;
use App\Core\Validation\AttributeDataValidation;
use Illuminate\Support\Arr;
use PDO;
use League\Csv\Reader;

class AttributeRepository extends BaseRepository implements AttributeRepositoryInterface
{
    private Language $language;
    private AttributeContent $attributeContent;
    private AttributeGroup $attributeGroup;
    private AttributeGroupContent $attributeGroupContent;
    public function __construct(
        PDO $db,
        AttributeContent $attributeContent,
        Language $language,
        AttributeGroup $attributeGroup,
        AttributeGroupContent $attributeGroupContent
    ) {
        parent::__construct($db, 'attribute', Attribute::class);
        $this->attributeContent = $attributeContent;
        $this->attributeContent->setDb($db);
        $this->language = $language;
        $this->language->setDb($db);
        $this->attributeGroup = $attributeGroup;
        $this->attributeGroup->setDb($db);
        $this->attributeGroupContent = $attributeGroupContent;
        $this->attributeGroupContent->setDb($db);
    }

    /**
     * Get all attributes with pagination and filtering
     */
    public function getAll(
        int $language_id = 1,
        ?array $product_id = null,
        ?int $attribute_group_id = null,
        int $start = 0,
        int $limit = 10,
        ?string $search = null
    ): array {
        $query = $this->model->select(['*']);

        // Add relationships
        $query->with(['content' => function ($q) use ($language_id) {
            $q->where('language_id', '=', $language_id);
        }]);

        $query->with(['groupContent' => function ($q) use ($language_id) {
            $q->where('language_id', '=', $language_id);
        }]);

        // Add product attributes if product_id is provided
        if ($product_id !== null) {
            $query->with(['productAttributes' => function ($q) use ($product_id) {
                $q->whereIn('product_id', $product_id);
            }]);
        }

        // Filter by attribute group
        if ($attribute_group_id !== null) {
            $query->where('attribute_group_id', '=', $attribute_group_id);
        }

        // Search by name
        if ($search !== null) {
            $query->whereLike('attribute_content.name', $search);
        }

        // Apply pagination
        $query->orderBy('sort_order')
            ->limit($limit)
            ->offset($start);

        return ['query' => $query->findAll(false)];

        // $data = $query->findAll();
        // $total = $query->countAll();

        // // Convert JSON relationships to models
        // foreach ($data as &$item) {
        //     $model = new Attribute();
        //     $model->set($item);
        //     $model->convertJsonObjectToModel();
        //     $model->convertJsonArrayToModelCollection();
        //     $item = $model;
        // }

        // return [
        //     'data' => $data,
        //     'total' => $total
        // ];
    }

    /**
     * Get attribute by ID with content
     */
    public function get(int $attribute_id, int $language_id): ?Attribute
    {
        $result = $this->model->where('attribute_id', '=', $attribute_id)
            ->with(['content' => function ($q) use ($language_id) {
                $q->where('language_id', '=', $language_id);
            }])
            ->find($attribute_id);

        if ($result) {
            $result->convertJsonObjectToModel();
        }

        return $result;
    }

    /**
     * Add new attribute with content
     */
    public function add(array $attribute, array $attribute_content, int $language_id): ?Attribute
    {
        try {
            $this->db->beginTransaction();

            // Add attribute
            $attribute = $this->model->create($attribute);
            if (!$attribute) {
                throw new \Exception('Failed to create attribute');
            }

            // Add attribute content
            $attribute_content['attribute_id'] = $attribute->getId();
            $attribute_content['language_id'] = $language_id;

            $stmt = $this->db->prepare("
                INSERT INTO attribute_content 
                (" . implode(', ', array_keys($attribute_content)) . ")
                VALUES (" . implode(', ', array_map(fn($key) => ":$key", array_keys($attribute_content))) . ")
            ");

            foreach ($attribute_content as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }

            if (!$stmt->execute()) {
                throw new \Exception('Failed to create attribute content');
            }

            $this->db->commit();
            return $attribute;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return null;
        }
    }

    /**
     * Update attribute and its content
     */
    public function edit(int $attribute_id, array $attribute, array $attribute_content): bool
    {
        try {
            $this->db->beginTransaction();

            // Update attribute
            // if (!$this->model->update($attribute_id, $attribute)) {
            //     throw new \Exception('Failed to update attribute');
            // }

            // Update attribute content
            $setClause = implode(', ', array_map(fn($key) => "$key = :$key", array_keys($attribute_content)));
            $stmt = $this->db->prepare("
                UPDATE attribute_content 
                SET $setClause
                WHERE attribute_id = :attribute_id
            ");

            foreach ($attribute_content as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            $stmt->bindValue(':attribute_id', $attribute_id);

            if (!$stmt->execute()) {
                throw new \Exception('Failed to update attribute content');
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function delete(int $attribute_id): bool
    {
        return true;
    }

    /**
     * Delete attribute and its content
     */
    public function deleteMultiple5(array $attribute_id): bool
    {
        try {
            $this->db->beginTransaction();

            // Delete attribute content
            $stmt = $this->db->prepare("DELETE FROM attribute_content WHERE attribute_id IN (" . implode(',', array_fill(0, count($attribute_id), '?')) . ")");
            if (!$stmt->execute($attribute_id)) {
                throw new \Exception('Failed to delete attribute content');
            }

            // Delete attribute
            if (!$this->model->deleteMultiple($attribute_id)) {
                throw new \Exception('Failed to delete attribute');
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    /** -----------------------------------
     * attribute related all transcation
     * 
     * ------------------------------------
     */
    public function getAllAttributes(): array
    {
        $query = $this->attributeContent
            ->select([
                'attribute.attribute_id',
                'attribute.attribute_group_id',
                'attribute.sort_order',
                'attribute_content.name AS name',
                'attribute_content.language_id AS language_id',
                'attribute_group_content.name AS group_name'
            ])
            ->Join('attribute', 'attribute_content.attribute_id', '=', 'attribute.attribute_id')
            ->Join('attribute_group_content', 'attribute.attribute_group_id', '=', 'attribute_group_content.attribute_group_id')
            ->orderBy('attribute.attribute_id', 'DESC');
        // 🔹 Execute query
        $attributes = $query->findAll(false);

        $attributeData = array_map(function ($item) {
            return [
                'attribute_id' => (int)($item['attribute_id'] ?? 0),
                'attribute_group_id' => (int)($item['attribute_group_id'] ?? 0),
                'sort_order' => (int)($item['sort_order'] ?? 0),
                'content' => [
                    'name' => $item['name'] ?? null,
                    'language_id' => $item['language_id'] ?? null,
                    'attribute_id' => $item['attribute_id'] ?? null,
                ],
                'group_content' => [
                    'name' => $item['group_name'] ?? null,
                    'language_id' => $item['language_id'] ?? null,
                    'attribute_group_id' => $item['attribute_group_id'] ?? null,
                ],
            ];
        }, $attributes);
        // $data = $this->getAll();
        // $attributes = $data['query'] ?? [];

        // // Format each record
        // $attributeData = array_map(function ($item) {
        //     // Decode JSON
        //     $content = json_decode($item['content'] ?? '', true);
        //     $groupContent = json_decode($item['groupContent'] ?? '', true);

        //     return [
        //         'attribute_id' => (int)($item['attribute_id'] ?? 0),
        //         'attribute_code' => $item['attribute_code'] ?? '',
        //         'attribute_group_id' => (int)($item['attribute_group_id'] ?? 0),
        //         'sort_order' => (int)($item['sort_order'] ?? 0),
        //         'content' => [
        //             'name' => $content['name'] ?? null,
        //             'language_id' => $content['language_id'] ?? null,
        //             'attribute_id' => $content['attribute_id'] ?? null,
        //         ],
        //         'group_content' => [
        //             'name' => $groupContent['name'] ?? null,
        //             'language_id' => $groupContent['language_id'] ?? null,
        //             'attribute_group_id' => $groupContent['attribute_group_id'] ?? null,
        //         ],
        //     ];
        // }, $attributes);
        return $attributeData;
    }

    public function getAllAttributeById($id)
    {
        $query = $this->attributeContent
            ->select([
                'attribute.attribute_id',
                'attribute.attribute_group_id',
                'attribute.sort_order',
                'attribute_content.name AS name',
                'attribute_content.language_id AS language_id',
                'attribute_group_content.name AS group_name'
            ])
            ->join('attribute', 'attribute_content.attribute_id', '=', 'attribute.attribute_id')
            ->join('attribute_group_content', 'attribute.attribute_group_id', '=', 'attribute_group_content.attribute_group_id')
            ->where('attribute_content.attribute_id', '=', $id)
            ->orderBy('attribute.attribute_id', 'DESC');

        $item = $query->first();

        if (!$item) {
            return null;
        }

        // If $item is an object, convert to array
        $item = is_array($item) ? $item : (array) $item;

        $attributeData = [
            'attribute_id' => (int)($item['attribute_id'] ?? 0),
            'attribute_group_id' => (int)($item['attribute_group_id'] ?? 1),
            'sort_order' => (int)($item['sort_order'] ?? 1),
            'content' => [
                'name' => $item['name'] ?? null,
                'language_id' => $item['language_id'] ?? 1,
                'attribute_id' => $item['attribute_id'] ?? null,
            ],
            'group_content' => [
                'name' => $item['group_name'] ?? null,
                'language_id' => $item['language_id'] ?? 1,
                'attribute_group_id' => $item['attribute_group_id'] ?? 1,
            ],
        ];

        return $attributeData;
    }

    public function createAttributes($data): array
    {
        $attribute = [
            'attribute_group_id' => $data['attribute_group_id'] ?? 1,
            'sort_order' => $data['sort_order'] ?? 1,
            'code' => str_replace(' ', '-', strtolower(trim($data['content']['name'])))
        ];
        try {
            $this->db->beginTransaction();

            $attributeObj = $this->model->create($attribute);
            $attributeId = $data['attribute_id'] = $attributeObj->attribute_id ?? null;
            if (isset($data['content']) && $attributeId) {
                $data['content']['attribute_id'] = (int) $attributeId;
                $this->attributeContent->insert([$data['content']]);
            }
            // if (isset($data['group_content'])) {
            //     // attribute_group
            //     $attributeGroup = [
            //         'sort_order' => isset($data['sort_order']) ? $data['sort_order'] : 1,
            //     ];
            //     $attributeGroupObj = $this->attributeGroup->create($attributeGroup);
            //     $attributeGroupId = $attributeGroupObj->attribute_group_id ?? null;
            //        Debug::dd($attributeGroupId, true);
            //     if ($attributeGroupId) {
            //         $data['group_content']['attribute_group_id'] = (int) $attributeGroupId;
            //         $this->attributeGroupContent->insert([$data['group_content']]);
            //     }
            // }

            $this->db->commit();
            return (array) $data;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update attributes: " . $e->getMessage());
        }
    }

    public function updateAttributes($data, $id): array
    {
        $attributeId = $data['attribute_id'];
        $this->model->clearQuery();
        $attribute = $this->model->where('attribute_id', '=', $attributeId)->first();
        $content = $this->attributeContent->where('attribute_id', '=', $attributeId)->first();

        $contentData = isset($data['content']) ? (array) $data['content'] : [];

        try {
            $this->db->beginTransaction();
            $attributeData =[
                'attribute_group_id' => $data['attribute_group_id'] ?? 1,
                'sort_order' => $data['sort_order'] ?? 1,
                'code' => str_replace(' ', '-', strtolower(trim($data['content']['name'])))
            ];
            $attribute->clearQuery();
            $attribute->update($attributeData);

            if (!$content) {
                return [];
            }

            if (!empty($contentData)) {
                $content->clearQuery();
                $content->upsert([$contentData], ['attribute_id', 'language_id']);
            }

            $this->db->commit();
            return (array) $data;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update attributes: " . $e->getMessage());
        }
    }

    // delete attribute
    public function deleteAttributes(int $id): bool
    {
        return true;
        try {
            $this->db->beginTransaction();

            $attribute = $this->model->where('attribute_id', '=', $id)->first();
            if ($attribute) {
                $attribute->delete($attribute->attribute_id);
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to delete attributes: " . $e->getMessage());
        }
    }

    public function deleteMultipleAttributes(array $attribute_ids): bool
    {
        try {
            $this->db->beginTransaction();

            $attributes = $this->model->select(['attribute_id'])->whereIn('attribute_id', $attribute_ids)->findAll();
            $attributeIds = array_column($attributes, 'attribute_id');
            if ($attributeIds) {
                $this->model->deleteMultiple($attributeIds);
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to delete attributes: " . $e->getMessage());
        }
    }

    // attribute group 
    public function getAllAttributeGroups(): array
    {
        // $this->attributeGroupContent->clearQuery();
        // $data = $this->attributeGroup->select(['attribute_group_id', 'name'])->findAll();
        // return $data;

        // $this->attributeGroup->clearQuery();
        $data = $this->attributeGroup
        ->Join('attribute_group_content', 'attribute_group.attribute_group_id', '=', 'attribute_group_content.attribute_group_id')
         ->select(['attribute_group_content.attribute_group_id as id', 'attribute_group_content.name'])
         ->where('attribute_group_content.attribute_group_id', '!=', '')
         ->findAll(false);
        return $data;
    }

    // import data
    public function importAttributes(string $csv_file): array
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
            'attribute_data' => [],
            'attribute_content_data' => [],
        ];
        $invalid = [];
        $existingData = [];
        $updated = [];
        $processed = [];

        $allLanguages = $this->language->select(['language_id', 'code'])->findAll();
        $languageMap = [];
        foreach ($allLanguages as $lang) {
            $languageMap[$lang['code']] = (int)$lang['language_id'];
        }
        $existingData = $this->attributeContent->select(['attribute_id', 'name'])->findAll(false);
        $attributeContentMap = array_column($existingData, 'attribute_id', 'name');
        $attributeGroupData = $this->attributeGroup->select(['attribute_group_id', 'code'])->findAll(false);
        $attributeGroupContentMap = array_column($attributeGroupData, 'code', 'attribute_group_id');
        $languageMap = $this->language->select(['language_id', 'code'])->findAll(false);
        $languageMap = array_column($languageMap, 'language_id', 'code');
        $existingDataMaps = [
            'attributeContentMap' => $attributeContentMap,
            'attributeGroupContentMap' => $attributeGroupContentMap,
            'attributeGroupIds' => array_values($attributeGroupContentMap),
            'languageMap' => $languageMap,
        ];

        foreach ($records as $offset => $record) {
            try {
                // Merge defaults with record (ensures required fields exist)
                $record = $this->prepareRecord($record, $defaultFields);
                // $record['language_id'] = 1;
                $validator = new AttributeDataValidation($record, $existingDataMaps);
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

                if($validator->isExistingData) {
                    $toUpdate = (array) $validator->attribute_content;
                    $toUpdate['attribute_id'] = $validator->attribute->attribute_id;
                    $existingData[] = $toUpdate;
                }else{                    
                    $validData['attribute_data'][] = (array) $validator->attribute;
                    $validData['attribute_content_data'][] = (array) $validator->attribute_content;
                }

                // Skip if product has already been processed
                if (in_array($unique, $processed, true)) {
                    $updated[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'identifier' => $unique
                    ];
                    continue;
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

        // $result = $this->attributeAndContentInsertorUpdate($validData, $languageMap);

        try{
            $this->db->beginTransaction();
            // if(count($existingData) > 0){
            //     $this->attributeContent->upsert($existingData, ['attribute_id', 'language_id']);
            // }

            if(count($validData['attribute_data']) > 0){
                $this->model->upsert($validData['attribute_data'], ['code']);
                $attributeCodes = array_column($validData['attribute_data'], 'code');
                $this->model->clearQuery();
                // $this->model->softDelete(false);
                $attributeData = $this->model->whereIn('code', $attributeCodes)->select(['attribute_id', 'code'])->limit(0)->findAll(false);
                $attributeData = array_column($attributeData, 'attribute_id', 'code');
            }
            if(count($validData['attribute_content_data']) > 0){
                foreach($validData['attribute_content_data'] as &$content){
                    $content['attribute_id'] = $attributeData[$content['code']];
                    unset($content['code']);
                }   
                $this->attributeContent->upsert($validData['attribute_content_data'], ['attribute_id', 'language_id']);
            }
            $this->db->commit();
        }catch(\Exception $e){
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update attributes: " . $e->getMessage());
        }

        // return $result;
        return [
            'success' => true,
            'total_records' => iterator_count($records),
            'valid_records' => count($validData['attribute_content_data']),
            'invalid_records' => count($invalid),
            'invalid_data' => $invalid,
            'updated_records' => count($existingData),
            'updated_data' => $existingData,
            'duplicated_records' => count($updated),
            'duplicated_data' => $updated,
            'attributes' => [
                'inserted_count' => count($validData['attribute_data']),
                'valid_data' => $validData['attribute_data']
            ],
            'attribute_contents' => [
                'inserted_count' => count($validData['attribute_content_data']),
                'valid_data' => $validData['attribute_content_data']
            ],
         
            'summary' => [
                'success_rate' => count($validData['attribute_content_data']) > 0 ? round((count($validData['attribute_content_data']) / iterator_count($records)) * 100, 2) . '%' : '0%',
                'attribute_processed' => count($validData['attribute_content_data']),
                'content_records_created' => $validData['attribute_content_data'],
                'errors' => count($invalid),
            ]
        ];
    }

    public function attributeAndContentInsertorUpdate(array $validData, $languageMap = []): array
    {
        $attributes = $validData['attribute_data'] ?? [];
        $contents   = $validData['attribute_content_data'] ?? [];

        if (empty($attributes) || empty($contents)) {
            return ['inserted_count' => 0, 'inserted_content_count' => 0];
        }

        try {
            $this->db->beginTransaction();

            // Fetch all attribute data existing name → id [name, attribute id]
            // $existing = $this->model
            //     ->join('attribute_content', 'attribute_content.attribute_id = attribute.attribute_id')
            //     ->select(['attribute.attribute_id', 'attribute_content.name'])
            //     ->findAll(false);

            $existingMap = [];
            // foreach ($existing as $row) {
            //     $existingMap[strtolower(trim($row['name']))] = (int)$row['attribute_id'];
            // }

            // Prepare new attribute + content
            $newAttributes = [];
            $newContents = [];

            foreach ($contents as $i => $content) {
                // Ensure $attribute exists
                if (!isset($attributes[$i])) {
                    continue; // skip if attribute is missing
                }

                $attribute = $attributes[$i];

                $name = is_array($content) ? $content['name'] ?? '' : $content->name ?? '';
                $languageCode = is_array($content)
                    ? ($content['language_code'] ?? null)
                    : ($content->language_code ?? null);

                // Map language code to ID, fallback to 1
                $languageId = ($languageCode !== null && isset($languageMap[$languageCode]))
                    ? $languageMap[$languageCode]
                    : 1;

                $nameKey = strtolower(trim($name));

                // Skip duplicates
                if (isset($existingMap[$nameKey])) {
                    continue;
                }

                $newAttributes[] = [
                    'attribute_group_id' => $attribute->attribute_group_id ?? 1,
                    'sort_order'         => $attribute->sort_order ?? 0,
                ];

                $newContents[] = [
                    'name'        => $name,
                    'language_id' => $languageId,
                    // attribute_id assigned later
                ];

                $existingMap[$nameKey] = 'pending';
            }


            if (empty($newAttributes)) {
                return ['inserted_count' => 0, 'inserted_content_count' => 0];
            }

            // Get MAX ID before insert
            $lastRow = $this->model
                ->select(['attribute_id'])
                ->orderBy('attribute_id', 'DESC')
                ->findAll(false);

            $before = !empty($lastRow) ? (int)$lastRow[0]['attribute_id'] : 0;

            // Batch insert new attributes
            $this->model->insert($newAttributes);

            $insertCount = count($newAttributes);
            // new IDs = (before + 1) ... (before + insertCount)
            $startId = $before + 1;

            for ($i = 0; $i < $insertCount; $i++) {
                $newContents[$i]['attribute_id'] = $startId + $i;
            }

            // Insert new content batch
            $this->attributeContent->insert($newContents);

            $this->db->commit();

            return [
                'attribute' => $newAttributes,
                'attribute_content'  => $newContents,
            ];
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function attributeAndContentInsertorUpdate_backup(array $validData): array
    {
        $insertedCount = 0;
        $insertedContentCount = 0;
        $idMap = []; // maps attribute name to attribute_id
        $newAttributes = [];
        $finalContentData = [];
        $attributes = $validData['attribute_data'] ?? [];
        $contents = $validData['attribute_content_data'] ?? [];

        if (empty($attributes) || empty($contents)) {
            return ['inserted_count' => 0, 'inserted_content_count' => 0, 'attributeIds' => []];
        }

        try {
            $this->db->beginTransaction();

            // Fetch existing attributes
            $existingAttributes = $this->model
                ->join('attribute_content', 'attribute_content.attribute_id', '=', 'attribute.attribute_id')
                ->select(['attribute.attribute_id', 'attribute_content.name'])
                ->findAll();

            foreach ($existingAttributes as $row) {
                $idMap[strtolower($row['name'])] = (int)$row['attribute_id'];
            }

            // Prepare new attributes to insert
            foreach ($attributes as $index => $attribute) {
                $name = '';
                if (isset($contents[$index])) {
                    $content = $contents[$index];
                    $name = is_array($content) ? ($content['name'] ?? '') : ($content->name ?? '');
                }
                $key = strtolower($name);
                if (!isset($idMap[$key])) {
                    $newAttributes[] = [
                        'attribute_group_id' => $attribute->attribute_group_id ?? 1,
                        'sort_order' => $attribute->sort_order ?? 0
                    ];
                }
            }

            // Insert new attributes
            if (!empty($newAttributes)) {
                $this->model->insert($newAttributes);
                $insertedCount = count($newAttributes);

                // Fetch the newly inserted attributes to map their IDs
                $allAttributes = $this->model
                    ->select(['attribute_id', 'attribute_group_id'])
                    ->orderBy('attribute_id', 'asc')
                    ->findAll();

                foreach ($contents as $content) {
                    $name = is_array($content) ? ($content['name'] ?? '') : ($content->name ?? '');
                    $key = strtolower($name);

                    if (!isset($idMap[$key])) {
                        // Find first unmatched attribute_id
                        foreach ($allAttributes as $row) {
                            if (!in_array($row['attribute_id'], $idMap, true)) {
                                $idMap[$key] = $row['attribute_id'];
                                break;
                            }
                        }
                    }
                }
            }

            // Prepare final content data
            foreach ($contents as $content) {
                $name = is_array($content) ? ($content['name'] ?? '') : ($content->name ?? '');
                $languageId = is_array($content) ? ($content['language_id'] ?? 1) : ($content->language_id ?? 1);
                $key = strtolower($name);

                if (isset($idMap[$key])) {
                    $finalContentData[] = [
                        'attribute_id' => $idMap[$key],
                        'language_id' => $languageId,
                        'name' => $name
                    ];
                }
            }

            if (!empty($finalContentData)) {
                $insertedContentCount = $this->attributeContent->upsert(
                    $finalContentData,
                    ['attribute_id', 'language_id']
                );
            }

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update attributes: " . $e->getMessage());
        }

        return [
            'inserted_count' => count($finalContentData),
            'valid_attribute_data' => count($newAttributes),
            'valid_attribute_data' => $newAttributes,
            'valid_attribute_content_data' => $finalContentData,
            'inserted_content_count' => count($finalContentData),
            'attributeIds' => $idMap
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
        $defaultFields['attribute_group_id'] = 1;

        return $defaultFields;
    }

    private function prepareRecord(array $record, array $defaultFields): array
    {
        return isset($record['attribute_id']) && $record['attribute_id'] ? $record : array_merge($defaultFields, $record);
    }
}
