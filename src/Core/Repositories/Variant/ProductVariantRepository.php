<?php

declare(strict_types=1);

namespace App\Core\Repositories\Variant;

use App\Core\Models\Base\Model;
use App\Core\Models\Item\Item;
use App\Core\Models\Item\ItemOption;
use App\Core\Models\Localisation\Language;
use App\Core\Models\Product\Product;
use App\Core\Models\Product\ProductVariant;
use App\Core\Models\ProductOptionGroup\ProductOptionGroup;
use App\Core\Models\Product\ProductOption;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Validation\ProductVariantDataValidation;
use App\Core\Repositories\Product\ProductAccessoriesRepository;
use League\Csv\Reader;
use PDO;

use function App\Core\System\utils\app;

class ProductVariantRepository extends BaseRepository implements ProductVariantRepositoryInterface
{
    protected Model $model;
    protected PDO $db;
    private Language $language;
    private Product $product;
    private ProductOptionGroup $productOptionGroup;
    private ProductOption $productOption;
    private ItemOption $itemOption;
    private ProductAccessoriesRepository $productAccessoriesRepository;
    private Item $item;
    public function __construct(
        PDO $db,
        Language $language,
        Product $product,
        ProductOptionGroup $productOptionGroup,
        ProductOption $productOption,
        ItemOption $itemOption,
        ProductAccessoriesRepository $productAccessoriesRepository,
        Item $item,
    ) {
        parent::__construct($db, 'product_variant', ProductVariant::class);
        $this->language = $language;
        $this->language->setDb($db);
        $this->product = $product;
        $this->product->setDb($db);
        $this->productOptionGroup = $productOptionGroup;
        $this->productOptionGroup->setDb($db);
        $this->productOption = $productOption;
        $this->productOption->setDb($db);
        $this->itemOption = $itemOption;
        $this->itemOption->setDb($db);
        $this->productAccessoriesRepository = $productAccessoriesRepository;
        $this->item = $item;
        $this->item->setDb($db);
    }

    public function getVariants(): array
    {
        $query = $this->model
            // ->with(['content', 'content.language'])
            ->join('product', 'product_variant.product_id', '=', 'product.product_id')
            ->select([
                'product_variant.product_variant_id',
                'product_variant.product_id',
                'product_variant.variant_name',
                'product_variant.sort_order',
                'product_variant.active_status',
                'product.product_code',
                'product_variant.image',
            ])
            ->whereNull('product_variant.deleted_at')
            ->limit(0)
            ->orderBy('sort_order', 'ASC');
        $data = $query->findAll(false);

        $variants = array_map(function ($item) {
            if(isset($item['image']) && !empty($item['image'])){
                $item['image'] = json_decode($item['image'], true);
            }
            return [
                'product_variant_id' => (int) ($item['product_variant_id'] ?? 0),
                'product_id' => (int) ($item['product_id'] ?? 0),
                'product_code' => $item['product_code'] ?? '',
                'variant_name' => $item['variant_name'] ?? '',
                'active_status' => (int) ($item['active_status'] ?? 0),
                'code' => $item['code'] ?? '',
                'image' => $item['image'] ?? [],
                'sort_order' => (int) ($item['sort_order'] ?? 0),
                'content' => [
                    'product_code' => $item['product_code'] ?? '',
                ]
            ];
        }, $data);

        return $variants;
    }

    // public function getVariantById($id)
    // {
    //     $product_variant = $this->model->where('product_variant_id', '=', $id)->whereNull('deleted_at')->first();
    //     if (!$product_variant) {
    //         return [];
    //     }
    //     // if ($product_variant && $product_variant->data->content) {
    //     //     $product_variant->data->content = (array) json_decode($product_variant->data->content);
    //     //     $product_variant->data->content = array_find(
    //     //         $product_variant->data->content,
    //     //         fn($item) => isset($item->language_id) && $item->language_id == 1
    //     //     );
    //     // }
    //     return $product_variant ? (array) $product_variant->data : [];
    // }
    public function getVariantById($id)
    {
        $this->model->clearQuery();
        $variants = $this->model
            ->where('product_variant_id', '=', $id)
            ->whereNull('deleted_at')
            ->orderBy('product_variant_id', 'DESC')
            ->limit(0);
        $variants = $variants->findAll(false);

        // $lastQuery = $this->model->getQueryString();
        // echo $lastQuery;
        // exit;

        $variantIds = array_column($variants, 'product_variant_id');


        $productOptionGroups = $this->productOptionGroup
            ->whereIn('product_variant_id', $variantIds)
            ->limit(0)
            ->findAll(false);

        $productOptionGroupIds = array_column($productOptionGroups, 'product_option_group_id');
        $productOptions = $this->productOption
            ->whereIn('product_option_group_id', $productOptionGroupIds)
            ->limit(0)->findAll(false);
        $formatedProductOptions = [];
        foreach ($productOptions as $productOption) {
            // if option image is set, then decode it
            if(isset($productOption['option_image']) && !empty($productOption['option_image'])){
                $productOption['image'] = json_decode($productOption['option_image'], true);
            }
            $formatedProductOptions[$productOption['product_option_group_id']][] = $productOption;
        }

        $formattedProductOptionGroups = [];
        foreach ($productOptionGroups as $productOptionGroup) {
            $productOptionGroup['productOptions'] = $formatedProductOptions[$productOptionGroup['product_option_group_id']] ?? [];
            $formattedProductOptionGroups[$productOptionGroup['product_variant_id']][] = $productOptionGroup;
        }
        foreach ($variants as &$variant) {
            if(isset($variant['image']) && !empty($variant['image'])){
                $variant['image'] = json_decode($variant['image'], true);
            }
            $variant['productOptionGroups'] = $formattedProductOptionGroups[$variant['product_variant_id']] ?? [];
        }

        return $variants;
    }

    public function findByName(array $data, ?int $id = null): bool
    {
        $this->model->clearQuery();
        $query = $this->model
            ->where(strtolower(trim('variant_name')), '=', strtolower(trim($data['variant_name'])))
            ->where('product_id', '=', intval($data['product_id']));
            if ($id) {
                $query->where('product_variant_id', '!=', $id);
            }
            $productVariant = $query->first();
            // $lastQuery = $this->model->getQuery();

        return $productVariant ? true : false;
    }
    public function findByCode(string $code): ?ProductVariant
    {
        return $this->model
            ->Join('variant_content', 'product_variant.product_variant_id', '=', 'variant_content.product_variant_id')
            ->where('product_variant.code', '=', $code)
            ->select(['variant_content.product_variant_id as id', 'variant_content.name', 'product_variant.sort_order'])
            ->first();
    }

    public function createVariant($data): array
    {
        try {
            $productOptionGroups = [];
            $productOptions = [];
            $this->db->beginTransaction();
            if (!empty($data)) {
                // prepare for totally new variant 
                $variantData = [
                    'product_id'         => $data['product_id'],
                    'variant_name'       => $data['variant_name'],
                    'variant_description' => $data['variant_description'],
                    'sort_order'         => $data['sort_order'],
                    'active_status'      => isset($data['active_status']) ? 1 : 0,
                ];

                $this->model->clearQuery();
                $variantObj = $this->model->create($variantData);
                $variantId = $variantObj->data->product_variant_id ?? null;
                // loop productOptionGroups and create group records
                $unique = [];
                if (!empty($data['productOptionGroups'] && $variantId)) {
                    foreach ($data['productOptionGroups'] as $group) {
                        $unique[] = $group['product_id'] . '-' . $variantId . '-' . $group['option_group_name'];
                        $productOptionGroups[] = [
                            'product_variant_id'      => $variantId,
                            'product_id'              => $data['product_id'],
                            'option_group_name'       => $group['option_group_name'],
                            'option_group_description'=> $group['description'] ?? null,
                            'sort_order'              => $group['sort_order'] ?? 0,
                            'active_status'           => isset($group['active_status']) ? 1 : 0,
                        ];
                        // loop productOptions for this group
                        if (!empty($group['productOptions'])) {
                            foreach ($group['productOptions'] as $option) {
                                $productOptions[] = [
                                    'product_id'              => $data['product_id'],
                                    'product_variant_id'      => $variantId,
                                    'option_group_name'      => $group['option_group_name'],
                                    'option_description'             => $option['description'] ?? null,
                                    'option_name'             => $option['option_name'],
                                    'description'             => $option['option_description'],
                                    'sort_order'              => $option['sort_order'] ?? 0,
                                    'price'                   => $option['price'] ?? 0,
                                    'type_id'                 => $option['type_id'] ?? 0,
                                ];
                            }
                        }
                    }
                    // insert productOptionGroups
                    // $this->productOptionGroup->insert($productOptionGroups);
                    $this->productOptionGroup->insert($productOptionGroups);
                    // unique product id, product variant id, product option group id
                    $productOptionGroupIds = $this->getProductOptionGroupIds($unique);
                    $productOptions = array_map(function ($item) use ($productOptionGroupIds) {
                        $unique = $item['product_id'] . '-' . $item['product_variant_id'] . '-' . $item['option_group_name'];
                        if (isset($productOptionGroupIds[$unique])) {
                            return [
                                'product_id'              => $item['product_id'],
                                'product_variant_id'      => $item['product_variant_id'],
                                'product_option_group_id' => $productOptionGroupIds[$unique],
                                'option_name'             => $item['option_name'],
                                'option_description'             => $item['description'],
                                'sort_order'              => $item['sort_order'] ?? 0,
                                'price'                   => $item['price'] ?? 0,
                                'type_id'                 => $item['type_id'] ?? 0,
                            ];
                        }
                    }, $productOptions);
                    // $this->productOption->insert($productOptions);
                    $this->productOption->insert($productOptions);
                }
            }
            $this->db->commit();
            $productId = intval($data['product_id']);
            $variants = $this->getVariantsByProductId($productId);
            if (!empty($variants)) {
                return $variants;
            }
            return [];
            // return $this->searchVariants(intval($data['product_id']));
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update variants: " . $e->getMessage());
        }
    }

    public function updateProductVariant($data, $id): array
    {
        $this->model->clearQuery();
        $productVariant = $this->model->where('product_variant_id', '=', $id)->first();
        try {
            $this->db->beginTransaction();
            $productOptionGroupInsert = [];
            $productOptionGroupUpdate = [];
            $productOptions = [];
            $insertOptions = [];
            $updateOptions = [];
            if (!empty($data)) {
                // prepare variant
                $variantData = [
                    'product_variant_id' => $data['product_variant_id'],
                    'product_id'         => $data['product_id'],
                    'variant_name'       => $data['variant_name'],
                    'variant_description' => $data['variant_description'],
                    'sort_order'         => $data['sort_order'],
                    'active_status'      => $data['active_status'],
                ];

                

                // Update product variant
                // $productVariant->clearQuery();
                $productVariant->update($variantData);

                // loop productOptionGroups and create group records
                if (!empty($data['productOptionGroups'])) {
                    $unique = [];
                    foreach ($data['productOptionGroups'] as $group) {
                        $productOptionGroupId = intval($group['product_option_group_id'] ?? 0);
                        $itemGroup = [
                            'product_id'              => $data['product_id'],
                            'product_variant_id'      => $data['product_variant_id'],
                            'option_group_name'       => $group['option_group_name'],
                            'option_group_description'=> $group['description'] ?? null,
                            'sort_order'              => $group['sort_order'] ?? 0,
                            'active_status'           => $group['active_status'] ?? 1,
                        ];
                        // IF GROUP ID IS NOT PROVIDED, THEN WE NEED TO INSERT A NEW GROUP
                        if ($productOptionGroupId > 0) {
                            $itemGroup['product_option_group_id'] = $productOptionGroupId;
                            $productOptionGroupUpdate[] = $itemGroup;
                        } else {
                            $unique[] = $itemGroup['product_id'] . '-' . $itemGroup['product_variant_id'] . '-' . $itemGroup['option_group_name'];
                            $productOptionGroupInsert[] = $itemGroup;
                        }
                        // loop productOptions for this group
                        if (!empty($group['productOptions'])) {
                            foreach ($group['productOptions'] as $option) {
                                // IF GROUP ID AND OPTION ID ARE NOT PROVIDED, DATA OPTION ID WILL BE 0 
                                $productOptionId = isset($option['product_option_id']) && $productOptionGroupId > 0 ? intval($option['product_option_id']) : 0;
                                $productOption = [
                                    'product_id'              => $data['product_id'],
                                    'product_variant_id'      => $data['product_variant_id'],
                                    'product_option_group_id' => $productOptionGroupId,
                                    'option_name'             => $option['option_name'],
                                    'option_description'      => $option['option_description'],
                                    'sort_order'              => $option['sort_order'] ?? 0,
                                    'price'                   => $option['price'] ?? 0,
                                    'type_id'                 => $option['type_id'] ?? 0,
                                    'hex_color'               => $option['hex_color'] ?? null,
                                ];
                                // IF OPTION ID IS NOT PROVIDED, DATA INSERTED. ELSE UPDATE.
                                if ($productOptionId) {
                                    $productOption['product_option_id'] = $productOptionId;
                                    $updateOptions[] = $productOption;
                                } else {
                                    $productOption['option_group_name'] = $itemGroup['option_group_name'];
                                    $productOptions[] = $productOption;
                                }
                            }
                        }
                    }

                    // insert productOptionGroups
                    if (!empty($productOptionGroupInsert)) {
                        $this->productOptionGroup->insert($productOptionGroupInsert);
                    }
                    if (!empty($productOptionGroupUpdate)) {
                        // Update multiple groups: use upsert to handle batch update/insert by primary key
                        $this->productOptionGroup->upsert($productOptionGroupUpdate, ['product_option_group_id']);
                    }
                    // UPDATE PRODUCT OPTIONS
                    if (!empty($updateOptions)) {
                        // Batch update product options by primary key
                        $this->productOption->upsert($updateOptions, ['product_option_id']);
                    }
                    // INSERT PRODUCT OPTIONS
                    $productOptionGroupIds = $this->getProductOptionGroupIds($unique);
                    foreach ($productOptions as $item) {
                        $key = $item['product_id'] . '-' . $item['product_variant_id'] . '-' . $item['option_group_name'];
                        // IF OPTION ID IS NOT PROVIDED, DATA INSERTED. ELSE UPDATE.
                        $productOptionGroupId = $item['product_option_group_id'] > 0 ? $item['product_option_group_id'] : $productOptionGroupIds[$key];
                        $insertOptions[] = [
                            'product_id'              => $item['product_id'],
                            'product_variant_id'      => $item['product_variant_id'],
                            'product_option_group_id' => $productOptionGroupId,
                            'option_name'             => $item['option_name'],
                            'option_description'      => $item['option_description'],
                            'sort_order'              => $item['sort_order'] ?? 0,
                            'price'                   => $item['price'] ?? 0,
                            'type_id'                 => $item['type_id'] ?? 0,
                            'hex_color'               => $item['hex_color'] ?? null,
                        ];
                    }
                    // insert productOptions
                    if (!empty($insertOptions)) {
                        $this->productOption->insert($insertOptions);
                    }
                }
            }

            $this->db->commit();
            return (array) $this->getVariantById($id);
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to update variant: " . $e->getMessage());
        }
    }

    public function updateProductVariant_BACKUP($data, $id): array
    {
        $productVariant = $this->model->where('product_variant_id', '=', $id)->first();
        try {
            $this->db->beginTransaction();
            $productOptionGroup = [];
            $productOptions = [];
            if (!empty($data)) {
                // prepare variant
                $variantData = [
                    'product_variant_id' => $data['product_variant_id'],
                    'product_id'         => $data['product_id'],
                    'variant_name'       => $data['variant_name'],
                    'sort_order'         => $data['sort_order'],
                    'active_status'      => $data['active_status'],
                ];

                // Update product variant
                $productVariant->clearQuery();
                $productVariant->update($variantData);

                // loop productOptionGroups and create group records
                if (!empty($data['productOptionGroups'])) {
                    $unique = [];
                    foreach ($data['productOptionGroups'] as $group) {
                        $productOptionGroupId = intval($group['product_option_group_id'] ?? 0);
                        $itemGroup = [
                            'product_id'              => $data['product_id'],
                            'product_variant_id'      => $data['product_variant_id'],
                            'option_group_name'       => $group['option_group_name'],
                            'sort_order'              => $group['sort_order'] ?? 0,
                            'active_status'           => $group['active_status'] ?? 1,
                        ];

                        if ($productOptionGroupId < 0) {
                            $unique[] = $group['product_id'] . '-' . $group['product_variant_id'] . '-' . $group['option_group_name'];
                        } else {
                            $itemGroup['product_option_group_id'] = $productOptionGroupId;
                        }
                        // loop productOptions for this group
                        if (!empty($group['productOptions'])) {
                            foreach ($group['productOptions'] as $option) {
                                $productOptions[] = [
                                    'option_group_name'       => $group['option_group_name'],
                                    'product_option_id'       => isset($option['product_option_id']) ? intval($option['product_option_id']) : 0,
                                    'product_id'              => $data['product_id'],
                                    'product_variant_id'      => $data['product_variant_id'],
                                    'product_option_group_id' => isset($group['product_option_group_id']) ? intval($group['product_option_group_id']) : 0,
                                    'option_name'             => $option['option_name'],
                                ];
                            }
                        }
                    }

                    // insert productOptionGroups
                    $this->productOptionGroup->upsert($productOptionGroup, ['product_id', 'product_variant_id', 'option_group_name']);
                    $productOptionGroupIds = $this->getProductOptionGroupIds($unique);
                    $productOptions = array_map(function ($item) use ($productOptionGroupIds) {
                        $key = $item['product_id'] . '-' . $item['product_variant_id'] . '-' . $item['option_group_name'];
                        $productOptionGroupId = isset($productOptionGroupIds[$key]) ? intval($productOptionGroupIds[$key]) : $item['product_option_group_id'];
                        $productOptionId = isset($item['product_option_id']) ? intval($item['product_option_id']) : $item['product_option_id'];
                        $options = [
                            'product_id'              => $item['product_id'],
                            'product_variant_id'      => $item['product_variant_id'],
                            'product_option_group_id' => $productOptionGroupId,
                            'option_name'             => $item['option_name'],
                        ];
                        // if ($productOptionId > 0) {
                        //     $options['product_option_id'] = $productOptionId;
                        // }
                        return $options;
                    }, $productOptions);
                    // insert productOptions
                    $this->productOption->upsert($productOptions, ['product_id', 'product_variant_id', 'product_option_group_id', 'option_name']);
                }
            }

            $this->db->commit();
            return (array) $this->getVariantById($id);
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to update variant: " . $e->getMessage());
        }
    }

    private function getProductOptionGroupIds(array $unique): array
    {
        $response = [];
        $unique = array_unique($unique);
        if (empty($unique)) {
            return $response;
        }
        $this->productOptionGroup->clearQuery();
        $optionGroups = $this->productOptionGroup
            ->whereIn(
                'CONCAT(`product_option_group`.`product_id`, "-", `product_option_group`.`product_variant_id`, "-", `product_option_group`.`option_group_name`)',
                $unique
            )
            ->select(['product_option_group.*'])
            ->limit(0)->findAll(false);
        foreach ($optionGroups as $optionGroup) {
            $response[$optionGroup['product_id'] . '-' . $optionGroup['product_variant_id'] . '-' . $optionGroup['option_group_name']] = $optionGroup['product_option_group_id'];
        }
        return $response;
    }

    // delete product_variant
    public function deleteVariant(int $product_variant_id): bool
    {
        try {
            $this->db->beginTransaction();

            $this->model->clearQuery();
            $product_variant = $this->model->where('product_variant_id', '=', $product_variant_id)->first();
            if (!$product_variant) {
                return false;
            }
            $product_variant->update(['deleted_at' => date('Y-m-d H:i:s')]);
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to delete options: " . $e->getMessage());
        }
    }

    // import data
    public function importVariants(string $csv_file): array
    {
        $reader = Reader::createFromPath($csv_file, 'r');
        $reader->setHeaderOffset(0);
        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }
        $defaultFields = $this->getDefaultFields($headers);
        $requiredFields = [
            'variant_name',
            'product_id'
        ];
        $records = $reader->getRecords();

        $validData = 0;
        $inserted = [];
        $insertedData = [];
        $updated = [];
        $updatedData = [];
        $duplicated = [];
        $invalid = [];
        $processed = [];
        $productMap = $this->product->select(['product_id', 'product_code'])->limit(0)->findAll(false);
        $productMap = array_column($productMap, 'product_id', 'product_code');

        $existingVariantMapRaw = $this->model->select(['product_variant_id', 'product_id', 'variant_name'])->limit(0)->findAll(false);
        // Build a map: [product_id][variant_name] => product_variant_id
        $existingVariantMap = [];
        $existingVariantIds = [];
        foreach ($existingVariantMapRaw as $row) {
            $existingVariantMap[strtolower($row['product_id'] . '-' . $row['variant_name'])] = $row['product_variant_id'];
            $existingVariantIds[] = $row['product_variant_id'];
        }


        $existingDataMaps = [
            'productMap' => $productMap,
            'variantMap' => $existingVariantMap,
            'variantIds' => $existingVariantIds,
        ];

        foreach ($records as $offset => $record) {
            try {
                $record = $this->prepareRecord($record, $defaultFields);
                $validator = new ProductVariantDataValidation($record, $requiredFields, array_keys($defaultFields), $existingDataMaps);
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
                $validData++;
                $record['row'] = $offset + 1;
                if ($validated->isExistingData) {
                    $updated[] = (array) $validated->variant;
                    $updatedData[] = $record;
                } else {
                    $inserted[] = (array) $validated->variant;
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
            if (count($inserted) > 0) {
                $this->model->upsert($inserted, ['product_id', 'variant_name']);
            }
            if (count($updated) > 0) {
                $this->model->upsert($updated, ['product_id', 'variant_name']);
            }
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update variants: " . $e->getMessage());
        }

        return [
            'success' => true,
            'total_records' => iterator_count($records),
            'valid_records' => $validData,
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
                    ? round(($validData / iterator_count($records)) * 100, 2) . '%'
                    : '0%',
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

        $defaultFields['sort_order'] = 1;
        $defaultFields['active_status'] = 1;
        $defaultFields['created_at'] = date('Y-m-d H:i:s');
        $defaultFields['updated_at'] = date('Y-m-d H:i:s');

        return $defaultFields;
    }

    private function prepareRecord(array $record, array $defaultFields): array
    {
        return isset($record['product_variant_id']) && $record['product_variant_id'] ? $record : array_merge($defaultFields, $record);
    }

    /**
     * Example return structure:
     * [
     *     {
     *         "product_variant_id": 1,
     *         "product_id": 1,
     *         "variant_name": "Extra High",
     *         "sort_order": 5,
     *         "active_status": true,
     *         "variant_description": "Lorem ipsum dolor sit amet.",
     *         "variant_code": "EH001",
     *         "productVariantImage": [...],
     *         "productOptionGroups": [
     *             {
     *                 "product_option_group_id": 1,
     *                 "option_group_name": "Locker Finish",
     *                 "optionGroupImage": [...],
     *                 "productOptions": [...]
     *             }
     *         ]
     *     }
     * ]
     */

    // search variants by name and product id
    public function searchVariants(int $product_id, string|null $name = null): array
    {
        $variants = $this->model
            ->where('product_id', '=', $product_id)
            ->whereNull('deleted_at')
            ->orderBy('product_variant_id', 'DESC')
            ->limit(50);
        if ($name) {
            $variants->where('variant_name', 'like', '%' . $name . '%');
        }
        $variants = $variants->findAll(false);

        $variantIds = array_column($variants, 'product_variant_id');


        $productOptionGroups = $this->productOptionGroup
            ->whereIn('product_variant_id', $variantIds)
            ->where('product_id', '=', $product_id)
            ->limit(0)
            ->findAll(false);

        $productOptionGroupIds = array_column($productOptionGroups, 'product_option_group_id');
        $productOptions = $this->productOption
            ->whereIn('product_option_group_id', $productOptionGroupIds)
            ->where('product_id', '=', $product_id)
            ->join('`type`', 'product_option.type_id', '=', '`type`.type_id')
            ->select(['product_option.*', '`type`.type'])
            ->limit(0)->findAll(false);
        $formatedProductOptions = [];
        foreach ($productOptions as $productOption) {
            $formatedProductOptions[$productOption['product_option_group_id']][] = $productOption;
        }

        $formattedProductOptionGroups = [];
        foreach ($productOptionGroups as $productOptionGroup) {
            $productOptionGroup['productOptions'] = $formatedProductOptions[$productOptionGroup['product_option_group_id']] ?? [];
            $formattedProductOptionGroups[$productOptionGroup['product_variant_id']][] = $productOptionGroup;
        }
        foreach ($variants as &$variant) {
            if(isset($variant['image']) && !empty($variant['image'])){
                $variant['image'] = json_decode($variant['image'], true);
            }
            $variant['productOptionGroups'] = $formattedProductOptionGroups[$variant['product_variant_id']] ?? [];
        }

        return $variants;
    }

    public function searchItemOptionVariants(string $name, int $product_id): array
    {
        $this->model->clearQuery();
        $variants = $this->model
            ->join('variant_item', 'product_variant.product_variant_id', '=', 'variant_item.product_variant_id')
            ->where('product_id', '=', $product_id)
            ->whereNull('deleted_at')
            ->orderBy('product_variant_id', 'DESC')
            ->limit(50);
        if ($name) {
            $variants->where('variant_name', 'like', '%' . $name . '%');
        }
        $variants = $variants->findAll(false);

        return $variants;
    }
    
    public function searchVariantItems(int $product_id, string|null $name = null): array
    {
        $variants = $this->model
            ->where('product_id', '=', $product_id)
            ->whereNull('deleted_at')
            ->orderBy('product_variant_id', 'DESC')
            ->limit(50);
        if ($name) {
            $variants->where('variant_name', 'like', '%' . $name . '%');
        }
        $variants = $variants->findAll(false);

        $variantIds = array_column($variants, 'product_variant_id');

        $productOptionGroups = $this->productOptionGroup
            ->whereIn('product_variant_id', $variantIds)
            ->where('product_id', '=', $product_id)
            ->select(['product_option_group.*', 'product_option_group.product_option_group_id as item_option_group_id'])
            ->limit(0)
            ->findAll(false);

        $productOptionGroupIds = array_column($productOptionGroups, 'product_option_group_id');
        $productOptions = $this->productOption
            ->whereIn('product_option_group_id', $productOptionGroupIds)
            ->where('product_id', '=', $product_id)
            ->join('`type`', 'product_option.type_id', '=', '`type`.type_id')
            ->select(['product_option.*', '`type`.type'])
            ->limit(0)->findAll(false);
        $formatedProductOptions = [];
        foreach ($productOptions as $productOption) {
            $formatedProductOptions[$productOption['product_option_group_id']][] = $productOption;
        }

        $formattedProductOptionGroups = [];
        foreach ($productOptionGroups as $productOptionGroup) {
            // Get only the first product option (if any) from the group
            $productOptions = $formatedProductOptions[$productOptionGroup['product_option_group_id']] ?? [];
            $productOptionGroup['productOptions'] = !empty($productOptions) ? [reset($productOptions)] : [];
            $formattedProductOptionGroups[$productOptionGroup['product_variant_id']][] = $productOptionGroup;
        }
        foreach ($variants as &$variant) {
            $variant['productOptionGroups'] = $formattedProductOptionGroups[$variant['product_variant_id']] ?? [];
        }

        return $variants;
    }

    public function getVariantsByProductId(int $product_id): array
    {
        $this->model->clearQuery();
        $variants = $this->model
            // ->whereIn('product_variant_id', [761,762,763,764,765,766,767, 768,769,770,771]);
            ->where('product_id', '=', $product_id)
            ->whereNull('deleted_at')
            ->orderBy('product_variant_id', 'DESC')
            ->limit(0);
        $variants = $variants->findAll(false);

        // $lastQuery = $this->model->getQueryString();
        // echo $lastQuery;
        // exit;

        $variantIds = array_column($variants, 'product_variant_id');

        $variantsItems = $this->itemOption
            ->join('item', 'item.item_id', '=', 'item_option.item_id')
            ->whereIn('item_option.product_variant_id', $variantIds)
            ->where('item_option.product_id', '=', $product_id)
            ->select(['item_option.*', 'item.item_code', 'item.description', 'item.item_id', 'item.km_item_id', 'item.quote_image', 'item.is_default', 'item.display_width', 'item.display_height', 'item.display_depth', 'item.dimensions_image'])
            ->limit(0)
            ->orderBy('item.is_default', 'DESC')
            ->findAll(false);
        $itemOptions = []; 
        // $optionImages = [];
 
        foreach ($variantsItems as $variantItem) {
            $uniqueKey = $variantItem['product_variant_id'] . '_' . $variantItem['product_option_group_id'] . '_' . $variantItem['product_option_id'];
            // image format
            $dimensionsImage = '';
            if(isset($variantItem['dimensions_image']) && !empty($variantItem['dimensions_image'])){
                $dimensionsImage = json_decode($variantItem['dimensions_image'], true);
                $dimensionsImage = $dimensionsImage[0]['objectURL'] ?? '';
            }

            $optionImage = '';
            if(isset($variantItem['option_image']) && !empty($variantItem['option_image'])){
                $optionImage = json_decode($variantItem['option_image'], true);
                $optionImage = $optionImage[0]['objectURL'] ?? '';
            }
            $variantItem['option_image'] = $optionImage;
            // $optionImages[$uniqueKey] = $optionImage;

            $itemOptions[$variantItem['product_variant_id']][$variantItem['item_code']]['item_code'] = $variantItem['item_code'];
            $itemOptions[$variantItem['product_variant_id']][$variantItem['item_code']]['description'] = $variantItem['description'];
            $itemOptions[$variantItem['product_variant_id']][$variantItem['item_code']]['quote_image'] = $variantItem['quote_image'];
            $itemOptions[$variantItem['product_variant_id']][$variantItem['item_code']]['is_default'] = $variantItem['is_default'];
            $itemOptions[$variantItem['product_variant_id']][$variantItem['item_code']]['display_width'] = $variantItem['display_width'];
            $itemOptions[$variantItem['product_variant_id']][$variantItem['item_code']]['display_height'] = $variantItem['display_height'];
            $itemOptions[$variantItem['product_variant_id']][$variantItem['item_code']]['display_depth'] = $variantItem['display_depth'];
            $itemOptions[$variantItem['product_variant_id']][$variantItem['item_code']]['dimensions_image'] = $dimensionsImage;
            $itemOptions[$variantItem['product_variant_id']][$variantItem['item_code']]['item_id'] = $variantItem['item_id'];
            $itemOptions[$variantItem['product_variant_id']][$variantItem['item_code']]['km_item_id'] = $variantItem['km_item_id'];
            $itemOptions[$variantItem['product_variant_id']][$variantItem['item_code']]['options'][] = $variantItem;
        }

        $productOptionGroups = $this->productOptionGroup
            ->whereIn('product_variant_id', $variantIds)
            ->where('product_id', '=', $product_id)
            ->limit(0)
            ->findAll(false);

        $productOptionGroupIds = array_column($productOptionGroups, 'product_option_group_id');
        $productOptions = $this->productOption
            ->join('`type`', 'product_option.type_id', '=', '`type`.type_id')
            ->select(['product_option.*', '`type`.type'])
            ->whereIn('product_option_group_id', $productOptionGroupIds)
            ->where('product_id', '=', $product_id)
            ->orderBy('option_name', 'DESC')
            ->limit(0)->findAll(false);
        $formatedProductOptions = [];
        foreach ($productOptions as $productOption) {
            if(isset($productOption['option_image']) && !empty($productOption['option_image'])){
                $productOption['option_image'] = json_decode($productOption['option_image'], true);
                $productOption['option_image'] = $productOption['option_image'][0]['objectURL'] ?? '';
            }else{
                $productOption['option_image'] = '';
            }
            $formatedProductOptions[$productOption['product_option_group_id']][] = $productOption;
        }

        $formattedProductOptionGroups = [];
        foreach ($productOptionGroups as $productOptionGroup) {
            $productOptionGroup['productOptions'] = $formatedProductOptions[$productOptionGroup['product_option_group_id']] ?? [];
            $formattedProductOptionGroups[$productOptionGroup['product_variant_id']][] = $productOptionGroup;
        }
        foreach ($variants as &$variant) {
            $variant['productOptionGroups'] = $formattedProductOptionGroups[$variant['product_variant_id']] ?? [];
            $variant['items'] = $itemOptions[$variant['product_variant_id']] ?? [];
        }

        // get accessories by product id
        // $variant['accessories'] = $this->productAccessoriesRepository->getAccessoriesByProductId($product_id);
        return $variants;
    }

    public function getDefaultItemByProductId(int $product_id): array
    {
        $this->model->clearQuery();

        // $variants = $this->model
        //     ->where('product_id', '=', $product_id)
        //     ->whereNull('deleted_at')
        //     ->orderBy('product_variant_id', 'DESC')
        //     ->where('is_default', '=', 1)
        //     ->limit(0)
        //     ->findAll(false);

        // if (empty($variants)) {
        //     return [];
        // }

        // $variantIds = array_column($variants, 'product_variant_id');

        $variantsItems = $this->item
    
        ->where('item.product_id', '=', $product_id)
        ->where('item.is_default', '=', 1)
        ->select([
            'item.item_code',
            'item.description',
            'item.item_id',
            'item.km_item_id',
            'item.quote_image',
            'item.is_default',
            'item.display_width',
            'item.display_height',
            'item.display_depth',
            'item.dimensions_image'
        ])
        ->limit(0)
        ->orderBy('item.is_default', 'DESC');

        if(!empty($variantIds)){
            $variantsItems->whereIn('item_option.product_variant_id', $variantIds);
        }
        $variantsItems = $variantsItems->findAll(false);

        foreach ($variantsItems as $variantItem) {

            $dimensionsImage = '';

            if (!empty($variantItem['dimensions_image'])) {
                $dimensionsImage = json_decode($variantItem['dimensions_image'], true);

                if (json_last_error() === JSON_ERROR_NONE) {
                    $dimensionsImage = $dimensionsImage[0]['objectURL'] ?? '';
                } else {
                    $dimensionsImage = '';
                }
            }

            return [
                'item_code'        => $variantItem['item_code'],
                'is_default'       => $variantItem['is_default'],
                'display_width'    => $variantItem['display_width'],
                'display_height'   => $variantItem['display_height'],
                'display_depth'    => $variantItem['display_depth'],
                'dimensions_image' => $dimensionsImage,
            ];
        }

        return [];
    }

    public function getProductVariantById(int $product_variant_id): array
    {
        $variant = $this->model->where('product_variant_id', '=', $product_variant_id)->first();
        if (!$variant) {
            return [];
        }
        $variant = (array) $variant->data;
        $productOptionGroups = $this->productOptionGroup
            ->whereIn('product_variant_id', [$product_variant_id])
            // ->where('product_id', '=', $product_id)
            ->limit(0)
            ->findAll(false);

        $productOptionGroupIds = array_column($productOptionGroups, 'product_option_group_id');
        $productOptions = $this->productOption
            ->whereIn('product_option_group_id', $productOptionGroupIds)
            // ->where('product_id', '=', $product_id)
            ->limit(0)->findAll(false);
        $formatedProductOptions = [];
        foreach ($productOptions as $productOption) {
            $formatedProductOptions[$productOption['product_option_group_id']][] = $productOption;
        }

        $formattedProductOptionGroups = [];
        foreach ($productOptionGroups as $productOptionGroup) {
            $productOptionGroup['productOptions'] = $formatedProductOptions[$productOptionGroup['product_option_group_id']] ?? [];
            $formattedProductOptionGroups[$productOptionGroup['product_variant_id']][] = $productOptionGroup;
        }

        $variant['productOptionGroups'] = $formattedProductOptionGroups[$variant['product_variant_id']] ?? [];

        return (array) $variant;
    }
    public function uploadVariantImage(array $data, int $product_variant_id): bool
    {
        $variant = $this->model->where('product_variant_id', '=', $product_variant_id)->first();
        if (!$variant) {
            return false;
        }

        $imageData = [];
        $config = app('config');
        $imageServer = rtrim($config['APP_URL'], '/');

        foreach ($data as $image) {
            $img = [
                'product_variant_id' => $product_variant_id,
                'file' => [
                    'name' => $image['file']['name'] ?? $image['name'],
                    'size' => $image['file']['size'] ?? $image['size'],
                    'type' => $image['file']['type'] ?? $image['type'],
                    'error' => $image['file']['error'] ?? 0,
                    'tmp_name' => $image['file']['tmp_name'] ?? '',
                    'full_path' => $image['file']['full_path'] ?? $image['name'],
                ],
                'name' => $image['name'],
                'size' => $image['size'],
                'type' => $image['type'],
                'image' => $image['image'],
                'status' => [
                    'name' => $image['status']['name'] ?? 'Uploaded',
                    'severity' => $image['status']['severity'] ?? 'success',
                ],
                'media_id' => null,
                'objectURL' => $imageServer . $image['objectURL'],
                'created_at' => '',
                'description' => $image['description'] ?? '',
            ];

            $imageData[] = $img;
        }

        $this->db->beginTransaction();
        try {
            // Convert array to JSON before saving
            $variant->update(['image' => json_encode($imageData)]);
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }


    public function uploadProductOptionImage(array $data, int $product_option_id): bool
    {
        $productOption = $this->productOption->where('product_option_id', '=', $product_option_id)->first();

        if (!$productOption) {
            return false;
        }

        $imageData = [];
        $config = app('config');
        $imageServer = rtrim($config['APP_URL'], '/');

        foreach ($data as $image) {
            $img = [
                'product_option_id' => $product_option_id,
                'file' => [
                    'name' => $image['file']['name'] ?? $image['name'],
                    'size' => $image['file']['size'] ?? $image['size'],
                    'type' => $image['file']['type'] ?? $image['type'],
                    'error' => $image['file']['error'] ?? 0,
                    'tmp_name' => $image['file']['tmp_name'] ?? '',
                    'full_path' => $image['file']['full_path'] ?? $image['name'],
                ],
                'name' => $image['name'],
                'size' => $image['size'],
                'type' => $image['type'],
                'image' => $image['image'],
                'status' => [
                    'name' => $image['status']['name'] ?? 'Uploaded',
                    'severity' => $image['status']['severity'] ?? 'success',
                ],
                'media_id' => null,
                'objectURL' => $imageServer . $image['objectURL'],
                'created_at' => '',
                'description' => $image['description'] ?? '',
            ];

            $imageData[] = $img;
        }

        $this->db->beginTransaction();
        try {
            // Convert array to JSON before saving
            $productOption->update(['option_image' => json_encode($imageData)]);
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function deleteVariantImage(int $variant_id): bool
    {
        $variant = $this->model->where('product_variant_id', '=', $variant_id)->first();
        if (!$variant) {
            return false;
        }
        $variant->update(['image' => null]);
        return true;
    }

    public function deleteVariantOptionImage(int $product_variant_option_id): bool
    {
        $variant = $this->productOption->where('product_option_id', '=', $product_variant_option_id)->first();
        if (!$variant) {
            return false;
        }
        $variant->update(['option_image' => null]);
        return true;
    }

    public function getKmItemIdsByProductId(array $product_ids): array
    {
        $this->model->clearQuery();
        $variants = $this->model
            // ->whereIn('product_variant_id', [761,762,763,764,765,766,767, 768,769,770,771]);
            ->whereIn('product_id', $product_ids)
            ->whereNull('deleted_at')
            ->orderBy('product_variant_id', 'DESC')
            ->limit(0);
        $variants = $variants->findAll(false);

        // $lastQuery = $this->model->getQueryString();
        // echo $lastQuery;
        // exit;

        $variantIds = array_column($variants, 'product_variant_id');

        $variantsItems = $this->itemOption
            ->join('item', 'item.item_id', '=', 'item_option.item_id')
            ->whereIn('item_option.product_variant_id', $variantIds)
            ->whereIn('item_option.product_id', $product_ids)
            ->select(['item_option.*', 'item.item_code', 'item.description', 'item.item_id', 'item.km_item_id', 'item.quote_image', 'item.is_default', 'item.display_width', 'item.display_height', 'item.display_depth', 'item.dimensions_image'])
            ->limit(0)
            ->findAll(false);
        $itemOptions = []; 
        // $optionImages = [];
 
        foreach ($variantsItems as $variantItem) {
            $uniqueKey = $variantItem['product_variant_id'] . '_' . $variantItem['product_option_group_id'] . '_' . $variantItem['product_option_id'];
            // image format
            $dimensionsImage = '';
            if(isset($variantItem['dimensions_image']) && !empty($variantItem['dimensions_image'])){
                $dimensionsImage = json_decode($variantItem['dimensions_image'], true);
                $dimensionsImage = $dimensionsImage[0]['objectURL'] ?? '';
            }

            $optionImage = '';
            if(isset($variantItem['option_image']) && !empty($variantItem['option_image'])){
                $optionImage = json_decode($variantItem['option_image'], true);
                $optionImage = $optionImage[0]['objectURL'] ?? '';
            }
            $variantItem['option_image'] = $optionImage;
            $itemOptions[$variantItem['product_variant_id']][$variantItem['item_code']]['item_code'] = $variantItem['item_code'];
            $itemOptions[$variantItem['product_variant_id']][$variantItem['item_code']]['description'] = $variantItem['description'];
            $itemOptions[$variantItem['product_variant_id']][$variantItem['item_code']]['quote_image'] = $variantItem['quote_image'];
            $itemOptions[$variantItem['product_variant_id']][$variantItem['item_code']]['is_default'] = $variantItem['is_default'];
            $itemOptions[$variantItem['product_variant_id']][$variantItem['item_code']]['display_width'] = $variantItem['display_width'];
            $itemOptions[$variantItem['product_variant_id']][$variantItem['item_code']]['display_height'] = $variantItem['display_height'];
            $itemOptions[$variantItem['product_variant_id']][$variantItem['item_code']]['display_depth'] = $variantItem['display_depth'];
            $itemOptions[$variantItem['product_variant_id']][$variantItem['item_code']]['dimensions_image'] = $dimensionsImage;
            $itemOptions[$variantItem['product_variant_id']][$variantItem['item_code']]['item_id'] = $variantItem['item_id'];
            $itemOptions[$variantItem['product_variant_id']][$variantItem['item_code']]['km_item_id'] = $variantItem['km_item_id'];
            $itemOptions[$variantItem['product_variant_id']][$variantItem['item_code']]['options'][] = $variantItem;
        }

        foreach ($variants as &$variant) {
            $variant['items'] = $itemOptions[$variant['product_variant_id']] ?? [];
        }

        return $variants;
    }

}
