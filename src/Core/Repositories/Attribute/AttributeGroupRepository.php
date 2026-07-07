<?php

declare(strict_types=1);

namespace App\Core\Repositories\Attribute;

use App\Core\Models\Attribute\AttributeGroup;
use App\Core\Models\Attribute\AttributeGroupContent;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Models\Localisation\Language;
use App\Core\Validation\AttributeGroupDataValidation;
use League\Csv\Reader;
use PDO;

class AttributeGroupRepository extends BaseRepository implements AttributeGroupRepositoryInterface
{
    private AttributeGroupContent $attributeGroupContent;
    private Language $language;
    public function __construct(
        PDO $db,
        Language $language,
        AttributeGroupContent $attributeGroupContent
    ) {
        parent::__construct($db, 'attribute_group', AttributeGroup::class);
        $this->language = $language;
        $this->language->setDb($db);
        $this->attributeGroupContent = $attributeGroupContent;
        $this->attributeGroupContent->setDb($db);
    }

    /**
     * Get all attribute groups with pagination and filtering
     */
    public function getAll(int $start = 0, int $limit = 10, ?int $sort_order = null): array
    {
        $query = $this->model->select(['*']);

        if ($sort_order !== null) {
            $query->where('sort_order', '=', $sort_order);
        }

        $query->orderBy('sort_order')
            ->limit($limit)
            ->offset($start);

        $data = $query->findAll();
        $total = $query->countAll();

        return [
            'data' => $data,
            'total' => $total
        ];
    }

    /**
     * Get attribute group by ID
     */
    public function get( $attribute_group_id): ?AttributeGroup
    {
        $result = $this->model->where('attribute_group_id', '=', $attribute_group_id)->find($attribute_group_id);
        return $result;
    }
    public function findByName(string $name): ?AttributeGroup
    {
        return $this->model
        ->Join('attribute_group_content', 'attribute_group.attribute_group_id', '=', 'attribute_group_content.attribute_group_id')
        ->where('attribute_group_content.name', '=', $name)
        ->select(['attribute_group_content.attribute_group_id as id', 'attribute_group_content.name', 'attribute_group.sort_order'])
        ->first();
    }
    public function findByCode(string $code): ?AttributeGroup
    {
        return $this->model
        ->Join('attribute_group_content', 'attribute_group.attribute_group_id', '=', 'attribute_group_content.attribute_group_id')
        ->where('attribute_group.code', '=', $code)
        ->select(['attribute_group_content.attribute_group_id as id', 'attribute_group_content.name', 'attribute_group.sort_order'])
        ->first();
    }

    public function getAllAttributeGroups(): array
    {
        $data = $this->model
            ->Join('attribute_group_content', 'attribute_group.attribute_group_id', '=', 'attribute_group_content.attribute_group_id')
            ->select(['attribute_group_content.attribute_group_id as id', 'attribute_group_content.name', 'attribute_group.sort_order'])
            ->limit(0)
            ->findAll(false);
        return $data;
    }


    /**
     * Add new attribute group
     */
    public function add(array $data): array
    {
        // create attribute group first and get the attribute group id.
        $attributeGroup = $this->model->create([
            'sort_order' => $data['sort_order'],
            'code' => $data['code']
        ]);

        $attributeGroupContentCreated = $this->attributeGroupContent->insert([
            [
                'attribute_group_id' => $attributeGroup->attribute_group_id,
                'language_id' => $data['content']['language_id'],
                'name' => $data['content']['name']
            ]
        ]);

        $response = (array) $attributeGroup->data;

        if ($attributeGroupContentCreated) {
            $attributeGroupContent = $this->attributeGroupContent->where('attribute_group_id', '=', $attributeGroup->attribute_group_id)
                ->where('language_id', '=', $data['content']['language_id'])->first();
            $response['content'] = (array) $attributeGroupContent->data;
        }

        return $response;
    }

    /**
     * Update attribute group
     */
    public function edit(int $attribute_group_id, array $data): ?AttributeGroup
    {
        return $this->model->where('attribute_group_id', '=', $attribute_group_id)->update($data);
    }

    public function updateAttributeGroups($id, $data): array
    {
        try {
            $this->db->beginTransaction();
            // Update attribute_group table (sort_order)
            $query = $this->model->where('attribute_group_id', '=', $id)->first();
            $attributeGroup = $query->update(['sort_order' => $data['sort_order']]);
        
            $content = $this->attributeGroupContent->where('attribute_group_id', '=', $id)->first();
            $contentData = isset($data['content']) ? (array) $data['content'] : [];

            if (!$content) {
                return [];
            }

            if (!empty($contentData)) {
                $content->clearQuery();
                $updated = $content->upsert([$contentData], ['attribute_group_id', 'language_id']);
            }

            $this->db->commit();
            return (array) $data;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update attributes: " . $e->getMessage());
        }
    }

    /**
     * Delete attribute group
     */
    public function deleteAttributeGroup(int $attribute_group_id): ?AttributeGroup
    {
        $attributeGroup = $this->model->where('attribute_group_id', '=', $attribute_group_id)->first();
        if ($attributeGroup) {
            $attributeGroup->update(['deleted_at' => date('Y-m-d H:i:s')]);
            return $attributeGroup;
        }
        return null;
    }

    /**
     * Get attribute group with its content
     */
    public function getWithContent(int $attribute_group_id): ?AttributeGroup
    {
        $result = $this->model->where('attribute_group_id', '=', $attribute_group_id)
            ->with(['attributeGroupContent'])
            ->find($attribute_group_id);

        if ($result) {
            $result->convertJsonArrayToModelCollection();
        }

        return $result;
    }

    /**
     * Get attribute group with its attributes
     */
    public function getWithAttributes(int $attribute_group_id): ?AttributeGroup
    {
        $result = $this->model->where('attribute_group_id', '=', $attribute_group_id)
            ->with(['attribute'])
            ->find($attribute_group_id);

        if ($result) {
            $result->convertJsonArrayToModelCollection();
        }

        return $result;
    }
    // import data
    public function importAttributeGroups(string $csv_file): array
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
            'attribute_group_data' => [],
            'attribute_group_content_data' => [],
        ];
        $showFrontendValidData = ['attribute_group_content_data' => []];
        $existingData = [];
        $showFrontendExistingData = [];
        $invalid = [];
        $updated = [];
        $processed = [];
        $existingGroupMap = $this->model->select(['attribute_group_id', 'code'])->findAll(false);
        $existingGroupMap = array_column($existingGroupMap, 'attribute_group_id','code');
        $existingGroupIds = array_values($existingGroupMap);
        $languageMap = $this->language->select(['language_id', 'code'])->findAll(false);
        $languageMap = array_column($languageMap, 'language_id', 'code');
        $existingDataMaps = [
            'attributeGroupContentMap' => $existingGroupMap,
            'attributeGroupIds' => $existingGroupIds,
            'languageMap' => $languageMap,
        ];

        foreach ($records as $offset => $record) {
            try {
                // Merge defaults with record (ensures required fields exist)
                $record = $this->prepareRecord($record, $defaultFields);
                // $record['language_id'] = 1;
                $validator = new AttributeGroupDataValidation($record, $existingDataMaps);
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

                $unique = $validator->getAttributeGroupUniqueIdentifier();

                // Skip if product has already been processed
                if (in_array($unique, $processed, true)) {
                    $updated[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'identifier' => $unique
                    ];
                    continue;
                }
                if($validated->isExistingData){
                    $existingData[] = (array) $validated->attribute_group_content;
                    $showFrontendExistingData[] = $record;
                }else{
                    $validData['attribute_group_data'][] = (array) $validated->attribute_group;
                    $validData['attribute_group_content_data'][] = (array) $validated->attribute_group_content;
                    $validData['attribute_group_data'][] = (array) $validated->attribute_group;
                    $contentData = (array) $validated->attribute_group_content;
                    $contentData['language_code'] = $record['language_code'];
                    $showFrontendValidData['attribute_group_content_data'][] = $contentData;
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
                $this->attributeGroupContent->upsert($existingData, ['attribute_group_id', 'language_id']);
            }
            if(count($validData['attribute_group_data']) > 0){
                $this->model->upsert($validData['attribute_group_data'], ['code']);
                $attributeGroupCodes = array_column($validData['attribute_group_data'], 'code');
                $this->model->clearQuery();
                $this->model->softDelete(false);
                $attributeGroupData = $this->model->whereIn('code', $attributeGroupCodes)->select(['attribute_group_id', 'code'])->findAll(false);
                $attributeGroupData = array_column($attributeGroupData, 'attribute_group_id', 'code');
            }
            if(count($validData['attribute_group_content_data']) > 0){
                foreach($validData['attribute_group_content_data'] as &$content){
                    $content['attribute_group_id'] = $attributeGroupData[$content['code']];
                    unset($content['code']);
                }
                $this->attributeGroupContent->upsert($validData['attribute_group_content_data'], ['name', 'language_id']);
            }
            $this->db->commit();
        }catch(\Exception $e){
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update attribute groups: " . $e->getMessage());
        }

        return [
            'success' => true,
            'total_records' => iterator_count($records),
            'valid_records' => count($validData['attribute_group_data']),
            'valid_data' => $showFrontendValidData['attribute_group_content_data'],
            'invalid_records' => count($invalid),
            'updated_records' => count($showFrontendExistingData),
            'updated_data' => $showFrontendExistingData,
            'duplicated_records' => count($updated),
            'duplicated_data' => $updated,
            'attributes' => [
                'inserted_count' => count($validData['attribute_group_data']),
                'valid_data' => $validData['attribute_group_data']
            ],
            'attribute_contents' => [
                'inserted_count' => count($showFrontendValidData['attribute_group_content_data']),
                'valid_data' => $showFrontendValidData['attribute_group_content_data']
            ],
            'invalid_data' => $invalid,
          
            'summary' => [
                'success_rate' => iterator_count($records) > 0
                    ? round((count($validData['attribute_group_content_data']) / iterator_count($records)) * 100, 2) . '%'
                    : '0%',
                'attribute_processed' => count($validData['attribute_group_content_data']),
                'content_records_created' => $validData['attribute_group_content_data'],
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
        $defaultFields['attribute_group_id'] = null;

        return $defaultFields;
    }

    private function prepareRecord(array $record, array $defaultFields): array
    {
        return isset($record['attribute_group_id']) && $record['attribute_group_id'] ? $record : array_merge($defaultFields, $record);
    }

    /**
     * Get attribute group details by ID with content.
     *
     * @param int $id
     * @return array|null
     */
    public function getAllAttributeGroupById($id)
    {
        $query = $this->attributeGroupContent
            ->join('attribute_group', 'attribute_group.attribute_group_id', '=', 'attribute_group_content.attribute_group_id')
            ->select([
                'attribute_group.attribute_group_id',
                'attribute_group.sort_order',
                'attribute_group_content.name',
                'attribute_group_content.language_id',
            ])
            ->where('attribute_group.attribute_group_id', '=', $id);

        $item = $query->findAll(false);

        if (!$item[0]) {
            return null;
        }

        // If $item is an object, convert to array.
        $item = is_array($item[0]) ? $item[0] : (array) $item;

        // Return the attribute group details in a structured format similar to get by ID in AttributeRepository.
        return [
            'attribute_group_id' => (int)($item['attribute_group_id'] ?? 0),
            'sort_order' => (int)($item['sort_order'] ?? 1),
            'content' => [
                'name' => $item['name'] ?? null,
                'language_id' => (int)($item['language_id'] ?? 1),
                'attribute_group_id' => (int)($item['attribute_group_id'] ?? 0),
            ]
        ];
    }
}
