<?php

declare(strict_types=1);

namespace App\Core\Repositories\Item;


use App\Core\Repositories\Base\BaseRepository;
use PDO;
use App\Core\Models\Base\Model;
use App\Core\Models\Item\VariantItem;
use App\Core\Validation\VariantItemDataValidation;
use App\Core\Models\Product\Product;
use App\Core\Models\Product\ProductVariant;
use App\Core\Models\Item\Item;
use App\Core\Models\Item\ItemOption;
use App\Core\Models\Item\RequestResponse\ItemVariantRequest;
use App\Core\Models\ProductOptionGroup\ProductOptionGroup;
use App\Core\Models\Product\ProductOption;
use Exception;
use League\Csv\Reader;

// use App\Core\Repositories\ValidationCSVFileRepository;

class VariantItemRepository extends BaseRepository implements VariantItemRepositoryInterface
{
    private Product $product;
    private ProductVariant $productVariant;
    private Item $item;
    private ItemOption $itemOption;
    private ProductOptionGroup $productOptionGroup;
    private ProductOption $productOption;

    public function __construct(
        PDO $db,
        Product $product,
        ProductVariant $productVariant,
        Item $item,
        ItemOption $itemOption, 
        ProductOptionGroup $productOptionGroup, 
        ProductOption $productOption
    ) {
        parent::__construct($db, 'variant_item', VariantItem::class);
        $this->product = $product;
        $this->product->setDb($db);
        $this->productVariant = $productVariant;
        $this->productVariant->setDb($db);
        $this->item = $item;
        $this->item->setDb($db);
        $this->itemOption = $itemOption;
        $this->itemOption->setDb($db);
        $this->productOptionGroup = $productOptionGroup;
        $this->productOptionGroup->setDb($db);
        $this->productOption = $productOption;
        $this->productOption->setDb($db);
    }

    /**
     * Get all length types with content for a specific language
     * 
     * @param int $languageId Language ID
     * @param int $start Pagination start
     * @param int $limit Pagination limit
     * @return array{items: array, total: int}
     */
    public function getAll(?int $languageId = null, int $start = 0, int $limit = 10): array
    {
        $query = $this->model->with(['variantsItemContent']);

        if ($languageId !== null) {
            $query->where('length_type_content.language_id', '=', $languageId);
        }

        // Add pagination
        if ($limit !== null) {
            $query->limit($limit);
        }
        if ($start !== null) {
            $query->offset($start);
        }

        // Get results
        $results = $query->findAll() ?? [];
        $total = $query->countAll();
        // $perPage = $limit ?? $this->model->limitValue;

        return $results;
        // return [
        //     'items' => collect($results),
        //     'total' => $total,
        //     "total_pages" => (int)ceil($total / $perPage),
        //     "current_page" => (int)($start / $perPage + 1),
        //     "per_page" => $perPage
        // ];
    }

    /**
     * Get a specific length type with content
     * 
     * @param int $variantItemId Length type ID
     * @param int|null $languageId Optional language ID
     * @return VariantsItem|null
     */
    public function get(int $variantItemId, ?int $languageId = null): ?VariantItem
    {
        $query = $this->model;

        // Join with length_type_content
        $query->join(
            'length_type_content',
            'length_type.length_type_id',
            '=',
            'length_type_content.length_type_id',
            'LEFT'
        );

        // Add length_type_id filter
        $query->where('length_type.length_type_id', '=', $variantItemId);

        // Add language filter if provided
        if ($languageId !== null) {
            $query->where('length_type_content.language_id', '=', $languageId);
        }

        $result = $query->findAll();
        return !empty($result) ? $this->model->set($result[0]) : null;
    }

    public function findAll(): array //this->model = length_type
    {
        // $results = $this->model->with(['variantsItemContent'])->whereNull('length_type.deleted_at')->findAll();
        // foreach ($results as &$result) {
        //     if (isset($result['length_type_content_data'])) {
        //         $result['length_type_content_data'] = json_decode($result['length_type_content_data'], true);
        //     }
        // }

        $variantItems = $this->model
            ->join('product_variant', 'variant_item.product_variant_id', '=', 'product_variant.product_variant_id')
            ->join('item', 'variant_item.item_id', '=', 'item.item_id')
            ->select(['variant_item.*', 'product_variant.variant_name', 'item.item_code'])
            ->whereNull('deleted_at')
            ->orderBy('variant_item_id', 'DESC')
            ->limit(0);
        $results = $variantItems->findAll(false);

        return $results ?? [];
    }

    public function find(int $id): ?object
    {
        $result = $this->model->with(['variantsItemContent'])->find($id);
        if ($result && isset($result->length_type_content_data)) {
            $result->length_type_content_data = json_decode($result->length_type_content_data, true);
        }

        return $result;
    }

    public function deleteVariantItem(int $variantItemId): bool
    {
        try {
            $this->db->beginTransaction();

            $this->model->clearQuery();
            $variantsItem = $this->model->where('variant_item_id', '=', $variantItemId)->first();
            if (!$variantsItem) {
                return false;
            }
            $variantsItem->update(['deleted_at' => date('Y-m-d H:i:s')]);

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to delete variant item: " . $e->getMessage());
        }
    }

    // import data
    public function importVariantItem(string $csv_file): array
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
        $validData = [];
        $invalid = [];
        $updated = [];
        $updateData = [];
        $duplicated = [];
        $processed = [];
        $requiredFields = ['item_id', 'product_variant_id', 'product_id', 'km_item_id'];

        // Get default field values to merge with incoming records
        $defaultFields = $this->getDefaultFields($headers);
        $existingDataMaps = $this->getExistingDataMaps($records);

        foreach ($records as $offset => $record) {
            try {
                $record = $this->prepareRecord($record, $defaultFields);
                $validator = new VariantItemDataValidation($record, $requiredFields, array_keys($defaultFields), $existingDataMaps);
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
                    $updateData[] = $record;
                } else {
                    $valid[] = $validated->toArray();
                    $validData[] = $record;
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

        if (!empty($valid)) {
            try {
                $this->db->beginTransaction();
                // if (!empty($existing)) {
                //     $this->model->upsert($existing, ['item_option_id']);
                // }
                $this->model->clearQuery();
                if (!empty($valid)) {
                    $this->model->upsert($valid, ['product_id', 'product_variant_id', 'item_id']);
                }
                if (!empty($updated)) {
                    $this->model->upsert($updated, ['product_id', 'product_variant_id', 'item_id']);
                }
                $this->db->commit();
            } catch (Exception $e) {
                $this->db->rollBack();
                throw new Exception("Failed to insert variant items: " . $e->getMessage());
            }
        }
        return [
            'success' => true,
            'total_records' => iterator_count($records),
            'valid_records' => count($valid),
            'valid_data' => $validData,
            'updated_records' => count($updated),
            'updated_data' => $updateData,
            'invalid_records' => count($invalid),
            'invalid_data' => $invalid,
            'duplicated_records' => count($duplicated),
            'duplicated_data' => $duplicated,
            'variant_items' => [
                'inserted_count' => count($valid),
                'valid_data' => $validData
            ],
            'summary' => [
                'success_rate' => count($valid) > 0 ? round(((count($valid)+count($updated)) / iterator_count($records)) * 100, 2) . '%' : '0%',
                'variant_items_processed' => iterator_count($records),
                'variant_items_records_created' => count($valid),
                'variant_items_records_updated' => count($updated)
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
        $itemCodes = array_unique(array_column($recordsArray, 'item_code'));
        $productCodes = array_unique(array_column($recordsArray, 'web_product_code'));
        $this->item->clearQuery();
        $itemIdsMap = $this->item->select(['item_id', 'item_code'])->limit(0)->findAll(false);
        $itemIdsMap = array_column($itemIdsMap, 'item_id', 'item_code');


        //Existing item variant 
        // [item_code + variant_name ] => variant_item_id
        $this->model->clearQuery();
        $existingItemVariantMapRaw = $this->model
        ->join('item', 'item.item_id', '=', 'variant_item.item_id')
        ->join('product_variant', 'product_variant.product_variant_id', '=', 'variant_item.product_variant_id')
        ->join('product', 'product.product_id', '=', 'product_variant.product_id')
        ->whereIn('item.item_code', $itemCodes)
        ->select([
            'product.product_code',
            'product.product_id',
            'product_variant.product_variant_id',
            'product_variant.variant_name',
            'variant_item.variant_item_id', 
            'item.item_code',
            ])
        ->limit(0)->findAll(false);

        $existingItemVariantMap = [];
        foreach ($existingItemVariantMapRaw as $row) {
            $existingItemVariantMap[$row['product_code'] . '-' . $row['variant_name'] . '-' . $row['item_code']] = $row['variant_item_id'];
        }

         //Existing variant map 
        // [product_code + variant_name ] => product_variant_id

        $existingVariantMapRaw = $this->productVariant
        ->join('product', 'product.product_id', '=', 'product_variant.product_id')
        ->whereIn('product.product_code', $productCodes)
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
            'existingItemVariantMap' => $existingItemVariantMap,
            'variantsMap' => $variantMap,
            'itemIdsMap' => $itemIdsMap
        ];
        
        return [];
    
    }

    private function getDefaultFields(array $headers): array
    {
        $defaultFields = [];

        // Initialize all CSV headers with null
        foreach ($headers as $header) {
            $defaultFields[$header] = null;
        }

        // mandatory fields
        $defaultFields['sort_order']                     = 1;
        $defaultFields['active_status']                  = 1;

        return $defaultFields;
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
            $variant['productOptionGroups'] = $formattedProductOptionGroups[$variant['product_variant_id']] ?? [];
        }

        return $variants;
    }

    public function getVariantByItem(int $item_id): array
    {
        $this->model->clearQuery();
        $variants = $this->model
            ->join('product_variant', 'variant_item.product_variant_id', '=', 'product_variant.product_variant_id')
            ->select(['product_variant.*', 'variant_item.variant_item_id', 'variant_item.item_id'])
            ->where('item_id', '=', $item_id)
            ->whereNull('deleted_at')
            ->orderBy('product_variant_id', 'DESC')
            ->limit(1);
        $variants = $variants->findAll(false);

        // $lastQuery = $this->model->getQueryString();
        // echo $lastQuery;
        // exit;

        $variantIds = array_column($variants, 'product_variant_id');

        $this->itemOption->clearQuery();
        $itemOptionGroups = $this->itemOption
            ->join('product_option_group', 'item_option.product_option_group_id', '=', 'product_option_group.product_option_group_id')
            ->select(['item_option.*', 'product_option_group.option_group_name', 'item_option.product_option_group_id as item_option_group_id'])
            ->whereIn('product_variant_id', $variantIds)
            ->where('item_id', '=', $item_id)
            ->limit(0)
            ->findAll(false);

        $itemOptionGroupIds = array_column($itemOptionGroups, 'product_option_group_id');
        $itemOptions = $this->itemOption
            ->join('product_option', 'item_option.product_option_id', '=', 'product_option.product_option_id')
            ->join('`type`', 'item_option.type_id', '=', '`type`.type_id')
            ->select(['item_option.*', '`type`.type', 'product_option.product_option_id'])
            ->whereIn('product_option_group_id', $itemOptionGroupIds)
            ->where('item_id', '=', $item_id)
            ->limit(0)->findAll(false);

        $formatedItemOptions = [];
        foreach ($itemOptions as $itemOption) {
            $formatedItemOptions[$itemOption['product_option_group_id']][] = $itemOption;
        }

        $formattedItemOptionGroups = [];
        foreach ($itemOptionGroups as $itemOptionGroup) {
            $itemOptionGroup['productOptions'] = $formatedItemOptions[$itemOptionGroup['product_option_group_id']] ?? [];
            $formattedItemOptionGroups[$itemOptionGroup['product_variant_id']][] = $itemOptionGroup;
        }
        foreach ($variants as &$variant) {
            $variant['productOptionGroups'] = $formattedItemOptionGroups[$variant['product_variant_id']] ?? [];
        }
        return $variants;
    }

    public function getVariantByVariantId(int $variant_id): array
    {
        $this->model->clearQuery();
        $variants = $this->model
            ->join('product_variant', 'variant_item.product_variant_id', '=', 'product_variant.product_variant_id')
            ->select(['product_variant.*', 'variant_item.variant_item_id', 'variant_item.item_id'])
            ->where('variant_item_id', '=', $variant_id)
            ->whereNull('deleted_at')
            ->orderBy('product_variant_id', 'DESC')
            ->limit(1);
        $variants = $variants->findAll(false);
        $item_id = $variants[0]['item_id'];

        // $lastQuery = $this->model->getQueryString();
        // echo $lastQuery;
        // exit;

        $variantIds = array_column($variants, 'product_variant_id');

        $this->itemOption->clearQuery();
        $itemOptionGroups = $this->itemOption
            ->join('product_option_group', 'item_option.product_option_group_id', '=', 'product_option_group.product_option_group_id')
            ->select(['item_option.*', 'product_option_group.option_group_name', 'item_option.product_option_group_id as item_option_group_id'])
            ->whereIn('product_variant_id', $variantIds)
            ->where('item_id', '=', $item_id)
            ->limit(0)
            ->findAll(false);

        $itemOptionGroupIds = array_column($itemOptionGroups, 'product_option_group_id');
        $itemOptions = $this->itemOption
            ->join('product_option', 'item_option.product_option_id', '=', 'product_option.product_option_id')
            ->join('`type`', 'item_option.type_id', '=', '`type`.type_id')
            ->select(['item_option.*', '`type`.type', 'product_option.product_option_id'])
            ->whereIn('product_option_group_id', $itemOptionGroupIds)
            ->where('item_id', '=', $item_id)
            ->limit(0)->findAll(false);

        $formatedItemOptions = [];
        foreach ($itemOptions as $itemOption) {
            $formatedItemOptions[$itemOption['product_option_group_id']][] = $itemOption;
        }

        $formattedItemOptionGroups = [];
        foreach ($itemOptionGroups as $itemOptionGroup) {
            $itemOptionGroup['productOptions'] = $formatedItemOptions[$itemOptionGroup['product_option_group_id']] ?? [];
            $formattedItemOptionGroups[$itemOptionGroup['product_variant_id']][] = $itemOptionGroup;
        }
        foreach ($variants as &$variant) {
            $variant['productOptionGroups'] = $formattedItemOptionGroups[$variant['product_variant_id']] ?? [];
        }
        return $variants;
    }

    public function createProductVariant($data): array
    {
         try {
            $variantData = [
                'product_id'         => $data['product_id'],
                'variant_name'       => $data['variant_name'],
                'variant_description' => $data['variant_description'],
                'sort_order'         => $data['sort_order'],
                'active_status'      => isset($data['active_status']) ? 1 : 0,
            ];
            $this->db->beginTransaction();
            $variantObj = $this->productVariant->create($variantData);
            $product_variant_id = $variantObj->data->product_variant_id;
            $this->db->commit();
            return (array) ['product_variant_id' => $product_variant_id];
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception('Failed to update variant: ' . $e->getMessage());
        }
    }
    public function checkDuplicateProudctVariant(int $product_id, string $variant_name): bool
    {
        $this->productVariant->clearQuery();
        $variant = $this->productVariant
                   ->where('product_id', '=', $product_id)
                   ->where('variant_name', '=', $variant_name)
                   ->whereNull('deleted_at')
                   ->limit(1)
                   ->first();
        return $variant ? true : false;
    }

    public function checkDuplicateVariantItem(int $product_id, int $item_id): bool
    {
        $this->model->clearQuery();
        $variant = $this->model
                   ->where('product_id', '=', $product_id)
                   ->where('item_id', '=', $item_id)
                   ->whereNull('deleted_at')
                   ->limit(1)
                   ->first();
        return $variant ? true : false;
    }

    public function addVariantItem(array $data): array
    {
        $this->model->clearQuery();
        $insertData = [
            'product_id'         => $data['product_id'],
            'product_variant_id' => $data['product_variant_id'],
            'item_id'            => $data['item_id'],
            'km_item_id'         => $data['km_item_id'] ?? 0,
            'sort_order'         => $data['sort_order'],
            'active_status'      => isset($data['active_status']) ? 1 : 0,
        ];
        $variantItem = $this->model->create($insertData);
        return (array) ['variant_item_id' => $variantItem->data->variant_item_id];
    }

    public function createVariantItem(ItemVariantRequest $itemVariantRequest): array
    {
         try {
            $variantItem = (array) $itemVariantRequest->itemVariant; // can be used for further validation
            $this->db->beginTransaction();
            //Option groups name changed and need to insert new group
            $optionGroupsToInsert = [];
            array_walk($itemVariantRequest->itemOptionGroups, function($optionGroup, $key) use(&$optionGroupsToInsert) {
                if($optionGroup->product_option_group_id == 0){
                    $optionGroupsToInsert[$key] = (array) $optionGroup;
                    // unset($optionGroup->item_option_group_id);
                }
            });

            //Option groups description changed and need to update group
            $optionGroupsToUpdate = [];
            array_walk($itemVariantRequest->itemOptionGroups, function($optionGroup, $key) use(&$optionGroupsToUpdate) {
                if($optionGroup->product_option_group_id > 0){
                    $optionGroupsToUpdate[$key] = (array) $optionGroup;
                    // unset($optionGroup->item_option_group_id);
                }
            });

            // insert group
            if(!empty($optionGroupsToInsert)){
                $this->productOptionGroup->insert(array_values($optionGroupsToInsert)); // after testing we can use upsert instead of insert
            }
            // update group
            if(!empty($optionGroupsToUpdate)){
                // unique key is product_id - product_variant_id - option_group_name
                $this->productOptionGroup->upsert(array_values($optionGroupsToUpdate), ['product_id', 'product_variant_id', 'option_group_name']);
            }
            
            //Retrieve above inserted and updated product groups ids from database
            //collect all unique keys
            $groupUniqueKeys = array_merge(array_keys($optionGroupsToInsert), array_keys($optionGroupsToUpdate));
            //map the unique keys to the product group ids
            $optionGroupsIdsMap = $this->getProductOptionGroupIds($groupUniqueKeys);

            //to collect all product_option_id from inserted and updated below
            $productOptionsUniqueKeys = [];

            //product options name changed and need to insert new option
            $productOptionsToInsert = [];
            array_walk($itemVariantRequest->itemOptions, 
            function($option, $key) use(&$productOptionsToInsert, $optionGroupsIdsMap, &$productOptionsUniqueKeys) {
                if($option->product_option_id == 0){
                    if(isset($optionGroupsIdsMap[$option->product_option_group_unique_key])){
                        $option->product_option_group_id = $optionGroupsIdsMap[$option->product_option_group_unique_key];
                    };
                    $optionData = (array) $option;
                    $productOptionsUniqueKeys[] = $optionData['product_option_unique_key'];
                    unset($optionData['product_option_group_unique_key']);
                    unset($optionData['product_option_unique_key']);
                    unset($optionData['item_id']);
                    unset($optionData['item_option_id']);
                    unset($optionData['is_default']);
                    unset($optionData['option_description']);
                    $productOptionsToInsert[$key] = $optionData;
                }
            });

            //product options description changed and need to update option
            $productOptionsToUpdate = [];
            array_walk($itemVariantRequest->itemOptions, 
            function($option, $key) use(&$productOptionsToUpdate, $optionGroupsIdsMap, &$productOptionsUniqueKeys) {
                if($option->product_option_id > 0){
                    if(isset($optionGroupsIdsMap[$option->product_option_group_unique_key])){
                        $option->product_option_group_id = $optionGroupsIdsMap[$option->product_option_group_unique_key];
                    };
                    $optionData = (array) $option;
                    $productOptionsUniqueKeys[] = $optionData['product_option_unique_key'];
                    unset($optionData['product_option_group_unique_key']);
                    unset($optionData['product_option_unique_key']);
                    unset($optionData['item_id']);
                    unset($optionData['item_option_id']);
                    unset($optionData['is_default']);
                    unset($optionData['option_description']);
                    $productOptionsToUpdate[$key] = $optionData;
                }
            });

            // insert product option
            if(!empty($productOptionsToInsert)){
                $this->productOption->insert(array_values($productOptionsToInsert)); // after testing we can use upsert instead of insert
            }
            // updated product option
            if(!empty($productOptionsToUpdate)){
                // unique key is product_variant_id - product_option_group_id - option_name
                $this->productOption->upsert(array_values($productOptionsToUpdate), ['product_variant_id', 'product_option_group_id', 'option_name']);
            }

            //Retrive and map product_option_id s from inserted and updated above
            $productOptionsIdsMap = $this->getProductOptionIds($productOptionsUniqueKeys);

            //Now make sure all option will have product_option_id and product_option_group_id
            $optionsToUpsert = array_map(function($option) use($optionGroupsIdsMap, $productOptionsIdsMap){
                if(isset($optionGroupsIdsMap[$option->product_option_group_unique_key])){
                    $option->product_option_group_id = $optionGroupsIdsMap[$option->product_option_group_unique_key];
                };
                if(isset($productOptionsIdsMap[$option->product_option_unique_key])){
                    $option->product_option_id = $productOptionsIdsMap[$option->product_option_unique_key];
                };
                unset($option->product_option_group_unique_key);
                unset($option->product_option_unique_key);
                unset($option->is_default);
                unset($option->description);
                return (array) $option;
            }, $itemVariantRequest->itemOptions);

            // update item option
            if(!empty($optionsToUpsert)){
                // unique key is item_id - product_variant_id - product_option_group_id - option_name
                $this->itemOption->upsert(array_values($optionsToUpsert), ['item_id', 'product_variant_id', 'product_option_group_id', 'option_name']);
            }

            $this->db->commit();
            return (array) $this->getVariantByItem(intval($variantItem['item_id']));

        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception('Failed to update variant: ' . $e->getMessage());
        }
    }

    /***
     * Update variant item
     * @param ItemVariantRequest $itemVariantRequest
     * @return array|Throwable
     * @throws \Exception
     */

    public function updateVariantItem(ItemVariantRequest $itemVariantRequest): array
    {
        try {
            $variantItem = (array) $itemVariantRequest->itemVariant; // can be used for further validation
            
            $this->db->beginTransaction();
            
            //Option groups name changed and need to insert new group
            $optionGroupsToInsert = [];
            array_walk($itemVariantRequest->itemOptionGroups, function($optionGroup, $key) use(&$optionGroupsToInsert) {
                if($optionGroup->product_option_group_id == 0){
                    $optionGroupsToInsert[$key] = (array) $optionGroup;
                    // unset($optionGroup->item_option_group_id);
                }
            });

            //Option groups description changed and need to update group
            $optionGroupsToUpdate = [];
            array_walk($itemVariantRequest->itemOptionGroups, function($optionGroup, $key) use(&$optionGroupsToUpdate) {
                if($optionGroup->product_option_group_id > 0){
                    $optionGroupsToUpdate[$key] = (array) $optionGroup;
                    // unset($optionGroup->item_option_group_id);
                }
            });

            // insert group
            if(!empty($optionGroupsToInsert)){
                $this->productOptionGroup->insert(array_values($optionGroupsToInsert)); // after testing we can use upsert instead of insert
            }
            // update group
            if(!empty($optionGroupsToUpdate)){
                // unique key is product_id - product_variant_id - option_group_name
                $this->productOptionGroup->upsert(array_values($optionGroupsToUpdate), ['product_id', 'product_variant_id', 'option_group_name']);
            }
            
            //Retrieve above inserted and updated product groups ids from database
            //collect all unique keys
            $groupUniqueKeys = array_merge(array_keys($optionGroupsToInsert), array_keys($optionGroupsToUpdate));
            //map the unique keys to the product group ids
            $optionGroupsIdsMap = $this->getProductOptionGroupIds($groupUniqueKeys);

            //to collect all product_option_id from inserted and updated below
            $productOptionsUniqueKeys = [];

            //product options name changed and need to insert new option
            $productOptionsToInsert = [];
            array_walk($itemVariantRequest->itemOptions, 
            function($option, $key) use(&$productOptionsToInsert, $optionGroupsIdsMap, &$productOptionsUniqueKeys) {
                if($option->product_option_id == 0){
                    if(isset($optionGroupsIdsMap[$option->product_option_group_unique_key])){
                        $option->product_option_group_id = $optionGroupsIdsMap[$option->product_option_group_unique_key];
                    };
                    $optionData = (array) $option;
                    $productOptionsUniqueKeys[] = $optionData['product_option_unique_key'];
                    unset($optionData['product_option_group_unique_key']);
                    unset($optionData['product_option_unique_key']);
                    unset($optionData['item_id']);
                    unset($optionData['item_option_id']);
                    unset($optionData['is_default']);
                    unset($optionData['option_description']);
                    $productOptionsToInsert[$key] = $optionData;
                }
            });

            //product options description changed and need to update option
            $productOptionsToUpdate = [];
            array_walk($itemVariantRequest->itemOptions, 
            function($option, $key) use(&$productOptionsToUpdate, $optionGroupsIdsMap, &$productOptionsUniqueKeys) {
                if($option->product_option_id > 0){
                    if(isset($optionGroupsIdsMap[$option->product_option_group_unique_key])){
                        $option->product_option_group_id = $optionGroupsIdsMap[$option->product_option_group_unique_key];
                    };
                    $optionData = (array) $option;
                    $productOptionsUniqueKeys[] = $optionData['product_option_unique_key'];
                    unset($optionData['product_option_group_unique_key']);
                    unset($optionData['product_option_unique_key']);
                    unset($optionData['item_id']);
                    unset($optionData['item_option_id']);
                    unset($optionData['is_default']);
                    unset($optionData['option_description']);
                    $productOptionsToUpdate[$key] = $optionData;
                }
            });

            // insert product option
            if(!empty($productOptionsToInsert)){
                $this->productOption->insert(array_values($productOptionsToInsert)); // after testing we can use upsert instead of insert
            }
            // updated product option
            if(!empty($productOptionsToUpdate)){
                // unique key is product_variant_id - product_option_group_id - option_name
                $this->productOption->upsert(array_values($productOptionsToUpdate), ['product_variant_id', 'product_option_group_id', 'option_name']);
            }

            //Retrive and map product_option_id s from inserted and updated above
            $productOptionsIdsMap = $this->getProductOptionIds($productOptionsUniqueKeys);

            //Now make sure all option will have product_option_id and product_option_group_id
            $optionsToUpsert = array_map(function($option) use($optionGroupsIdsMap, $productOptionsIdsMap){
                if(isset($optionGroupsIdsMap[$option->product_option_group_unique_key])){
                    $option->product_option_group_id = $optionGroupsIdsMap[$option->product_option_group_unique_key];
                };
                if(isset($productOptionsIdsMap[$option->product_option_unique_key])){
                    $option->product_option_id = $productOptionsIdsMap[$option->product_option_unique_key];
                };
                unset($option->product_option_group_unique_key);
                unset($option->product_option_unique_key);
                unset($option->is_default);
                unset($option->description);
                return (array) $option;
            }, $itemVariantRequest->itemOptions);

            // update item option
            if(!empty($optionsToUpsert)){
                // unique key is item_id - product_variant_id - product_option_group_id - option_name
                $this->itemOption->upsert(array_values($optionsToUpsert), ['item_id', 'product_variant_id', 'product_option_group_id', 'option_name']);
            }

            $this->db->commit();
            return (array) $this->getVariantByItem(intval($variantItem['item_id']));
    
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception('Failed to update variant: ' . $e->getMessage());
        }
    }

    public function editVariantItem(array $data): array
    {
        $this->model->clearQuery();
        $variantItem = $this->model->where('variant_item_id', '=', $data['variant_item_id'])->first();
        if(!$variantItem){
            throw new \Exception('Variant item not found');
        }
        $variantItemData = [
            'product_id'         => $data['product_id'],
            'product_variant_id' => $data['product_variant_id'],
            'item_id'            => $data['item_id'],
            'km_item_id'         => $data['km_item_id'] ?? 0,
            'sort_order'         => $data['sort_order'],
            'active_status'      => isset($data['active_status']) ? 1 : 0,
        ];
        $variantItem->update($variantItemData);
        return (array) $variantItem;
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

    public function findByVariantItemId(int $item_id, int $product_variant_id, ?int $variant_item_id = null): bool
    {
        $this->model->clearQuery();
        $query = $this->model
            ->where('item_id', '=', $item_id)
            ->where('product_variant_id', '=', $product_variant_id);
        $variantItem = $query->first();
        return $variantItem ? true : false;
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

    private function getProductOptionIds(array $unique): array
    {
        $response = [];
        $unique = array_unique($unique);
        if (empty($unique)) {
            return $response;
        }
        $this->productOption->clearQuery();
        $options = $this->productOption
            ->whereIn(
                'CONCAT(`product_option`.`product_id`, "-", `product_option`.`product_variant_id`,"-", `product_option`.`product_option_group_id`, "-", `product_option`.`option_name`)',
                $unique
            )
            ->select(['product_option.*'])
            ->limit(0)->findAll(false);
        foreach ($options as $option) {
            $response[$option['product_id'] . '-' . $option['product_variant_id'] . '-' . $option['product_option_group_id'] . '-' . $option['option_name']] = $option['product_option_id'];
        }
        return $response;
    }

}
