<?php

namespace App\Core\Repositories\Product;

use App\Core\Models\Product\Vendor;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Validation\VendorDataValidation;
use League\Csv\Reader;
use PDO;
use Exception;

use function App\Core\System\utils\env;

class VendorRepository extends BaseRepository implements VendorRepositoryInterface
{

    public function __construct(PDO $db)
    {
        parent::__construct($db, 'vendor', Vendor::class);
    }


    public function getAllVendors(): array
    {
        $query = $this->model->whereNull('deleted_at')->select(['vendor.*']);
        $results = $query->orderBy('vendor_id', 'desc')->findAll();
        return $results;
    }

    public function searchVendors(string $query): array
    {
        $query = $this->model->whereNull('deleted_at')->where('name', 'like', '%' . $query . '%')->select(['vendor_id', 'name, vendor_code']);
        $results = $query->orderBy('vendor_id', 'desc')->findAll();
        return $results;
    }

    
    public function getVendorById(int $id): ?array
    {
        $query = $this->model->whereNull('deleted_at')->where('vendor_id', '=', $id)->select(['vendor.*']);
        $result = $query->first();
        return $result ? (array) $result->data: [];
    }

    public function createVendor(array $data): ?array
    {
        $data['slug'] = strtolower(str_replace(' ', '-', $data['name']));
        $this->db->beginTransaction();
        try {
            $obj = $this->model->create($data);
            $result = (array)$obj->data;
            $this->db->commit();
            return $result;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to create vendor: " . $e->getMessage());
        }
    }

    public function updateVendor(int $id, array $data): ?array
    {
        $vendor = $this->model->where('vendor_id', '=', $id)->first();
        if (!$vendor) {
            return []; // vendor not found
        }
        try {
            $data['slug'] = strtolower(str_replace(' ', '-', $data['name']));
            $data['vendor_code'] = strtolower(str_replace(' ', '-', $data['vendor_code']));
            $this->db->beginTransaction();
            $vendor->update($data);
            $this->db->commit();
            return $this->getVendorById($id);
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to update vendor: " . $e->getMessage());
        }
    }

    public function deleteVendor(int $id): bool
    {
        $query = $this->model->whereNull('deleted_at')->where('vendor_id', $id)->delete($id);
        return $query;
    }


    // Import vendors from CSV file
    public function importVendors(string $csv_file): array
    {
        $reader = Reader::createFromPath($csv_file, 'r');
        $reader->setHeaderOffset(0);
        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }
        $defaultFields = $this->getDefaultFields($headers);
        $requiredFields = [
            'vendor_code',
            'name',
            'slug',
            'image',
            'sort_order'
        ];
        $records = $reader->getRecords();

        $validData = [];
        $showData = [];
        $invalid = [];
        $updated = [];
        $processed = [];
        $existingData = [];
        $showExistingData = [];
        $existingVendorIds = $this->model->select(['vendor_id', 'vendor_code'])->limit(0)->findAll(false);
        $existingVendorIds = array_column($existingVendorIds, 'vendor_id', 'vendor_code');

        foreach ($records as $offset => $record) {
            try {
                $record = $this->prepareRecord($record, $defaultFields);
                $validator = new VendorDataValidation($record, $requiredFields, array_keys($defaultFields), $existingVendorIds);
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
                    $updated[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'identifier' => $unique
                    ];
                    continue;
                }
                if ($validated->isExistingData) {
                    $existingData[] = (array) $validated->vendor;
                    $showExistingData[] = $record;
                } else {
                    $validData[] = (array) $validated->vendor;
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
            if (count($validData) > 0) {
                $this->model->insert($validData);
            }
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update vendors: " . $e->getMessage());
        }

        return [
            'success' => true,
            'total_records' => iterator_count($records),
            'valid_records' => count($validData),
            'valid_data' => $showData,
            'invalid_records' => count($invalid),
            'updated_records' => count($updated),
            'updated_data' => $updated,
            'duplicated_records' => count($existingData),
            'duplicated_data' => $showExistingData,
            'vendors' => [
                'inserted_count' => count($validData),
                'valid_data' => $validData
            ],
            'invalid_data' => $invalid,
            'summary' => [
                'success_rate' => iterator_count($records) > 0
                    ? round((count($validData) / iterator_count($records)) * 100, 2) . '%'
                    : '0%',
                'vendor_processed' => count($validData),
                'vendor_records_created' => $validData,
                'errors' => count($invalid),
            ],
            'mapping_data' => [],
        ];
    }

    private function getDefaultFields(array $headers): array
    {
        $defaultFields = [];
        // Initialize all CSV headers as null by default
        foreach ($headers as $header) {
            $defaultFields[$header] = null;
        }

        $defaultFields['admin_id'] = 1;
        // $defaultFields['created_at'] = date('Y-m-d H:i:s');
        // $defaultFields['updated_at'] = date('Y-m-d H:i:s');

        return $defaultFields;
    }

    private function prepareRecord(array $record, array $defaultFields): array
    {
        return isset($record['vendor_code']) && $record['vendor_code'] ? $record : array_merge($defaultFields, $record);
    }

    // upoad vendor image
    public function updateVendorImage(array $data, int $vendor_id): bool
    {
        $vendor = $this->model->where('vendor_id', '=', $vendor_id)->first();
        if (!$vendor) {
            return false; // vendor not found
        }

        $imageServer = env('APP_URL');
        $dataobj = $data;

        $img = [];
        foreach ($dataobj as $item) {
            $img[] = [
                'vendor_id' => $vendor_id,
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
                'vendor_image_id' => $vendor_id,
            ];
        }
        $imgJson = json_encode($img);
        $this->db->beginTransaction();
        try {
            // UPDATE `vendor` SET `image` = $img WHERE `vendor`.`vendor_id` = $vendor_id
            $vendor->update(['image' => $imgJson]);
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // delete vendor image
    public function deleteVendorImage(int $vendor_id): bool
    {
        $vendor = $this->model->where('vendor_id', '=', $vendor_id)->first();
        if (!$vendor) {
            return false; // vendor not found
        }
        $vendor->update(['image' => null]);
        return true;
    }
}
