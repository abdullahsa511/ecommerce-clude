<?php

declare(strict_types=1);

namespace App\Core\Repositories\Product;

use PDO;
use App\Core\Models\Product\Product;
use App\Core\Models\Product\ProductOption;
use App\Core\Models\ProductOptionGroup\ProductOptionGroup;
use App\Core\Models\Variant\ProductVariant;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Validation\ProductOptionDataValidation;
use League\Csv\Reader;
use App\Core\Exceptions\ValidationException;

class ProductOptionRepository extends BaseRepository implements ProductOptionRepositoryInterface
{

    private Product $product;
    private ProductVariant $productVariant;
    private ProductOptionGroup $productOptionGroup;

    public function __construct(PDO $db, Product $product, ProductVariant $productVariant, ProductOptionGroup $productOptionGroup)
    {
        parent::__construct($db, 'product_option', ProductOption::class);
        $this->product = $product;
        $this->product->setDb($db);
        $this->productVariant = $productVariant;
        $this->productVariant->setDb($db);
        $this->productOptionGroup = $productOptionGroup;
        $this->productOptionGroup->setDb($db);
    }

    /**
     * {@inheritdoc}
     */
    public function getAll(
        int $languageId,
        ?int $productId = null,
        ?int $start = null,
        ?int $limit = null
    ): array {
        $query = $this->model->select(['product_option.*'])
            ->with([
                'option',
                'option.contentInLanguage' => function ($query) use ($languageId) {
                    $query->where('language_id', '=', $languageId);
                }
            ]);

        if ($productId !== null) {
            $query->where('product_option.product_id', '=', $productId);
        }

        // Get total count before pagination
        $total = $query->countAll();

        if ($start !== null && $limit !== null) {
            $query->offset($start)->limit($limit);
        }

        $items = $query->findAll();

        // Map the results to include name and type from relationships
        $mappedItems = array_map(function ($item) {
            $item->name = $item->option->contentInLanguage->name ?? null;
            $item->type = $item->option->type ?? null;
            $item->array_key = $item->product_option_id;
            return $item;
        }, $items);

        return [
            'items' => $mappedItems,
            'total' => $total
        ];
    }

    public function getProductOptions(): array
    {
        $query = $this->model
            ->join('product', 'product.product_id', '=', 'product_option.product_id')
            ->join('product_variant', 'product_variant.product_variant_id', '=', 'product_option.product_variant_id')
            ->join('product_option_group', 'product_option_group.product_option_group_id', '=', 'product_option.product_option_group_id')
            ->select(['product_option.*', 'product.product_code', 'product_variant.variant_name', 'product_option_group.option_group_name']);
        $productOptions = $query->limit(0)->findAll(false);
        return $productOptions;
    }

    public function getProductOptionById(int $productOptionId): ?array
    {
        $productOption = $this->model
        ->join('type', 'type.type_id', '=', 'product_option.type_id')
        ->select(['product_option.*','type.type'])
        ->where('product_option_id', '=', $productOptionId)->select(['*'])->first();
        if (!$productOption) {
            return [];
        }
        return (array) $productOption->data;
    }

    public function createProductOption(array $productOptionData): ?array
    {
        $this->model->clearQuery();
        $productOption = $this->model->create($productOptionData);
        if (!$productOption) {
            return [];
        }
        return (array) $productOption->data;
    }

    public function updateProductOption(int $productOptionId, array $productOptionData): ?array
    {
        $this->model->clearQuery();
        $productOption = $this->model->where('product_option_id', '=', $productOptionId)->first();
        if (!$productOption) {
            throw new ValidationException([
                'global_message' => ['Product option not found'],
            ]);
        }
        $productOption->clearQuery();
        $productOption->update($productOptionData);
        return (array) $productOption->data;
    }

    public function deleteProductOption2(int $id): bool
    {
        $productOption = $this->model->where('product_option_id', '=', $id)->first();
        if (!$productOption) {
            throw new ValidationException([
                'global_message' => ['Product option not found'],
            ]);
        }
        try {
            $this->db->beginTransaction();
            $productOption->clearQuery();
            $productOption->delete($id);
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Failed to delete product option: " . $e->getMessage());
        }
    }

    public function deleteProductOption(int $id): bool
    {
        try {
            $this->db->beginTransaction();
            $this->model->clearQuery();
            $this->model->delete($id);
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Failed to delete product option: " . $e->getMessage());
        }
    }


    public function isProductOptionUnique(array $productOptionData, ?int $id = null): bool
    {
        $this->model->clearQuery();
        $query = $this->model->where('product_id', '=', $productOptionData['product_id'])
            ->where('product_variant_id', '=', $productOptionData['product_variant_id'])
            ->where('product_option_group_id', '=', $productOptionData['product_option_group_id'])
            ->where('option_name', '=', $productOptionData['option_name']);
        if ($id !== null) {
            $query->where('product_option_id', '!=', (int) $id);
        }
        $productOption = $query->first();
        return $productOption ? true : false;
    }
    /**
     * {@inheritdoc}
     */
    public function get(int $productOptionId, int $languageId): ?array
    {
        $result = $this->model->select(['product_option.*'])
            ->with([
                'option',
                'option.contentInLanguage' => function ($query) use ($languageId) {
                    $query->where('language_id', '=', $languageId);
                }
            ])
            ->where('product_option.product_option_id', '=', $productOptionId)
            ->findAll();

        if (empty($result)) {
            return null;
        }

        $item = $result[0];
        $item->name = $item->option->contentInLanguage->name ?? null;
        $item->type = $item->option->type ?? null;

        $data = get_object_vars($item);
        unset($data['db']);
        return $data;
    }

    // import data
    public function importProductOptions(string $csv_file): array
    {
        $reader = Reader::createFromPath($csv_file, 'r');
        $reader->setHeaderOffset(0);
        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }
        $defaultFields = $this->getDefaultFields($headers);
        $requiredFields = [
            'product_id',
            'product_variant_id',
            'product_option_group_id',
            'option_name'
        ];
        $records = $reader->getRecords();

        $validCount = 0;
        $inserted = [];
        $insertedData = [];
        $updated = [];
        $updatedData = [];
        $duplicated = [];
        $invalid = [];
        $processed = [];
        $existingDataMaps = $this->getExistingDataMaps($records);
        $existingOptionids = $this->model->select(['product_option_id'])->limit(0)->findAll(false);

        foreach ($records as $offset => $record) {
            try {
                $record = $this->prepareRecord($record, $defaultFields);
                $validator = new ProductOptionDataValidation($record, $requiredFields, array_keys($defaultFields), $existingDataMaps, $existingOptionids);
                $validated = $validator->validate();

                if ($validated === false) {
                    $invalid[] = [
                        'row' => $offset + 1,
                        'data' => $record,
                        'errors' => $validator->getErrors()
                    ];
                    continue;
                }

                $unique = $validator->getUniqueIdentifier();

                if (in_array($unique, $processed, true)) {
                    $duplicated[] = [
                        'row' => $offset + 1,
                        'data' => $record,
                        'identifier' => $unique
                    ];
                    continue;
                }
                $record['row'] = $offset + 1;
                $validCount++;
                if ($validated->isExistingData) {
                    $updated[] = (array) $validated->productOption;
                    $updatedData[] = $record;
                } else {
                    $inserted[] = (array) $validated->productOption;
                    $insertedData[] = $record;
                }
                $processed[] = $unique;
            } catch (Exception $e) {
                // Capture any runtime exception per record
                $invalid[] = [
                    'row' => $offset + 1,
                    'data' => $record,
                    'errors' => ['processing_error' => $e->getMessage()]
                ];
                continue;
            }
        }

        try {
            $this->db->beginTransaction();
            // if (count($existingData) > 0) {
            //     $this->model->upsert($existingData, ['product_variant_id', 'product_option_group_id', 'option_name']);
            // }
            if (count($inserted) > 0) {
                $this->model->upsert($inserted, ['product_id', 'product_variant_id', 'product_option_group_id', 'option_name']);
            }
            if (count($updated) > 0) {
                $this->model->upsert($updated, ['product_id', 'product_variant_id', 'product_option_group_id', 'option_name']);
            }
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update product options: " . $e->getMessage());
        }

        return [
            'success' => true,
            'total_records' => iterator_count($records),
            'valid_records' => $validCount,
            'inserted_records' => count($inserted),
            'inserted_data' => $insertedData,
            'updated_records' => count($updated),
            'updated_data' => $updatedData,
            'duplicated_records' => count($duplicated),
            'duplicated_data' => $duplicated,
            'invalid_records' => count($invalid),
            'invalid_data' => $invalid,
            'summary' => [
                'success_rate' => iterator_count($records) > 0
                    ? round(($validCount / iterator_count($records)) * 100, 2) . '%'
                    : '0%',
                'errors' => count($invalid),
            ]
        ];
    }

    private function getExistingDataMaps($records): array
    {
        $recordsArray = iterator_to_array($records);
        $importingProductCodes = array_unique(array_column($recordsArray, 'web_product_code'));
        
        $existingProductOptionMapRaw = $this->model
        ->join('product', 'product.product_id', '=', 'product_option.product_id')
        ->join('product_variant', 'product_variant.product_variant_id', '=', 'product_option.product_variant_id')
        ->join('product_option_group', 'product_option_group.product_option_group_id', '=', 'product_option.product_option_group_id')
        ->whereIn('product.product_code', $importingProductCodes)
        ->select([
            'product_option.product_option_id', 
            'product_option.option_name',
            'product_option_group.product_option_group_id', 
            'product_option_group.option_group_name',
            'product_variant.product_variant_id',
            'product_variant.variant_name',
            'product.product_code',
            'product.product_id'
            ])
        ->limit(0)->findAll(false);

        $existingProductOptionMap = [];
        foreach ($existingProductOptionMapRaw as $row) {
            $existingProductOptionMap[
                $row['product_code'] 
                . '-' . $row['variant_name'] 
                . '-' . $row['option_group_name']
                .'-'.$row['option_name']
            ] = $row['product_option_id'];
        }

         //Existing option group map 
        // [product_code + variant_name ] => product_variant_id

        $existingGroupMapRaw = $this->productOptionGroup
        ->join('product', 'product.product_id', '=', 'product_option_group.product_id')
        ->join('product_variant', 'product_variant.product_variant_id', '=', 'product_option_group.product_variant_id')
        ->whereIn('product.product_code', $importingProductCodes)
        ->select(['product_option_group.product_option_group_id', 
        'product_option_group.option_group_name', 
        'product_variant.product_variant_id',
        'product_variant.variant_name',
        'product.product_id',
        'product.product_code' 
        ])
        ->limit(0)
        ->findAll(false);

        
        $groupMap = [];
        foreach ($existingGroupMapRaw as $row) {
            $groupMap[
                $row['product_code'] 
                . '-' . $row['variant_name'] 
                . '-' . $row['option_group_name']
            ] = [
                'product_option_group_id' => $row['product_option_group_id'],
                'product_variant_id' => $row['product_variant_id'],
                'product_id' => $row['product_id'],
            ];
        }

        return [
            'existingProductOptionMap' => $existingProductOptionMap,
            'groupsMap' => $groupMap,
        ];
    }

    private function getDefaultFields(array $headers): array
    {
        $defaultFields = [];
        // Initialize all CSV headers as null by default
        foreach ($headers as $header) {
            $defaultFields[$header] = null;
        }

        $defaultFields['sort_order'] = 1;
        $defaultFields['type_id'] = 1;
        $defaultFields['active_status'] = 1;
        $defaultFields['created_at'] = date('Y-m-d H:i:s');
        // $defaultFields['updated_at'] = date('Y-m-d H:i:s');

        return $defaultFields;
    }

    private function prepareRecord(array $record, array $defaultFields): array
    {
        return isset($record['product_option_id  ']) && $record['product_option_id  '] ? $record : array_merge($defaultFields, $record);
    }

    public function searchProductOptions(string $name, int $product_id): array
    {
        $this->model->clearQuery();
        
        // Use a subquery to get only the latest product_option_id for each option_name
        // This ensures we get the most recent record per option_name
        $subquery = "SELECT MAX(product_option.product_option_id) as latest_id
                     FROM product_option
                     WHERE product_option.option_name LIKE :name_pattern
                     AND product_option.product_id = :product_id
                     GROUP BY product_option.option_name";
        
        // Main query that joins with the subquery to get only latest records
        $sql = "SELECT product_option.*, `type`.type
                FROM product_option
                INNER JOIN (
                    {$subquery}
                ) AS latest_options ON product_option.product_option_id = latest_options.latest_id
                INNER JOIN `type` ON product_option.type_id = `type`.type_id
                ORDER BY product_option.product_option_id DESC";
        
        // Prepare and execute the query with parameters
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':name_pattern', '%' . $name . '%', PDO::PARAM_STR);
        $stmt->bindValue(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        $productOptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $output = [
            "productOptions" => $productOptions
        ];
        return $output;
    }

    // find product options by group ids
    public function findProductOptionsByGroupIds(array $groupIds): array
    {
        $productOptions = $this->model
                    ->whereIn('product_option_group_id', $groupIds)
                    ->limit(0)
                    ->findAll(false);

        return $productOptions;
    }
    public function searchItemOptionsByQuery(string $name, int $product_id, int $product_variant_id, int $product_option_group_id): array
    {
        $this->model->clearQuery();
        $productOptions = $this->model
                        // ->where('option_name', 'like', '%' . $name . '%')
                        ->where('product_id', '=', $product_id)
                        // ->where('product_variant_id', '=', $product_variant_id)
                        // ->where('product_option_group_id', '=', $product_option_group_id)
                        ->limit(0)
                        ->findAll(false);
        return $productOptions;
    }
}
