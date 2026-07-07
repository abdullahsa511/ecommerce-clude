<?php

declare(strict_types=1);

namespace App\Core\Repositories\ProductOptionGroup;

use App\Core\Http\Response;
use App\Core\Models\Base\Model;
use App\Core\Models\Localisation\Language;
use App\Core\Models\Product\Product;
use App\Core\Models\Variant\Variant;
use App\Core\Models\ProductOptionGroup\ProductOptionGroup;
use App\Core\Models\Variant\ProductVariant;
use App\Core\Models\Product\ProductOption;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Validation\ProductOptionGroupDataValidation;
use League\Csv\Reader;
use PDO;

class ProductOptionGroupRepository extends BaseRepository implements ProductOptionGroupRepositoryInterface
{
    protected Model $model;
    protected PDO $db;
    private Language $language;
    private ProductVariant $productVariant;
    private Product $product;
    private ProductOption $productOption;

    public function __construct(
        PDO $db,
        Product $product,
        ProductVariant $productVariant,
        Language $language,
        ProductOption $productOption,
    ) {
        parent::__construct($db, 'product_option_group', ProductOptionGroup::class);
        $this->product = $product;
        $this->product->setDb($db);
        $this->productVariant = $productVariant;
        $this->productVariant->setDb($db);
        $this->language = $language;
        $this->language->setDb($db);
        $this->productOption = $productOption;
        $this->productOption->setDb($db);
    }

    public function getProductOptionGroups(): array
    {
        $query = $this->model
            ->join('product_variant', 'product_option_group.product_variant_id', '=', 'product_variant.product_variant_id')
            ->select([
                'product_option_group.product_option_group_id',
                'product_option_group.option_group_name',
                'product_option_group.product_variant_id',
                'product_option_group.sort_order',
                'product_option_group.active_status',
                'product_variant.variant_name',
            ])
            ->whereNull('product_option_group.deleted_at')
            ->limit(0)
            ->orderBy('sort_order', 'ASC');
        $allProductOptionGroups = $query->findAll(false);

        return $allProductOptionGroups;
    }

    public function getProductOptionGroupById($id)
    {
        $productOptionGroup = $this->model
            ->where('product_option_group_id', '=', $id)
            ->limit(0)
            ->first();

        if (!$productOptionGroup) {
            return [];
        }

        $productOptionGroup = (array) $productOptionGroup->data;

        $productOptions = $this->productOption
            ->whereIn('product_option_group_id', [$id])
            ->limit(0)->findAll(false);
        $formatedProductOptions = [];
        foreach ($productOptions as $productOption) {
            $formatedProductOptions[$productOption['product_option_group_id']][] = $productOption;
        }

        $productOptionGroup['productOptions'] = $formatedProductOptions[$productOptionGroup['product_option_group_id']] ?? [];


        return $productOptionGroup;
    }

    public function findByName(array $data): ?ProductOptionGroup
    {
        return $this->model
            ->where('option_group_name', '=', $data['option_group_name'])
            ->where('product_id', '=', $data['product_id'])
            ->where('product_variant_id', '=', $data['product_variant_id'])
            ->select(['product_option_group_id'])
            ->first();
    }
    public function findByCode(string $code): ?ProductOptionGroup
    {
        return $this->model
            ->Join('variant_content', 'variant.variant_id', '=', 'variant_content.variant_id')
            ->where('variant.code', '=', $code)
            ->select(['variant_content.variant_id as id', 'variant_content.name', 'variant.sort_order'])
            ->first();
    }

    public function createProductOptionGroup($data): array
    {
        try {
            $productOptions = [];
            $this->db->beginTransaction();
            if (!empty($data)) {
                // prepare variant
                $productOptionGroupData = [
                    'product_variant_id'      => $data['product_variant_id'],
                    'product_id'              => $data['product_id'],
                    'option_group_name'       => $data['option_group_name'],
                    'description'             => $data['description'] ?? '',
                    'sort_order'              => $data['sort_order'] ?? 0,
                    'active_status'           => isset($data['active_status']) ? 1 : 0,
                ];

                $this->model->clearQuery();
                $productOptionGroupObj = $this->model->create($productOptionGroupData);
                $productOptionGroupId = $productOptionGroupObj->data->product_option_group_id ?? null;
                // loop productOptions for this group
                if (!empty($data['productOptions']) && $productOptionGroupId) {
                    foreach ($data['productOptions'] as $option) {
                        $productOptions[] = [
                            'product_id'              => $data['product_id'],
                            'product_variant_id'      => $data['product_variant_id'],
                            'product_option_group_id' => $productOptionGroupId,
                            'option_name'             => $option['option_name'],
                            'price'                   => $option['price'] ?? 0,
                            'type_id'                 => $option['type_id'] ?? 0,
                            'description'             => $option['description'] ?? '',
                            'sort_order'              => $option['sort_order'] ?? 0,
                            'active_status'           => isset($option['active_status']) ? 1 : 0,
                        ];
                    }
                }
                // insert productOptions
                $this->productOption->insert($productOptions);
                // $this->productOption->upsert($productOptions, ['product_option_id', 'product_id', 'product_variant_id', 'product_option_group_id', 'option_name']);
            }

            $this->db->commit();
            return (array) $this->getProductOptionGroupById($productOptionGroupId);
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update product option groups: " . $e->getMessage());
        }
    }

    public function updateProductOptionGroup($data, $id): array
    {
        $groupId = intval($id);
        $productOptionGroup = $this->model->where('product_option_group_id', '=', $groupId)->first();
        try {
            $this->db->beginTransaction();
            if (!$productOptionGroup) {
                return [];
            }
            $insertOptions = [];
            $updateOptions = [];
            if (!empty($data)) {
                // prepare variant
                $productOptionGroupData = [
                    'product_option_group_id' => $groupId,
                    'product_variant_id'      => $data['product_variant_id'],
                    'product_id'              => $data['product_id'],
                    'option_group_name'       => $data['option_group_name'],
                    'description'             => $data['description'] ?? '',
                    'sort_order'              => $data['sort_order'] ?? 0,
                    'active_status'           => isset($data['active_status']) ? 1 : 0,
                ];

                $this->model->clearQuery();
                $productOptionGroup->update($productOptionGroupData);
                // loop productOptions for this group
                if (!empty($data['productOptions']) && $groupId > 0) {
                    foreach ($data['productOptions'] as $option) {
                        $productOptionId = intval($option['product_option_id'] ?? 0);
                        $item = [
                            'product_id'              => $data['product_id'],
                            'product_variant_id'      => $data['product_variant_id'],
                            'product_option_group_id' => $groupId,
                            'option_name'             => $option['option_name'],
                            'price'                   => $option['price'] ?? 0,
                            'type_id'                 => $option['type_id'] ?? 0,
                            'description'             => $option['description'] ?? '',
                            'sort_order'              => $option['sort_order'] ?? 0,
                            'active_status'           => isset($option['active_status']) ? 1 : 0,
                        ];
                        if ($productOptionId > 0) {
                            $item['product_option_id'] = $productOptionId;
                            $updateOptions[] = $item;
                        } else {
                            $insertOptions[] = $item;
                        }
                    }
                }
                // insert productOptions
                if (!empty($insertOptions)) {
                    $this->productOption->insert($insertOptions);
                }
                if (!empty($updateOptions)) {
                    $this->productOption->upsert($updateOptions, ['product_option_id']);
                }
                // $this->productOption->upsert($productOptions, ['product_id', 'product_variant_id', 'product_option_group_id', 'option_name']);
            }

            $this->db->commit();
            return (array) $this->getProductOptionGroupById($id);
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update product option groups: " . $e->getMessage());
        }
    }

    // delete variant
    public function deleteProductOptionGroup(int $product_option_group_id): bool
    {
        try {
            $this->db->beginTransaction();

            $this->model->clearQuery();
            $productOptionGroup = $this->model->where('product_option_group_id', '=', $product_option_group_id)->first();
            if (!$productOptionGroup) {
                return false;
            }
            // $productOptionGroup->update(['deleted_at' => date('Y-m-d H:i:s')]);
            $productOptionGroup->delete($product_option_group_id);
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to delete options: " . $e->getMessage());
        }
    }

    // import data
    public function importProductOptionGroups(string $csv_file): array
    {
        $reader = Reader::createFromPath($csv_file, 'r');
        $reader->setHeaderOffset(0);
        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }
        $defaultFields = $this->getDefaultFields($headers);
        $requiredFields = [
            'option_group_name',
            'product_variant_id'
        ];
        $records = $reader->getRecords();

        $validData = [];
        $showData = [];
        $invalid = [];
        $duplicate = [];
        $updated = [];
        $updatedData = [];
        $processed = [];
       
        $existingDataMaps = $this->getMaps($records);

        foreach ($records as $offset => $record) {
            try {
                $record = $this->prepareRecord($record, $defaultFields);
                $validator = new ProductOptionGroupDataValidation($record, $requiredFields, array_keys($defaultFields), $existingDataMaps);
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
                    $updated[] = (array) $validated->productOptionGroup;
                    $updatedData[] = $record;
                } else {
                    $validData[] = (array) $validated->productOptionGroup;
                    $showData[] = $record;
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
                $this->model->upsert($updated, ['product_variant_id', 'option_group_name']);
            }
            if (count($validData) > 0) {
                $this->model->upsert($validData, ['product_variant_id', 'option_group_name']);
            }
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update product option groups: " . $e->getMessage());
        }

        return [
            'success' => true,
            'total_records' => iterator_count($records),
            'valid_records' => count($validData),
            'valid_data' => $showData,
            'invalid_records' => count($invalid),
            'updated_records' => count($updated),
            'updated_data' => $updatedData,
            'duplicate_records' => count($duplicate),
            'duplicate_data' => $duplicate,
            'productOptionGroups' => [
                'inserted_count' => count($validData),
                'valid_data' => $validData
            ],
            'invalid_data' => $invalid,
            'summary' => [
                'success_rate' => iterator_count($records) > 0
                    ? round((count($validData) / iterator_count($records)) * 100, 2) . '%'
                    : '0%',
                'productOptionGroup_processed' => count($validData),
                'productOptionGroup_records_created' => $validData,
                'errors' => count($invalid),
            ],
        ];
    }

    private function getMaps($records): array
    {
        $recordsArray = iterator_to_array($records);
        $importingProductCodes = array_unique(array_column($recordsArray, 'web_product_code'));
        
        //Existing product group map 
        // [product_code + variant_name + option_group_name] => product_option_group_id
        
        $existingProductOptionGroupMapRaw = $this->model
        ->join('product', 'product.product_id', '=', 'product_option_group.product_id')
        ->join('product_variant', 'product_variant.product_variant_id', '=', 'product_option_group.product_variant_id')
        ->whereIn('product.product_code', $importingProductCodes)
        ->select([
            'product_option_group.product_option_group_id', 
            'product_option_group.option_group_name',
            'product.product_code',
            'product_variant.product_variant_id',
            'product_variant.variant_name'
            ])
        ->limit(0)->findAll(false);

        $existingProductOptionGroupMap = [];
        foreach ($existingProductOptionGroupMapRaw as $row) {
            $existingProductOptionGroupMap[$row['product_code'] . '-' . $row['variant_name'] . '-' . $row['option_group_name']] = $row['product_option_group_id'];
        }

         //Existing variant map 
        // [product_code + variant_name ] => product_variant_id

        $existingVariantMapRaw = $this->productVariant
        ->join('product', 'product.product_id', '=', 'product_variant.product_id')
        ->whereIn('product.product_code', $importingProductCodes)
        ->select(['product_variant_id', 'variant_name', 'product.product_code', 'product.product_id'])
        ->limit(0)
        ->findAll(false);

        
        $variantMap = [];
        foreach ($existingVariantMapRaw as $row) {
            $variantMap[$row['product_code'] . '-' . $row['variant_name']] = [
                'product_variant_id' => $row['product_variant_id'],
                'product_id' => $row['product_id']
            ];
        }

        return [
            'existingProductOptionGroupMap' => $existingProductOptionGroupMap,
            'variantsMap' => $variantMap
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
        $defaultFields['active_status'] = 1;
        $defaultFields['created_at'] = date('Y-m-d H:i:s');
        // $defaultFields['updated_at'] = date('Y-m-d H:i:s');

        return $defaultFields;
    }

    private function prepareRecord(array $record, array $defaultFields): array
    {
        return isset($record['product_option_group_id ']) && $record['product_option_group_id '] ? $record : array_merge($defaultFields, $record);
    }

    public function searchProductOptionGroups(string $name, int $product_id, $product_variant_id = null): array
    {
        $productOptionGroups = $this->model
            ->where('option_group_name', 'like', '%' . $name . '%')
            ->where('product_id', '=', $product_id);
            if ($product_variant_id) {
                $productOptionGroups->where('product_variant_id', '=', $product_variant_id);
            }
        $productOptionGroups = $productOptionGroups->limit(0)->findAll(false); 


        // Extract product_option_group_ids for querying product options
        $productOptionGroupIds = array_column($productOptionGroups, 'product_option_group_id');

        // Fetch all product options for the retrieved groups
        $productOptions = $this->productOption
            ->whereIn('product_option_group_id', $productOptionGroupIds)
            ->select(['product_option.*', 'product_option.product_option_id as item_option_id'])
            ->limit(0)
            ->findAll(false);

        // Group product options by their product_option_group_id
        $groupedProductOptions = [];
        foreach ($productOptions as $option) {
            $groupedProductOptions[$option['product_option_group_id']][] = $option;
        }

        // Format the final output
        $formattedProductOptionGroups = [];
        foreach ($productOptionGroups as $group) {
            $groupId = $group['product_option_group_id'];
            $group['productOptions'] = $groupedProductOptions[$groupId] ?? [];
            $formattedProductOptionGroups[] = $group;
        }

        return $formattedProductOptionGroups;
    }

    public function searchItemOptionGroups(string $name, int $product_id, $product_variant_id = null): array
    {
        $productOptionGroups = $this->model
            ->where('option_group_name', 'like', '%' . $name . '%')
            ->where('product_id', '=', $product_id);
            if ($product_variant_id) {
                $productOptionGroups->where('product_variant_id', '=', $product_variant_id);
            }
        $productOptionGroups = $productOptionGroups->limit(0)->findAll(false); 


        // Extract product_option_group_ids for querying product options
        $productOptionGroupIds = array_column($productOptionGroups, 'product_option_group_id');

        // Fetch all product options for the retrieved groups
        $productOptions = $this->productOption
            ->whereIn('product_option_group_id', $productOptionGroupIds)
            ->select(['product_option.*', 'product_option.product_option_id as item_option_id'])
            ->limit(0)
            ->findAll(false);

        // Group product options by their product_option_group_id
        $groupedProductOptions = [];
        foreach ($productOptions as $option) {
            // Only keep the first option for each group
            if (!isset($groupedProductOptions[$option['product_option_group_id']])) {
                $groupedProductOptions[$option['product_option_group_id']] = [$option];
            }
        }

        // Format the final output
        $formattedProductOptionGroups = [];
        foreach ($productOptionGroups as $group) {
            $groupId = $group['product_option_group_id'];
            // Only one option per group
            $group['productOptions'] = $groupedProductOptions[$groupId] ?? [];
            $formattedProductOptionGroups[] = $group;
        }

        return $formattedProductOptionGroups;
    }

    // search product option groups by names
    public function findProductOptionGroupsByNames(array $names, int $product_id, int $product_variant_id): array
    {
        $this->model->clearQuery();
        $productOptionGroups = $this->model
                            ->where('product_id', '=', $product_id)
                            ->where('product_variant_id', '=', $product_variant_id)
                            ->whereIn('option_group_name', $names)
                            ->limit(0)
                            ->findAll(false);

        return $productOptionGroups;
    }

    public function searchItemOptionGroupsByQuery(string $name, int $product_id, $product_variant_id = null): array
    {
        $productOptionGroups = $this->model
            ->where('option_group_name', 'like', '%' . $name . '%')
            ->where('product_id', '=', $product_id);
            if ($product_variant_id) {
                $productOptionGroups->where('product_variant_id', '=', $product_variant_id);
            }
        $productOptionGroups = $productOptionGroups->limit(0)->findAll(false); 
        return $productOptionGroups;
    }
}
