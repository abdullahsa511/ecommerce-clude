<?php

declare(strict_types=1);

namespace App\Core\Repositories\Product;

use App\Core\Models\Product\Manufacturer;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Validation\ManufacturerDataValidation;
use League\Csv\Reader;
use PDO;

class ManufacturerRepository extends BaseRepository implements ManufacturerRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'manufacturer', Manufacturer::class);
    }

    public function getAllManufacturers(): array
    {
        $query = $this->model->whereNull('deleted_at')->select(['manufacturer.*']);
        $results = $query->orderBy('manufacturer_id', 'desc')->findAll();
        return $results;
    }
    
    public function getManufacturerById(int $id): ?array
    {
        $query = $this->model->whereNull('deleted_at')->where('manufacturer_id', '=', $id)->select(['manufacturer.*']);
        $result = $query->first();
        return $result ? (array) $result->data: [];
    }

    public function createManufacturer(array $data): ?array
    {
        $data['slug'] = strtolower(str_replace(' ', '-', $data['name']));
        $data['manufacturer_code'] = strtolower(str_replace(' ', '-', $data['manufacturer_code']));
        $this->db->beginTransaction();
        try {
            $obj = $this->model->create($data);
            $result = (array)$obj->data;
            $this->db->commit();
            return $result;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to create manufacturer: " . $e->getMessage());
        }
    }

    public function updateManufacturer(int $id, array $data): ?array
    {
        $manufacturer = $this->model->where('manufacturer_id', '=', $id)->first();
        if (!$manufacturer) {
            return []; // manufacturer not found
        }
        try {
            $data['slug'] = strtolower(str_replace(' ', '-', $data['name']));
            $data['manufacturer_code'] = strtolower(str_replace(' ', '-', $data['manufacturer_code']));
            $this->db->beginTransaction();
            $manufacturer->update($data);
            $this->db->commit();
            return $this->getManufacturerById($id);
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to update manufacturer: " . $e->getMessage());
        }
    }

    public function deleteManufacturer(int $id): bool
    {
        $query = $this->model->whereNull('deleted_at')->where('manufacturer_id', '=', $id)->delete($id);
        return $query;
    }           

    public function importManufacturers(string $csv_file): array
    {
        $reader = Reader::createFromPath($csv_file, 'r');
        $reader->setHeaderOffset(0);
        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }
        $defaultFields = $this->getDefaultFields($headers);
        $requiredFields = [
            'manufacturer_code',
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
        $existingManufacturerIds = $this->model->select(['manufacturer_id', 'manufacturer_code'])->limit(0)->findAll(false);
        $existingManufacturerIds = array_column($existingManufacturerIds, 'manufacturer_id', 'manufacturer_code');

        foreach ($records as $offset => $record) {
            try {
                $record = $this->prepareRecord($record, $defaultFields);
                $validator = new ManufacturerDataValidation($record, $requiredFields, array_keys($defaultFields), $existingManufacturerIds);
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
                    $existingData[] = (array) $validated->manufacturer;
                    $showExistingData[] = $record;
                } else {
                    $validData[] = (array) $validated->manufacturer;
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
            throw new \Exception("Failed to insert/update manufacturers: " . $e->getMessage());
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
            'manufacturers' => [
                'inserted_count' => count($validData),
                'valid_data' => $validData
            ],
            'invalid_data' => $invalid,
            'summary' => [
                'success_rate' => iterator_count($records) > 0
                    ? round((count($validData) / iterator_count($records)) * 100, 2) . '%'
                    : '0%',
                'manufacturer_processed' => count($validData),
                'manufacturer_records_created' => $validData,
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
        return isset($record['manufacturer_code']) && $record['manufacturer_code'] ? $record : array_merge($defaultFields, $record);
    }

    // upoad manufacturer image
    public function updateManufacturerImage(array $data, int $manufacturer_id): bool
    {
        $manufacturer = $this->model->where('manufacturer_id', '=', $manufacturer_id)->first();
        if (!$manufacturer) {
            return false; // manufacturer not found
        }
        $dataobj = $data;

        $img = [];
        foreach ($dataobj as $item) {
            $img[] = [
                'manufacturer_id' => $manufacturer_id,
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
                'manufacturer_image_id' => $manufacturer_id,
            ];
        }
        $imgJson = json_encode($img);
        $this->db->beginTransaction();
        try {
            // UPDATE `manufacturer` SET `image` = $img WHERE `manufacturer`.`manufacturer_id` = $manufacturer_id
            $manufacturer->update(['image' => $imgJson]);
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // delete manufacturer image
    public function deleteManufacturerImage(int $manufacturer_id): bool
    {
        $manufacturer = $this->model->where('manufacturer_id', '=', $manufacturer_id)->first();
        if (!$manufacturer) {
            return false; // manufacturer not found
        }
        $manufacturer->update(['image' => null]);
        return true;
    }


    
    //old code must need may be
    public function getManufacturingProcessComponentData()
    {
        $query = $this->model
            ->select([
                'manufacturer_id',
                'name as title',
                'image',
                'slug'
            ]);

        $query->orderBy('sort_order', 'ASC')
              ->limit(1);

        $result = $query->first();

        if ($result) {
            // Process image data
            $imageUrl = $result['image'] ?? '/img/about/manufacturing.png';
            
            // If image is stored as JSON, extract the URL
            if (is_string($imageUrl) && strpos($imageUrl, '{') === 0) {
                $imageData = json_decode($imageUrl, true);
                $imageUrl = $imageData['url'] ?? $imageData['objectURL'] ?? '/img/about/manufacturing.png';
            }

            return [
                'title' => $result['title'] ?? 'Manufacturing Process',
                'description' => 'Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas et. Pellentesque libero donec sit egestas orci consequat est mauris duis. In viverra hac vestibulum pretium.',
                'image' => $imageUrl,
                'link_text' => 'Visit Showroom'
            ];
        }

        // Return default data if no manufacturer found
        return [
            'title' => 'Manufacturing Process',
            'description' => 'Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas et. Pellentesque libero donec sit egestas orci consequat est mauris duis. In viverra hac vestibulum pretium.',
            'image' => '/img/about/manufacturing.png',
            'link_text' => 'Visit Showroom'
        ];
    }
} 