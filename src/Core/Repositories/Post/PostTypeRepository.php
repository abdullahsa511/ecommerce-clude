<?php

declare(strict_types=1);

namespace App\Core\Repositories\Post;

use PDO;
use App\Core\Models\Post\PostType;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Validation\PostTypeDataValidation;
use League\Csv\Reader;
use Exception;

class PostTypeRepository extends BaseRepository implements PostTypeRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'post_type', PostType::class);
    }

    protected function getPrimaryKeyColumn(): string
    {
        return 'post_type_id';
    }

    public function getAll(
        ?int $siteId = null,
        ?string $type = null,
        ?string $source = null,
        int $start = 0,
        int $limit = 10
    ): array {
        $query = $this->model;

        // Load relationships
        $query->with(['site']);

        // Apply filters
        if ($siteId !== null) {
            $query->where('site_id', '=', $siteId);
        }

        if ($type !== null) {
            $query->where('type', '=', $type);
        }

        if ($source !== null) {
            $query->where('source', '=', $source);
        }

        // Apply ordering
        $query->orderBy('post_type_id', 'ASC');

        // Apply pagination
        if ($limit !== null) {
            $query->limit($limit);
        }

        if ($start !== null) {
            $query->offset($start);
        }

        // Get results
        $results = $query->findAll() ?? [];
        $total = $query->countAll();
        $perPage = $limit ?? $this->model->limitValue;

        return [
            'items' => collect($results),
            'total' => $total,
            "total_pages" => (int)ceil($total / $perPage),
            "current_page" => (int)($start / $perPage + 1),
            "per_page" => $perPage
        ];
    }

    public function get(int $postTypeId): ?PostType
    {
        $query = $this->model;
        $query->with(['site']);
        return $query->find($postTypeId);
    }
    
    public function importPostTypes(string $csv_file): array
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
            'type',
            'plural',
            'icon',
            'image',
            'source',
            'site_id',
        ];
        $records = $reader->getRecords();

        $valid = [];
        $invalid = [];
        $updated = [];
        $duplicate = [];
        $processed = [];
        $existingPostTypesMap = $this->model->select(['post_type_id', 'name'])->limit(0)->findAll(false);
        $existingPostTypesMap = array_column($existingPostTypesMap, 'post_type_id', 'name');

        foreach ($records as $offset => $record) {
            try {
                $record = $this->prepareRecord($record, $defaultFields);
                $validator = new PostTypeDataValidation($record, $requiredFields, array_keys($defaultFields), $existingPostTypesMap);
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
                    $duplicate[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'identifier' => $unique
                    ];
                    continue;
                }
                if ($validated->isExistingData) {
                    $updated[] = (array) $validated->postType;
                } else {
                    $valid[] = (array) $validated->postType;
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
            if (count($updated) > 0) {
                $this->model->upsert($updated, ['post_type_id']);
            }

            if (count($valid) > 0) {
                $this->model->insert($valid);
            }
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update post types: " . $e->getMessage());
        }

        return [
            'success' => true,
            'total_records' => iterator_count($records),
            'valid_records' => count($valid),
            'inserted_count' => count($valid),
            'valid_data' => $valid,
            'invalid_records' => count($invalid),
            'updated_records' => count($updated),
            'updated_data' => $updated,
            'duplicate_records' => count($duplicate),
            'duplicate_data' => $duplicate,
            'post_types' => [
                'inserted_count' => count($valid),
                'valid_data' => $valid
            ],
            'invalid_data' => $invalid,
            'summary' => [
                'success_rate' => iterator_count($records) > 0
                    ? round((count($valid) / iterator_count($records)) * 100, 2) . '%'
                    : '0%',
                'post_type_processed' => count($valid),
                'post_type_records_created' => $valid,
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
        $defaultFields['name'] = null;
        $defaultFields['type'] = null;
        $defaultFields['plural'] = null;
        $defaultFields['icon'] = null;
        $defaultFields['image'] = null;
        $defaultFields['source'] = null;
        $defaultFields['site_id'] = 1;
        return $defaultFields;
    }

    private function prepareRecord(array $record, array $defaultFields): array
    {
        return isset($record['post_type_id']) && $record['post_type_id'] ? $record : array_merge($defaultFields, $record);
    }

    public function updatePostTypeImage(array $data, int $post_type_id): bool
    {
        $postType = $this->model->where('post_type_id', '=', $post_type_id)->first();
        if (!$postType) {
            return false; // post type not found
        }

        $dataobj = $data;
        $image = $dataobj[0]['objectURL'];

        $this->db->beginTransaction();
        try {
            // UPDATE `post_type` SET `image` = $img WHERE `post_type`.`post_type_id` = $post_type_id
            $postType->update(['image' => $image]);
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // delete vendor image
    public function deletePostTypeImage(int $post_type_id): bool
    {
        $this->model->clearQuery();
        $postType = $this->model->where('post_type_id', '=', $post_type_id)->first();
        if (!$postType) {
            return false; // post type not found
        }
        $postType->update(['image' => '']);
        return true;
    }
} 