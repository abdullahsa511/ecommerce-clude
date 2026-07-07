<?php

namespace App\Core\Repositories\User;

use App\Core\Models\User\UserGroup;
use App\Core\Models\User\UserGroupContent;
use App\Core\Models\Localisation\Language;
use App\Core\Models\Base\Model;
use App\Core\Repositories\Base\BaseRepository;
use PDO;
use Exception;
use League\Csv\Reader;
use App\Core\Validation\UserGroupDataValidation;
class UserGroupRepository extends BaseRepository implements UserGroupRepositoryInterface
{
    protected Model $model;
    protected UserGroupContent $contentModel;
    protected Language $language;

    public function __construct(PDO $db, UserGroupContent $contentModel, Language $language) 
    {
        parent::__construct($db, 'user_group', UserGroup::class);
        $this->contentModel = $contentModel;
        $this->contentModel->setDb($db);
        $this->language = $language;
        $this->language->setDb($db);
    }

    /**
     * Get all user groups with content
     * 
     * @param int|null $languageId
     * @param int|null $start
     * @param int|null $limit
     * @return array
     */
    public function getAll(?int $languageId = null, ?int $start = null, ?int $limit = null): array
    {
        $query = $this->model->select(['user_group.*'])
            ->with(['content' => function($query) use ($languageId) {
                if ($languageId !== null) {
                    $query->where('language_id', '=', $languageId);
                }
                return $query;
            }]);

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
     * Get a single user group with content by ID
     * 
     * @param int $userGroupId
     * @return array|null
     */
    public function get(int $userGroupId): ?array
    {
        $result = $this->model->with(['content'])
            ->where('user_group_id', '=', $userGroupId)
            ->find($userGroupId);

        return $result ? $result->findAll() : null;
    }

    public function findAll(): array
    {
        // Use a simpler approach without the with relationship to avoid SQL syntax errors
        $results = $this->model->findAll();
        
        // Manually fetch and attach user group content for each result
        foreach ($results as &$result) {
            $userGroupId = $result['user_group_id'];
            
            // Fetch user group content for this user group
            $contentQuery = $this->contentModel->where('user_group_id', '=', $userGroupId);
            $contentResult = $contentQuery->findAll();
            
            if (!empty($contentResult)) {
                $result['userGroupContent'] = $contentResult[0];
            } else {
                $result['userGroupContent'] = null;
            }
        }

        return $results ?? [];
    }

    public function find(int $id): ?object
    {
        $result = $this->model->with(['userGroupContent'])->find($id);
        if ($result && isset($result->user_group_content_data)) {
            $result->user_group_content_data = json_decode($result->user_group_content_data, true);
        }
        return $result;
    }

    public function create(array $data): object
    {
        $this->db->beginTransaction();

        $code = str_replace(' ', '-', strtolower(trim($data['name'])));

        try {
            // Validate required fields
            // $requiredFields = [
            //     'status', 'sort_order', 'code', 'name', 'content'
            // ];
            
            // foreach ($requiredFields as $field) {
            //     if (!isset($data[$field])) {
            //         throw new \InvalidArgumentException("Missing required field: {$field}");
            //     }
            // }

            // Separate subscription plan content data
            $userGroupContent = [
                'name' => $data['name'],
                'language_id' => (int)$data['language_id'],
                'content' => $data['content'],
            ];
            
            
            // Prepare subscription plan data
            $userGroupData = [
                'code' => $code,
                'status' => (int)$data['status'],
                'sort_order' => (int)$data['sort_order'],
            ];

            // Insert subscription plan
            // Remove 'code' from userGroupData if it does not exist in the table
            // If 'code' is not an actual column, don't insert it
            // unset($userGroupData['code']);
            $newUserGroup = parent::create($userGroupData);
            $userGroupId = $newUserGroup->user_group_id;

            // Add subscription plan ID to content data
            $userGroupContent['user_group_id'] = $userGroupId;
            
            // Insert subscription plan content
            $this->contentModel->create($userGroupContent);

            $this->db->commit();
            return $this->find($userGroupId);
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }


    public function update(int $id, array $data): ?Model
    {
        $this->db->beginTransaction();

        try {
            // Separate user group content data
            $userGroupContent = [];
            $contentFields = ['name', 'language_id', 'content'];
            
            foreach ($contentFields as $field) {
                if (isset($data[$field])) {
                    $userGroupContent[$field] = $field === 'language_id' ? (int)$data[$field] : $data[$field];
                    unset($data[$field]);
                }
            }

            // Update user group data
            if (!empty($data)) {
                // Convert numeric fields to integers
                $numericFields = ['status', 'code', 'sort_order'];
                foreach ($numericFields as $field) {
                    if (isset($data[$field])) {
                        $data[$field] = $field === 'code' ? $data[$field] : (int)$data[$field];
                    }
                }

                // Find the user group first, then update it
                $userGroup = $this->model->find($id);
                if ($userGroup) {
                    unset($data['code']);
                    $userGroup->update($data);
                }
            }

            // Update user group content if provided
            if (!empty($userGroupContent)) {
                // Add user group ID to content data
                $userGroupContent['user_group_id'] = $id;
                
                // Find existing content record
                $existingContent = $this->contentModel->where('user_group_id', '=', $id);
                if (isset($userGroupContent['language_id'])) {
                    $existingContent = $existingContent->where('language_id', '=', $userGroupContent['language_id']);
                }
                $contentResult = $existingContent->first();
                
                if ($contentResult) {
                    // Update existing content
                    $contentResult->update($userGroupContent);
                } else {
                    // Create new content if none exists
                    $this->contentModel->create($userGroupContent);
                }
            }

            $this->db->commit();
            return $this->find($id);
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        $this->db->beginTransaction();

        try {
            // First delete the subscription plan content
            $this->contentModel->where('user_group_id', '=', $id);
            $this->contentModel->delete($id);

            // Then delete the subscription plan
            $this->model->where('user_group_id', '=', $id);
            $result = $this->model->delete($id);

            $this->db->commit();
            return $result;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    // 
    public function importUserGroups(string $csv_file): array
    {
        $reader = Reader::createFromPath($csv_file, 'r');
        $reader->setHeaderOffset(0);
        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }
        $defaultFields = $this->getDefaultFields($headers);
        $records = $reader->getRecords();
        $requiredFields = [
            'name',
            'content',
            'sort_order',
            'status',
            'language_id',
        ];
        $validData = [
            'user_group_data' => [],
            'user_group_content_data' => [],
        ];
        $showFrontendValidData = ['user_group_content_data' => []];
        $showFrontendExistingData = [];
        $invalid = [];
        $updated = [];
        $processed = [];
        $existingGroupMap = $this->model->select(['user_group_id', 'code'])->findAll(false);
        $existingGroupMap = array_column($existingGroupMap, 'user_group_id','code');
        $languageMap = $this->language->select(['language_id', 'code'])->findAll(false);
        $languageMap = array_column($languageMap, 'language_id', 'code');
        $existingDataMaps = [
            'userGroupIds' => $existingGroupMap,
            'languageMap' => $languageMap,
        ];

        foreach ($records as $offset => $record) {
            try {
                // Merge defaults with record (ensures required fields exist)
                $record = $this->prepareRecord($record, $defaultFields);
                // $record['language_id'] = 1;
                $validator = new UserGroupDataValidation($record, $requiredFields, array_keys($defaultFields), $existingDataMaps);
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
                if($validated->isExistingData){
                    $updated[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'identifier' => $unique
                    ];
                    continue;
                }else{
                    $validData['user_group_data'][] = (array) $validated->user_group;
                    $validData['user_group_content_data'][] = (array) $validated->user_group_content;
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

        // $result = $this->userGroupAndContentInsertorUpdate($validData, $languageMap);
        try{
            $this->db->beginTransaction();
            if(count($validData['user_group_data']) > 0){
                $this->model->upsert($validData['user_group_data'], ['code']);
                $userGroupCodes = array_column($validData['user_group_data'], 'code');
                $this->model->clearQuery();
                $this->model->softDelete(false);
                $userGroupData = $this->model->whereIn('code', $userGroupCodes)->select(['user_group_id', 'code'])->limit(0)->findAll(false);
                $userGroupData = array_column($userGroupData, 'user_group_id', 'code');
            }
            if(count($validData['user_group_content_data']) > 0){
                foreach($validData['user_group_content_data'] as &$content){
                    $content['user_group_id'] = $userGroupData[$content['code']];
                    unset($content['code']);
                }
                $this->contentModel->upsert($validData['user_group_content_data'], ['user_group_id', 'language_id']);
            }
            $this->db->commit();
        }catch(\Exception $e){
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update user groups: " . $e->getMessage());
        }

        return [
            'success' => true,
            'total_records' => iterator_count($records),
            'valid_records' => count($validData['user_group_data']),
            'valid_data' => $validData['user_group_content_data'],
            'inserted_count' => count($validData['user_group_data']),
            'inserted_data' => $validData['user_group_content_data'],
            'invalid_records' => count($invalid),
            'updated_records' => count($updated),
            'updated_data' => $updated,
            'duplicated_records' => count($updated),
            'duplicated_data' => $updated,
            'language_map' => array_flip($languageMap),
            'users' => [
                'inserted_count' => count($validData['user_group_data']),
                'valid_data' => $validData['user_group_data']
            ],
            'user_contents' => [
                'inserted_count' => count($showFrontendValidData['user_group_content_data']),
                'valid_data' => $showFrontendValidData['user_group_content_data']
            ],
            'invalid_data' => $invalid,
          
            'summary' => [
                'success_rate' => iterator_count($records) > 0
                    ? round((count($validData['user_group_content_data']) / iterator_count($records)) * 100, 2) . '%'
                    : '0%',
                'user_processed' => count($validData['user_group_content_data']),
                'content_records_created' => $validData['user_group_content_data'],
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
        $defaultFields['status'] = 1;
        $defaultFields['sort_order'] = 1;
        $defaultFields['user_group_id'] = null;

        return $defaultFields;
    }

    private function prepareRecord(array $record, array $defaultFields): array
    {
        return isset($record['user_group_id']) && $record['user_group_id'] ? $record : array_merge($defaultFields, $record);
    }

} 