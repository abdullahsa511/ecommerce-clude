<?php

declare(strict_types=1);

namespace App\Core\Repositories\Product;

use App\Core\Models\Item\Item;
use App\Core\Models\Product\Product;
use App\Core\Models\Product\ProductContent;
use PDO;
use App\Core\Models\Product\ProductAccessories;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Validation\ProductAccessoriesValidation;
use League\Csv\Reader;
use Exception;

class ProductAccessoriesRepository extends BaseRepository implements ProductAccessoriesRepositoryInterface
{
    private Product $product;
    private ProductContent $productContent;
    private Item $item;
    
    public function __construct(PDO $db, Product $product, ProductContent $productContent, Item $item)
    {
        parent::__construct($db, 'product_accessories', ProductAccessories::class, Product::class, ProductContent::class, Item::class);
        $this->product = $product;
        $this->product->setDb($db);
        $this->productContent = $productContent;
        $this->productContent->setDb($db);
        $this->item = $item;
        $this->item->setDb($db);
    }

    public function findAll(): array
    {
        $accessoriess = parent::findAll();
        // $accessoriess = $this->model
        // ->join('product_content as parent_product_content', 'parent_product_content.product_id', '=', 'product_accessories.parent_product_id')
        // ->join('product_content as accessory_product_content', 'accessory_product_content.product_id', '=', 'product_accessories.product_id')
        // ->join('item_content', 'item_content.item_id', '=', 'product_accessories.item_id')
        // ->select(['product_accessories.*', 'parent_product_content.name as parent_product_name', 'accessory_product_content.name as product_name', 'item_content.item_code as item_code'])
        // ->limit(0)
        // ->findAll(false);
        
        // Add accessories_count to each accessories
        foreach ($accessoriess as &$accessories) {
            $accessories['accessories_count'] = $this->getAccessoriesCount($accessories['product_accessories_id']);
        }
        return $accessoriess;
    }

    public function getAccessoriesData()
    {
        // $accessories = $this->model
        // ->join('product', 'product.product_id', '=', 'product_accessories.parent_product_id')
        // ->join(
        //     'product_content',
        //     'product_content.product_id',
        //     '=',
        //     'product.product_id'
        // )
        // ->join('product', 'product.product_id', '=', 'product_accessories.product_id')
        // ->join(
        //     'product_content',
        //     'product_content.product_id',
        //     '=',
        //     'product.product_id'
        // )
        // ->join('item', 'item.item_id', '=', 'product_accessories.item_id')
        // ->select([
        //     'product_accessories.product_accessories_id',
        //     'product_content.name as parent_product_name',
        //     'product_content.name as product_name',
        //     'item.item_code as item_code',
        //     'product_accessories.price',
        //     'product_accessories.created_at'
        // ])
        // ->findAll(false);
    
            $sql = "SELECT
                pa.product_accessories_id,
                ppc.name AS parent_product_name,
                apc.name AS product_name,
                i.item_code,
                pa.price,
                pa.created_at
            FROM product_accessories AS pa

            -- Parent product
            INNER JOIN product AS pp
                ON pp.product_id = pa.parent_product_id
            INNER JOIN product_content AS ppc
                ON ppc.product_id = pp.product_id

            -- Accessory product
            INNER JOIN product AS ap
                ON ap.product_id = pa.product_id
            INNER JOIN product_content AS apc
                ON apc.product_id = ap.product_id

            -- Item
            INNER JOIN item AS i
                ON i.item_id = pa.item_id

            -- Optional filters (recommended)
            WHERE pa.deleted_at IS NULL
            AND pa.active_status = 1;
            ";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $accessories = $stmt->fetchAll(PDO::FETCH_ASSOC);


    return $accessories;
    
    }


    public function getAccessoriesById(int $id)
    {
        $accessories = $this->model->where('product_accessories_id', '=', $id)->first();
        if (!$accessories) {
            return [];
        }

        // $accessories = $this->model
        //     ->join('product', 'product.product_id', '=', 'product_accessories.parent_product_id')
        //     ->join('product_content', 'product_content.product_id', '=', 'product_accessories.product_id')
        //     ->join('product_content as accessory_product_content', 'accessory_product_content.product_id', '=', 'product_accessories.product_id')
        //     ->join('item', 'item.item_id', '=', 'product_accessories.item_id')
        //     ->where('product_accessories.product_accessories_id', '=', $id)
        //     ->select([
        //         'product_accessories.*', 
        //         'product_content.name as parent_product_name', 
        //         'product_content.name as accessory_product_name', 
        //         'item.item_code'
        //     ])
        //     ->limit(0)
        //     ->findAll(false);

        return $accessories->data;
    }

    private function getAccessoriesCount(int $parentId): int
    {
        $sql = "SELECT COUNT(*) as count FROM product_accessories WHERE product_accessories_id = :product_accessories_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['product_accessories_id' => $parentId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return (int) $result['count'];
    }

    public function getAccessoriesByProductId(int $product_id): array
    {
        $sql = "SELECT
                    pa.product_accessories_id,
                    pa.parent_product_id,
                    ppc.name AS parent_product_name,
                    pa.product_id,
                    apc.name AS product_name,
                    i.item_id,
                    i.item_code,
                    i.description as item_description,
                    i.quote_image as item_image,
                    pa.price,
                    pa.created_at
                FROM product_accessories AS pa
                -- Parent product
                INNER JOIN product AS pp ON pp.product_id = pa.parent_product_id
                INNER JOIN product_content AS ppc ON ppc.product_id = pp.product_id
                -- Accessory product
                INNER JOIN product AS ap ON ap.product_id = pa.product_id
                INNER JOIN product_content AS apc ON apc.product_id = ap.product_id
                -- Item
                INNER JOIN item AS i ON i.item_id = pa.item_id
                -- Optional filters (recommended)
                WHERE pa.deleted_at IS NULL AND pa.active_status = 1 AND pa.parent_product_id = :parent_product_id";

            $stmt = $this->db->prepare($sql);
            $stmt->execute(['parent_product_id' => $product_id]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $formattedAccessories = [];
            foreach ($rows as $row) {
                $parentId = $row['parent_product_id'];

                // Create parent product if not exists
                if (!isset($formattedAccessories[$parentId])) {
                    $formattedAccessories[$parentId] = [
                        'parent_product_id'   => $row['parent_product_id'],
                        'parent_product_name' => $row['parent_product_name'],
                        'accessories'         => [],
                    ];
                }

                // Add accessory under parent
                $formattedAccessories[$parentId]['accessories'][] = [
                    'product_accessories_id' => $row['product_accessories_id'],
                    'product_id'             => $row['product_id'],
                    'item_id'                => $row['item_id'] ?? null, // if exists
                    'item_code'              => $row['item_code'],
                    'title'                  =>  $row['item_code'] . ' - ' . $row['product_name'],
                    'description'            => $row['item_description'],
                    'image'                  => $row['item_image'] ?? null,   // optional
                    'price'                  => (float) $row['price'],
                    'is_selected'            => 0, // default value
                ];
            }

            // Reindex array (remove parent_id keys)
            $accessories = array_values($formattedAccessories);

        return $accessories;

                // $accessories = $this->model
        //     ->join('product', 'product.product_id', '=', 'product_accessories.parent_product_id')
        //     ->join('product_content', 'product_content.product_id', '=', 'product_accessories.product_id')
        //     ->join('product_content as accessory_product_content', 'accessory_product_content.product_id', '=', 'product_accessories.product_id')
        //     ->join('item', 'item.item_id', '=', 'product_accessories.item_id')
        //     ->where('product_accessories.product_id', '=', $product_id)
        //     ->select([
        //         'product_accessories.*', 
        //         'product_content.name as parent_product_name', 
        //         'product_content.name as accessory_product_name', 
        //         'item.item_code'
        //     ])
        //     ->limit(0)
        //     ->findAll(false);
    }

    public function deleteAccessories(int $id): bool
    {
        $this->model->delete($id);
        return true;
    }


    // import accessories
    public function importAccessories(string $csv_file): array
    {
        $reader = Reader::createFromPath($csv_file, 'r');
        $reader->setHeaderOffset(0);
        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }
        $defaultFields = $this->getDefaultFields($headers);
        $requiredFields = [
            'parent_product',
            'web_product_range',
            'item_code',
            'price',
        ];
        $records = $reader->getRecords();
        $validData = [];
        $invalid = [];
        $processed = [];
        $updated = [];
        $duplicate = [];
        $existingData = [];

        // fetch existing data
        $fetchExistingData = $this->model->select(['product_accessories_id', 'parent_product_id', 'product_id','item_id','price'])->findAll(false);

        $existingDataMaps = [];
        foreach ($fetchExistingData as $existing) {
            $existingDataMaps[$existing['parent_product_id'] . '-' . $existing['product_id'] . '-' . $existing['item_id']][] = $existing['product_accessories_id'];
        }

        // product name and its id
        $productNameMap = $this->product->select(['product_id', 'product_code'])->limit(0)->findAll(false);
        $productNameMap = array_column($productNameMap, 'product_id', 'product_code');
       
        // item 
        // how to get item_code for the record
        
        $itemData = $this->item->whereIn('item_code', array_column(iterator_to_array($records), 'item_code'))
        ->select(['item_id', 'item_code'])
        ->limit(0)
        ->findAll(false);
        $itemData = array_column($itemData, 'item_id', 'item_code');

        $existingData = [
            'productIdMap' => $productNameMap,
            'itemIdMap' => $itemData,
            'existingDataMaps' => $existingDataMaps,
        ];

        foreach ($records as $offset => $record) {
            $record = $this->prepareRecord($record, $defaultFields);
            $validator = new ProductAccessoriesValidation($record, $requiredFields, array_keys($defaultFields), $existingData);
            $validated = $validator->validate();

            // small case and replace space with -
            // $parentProductCode = strtolower(str_replace(' ', '-', $record['parent_product'])); 
            // $webProductRange = strtolower(str_replace(' ', '-', $record['web_product_range'])); 
            // $parentProductId = $productNameMap[$parentProductCode] ?? null;
            // $productId = $productNameMap[$webProductRange] ?? null;

            
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
                $updated[] = [
                    'row' => $offset + 2,
                    'data' => $record,
                    'identifier' => $unique
                ];
                continue;
            }

            // if duplicate in the CSV (compare parent_product_id, product_id and item_id against already-collected valid rows)
            $parentProductId = $record['parent_product_id'] ?? null;
            $productId = $record['product_id'] ?? null;
            $itemId = $record['item_id'] ?? null;
            if ($parentProductId !== null && $productId !== null && $itemId !== null) {
                $parentProductIdInValidData = array_column($validData, 'parent_product_id');
                $productIdInValidData = array_column($validData, 'product_id');
                $itemIdInValidData = array_column($validData, 'item_id');
                if (in_array($parentProductId, $parentProductIdInValidData, true) && in_array($productId, $productIdInValidData, true) && in_array($itemId, $itemIdInValidData, true)) {
                    $duplicate[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'reason' => 'duplicate in CSV'
                    ];
                    continue;
                }
            }

            if (in_array($validator->toArray()['data']['parent_product_id'] . '-' . $validator->toArray()['data']['product_id'] . '-' . $validator->toArray()['data']['item_id'], $processed, true)) {
                $updated[] = [
                    'row' => $offset + 2,
                    'data' => $validator->toArray()['data'],
                    'reason' => 'duplicate in database'
                ];
                continue;
            }
            if ($validator->isExistingData) {
                $existingData[] = $record;
                $updated[] = [
                    'row' => $offset + 2,
                    'data' => $record,
                    'reason' => 'existing in database'
                ];
                continue;
            } else {
                $validData[] = $validator->toArray()['data'];
            }
            $processed[] = $unique;
        }
        try {
            $this->db->beginTransaction();
            // if (count($existingData) > 0) {
            //     $this->model->upsert($existingData, ['tax_type_id']);
            // }
            if (count($validData) > 0) {
                $this->model->insert($validData);
            }
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to import product accessories: " . $e->getMessage());
        }

        return [
            'success' => true,
            'total_records' => iterator_count($records),
            'inserted_count' => count($validData),
            'inserted_data' => $validData,
            'valid_records' => count($validData),
            'valid_data' =>  $validData,
            'invalid_records' => count($invalid),
            'invalid_data' => $invalid,
            'updated_records' => count($updated),
            'updated_data' => $updated,
            'duplicated_records' => count($duplicate),
            'duplicated_data' => $duplicate,
            // 'existing_records' => count($existingData),
            // 'existing_data' => $existingData,
            'processed_records' => count($processed),
            'processed_data' => $processed,
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
        // $defaultFields['name'] = 'role_name'; // if have need more fields which is not in the CSV then add here.
    
        return $defaultFields;
    }

    private function prepareRecord(array $record, array $defaultFields): array
    {
        return isset($record['product_accessories_id']) && $record['product_accessories_id'] ? $record : array_merge($defaultFields, $record);
    }

} 