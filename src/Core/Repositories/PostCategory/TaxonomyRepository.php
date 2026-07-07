<?php

declare(strict_types=1);

namespace App\Core\Repositories\PostCategory;

use App\Core\Models\Base\Model;
use App\Core\Models\PostCategory\Taxonomy;
use App\Core\Models\PostCategory\TaxonomyContent;
use App\Core\Models\PostCategory\TaxonomyItem;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Validation\TaxonomyDataValidation;
use App\Core\Repositories\Product\ProductRepository;
use League\Csv\Reader;
use Exception;
use PDO;

class TaxonomyRepository extends BaseRepository implements TaxonomyRepositoryInterface
{
    private TaxonomyContent $taxonomyContent;
    private TaxonomyItem $taxonomyItem;
    private ProductRepository $productRepository;
    public function __construct(PDO $db, TaxonomyContent $taxonomyContent, TaxonomyItem $taxonomyItem, ProductRepository $productRepository)
    {
        parent::__construct($db, 'taxonomy', Taxonomy::class);
        $this->taxonomyContent = $taxonomyContent;
        $this->taxonomyContent->setDb($db);
        $this->taxonomyItem = $taxonomyItem;
        $this->taxonomyItem->setDb($db);
        $this->productRepository = $productRepository;
    }

    public function getAll(
        ?int $taxonomyItemId,
        int $start,
        int $limit,
        ?string $postType = null,
        ?string $type = null
    ): array 
    {
        $query = $this->model->with(['taxonomyContent', 'taxonomyItem', 'site']);

        if ($taxonomyItemId !== null) {
            $query->where('taxonomy_item.taxonomy_item_id', (string)$taxonomyItemId);
        }

        if ($postType !== null) {
            $query->where('post_type', $postType);
        }

        if ($type !== null) {
            $query->where('type', $type);
        }

        $total = $query->countAll();
        $list = $query->limit($limit)->offset($start)->orderBy('taxonomy_id', 'DESC')->findAll();

        return [
            'list' => $list,
            'total' => $total
        ];
    }

    public function getTaxonomy(int $taxonomyId): ?Taxonomy
    {
        return $this->model->with(['taxonomyContent', 'taxonomyItem', 'site'])
            ->where('taxonomy_id', (string)$taxonomyId)
            ->find('taxonomy_id');
    }

    public function insertTaxonomyContents(array $data): bool
    {
        return $this->taxonomyContent->insert($data);
    }

    public function getFinishTaxonomyIds(): array
    {
        $result = $this->model
            ->where('type', '=', 'finishes')
            ->where('post_type', '=', 'product')
            ->select(['taxonomy_id'])
            ->findAll(false);
        
        return array_column($result, 'taxonomy_id');
    }

    public function getTagTaxonomyIds(): array
    {
        $result = $this->model
            ->where('type', '=', 'tags')
            ->where('post_type', '=', 'product')
            ->select(['taxonomy_id'])
            ->findAll(false);
        
        return array_column($result, 'taxonomy_id');
    }
    
    
    public function importTaxonomies(string $csv_file): array
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
            'post_type',
            'type',
            'site_id',
        ];
        $records = $reader->getRecords();

        $valid = [];
        $invalid = [];
        $updated = [];
        $duplicate = [];
        $processed = [];
        $existingTaxonomiesMap = $this->model->select(['taxonomy_id', 'name'])->limit(0)->findAll(false);
        $existingTaxonomiesMap = array_column($existingTaxonomiesMap, 'taxonomy_id', 'name');

        foreach ($records as $offset => $record) {
            try {
                $record = $this->prepareRecord($record, $defaultFields);
                $validator = new TaxonomyDataValidation($record, $requiredFields, array_keys($defaultFields), $existingTaxonomiesMap);
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
                    $updated[] = (array) $validated->taxonomy;
                } else {
                    $valid[] = (array) $validated->taxonomy;
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
                $this->model->upsert($updated, ['taxonomy_id']);
            }
            if (count($valid) > 0) {
                $this->model->insert($valid);
            }
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update taxonomies: " . $e->getMessage());
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
            'taxonomies' => [
                'inserted_count' => count($valid),
                'valid_data' => $valid
            ],
            'invalid_data' => $invalid,
            'summary' => [
                'success_rate' => iterator_count($records) > 0
                    ? round((count($valid) / iterator_count($records)) * 100, 2) . '%'
                    : '0%',
                'taxonomy_processed' => count($valid),
                'taxonomy_records_created' => $valid,
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
        $defaultFields['type'] = null;
        $defaultFields['site_id'] = 1;
        return $defaultFields;
    }

    private function prepareRecord(array $record, array $defaultFields): array
    {
        return isset($record['taxonomy_id']) && $record['taxonomy_id'] ? $record : array_merge($defaultFields, $record);
    }

    // get taxonomy types
    public function getTaxonomyTypes(): array
    {
        // taxonomy type 
        $taxonomyTypes = [
            [ 'name' => 'Categories', 'code' => 'categories' ],
            [ 'name' => 'Tags', 'code' => 'tags' ],
        ];

        // model type
        $modelTypes = self::getModels();

        return [
            'taxonomyTypes' => $taxonomyTypes,
            'modelTypes' => $modelTypes
        ];
    }

    public static function getModels(): array
    {
        return [
            [
                'model_id' => 1,
                'name' => 'Web Product',
                'type' => 'product',
                'code' => 'product',
                'class' => \App\Core\Models\Product\Product::class,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'model_id' => 2,
                'name' => 'Item',
                'type' => 'item',
                'code' => 'item',
                'class' => \App\Core\Models\Item\Item::class,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'model_id' => 3,
                'name' => 'Project',
                'type' => 'project',
                'code' => 'project',
                'class' => \App\Core\Models\Project\Project::class,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'model_id' => 4,
                'name' => 'Design Resource',
                'type' => 'design_resource',
                'code' => 'design_resource',
                'class' => \App\Core\Models\Design\DesignResource::class,
                'created_at' => date('Y-m-d H:i:s')
            ],
             [
                'model_id' => 5,
                'name' => 'Showroom',
                'type' => 'showrooms',
                'code' => 'showroom',
                'class' => \App\Core\Models\Showroom\Showroom::class,
                'created_at' => date('Y-m-d H:i:s')
             ]
        ];
    }
} 