<?php

declare(strict_types=1);

namespace App\Core\Repositories\Item;

use App\Core\Exceptions\ValidationException;
use PDO;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Models\Item\Item;
use App\Core\Models\Item\ItemData;
use App\Core\Models\Product\Product;
use App\Core\Models\Product\ProductVariant;
use App\Core\ModelsFilters\RequestUri;
use App\Core\Validation\ItemDataValidation;
use App\Core\Validation\ItemDimensionDataValidation;
use App\Core\Repositories\Item\VariantItemRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use League\Csv\Reader;
use DateTime;
use Exception;

class ItemRepository extends BaseRepository implements ItemRepositoryInterface
{
    private Product $product;
    private ProductVariant $productVariant;
    private VariantItemRepositoryInterface $variantItemRepository;
    public function __construct(
        PDO $db,
        Product $product,
        ProductVariant $productVariant,
        VariantItemRepositoryInterface $variantItemRepository
    ) {
        parent::__construct($db, 'item', Item::class);
        $this->product = $product;
        $this->product->setDb($db);
        $this->productVariant = $productVariant;
        $this->productVariant->setDb($db);
        $this->variantItemRepository = $variantItemRepository;
    }

    /**
     * Get a single product with all its related data
     */
    public function get(
        ?int $itemId = null,
        ?int $productId = null,
        ?string $slug = null,
        ?int $languageId = null,
        bool $includePromotion = false,
        bool $includePoints = false,
        bool $includeStockStatus = false,
        bool $includeWeightType = false,
        bool $includeLengthType = false,
        bool $includeRating = false,
        bool $includeReviews = false
    ): ?array {
        if (!$itemId && !$productId && !$slug) {
            return null;
        }

        /** @var Builder $query */
        $query = $this->model->select(['*']);

        // Eager load relationships
        $with = [
            'content' => function ($query) use ($languageId) {
                if ($languageId !== null) {
                    $query->where('language_id', $languageId);
                }
            },
            'manufacturer',
            'vendor',
            'stockStatus' => function ($query) use ($languageId) {
                $query->where('language_id', $languageId);
            },
            'images',
            'related.content' => function ($query) use ($languageId) {
                if ($languageId !== null) {
                    $query->where('language_id', $languageId);
                }
            },
            'variants.content' => function ($query) use ($languageId) {
                if ($languageId !== null) {
                    $query->where('language_id', $languageId);
                }
            },
            'subscriptions',
            'attributes' => function ($query) use ($languageId) {
                if ($languageId !== null) {
                    $query->where('language_id', $languageId);
                }
            },
            'digitalAssets.content' => function ($query) use ($languageId) {
                if ($languageId !== null) {
                    $query->where('language_id', $languageId);
                }
            },
            'discounts',
            'promotions' => function ($query) {
                $query->whereNull('from_date')
                    ->orWhere('from_date', '<', new DateTime())
                    ->whereNull('to_date')
                    ->orWhere('to_date', '>', new DateTime())
                    ->orderBy('priority')
                    ->orderBy('price');
            },
            'points',
            'options' => function ($query) use ($languageId) {
                if ($languageId !== null) {
                    $query->where('language_id', $languageId);
                }
            },
            'sites'
        ];

        // Add conditional selects for aggregates
        if ($includeRating) {
            $query->withAvg(['reviews as rating' => function ($query) {
                $query->where('status', 1);
            }], 'rating');
        }

        if ($includeReviews) {
            $query->withCount(['reviews as reviews_count' => function ($query) {
                $query->where('status', 1);
            }]);
        }

        // Add conditions
        if ($productId !== null) {
            $query->where('product_id', $productId);
        } elseif ($itemId !== null) {
            $query->where('item_id', $itemId);
        } elseif ($slug !== null) {
            $query->whereHas('content', function ($query) use ($slug) {
                $query->where('slug', $slug);
            });
        }

        $product = $query->with($with)->first();
        if (!$product) {
            return null;
        }

        // Transform the Eloquent model to array format
        return [
            'product' => $product->toArray(),
            'content' => $product->content->toArray(),
            'images' => $product->images->toArray(),
            'related' => $product->related->map(function ($related) {
                return [
                    'product_related_id' => $related->product_id,
                    'product_id' => $related->pivot->product_id,
                    'name' => $related->content->first()->name ?? null,
                    'slug' => $related->content->first()->slug ?? null,
                    'id' => $related->product_id
                ];
            })->toArray(),
            'variant' => $product->variants->map(function ($variant) {
                return [
                    'product_variant_id' => $variant->product_id,
                    'product_id' => $variant->pivot->product_id,
                    'name' => $variant->content->first()->name ?? null,
                    'slug' => $variant->content->first()->slug ?? null,
                    'id' => $variant->product_id
                ];
            })->toArray(),
            'subscription' => $product->subscriptions->toArray(),
            'attribute' => $product->attributes->toArray(),
            'digital_asset' => $product->digitalAssets->toArray(),
            'discount' => $product->discounts->toArray(),
            'promotion' => $product->promotions->toArray(),
            'points' => $product->points->toArray(),
            'option' => $product->options->toArray(),
            'sites' => $product->sites->toArray()
        ];
    }

    public function getItems(RequestUri &$requestUri): ?array
    {
        $items = $this->model
            ->join('product', 'item.product_id', '=', 'product.product_id')
            ->join('product_variant', 'item.product_variant_id', '=', 'product_variant.product_variant_id')
            ->select([
                'item.*',
                'product.product_code',
                'product_variant.variant_name as product_variant',
            ]);
        $columns = $this->model->getTableColumns($this->model->getTable());
        $columns[] = 'product.product_code';
        $items = $requestUri->applyToQuery($items, $columns);
        $items = $items->findAll(false);
        $this->model->offset(0)->limit(0);
        $requestUri->totalRecords = $this->model->countAll();

        if (!$items) {
            return [];
        }
        return $items;
    }

    public function getItemById(int $itemId)
    {
        $this->model->clearQuery();
        $item = $this->model
            ->with(['product','productVariant'])
            // ->join('product', 'item.product_id', '=', 'product.product_id')
            // ->join('product_variant', 'item.product_variant_id', '=', 'product_variant.product_variant_id')
            ->where('item_id', '=', $itemId)
            ->first();

        if (!$item) {
            return null;
        }
        $response = $item->data;
        $response->variantItems = $this->variantItemRepository->getVariantByItem($itemId);

        return $response;
    }

    public function createItem(ItemData $itemData): ?Item
    {
        $itemDataArray = $itemData->toArray();
        // $existingDataMaps = $this->getExistingDataMaps($itemDataArray);
        // Validation check
        $validator = new ItemDataValidation($itemDataArray, [], [], ['existingItemMap' => [], 'variantsMap' => [], 'itemIdsMap' => []]);
        $validator->validate();
        // Validation failed check
        if ($validator === false) {
            throw new ValidationException($validator->getErrors(true));
        }
        // Existing data found check
        if ($validator->isExistingData == true) {
            throw new ValidationException([
                'unique_key' => ['Product, variant combination already exists or item code not unique: ' . $itemDataArray['item_code']],
            ]);
        }
        // Create item
        $validArray = $validator->toArray();
        try {
            $this->db->beginTransaction();
            $item = $this->model->create($validArray);
            if (!$item) {
                $this->db->rollBack();
                return null;
            }
            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Failed to create item: " . $e->getMessage());
        }
        return $item;
    }

    public function updateItem(ItemData $itemData): ?Item
    {
        $itemDataArray = $itemData->toArray();
        try {
            $this->db->beginTransaction();
            $item = $this->model->where('item_id', '=', $itemDataArray['item_id'])->first();
            unset($itemDataArray['product_code'], $itemDataArray['product_variant']);
            $item->clearQuery();
            $item->update($itemDataArray);
            if (!$item) {
                throw new Exception("Failed to update item");
            }
            $this->db->commit();
            return $item;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Failed to update item: " . $e->getMessage());
        }
    }

    public function importItems(string $csv_file): array
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

        $validItems = [];
        $validUpdatedItems = [];
        $invalid = [];
        $duplicated = [];
        $updated = [];
        $processed = [];
        $uniqueProductIdVariantId = [];
        $requiredFields = [
            'product_id',
            'product_variant_id',
            'item_code',
        ];

        // Get default field values to merge with incoming records
        $defaultFields = $this->getDefaultFields($headers);
        $existingDataMaps = $this->getExistingDataMaps($records);

        foreach ($records as $offset => $record) {
            try {
                $record = $this->prepareRecord($record, $defaultFields);
                $validator = new ItemDataValidation($record, $requiredFields, array_keys($defaultFields), $existingDataMaps);
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
                    $updated[] = [
                        'row' => $offset + 1,
                        'data' => $record,
                        'identifier' => $unique
                    ];
                    continue;
                }

                $processed[] = $unique;
                if ($validator->isExistingData) {
                    $validUpdatedItems[] = $validated->toArray();
                } else {
                    $validItems[] = $validated->toArray();
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

        if (!empty($validItems)) {
            try {   
                $this->db->beginTransaction();
                if (!empty($validUpdatedItems)) {
                    $this->model->upsert($validUpdatedItems, ['item_id']);
                }
                if (!empty($validItems)) {
                    // $this->model->upsert($validItems, ['product_id', 'product_variant_id']);
                    $this->model->upsert($validItems, ['item_code']);
                }
                $this->db->commit();
            } catch (\PDOException $e) {
                $this->db->rollBack();
                throw new Exception("Failed to insert items: " . $e->getMessage());
            } catch (Exception $e) {
                $this->db->rollBack();
                throw new Exception("Failed to insert items: " . $e->getMessage());
            } 
        }
        
        return [
            'success' => true,
            'total_records' => iterator_count($records),
            'valid_records' => count($validItems),
            'valid_data' => $validItems,
            'updated_records' => count($updated),
            'updated_data' => $updated,
            'invalid_records' => count($invalid),
            'invalid_data' => $invalid,
            'duplicated_records' => count($validUpdatedItems),
            'duplicated_data' => $validUpdatedItems,
            'items' => [
                'inserted_count' => count($validItems),
                'valid_data' => $validItems
            ],
            'summary' => [
                'success_rate' => count($validItems) > 0 ? round((count($validItems) / iterator_count($records)) * 100, 2) . '%' : '0%',
                'items_processed' => iterator_count($records),
                'items_records_created' => count($validItems)
            ],
            'maping_data' => $existingDataMaps
        ];
    }

    public function importDimensions(string $csv_file): array
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

        $validItems = [];
        $validUpdatedItems = [];
        $invalid = [];
        $duplicated = [];
        $updated = [];
        $processed = [];
        $requiredFields = [
            'item_code',
        ];

        // Get default field values to merge with incoming records
        $defaultFields = $this->getDefaultFields($headers);
        // get all item codes from the records
        $productCodes = array_unique(array_column(iterator_to_array($records), 'product_code'));
        $productCodes = array_values($productCodes);

        // fetch product codes from the item codes
        $productCodes = $this->product->whereIn('product_code', $productCodes)->select(['product_id', 'product_code'])->limit(0)->findAll(false);
        $productIdMap = array_column($productCodes, 'product_id', 'product_code');

        // product variant id from item
        $itemCodes = array_unique(array_column(iterator_to_array($records), 'item_code'));
        $itemCodes = array_values($itemCodes);
        $items = $this->model->whereIn('item_code', $itemCodes)->select(['item_id', 'product_variant_id', 'product_id', 'item_code'])->limit(0)->findAll(false);
        $itemMap = [];
        foreach ($items as $item) {
            $itemCode = $item['item_code'];
            $itemMap[$itemCode] = [
                'product_variant_id' => $item['product_variant_id'],
                'product_id' => $item['product_id'],
                'item_code' => $item['item_code'],
                'item_id' => $item['item_id'],
            ];
        }


        foreach ($records as $offset => $record) {
            try {
                $record = $this->prepareRecord($record, $defaultFields);
                $validator = new ItemDimensionDataValidation($record, $requiredFields, array_keys($defaultFields), $itemMap);
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
                    $updated[] = [
                        'row' => $offset + 1,
                        'data' => $record,
                        'identifier' => $unique
                    ];
                    continue;
                }

                $processed[] = $unique;
                if ($validator->isItemIdNotExists) {
                    $validUpdatedItems[] = $validated->toArray();
                } else {
                    $validItems[] = $validated->toArray();
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

        if (!empty($validItems)) {
            try {   
                $this->db->beginTransaction();
                if (!empty($validUpdatedItems)) {
                    $this->model->upsert($validUpdatedItems, ['item_id']);
                }
                if (!empty($validItems)) {
                    $this->model->upsert($validItems, ['item_code']);
                }
                $this->db->commit();
            } catch (\PDOException $e) {
                $this->db->rollBack();
                throw new Exception("Failed to insert items: " . $e->getMessage());
            } catch (Exception $e) {
                $this->db->rollBack();
                throw new Exception("Failed to insert items: " . $e->getMessage());
            } 
        }
        
        return [
            'success' => true,
            'total_records' => iterator_count($records),
            'valid_records' => count($validItems),
            'valid_data' => $validItems,
            'updated_records' => count($updated),
            'updated_data' => $updated,
            'invalid_records' => count($invalid),
            'invalid_data' => $invalid,
            'duplicated_records' => count($validUpdatedItems),
            'duplicated_data' => $validUpdatedItems,
            'items' => [
                'inserted_count' => count($validItems),
                'valid_data' => $validItems
            ],
            'summary' => [
                'success_rate' => count($validItems) > 0 ? round((count($validItems) / iterator_count($records)) * 100, 2) . '%' : '0%',
                'items_processed' => iterator_count($records),
                'items_records_created' => count($validItems)
            ],
            // 'maping_data' => $existingDataMaps
        ];
    }

    private function prepareRecord(array $record, array $defaultFields): array
    {
        return isset($record['item_id']) && $record['item_id'] ? $record : array_merge($defaultFields, $record);
    }

    private function getExistingDataMaps($records): array
    {
        $recordsArray = iterator_to_array($records);

        $productCodes = array_unique(array_column($recordsArray, 'product_code'));
        $productCodes = array_values($productCodes);

        $itemCodes = array_unique(array_column($recordsArray, 'item_code'));
        $itemCodes = array_values($itemCodes);

        $this->model->clearQuery();
        $query = $this->model;
        $query->join('product_variant', 'product_variant.product_variant_id', '=', 'product_variant.product_variant_id')
        ->join('product', 'product.product_id', '=', 'product_variant.product_id')
        ->select(['item_id', 'item_code', 'product.product_code', 'product_variant.variant_name', 'product_variant.product_variant_id'])
        ->limit(0);
        // $queryString = $query->getQuery();
        $existingItemData = $query->findAll();

        $existingItemMap = [];
        foreach ($existingItemData as $item) {
            $existingItemMap[
                $item['product_code']
                . '-' . $item['variant_name']
                . '-' . $item['item_code']
            ] = $item['item_id'];
        }

        $variantsMapData = $this->productVariant
        ->join('product', 'product.product_id', '=', 'product_variant.product_id')
        ->select(['product_variant_id', 'variant_name', 'product_id', 'product.product_code'])
        ->limit(0)
        ->findAll(false);

        $variantsMap = [];
        foreach ($variantsMapData as $variant) {
            $variantsMap[$variant['product_code'] . '-' . $variant['variant_name']] = [
                'product_variant_id' => $variant['product_variant_id'],
                'product_id' => $variant['product_id']
            ];
        }
        return [
            'existingItemMap' => $existingItemMap,
            'itemIdsMap' => array_values($existingItemMap),
            'variantsMap' => $variantsMap,
        ];
    }

    private function getDefaultFields(array $headers): array
    {
        $defaultFields = [];

        // Initialize all CSV headers with null
        foreach ($headers as $header) {
            $defaultFields[$header] = null;
        }

        // Database-correct defaults
        $defaultFields['km_item_id']               = 0;        // DB default
        $defaultFields['vendor_id']                = null;     // nullable
        $defaultFields['import_vendor_id']         = null;     // nullable
        $defaultFields['factory_vendor_id']        = null;     // nullable

        $defaultFields['item_category_id']         = 1;
        $defaultFields['item_type_id']             = 1;
        $defaultFields['sort_order']               = 0;
        $defaultFields['is_default']               = 0;

        $defaultFields['web_sku']                  = null;
        $defaultFields['class']                    = null;
        $defaultFields['description']              = null;
        $defaultFields['specifications']           = null;
        $defaultFields['warranty_period']          = null;

        $defaultFields['active']                   = 1;

        // Decimal nullable
        $defaultFields['width']                    = null;
        $defaultFields['height']                   = null;
        $defaultFields['depth']                    = null;
        $defaultFields['carton_qm']                = null;
        $defaultFields['gross_weight']             = null;

        // Decimal with default 0.00000
        $defaultFields['carton_width']             = 1;
        $defaultFields['carton_depth']             = 1;
        $defaultFields['carton_height']            = 1;

        $defaultFields['boradusages_sixteen']      = 0.00000;
        $defaultFields['boardusages_eighteen']     = 0.00000;
        $defaultFields['boardusages_twentyfive']   = 0.00000;
        $defaultFields['boardusages_thirtythree']  = 0.00000;

        $defaultFields['krost_zoho_id']            = null;
        $defaultFields['krost_qld_zoho_id']        = null;
        $defaultFields['meloz_zoho_id']            = null;
        $defaultFields['gregbar_zoho_id']          = null;
        $defaultFields['klein_zoho_id']            = null;

        $defaultFields['lead_days']                = 0;
        $defaultFields['melbourne_lead_days']      = 0;
        $defaultFields['brisbane_lead_days']       = 0;
        $defaultFields['safety_stock']             = 0;

        $defaultFields['quote_image']              = null;
        $defaultFields['delay_until']              = null;
        $defaultFields['delay_until_reason']       = null;
        $defaultFields['web_link']                 = null;

        $defaultFields['products_per_cartoon']     = null;
        $defaultFields['track_stock']              = 0;
        $defaultFields['user_note']                = null;
        $defaultFields['archive']                  = 0;

        $defaultFields['project_price_qty']        = null;
        $defaultFields['project_price_discount']   = 0.00000;

        // Optional auto timestamps
        $defaultFields['created_at']               = date('Y-m-d H:i:s');
        $defaultFields['updated_at']               = date('Y-m-d H:i:s');
        // $defaultFields['deleted_at']               = null;

        return $defaultFields;
    }

    public function searchItemOptions(string $name, int $product_id): array
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

    public function getItemByItemCode(string $itemCode): ?bool
    {
        $this->model->clearQuery();
        $item = $this->model->where('item_code', '=', $itemCode)->first();
        if (!$item) {
            return false;
        }
        return true;
    }
    
    public function searchItems(string $name, int $product_id = null): array
    {
        $this->model->clearQuery();
        $items = $this->model
        ->join('variant_item', 'variant_item.item_id', '=', 'item.item_id')
        ->where('item_code', 'like', '%' . $name . '%')
        // ->where('product_id', '=', $product_id)
        ->limit(200)
        ->findAll(false);
        return $items;
    }

    public function insertItemTableImageFile(array $data, string $property, int $item_id): bool
    {
        $item = $this->model->where('item_id', '=', $item_id)->first();
        if (!$item) {
            return false;
        }

        $imageData = '';
        if($property == 'dimensions_image') {
            $imageData = json_encode($data);
        } else {
           $imageData = isset($data[0]['objectURL']) ? $data[0]['objectURL'] : '';
        }
        $item->update([$property => $imageData]);
        return true;
    }

    public function deleteMediaByPath(string $property, int $item_id): bool
    {
        $item = $this->model->where('item_id', '=', $item_id)->first();
        if (!$item) {
            return false;
        }
        if($property == 'dimensions_image') {
            $item->update([$property => json_encode([])]);
        } else {
            $item->update([$property => null]);
        }
        return true;
    }
}
