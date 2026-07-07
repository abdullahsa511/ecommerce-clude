<?php

declare(strict_types=1);

namespace App\Core\Repositories\Item;

use PDO;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Models\Item\Item;
use App\Core\Models\Item\ItemOption;
use App\Core\Models\Product\Product;
use App\Core\Models\Product\ProductVariant;
use App\Core\Models\ProductOptionGroup\ProductOptionGroup;
use App\Core\Models\Product\ProductOption;
use App\Core\Models\Type\Type;
use App\Core\Validation\ItemOptionDataValidation;
use DateTime;
use App\Core\Exceptions\ValidationException;
use League\Csv\Reader;
use Exception;

class ItemOptionRepository extends BaseRepository implements ItemOptionRepositoryInterface
{
    private Product $product;
    private ProductVariant $productVariant;
    private ProductOptionGroup $productOptionGroup;
    private ProductOption $productOption;
    private Type $type;
    private Item $item;

    public function __construct(
        PDO $db,
        Product $product,
        ProductVariant $productVariant,
        ProductOptionGroup $productOptionGroup,
        ProductOption $productOption,
        Type $type,
        Item $item
    ) {
        parent::__construct($db, 'item_option', ItemOption::class);
        $this->product = $product;
        $this->product->setDb($db);
        $this->productVariant = $productVariant;
        $this->productVariant->setDb($db);
        $this->productOptionGroup = $productOptionGroup;
        $this->productOptionGroup->setDb($db);
        $this->productOption = $productOption;
        $this->productOption->setDb($db);
        $this->type = $type;
        $this->type->setDb($db);
        $this->item = $item;
        $this->item->setDb($db);
    }

    /**
     * Get a single product with all its related data
     */
    public function getItemOptions(): array
    {
        $query = $this->model
            ->join('item', 'item.item_id', '=', 'item_option.item_id')
            ->join('product_option', 'product_option.product_option_id', '=', 'item_option.product_option_id')
            ->join('product', 'product.product_id', '=', 'item.product_id')
            ->join('product_variant', 'product_variant.product_variant_id', '=', 'item_option.product_variant_id')
            ->join('product_option_group', 'product_option_group.product_option_group_id', '=', 'item_option.product_option_group_id')
            ->join('type', 'type.type_id', '=', 'item_option.type_id')
            ->select([
                'item_option.*',
                'item.item_code',
                'product.product_code',
                'product_variant.variant_name',
                'product_option_group.option_group_name',
                'item_option.option_name',
                'product_option.option_name as product_option_name',
                'type.type',
            ])
            ->limit(0);
        $itemOptions = $query->findAll(false);
        return $itemOptions;
    }

    public function getItemOptionById(int $itemOptionId): array
    {
        $query = $this->model
            ->join('item', 'item.item_id', '=', 'item_option.item_id')
            ->join('product_option', 'product_option.product_option_id', '=', 'item_option.product_option_id')
            ->join('product', 'product.product_id', '=', 'item.product_id')
            ->join('product_variant', 'product_variant.product_variant_id', '=', 'item_option.product_variant_id')
            ->join('product_option_group', 'product_option_group.product_option_group_id', '=', 'item_option.product_option_group_id')
            ->join('type', 'type.type_id', '=', 'item_option.type_id')
            ->select([
                'item_option.*',
                'item.item_code',
                'product.product_code',
                'product_variant.variant_name',
                'product_option_group.option_group_name',
                'item_option.option_name',
                'product_option.option_name as product_option_name',
                'type.type',
            ])
            ->limit(1);

        $query->where('item_option.item_option_id', '=', $itemOptionId);
        $itemOptions = $query->findAll(false);

        if (empty($itemOptions)) {
            return [];
        }

        // Work on first row
        $item = $itemOptions[0];

        // Decode image JSON
        $item['image'] = !empty($item['option_image'])
            ? json_decode($item['option_image'], true)
            : [];

        return $item;
    }

    public function getItemOptionById_backup(int $itemOptionId): ?array
    {
        $this->model->clearQuery();
        $itemOption = $this->model
        ->join('product_option', 'product_option.product_option_id', '=', 'item_option.product_option_id')
        ->join('product', 'product.product_id', '=', 'item_option.product_id')
        ->join('product_variant', 'product_variant.product_variant_id', '=', 'item_option.product_variant_id')
        ->join('product_option_group', 'product_option_group.product_option_group_id', '=', 'item_option.product_option_group_id')
        ->join('type', 'type.type_id', '=', 'item_option.type_id')
        ->join('item', 'item.item_id', '=', 'item_option.item_id')
        ->where('item_option.item_option_id', '=', $itemOptionId)
        ->select([
        'item_option.*',
        'product.product_code',
        'product_variant.variant_name',
        'product_option_group.option_group_name',
        'product_option.option_name as product_option_name',
        'type.type',
        'item.item_code',
        ])
        ->limit(0)
        ->first();

        // if (!$itemOption) {
        //     return null;
        // }
        return (array) $itemOption->data;
    }

    public function createItemOption(array $itemOptionData): ?array
    {
        try {
            $this->db->beginTransaction();
            // check if item option limit count is reached
            $itemOptionLimitCount = $this->checkItemOptionLimitCount($itemOptionData);
            if ($itemOptionLimitCount) {
                throw new ValidationException([
                    'global_message' => ['Item option limit count reached'],
                ]);
            }
            // check if item option with the same product variant, option group combined already exists
            $itemOptionUnique = $this->getItemOptionUnique($itemOptionData);
            if ($itemOptionUnique) {
                throw new ValidationException([
                    'global_message' => ['Item option with the same product variant, option group combined already exists.'],
                    'product_variant_id' => [''],
                    'product_option_group_id' => [''],
                    'name' => [''],
                ]);
            }
            // check if product option already exists
            $productOption = $this->checkProductOptionAndUpdate($itemOptionData);
            $insertData = [
                'product_option_id' => $productOption['product_option_id'],
                'item_id' => $itemOptionData['item_id'],
                'product_id' => $itemOptionData['product_id'],
                'product_variant_id' => $itemOptionData['product_variant_id'],
                'product_option_group_id' => $itemOptionData['product_option_group_id'],
                'option_name' => $itemOptionData['option_name'],    
                'type_id' => $itemOptionData['type_id'],
                'required' => isset($itemOptionData['required']) ? $itemOptionData['required'] : 0,
                'price' => isset($itemOptionData['price']) ? $itemOptionData['price'] : 0,
                'sort_order' => isset($itemOptionData['sort_order']) ? $itemOptionData['sort_order'] : 0,
                'option_description' => isset($itemOptionData['option_description']) ? $itemOptionData['option_description'] : null,
                'meta_description' => isset($itemOptionData['meta_description']) ? $itemOptionData['meta_description'] : null,
                'active_status' => isset($itemOptionData['active_status']) ? $itemOptionData['active_status'] : 1,
            ];


            $this->model->clearQuery();
            $itemOption = $this->model->create($insertData);
            $this->db->commit();
            return (array) $itemOption->data;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Failed to create item option: " . $e->getMessage());
        }
    }

    public function updateItemOption(int $id, array $itemOptionData): ?array
    {
        $itemOption = $this->model->where('item_option_id', '=', $id)->first();
        if (!$itemOption) {
            throw new ValidationException([
                'global_message' => ['Item option not found'],
            ]);
        }

        $itemOptionUnique = $this->getItemOptionUnique($itemOptionData, $id);
        if ($itemOptionUnique) {
            throw new ValidationException([
                'global_message' => ['Item option with the same product variant, option group combined already exists.'],
                'product_variant_id' => [''],
                'product_option_group_id' => [''],
                'name' => [''],
            ]);
        }
        $productOption = $this->checkProductOptionAndUpdate($itemOptionData);
        $updateData = [
            'item_option_id' => $id,
            'product_option_id' => $productOption['product_option_id'],
            'item_id' => $itemOptionData['item_id'],
            'product_id' => $itemOptionData['product_id'],
            'product_variant_id' => $itemOptionData['product_variant_id'],
            'product_option_group_id' => $itemOptionData['product_option_group_id'],
            'option_name' => $itemOptionData['option_name'],    
            'type_id' => $itemOptionData['type_id'],
            'required' => isset($itemOptionData['required']) ? $itemOptionData['required'] : 0,
            'price' => isset($itemOptionData['price']) ? $itemOptionData['price'] : 0,
            'sort_order' => isset($itemOptionData['sort_order']) ? $itemOptionData['sort_order'] : 0,
            'hex_color' => isset($itemOptionData['hex_color']) ? $itemOptionData['hex_color'] : null,
            'option_description' => isset($itemOptionData['option_description']) ? $itemOptionData['option_description'] : null,
            'meta_description' => isset($itemOptionData['meta_description']) ? $itemOptionData['meta_description'] : null,
            'active_status' => isset($itemOptionData['active_status']) ? $itemOptionData['active_status'] : 1,
        ];

        try {
            $this->db->beginTransaction();
            $itemOption->clearQuery();
            $itemOption->update($updateData);
            $this->db->commit();
            return (array) $this->getItemOptionById($id);
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Failed to create item option: " . $e->getMessage());
        }
    }

    private function checkProductOptionAndUpdate(array $itemOptionData): array
    {
        $this->model->clearQuery();
        $productOption = $this->productOption
        ->where('product_id', '=', $itemOptionData['product_id'])
        ->where('product_variant_id', '=', $itemOptionData['product_variant_id'])
        ->where('product_option_group_id', '=', $itemOptionData['product_option_group_id'])
        ->where('option_name', '=', $itemOptionData['option_name'])
        ->first();

        if (!$productOption) {
            $data = [
                'product_id' => $itemOptionData['product_id'],
                'product_variant_id' => $itemOptionData['product_variant_id'],
                'product_option_group_id' => $itemOptionData['product_option_group_id'],
                'type_id' => $itemOptionData['type_id'],
                'option_name' => $itemOptionData['option_name'],
                'price' => isset($itemOptionData['price']) ? $itemOptionData['price'] : 0,
                'sort_order' => isset($itemOptionData['sort_order']) ? $itemOptionData['sort_order'] : 0,
                'active_status' => isset($itemOptionData['active_status']) ? $itemOptionData['active_status'] : 1,
            ];
            $productOption = $this->productOption->create($data);
            $itemOptionData['product_option_id'] = $productOption->product_option_id;
        } else {
            $itemOptionData['product_option_id'] = $productOption->product_option_id;
        }
        return ['product_option_id' => $itemOptionData['product_option_id']];
    }

    public function deleteItemOption(int $id): bool
    {
        $itemOption = $this->model->where('item_option_id', '=', $id)->first();
        if (!$itemOption) {
            return false;
        }
        try {
            $this->db->beginTransaction();
            $itemOption->clearQuery();
            $itemOption->delete($id);
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Failed to delete item option: " . $e->getMessage());
        }
    }

    public function deleteItemOptionGroup(array $ids): bool
    {
        $this->model->clearQuery();
        $itemOption = $this->model->where('item_id', '=', $ids['item_id'])
        ->where('product_id', '=', $ids['product_id'])
        ->where('product_variant_id', '=', $ids['product_variant_id'])
        ->where('product_option_group_id', '=', $ids['product_option_group_id'])
        ->first();

        if (!$itemOption) {
            return false;
        }
        try {
            $this->db->beginTransaction();
            $itemOption->delete($itemOption->item_option_id);
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Failed to delete item option group: " . $e->getMessage());
        }
    }

    public function importItemOptions(string $csv_file): array
    {
        // Create a CSV reader from the file path
        $reader = Reader::createFromPath($csv_file, 'r');
        // Use the first row as the header keys for each record
        $reader->setHeaderOffset(0);
        // Fetch the headers from the CSV file
        $headers = $reader->getHeader();
        // Validate header presence (must exist for proper mapping)
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }
        $records = $reader->getRecords();

        $valid = [];
        $showValid = [];
        $duplicated = [];
        $invalid = [];
        $updated = [];
        $showUpdated = [];
        $processed = [];
        $requiredFields = ['item_id', 'product_variant_id', 'product_option_group_id', 'product_id', 'option_id', 'type_id', 'option_name'];

        // Get default field values to merge with incoming records
        $defaultFields = $this->getDefaultFields($headers);
        $existingDataMaps = $this->getExistingDataMaps($records);

        foreach ($records as $offset => $record) {
            try {
                $record = $this->prepareRecord($record, $defaultFields);
                $validator = new ItemOptionDataValidation($record, $requiredFields, array_keys($defaultFields), $existingDataMaps);
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

                $processed[] = $unique;
                if ($validator->isExistingData) {
                    $updated[] = $validated->toArray();
                    $showUpdated[] = $record;
                } else {
                    $valid[] = $validated->toArray();
                    $showValid[] = $record;
                }
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
        // $a = 'abdullah';
        // if (!empty($valid)) {
            try {
                $this->db->beginTransaction();
                if (!empty($updated)) {
                    $this->model->upsert($updated, ['item_id', 'product_variant_id', 'product_option_group_id','option_name']);
                }
                if (!empty($valid)) {
                    $this->model->upsert($valid, ['item_id', 'product_variant_id', 'product_option_group_id','option_name']);
                }
                $this->db->commit();
            } catch (Exception $e) {
                $this->db->rollBack();
                throw new Exception("Failed to insert item options: " . $e->getMessage());
            }
        // }
        return [
            'success' => true,
            'total_records' => iterator_count($records),
            'valid_records' => count($valid),
            'valid_data' => $showValid,
            'updated_records' => count($updated),
            'updated_data' => $showUpdated,
            'invalid_records' => count($invalid),
            'invalid_data' => $invalid,
            'duplicated_records' => count($duplicated),
            'duplicated_data' => $duplicated,
            'item_options' => [
                'inserted_count' => count($valid),
                'valid_data' => $showValid
            ],
            'summary' => [
                'success_rate' => count($valid) > 0 ? round(((count($valid)+count($updated)) / iterator_count($records)) * 100, 2) . '%' : '0%',
                'item_options_processed' => iterator_count($records),
                'item_options_records_created' => count($valid),
                'item_options_records_updated' => count($updated)
            ],
            'mapping_data' => $existingDataMaps
        ];
    }

    private function prepareRecord(array $record, array $defaultFields): array
    {
        return isset($record['item_id']) && $record['item_id'] ? $record : array_merge($defaultFields, $record);
    }

    private function getExistingDataMaps($records): array
    {
        $recordsArray = iterator_to_array($records);
        $productCodes = array_unique(array_column($recordsArray, 'web_product_code'));

        $existingItemOptionMapRaw = $this->model
        ->join('product', 'product.product_id', '=', 'item_option.product_id')
        ->join('item', 'item.item_id', '=', 'item_option.item_id')
        ->join('product_variant', 'product_variant.product_variant_id', '=', 'item_option.product_variant_id')
        ->join('product_option_group', 'product_option_group.product_option_group_id', '=', 'item_option.product_option_group_id')
        ->whereIn('product.product_code', $productCodes)
        ->select([
            'item_option.item_option_id', 
            'item_option.item_id',
            'item.item_code',
            'product.product_code',
            'product.product_id',
            'product_variant.product_variant_id',
            'product_variant.variant_name',
            'product_option_group.product_option_group_id', 
            'product_option_group.option_group_name',
            'item_option.option_name', 
            ])
        ->limit(0)->findAll(false);

        $existingItemOptionMap = [];
        foreach ($existingItemOptionMapRaw as $row) {
            $existingItemOptionMap[
                $row['item_code'] 
                . '-' . $row['variant_name'] 
                . '-' . $row['option_group_name']
                .'-'.$row['option_name']
            ] = $row['item_option_id'];
        }

        //Existing product option map 
        // [product_code + variant_name + option_group_name] => product_option_group_id
        $existingProductOptionMapRaw = $this->productOption
        ->join('product', 'product.product_id', '=', 'product_option.product_id')
        ->join('product_variant', 'product_variant.product_variant_id', '=', 'product_option.product_variant_id')
        ->join('product_option_group', 'product_option_group.product_option_group_id', '=', 'product_option.product_option_group_id')
        ->whereIn('product.product_code', $productCodes)
        ->select(['product_option_group.product_option_group_id', 
        'product_option_group.option_group_name', 
        'product_variant.product_variant_id',
        'product_variant.variant_name',
        'product_option.product_option_id',
        'product_option.option_name',
        'product.product_id',
        'product.product_code' 
        ])
        ->limit(0)
        ->findAll(false);

// product_id	238	A	No	
// product_variant_id	661	A	No
// product_option_group_id	1723	A	No
// option_name

        $productOptions = [];
        foreach ($existingProductOptionMapRaw as $row) {
            $productOptions[
                $row['product_code'] 
                . '-' . $row['variant_name'] 
                . '-' . $row['option_group_name']
                . '-' . $row['option_name']
            ] = [
                'product_option_group_id' => $row['product_option_group_id'],
                'product_variant_id' => $row['product_variant_id'],
                'product_id' => $row['product_id'],
                'product_option_id' => $row['product_option_id'],
            ];
        }


        $itemCodes = array_unique(array_column($recordsArray, 'item_code'));
        $this->item->clearQuery();
        $itemIdsMap = $this->item->whereIn('item_code', $itemCodes)
                    ->select(['item_id', 'item_code'])->limit(0)->findAll(false);
        $itemIdsMap = array_column($itemIdsMap, 'item_id', 'item_code');
        // type id map
        $typeIdsMap = $this->type->select(['type_id', 'type'])->limit(0)->findAll(false);
        $typeIdsMap = array_column($typeIdsMap, 'type_id', 'type');

        // Existing item option map
        return [
            'existingItemOptionMap' => $existingItemOptionMap,
            'productOptions' => $productOptions,
            'itemIdsMap' => $itemIdsMap,
            'typeIdsMap' => $typeIdsMap,
        ];
    }

    private function getDefaultFields(array $headers): array
    {
        $defaultFields = [];

        // Initialize all CSV headers with null
        foreach ($headers as $header) {
            $defaultFields[$header] = null;
        }

        // mandatory fields
        $defaultFields['option_name']              = 'sa techonology';
        // optional fields
        $defaultFields['value']                    = '{"value": "sa techonology"}'; // json format
        $defaultFields['meta_description']         = 'sa techonology';
        $defaultFields['required']                 = 0;
        $defaultFields['hex_color']                = '';
        $defaultFields['option_image']             = '[]';

        return $defaultFields;
    }

    private function getItemOptionUnique(array $itemOptionData, ?int $id = null): bool
    {
        $this->model->clearQuery();
        $itemId = $itemOptionData['item_id'];
        $variantId = $itemOptionData['product_variant_id'];
        $optionGroupId = $itemOptionData['product_option_group_id'];
        $optionName = $itemOptionData['option_name'];

        $query = $this->model
            ->select(['item_option_id'])
            ->where('item_id', '=', $itemId)
            ->where('product_variant_id', '=', $variantId)
            ->where('product_option_group_id', '=', $optionGroupId)
            ->where('option_name', '=', $optionName);

        if ($id !== null) {
            $query->where('item_option_id', '!=', $id);
        }

        $itemOption = $query->first();

        return $itemOption ? true : false;
    }

    private function checkItemOptionLimitCount(array $itemOptionData): bool
    {
        // each item variant group can have only 1 option
        $this->model->clearQuery();
        $itemOption = $this->model->where('item_id', '=', $itemOptionData['item_id'])
        ->where('product_variant_id', '=', $itemOptionData['product_variant_id'])
        ->where('product_option_group_id', '=', $itemOptionData['product_option_group_id'])
        ->limit(1)
        ->findAll(false);
        if (count($itemOption) > 0) {
            return true;
        }
        return false;
    }

    // update item option image
    public function updateItemOptionImage(array $data, int $item_option_id): bool
    {
        $itemOption = $this->model->where('item_option_id', '=', $item_option_id)->first();
        if (!$itemOption) {
            return false; // item option not found
        }

        $dataobj = $data;

        $img = [];
        foreach ($dataobj as $item) {
            $img[] = [
                'item_option_id' => $item_option_id,
                'name' => $item['name'] ?? '',
                'size' => $item['size'] ?? '',
                'type' => $item['type'] ?? '',
                'image' => $item['image'] ?? '',
                'status' => isset($item['status']) && is_array($item['status'])
                    ? $item['status']
                    : ['name' => 'Uploaded', 'severity' => 'success'],
                'media_id' => $item['media_id'] ?? null,
                'objectURL' => ($item['objectURL'] ?? ''),
                'created_at' => date('Y-m-d H:i:s'),
                'description' => $item['description'] ?? '',
                'item_option_image_id' => $item_option_id,
            ];
        }
        $imgJson = json_encode($img);
        $this->db->beginTransaction();
        try {
            // UPDATE `vendor` SET `image` = $img WHERE `vendor`.`vendor_id` = $vendor_id
            $itemOption->update(['option_image' => $imgJson]);
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // delete vendor image
    public function deleteItemOptionImage(int $item_option_id): bool
    {
        $itemOption = $this->model->where('item_option_id', '=', $item_option_id)->first();
        if (!$itemOption) {
            return false; // item option not found
        }
        $itemOption->update(['option_image' => null]);
        return true;
    }
}
