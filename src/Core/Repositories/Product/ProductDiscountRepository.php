<?php

declare(strict_types=1);

namespace App\Core\Repositories\Product;

use App\Core\Models\Product\ProductDiscount;
use App\Core\Repositories\Base\BaseRepository;
use PDO;
use App\Core\Models\Base\Model;
use App\Core\Models\Localisation\Language;
use App\Core\Models\Product\Product;
use App\Core\Models\User\UserGroupContent;
use App\Core\Validation\ProductDiscountDataValidation;
use League\Csv\Reader;

// use App\Core\Repositories\ValidationCSVFileRepository;

class ProductDiscountRepository extends BaseRepository implements ProductDiscountRepositoryInterface
{
 
    private Language $language;
    private Product $product;
    private UserGroupContent $userGroupContent;

    public function __construct(PDO $db, Product $product, UserGroupContent $userGroupContent)
    {
        parent::__construct($db, 'product_discount', ProductDiscount::class);
        $this->product = $product;
        $this->product->setDb($db);
        $this->userGroupContent = $userGroupContent;
        $this->userGroupContent->setDb($db);
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
        $query = $this->model;

        

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
     * @param int $productDiscountId Length type ID
     * @param int|null $languageId Optional language ID
     * @return ProductDiscount|null
     */
    public function get(int $productDiscountId, ?int $languageId = null): ?ProductDiscount
    {
        $query = $this->model;

      
        // Add product_discount_id filter
        $query->where('product_discount_id', '=', $productDiscountId);

       

        $result = $query->findAll();
        return !empty($result) ? $this->model->set($result[0]) : null;
    }

    public function findAll(): array 
    {
        $results = $this->model
        // ->with(['product', 'userGroup'])
        ->join('product', 'product.product_id', '=', 'product_discount.product_id')
        ->join('user_group_content','user_group_content.user_group_id', '=', 'product_discount.user_group_id')
        ->whereNull('product_discount.deleted_at')
        ->select([
            'product_discount.*',
            'product.product_code',
            'user_group_content.name as user_group_name',
        ])
        ->findAll(false);
        // encode user_group_content_data
        // foreach($results as $result){
        //     $result['product'] = json_decode($result['product'], true);
        //     $result['userGroup'] = json_decode($result['userGroup'], true);
        // }
        // 
        // $results = $this->model->whereNull('product_discount.deleted_at')->findAll(); 

        return $results ?? [];
    }

    public function find(int $id): ?object
    {
        $result = $this->model->with(['productDiscountContent'])->find($id);
        if ($result && isset($result->length_type_content_data)) {
            $result->length_type_content_data = json_decode($result->length_type_content_data, true);
        }

        return $result;
    }

    public function createProductDiscount(array $data): array
    {
        $response = [];
        try {
            $this->db->beginTransaction();
            $productDiscount = $this->model->create($data);
           
            $response = (array) $productDiscount->data;
           
            $this->db->commit();
            return $response;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update variants: " . $e->getMessage());
        }
    }


    public function updateProductDiscount(int $id, array $data): array
    {
        $this->db->beginTransaction();
        try {
            // Update length_type value
            $this->model->clearQuery();
            $productDiscount = $this->model->where('product_discount_id', '=', $id)->first();
            if (!$productDiscount) {
                throw new \Exception("Product discount not found");
            }
            $productDiscount->clearQuery();
            $productDiscount->update($data);
            $updated = $this->model->find($id);

            $this->db->commit();
            return $updated ? (array) $updated->data : [];
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to update product discount: " . $e->getMessage());
        }
    }

    public function deleteProductDiscount(int $productDiscountId): ?ProductDiscount
    {
        try {
            $this->db->beginTransaction();

            $this->model->clearQuery();
            $productDiscount = $this->model->where('product_discount_id', '=', $productDiscountId)->first();
            if (!$productDiscount) {
                return null;
            }
            $productDiscount->update(['deleted_at' => date('Y-m-d H:i:s')]);

            $this->db->commit();
            return $productDiscount;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to delete length type: " . $e->getMessage());
        }
    }

    // import data
    public function importCSVs(string $csv_file): array
    {
        $reader = Reader::createFromPath($csv_file, 'r');
        $reader->setHeaderOffset(0);
        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }
        $defaultFields = $this->getDefaultFields($headers);
        $records = $reader->getRecords();
        $requiredFields = ['product_id', 'user_group_id', 'discounted_price'];
        $valid = [];
        $invalid = [];
        $updated = [];
        $processed = [];

        $productMap = $this->product->select(['product_id', 'product_code'])->limit(0)->findAll(false);
        $productMap = array_column($productMap, 'product_id', 'product_code');

        $groupMap = $this->userGroupContent->select(['user_group_id', 'name'])->limit(0)->findAll(false);
        $groupMap = array_column($groupMap, 'user_group_id', 'name');

        $existingDiscountsMap = $this->model
        ->select(['product_discount_id','product_id', 'user_group_id', 'price','from_date','to_date'])
        ->whereNull('deleted_at')
        ->limit(0)
        ->findAll(false);

        $existingap = [];
        foreach($existingDiscountsMap as $discount){
            $uniqueIdentifier = $discount['product_id'] . '-' . $discount['user_group_id'] . '-' . $discount['from_date'] . '-' . $discount['to_date'];
            $existingap[$uniqueIdentifier] = $discount['product_discount_id'];
        }      

        $existingDataMaps = [
            'existsDiscount' => $existingap,
            'existsProduct' => $productMap,
            'existsGroup' => $groupMap,
            
        ];

        foreach ($records as $offset => $record) {
            try {
                // Merge defaults with record (ensures required fields exist)
                $record = $this->prepareRecord($record, $defaultFields);

                $validator = new ProductDiscountDataValidation($record, $requiredFields, array_keys($defaultFields), $existingDataMaps);
                $validated = $validator->validate();

                // $productId = $productCodeMap[$record['product_code']] ?? null;
              
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
                if ($validated->isExistingData) {
                    $updated[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'identifier' => $unique
                    ];
                    continue;
                } else {
                    $valid[] = (array) $validated->productDiscount; 
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

            if (count($valid) > 0) {
                $this->model->insert($valid);
            }

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update product discount: " . $e->getMessage());
        }

        return [
            'success' => true,
            'total_records' => iterator_count($records),
            'valid_records' => count($valid),
            'valid_data' => $valid,
            'invalid_records' => count($invalid),
            'updated_records' => count($updated),
            'updated_data' => $updated,
            'duplicated_records' => count($updated),
            'duplicated_data' => $updated,
            'product_discounts' => [
                'inserted_count' => count($valid),
                'valid_data' => $valid
            ],
            'invalid_data' => $invalid,
            'summary' => [
                'success_rate' => iterator_count($records) > 0
                    ? round((count($valid) / iterator_count($records)) * 100, 2) . '%'
                    : '0%',
                'product_discount_processed' => count($valid),
                'content_records_created' => $valid,
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
        $defaultFields['discounted_price'] = 0;

        return $defaultFields;
    }
    private function prepareRecord(array $record, array $defaultFields): array
    {
        return isset($record['product_discount_id']) && $record['product_discount_id'] ? $record : array_merge($defaultFields, $record);
    }

   
}
