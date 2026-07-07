<?php

declare(strict_types=1);

namespace App\Core\Repositories\Coupon;

use PDO;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Models\Coupon\Coupon;
use App\Core\Models\Coupon\CouponData;
use App\Core\Validation\CouponDataValidation;
use League\Csv\Reader;

class CouponRepository extends BaseRepository implements CouponRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'coupon', Coupon::class);
    }

    /**
     * Get all coupons
     *
     * @return array
     */
    public function all(): array
    {
        return $this->model->orderBy('created_at', 'DESC')->whereNull('deleted_at')->findAll();
    }

    /**
     * Get coupons by status
     *
     * @param int $status
     * @return array
     */
    public function findByStatus(int $status): array
    {
        return $this->model->where('status', '=', $status)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function find(int $id): ?object
    {
        $result = $this->model->find($id);
        

        return $result;
    }

    /**
     * Get coupon by code
     *
     * @param string $code
     * @return Coupon|null
     */
    public function findByCode(string $code): ?Coupon
    {
        return $this->model->where('code', '=', $code)->first();
    }

    public function createCoupon(CouponData $couponData): Coupon
    {
        $couponDataArray = $couponData->toArray();
        $coupon = $this->model->create($couponDataArray);
        return $coupon;
    }

    public function updateCoupon(CouponData $couponData): Coupon
    {
        $couponDataArray = $couponData->toArray();
        $coupon = $this->model->find($couponDataArray['coupon_id']);
        $coupon = $coupon->update($couponDataArray);

        return $coupon;
    }

    public function showCoupon(int $couponId): Coupon
    {
        $coupon = $this->model->where('coupon_id', '=', $couponId)
        ->with([
            'couponProducts' => function($query) use ($couponId){
                return $query->join('product', 'product.product_id', '=', 'couponProducts.product_id')
                ->join('product_content', 'product_content.product_id', '=', 'product.product_id')
                ->select([
                    'product.product_id',
                    'product.description',
                    'product.price',
                    'product_content.name'
                ]);
            },
        ])
        ->first();

        return $coupon;
    }

    public function deleteCoupon(int $id): ?Coupon
    {
        try {
            $this->db->beginTransaction();

            $this->model->clearQuery();
            $coupon = $this->model->where('coupon_id', '=', $id)->first();
            if (!$coupon) {
                return null;
            }
            $coupon->update(['deleted_at' => date('Y-m-d H:i:s')]);

            $this->db->commit();
            return $coupon;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to delete coupon: " . $e->getMessage());
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
        $requiredFields = ['name', 'code', 'discount', 'type', 'free_shipping', 'date_start', 'date_end', 'status', 'coupon_limit', 'user_limit', 'registered_user_only', 'cart_total_min'];

        $validData = [
            'coupon' => [],
        ];
        $showFrontendValidData = ['coupon' => []];
        $existingData = [];
        $showFrontendExistingData = [];
        $invalid = [];
        $updated = [];
        $processed = [];

        $existingCouponMap = $this->model->select(['coupon_id','code'])->limit(0)->findAll(false);
        // $existingmap = array_column($existingCouponMap,'code','coupon_id');
        $existingmap = array_column($existingCouponMap,'coupon_id','code');
        $existIds = array_values($existingmap);

        $existingDataMaps = [
            'existsCoupon' => $existingmap,
            'existIds' => $existIds,
        ];

        foreach ($records as $offset => $record) {
            try {
                // Merge defaults with record (ensures required fields exist)
                $record = $this->prepareRecord($record, $defaultFields);

                $validator = new CouponDataValidation($record,$requiredFields, array_keys($defaultFields), $existingDataMaps);
                $validated = $validator->validate();

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
                    // $existingData[] = (array) $validated->coupon;
                    // $showFrontendExistingData[] = $record;
                } else {
                    // $validData['coupon'][] = (array) [
                    //     'name' => $validated->coupon->name,
                    //     'code' => $validated->coupon->code,
                    //     'type' => $validated->coupon->type,
                    //     // 'cart_total_min' => $validated->coupon->cart_total_min,
                    //     'discount' => $validated->coupon->discount,
                    //     'free_shipping' => $validated->coupon->free_shipping,
                    //     'status' => $validated->coupon->status,
                        
                    //     'date_start' => $validated->coupon->date_start,
                    //     'date_end' => $validated->coupon->date_end,
                    // ]; 
                    $validData['coupon'][] = (array) $validated->coupon;
                    $showFrontendValidData['coupon'][] = (array) $validated->coupon;
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

            // if(count($existingData) > 0){
            //     $this->model->upsert($existingData, ['coupon_id', 'code']);
            // }

            if (count($validData['coupon']) > 0) {
                // $this->model->upsert($validData['coupon'], ['coupon_id', 'code']);
                $this->model->insert($validData['coupon']);
                // $this->model->clearQuery();
                // $this->model->softDelete(false);
            }

            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update coupon: " . $e->getMessage());
        }

        return [
            'success' => true,
            'total_records' => iterator_count($records),
            'valid_records' => count($validData['coupon']),
            'valid_data' => $showFrontendValidData['coupon'],
            'invalid_records' => count($invalid),
            'updated_records' => count($updated),
            'updated_data' => $updated,
            'duplicated_records' => count($updated),
            'duplicated_data' => $updated,
            'coupons' => [
                'inserted_count' => count($validData['coupon']),
                'valid_data' => $validData['coupon']
            ],
            'coupons' => [
                'inserted_count' => count($showFrontendValidData['coupon']),
                'valid_data' => $showFrontendValidData['coupon']
            ],
            'invalid_data' => $invalid,

            'summary' => [
                'success_rate' => iterator_count($records) > 0
                    ? round((count($validData['coupon']) / iterator_count($records)) * 100, 2) . '%'
                    : '0%',
                'productDiscount_processed' => count($validData['coupon']),
                'content_records_created' => $validData['coupon'],
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
        $defaultFields['code'] = null;
        $defaultFields['coupon_id'] = null;
        $defaultFields['status'] = 1;
        $defaultFields['coupon_limit'] = 50;
        $defaultFields['user_limit'] = 1000;
        $defaultFields['registered_user_only'] = 1;
        $defaultFields['cart_total_min'] = 1111;

        return $defaultFields;
    }
    private function prepareRecord(array $record, array $defaultFields): array
    {
        return isset($record['coupon_id']) && $record['coupon_id'] ? $record : array_merge($defaultFields, $record);
    }
} 