<?php

declare(strict_types=1);

namespace App\Core\Repositories\Product;

use App\Core\Models\Product\Product;
use PDO;
use App\Core\Models\Product\ProductMeta;
use App\Core\Repositories\Base\BaseRepository;
use League\Csv\Reader;

class ProductMetaRepository extends BaseRepository implements ProductMetaRepositoryInterface
{
    private Product $product;
    public function __construct(PDO $db, Product $product)
    {
        parent::__construct($db, 'product_content_meta', ProductMeta::class);
        $this->product = $product;
        $this->product->setDb($db);
    }

    /**
     * {@inheritdoc}
     */
    public function get(int $productId, string $namespace, string $key): ?string
    {
        $result = $this->model->select(['value'])
            ->where('key', '=', $key)
            ->where('product_id', '=', $productId)
            ->where('namespace', '=', $namespace)
            ->findAll();

        return $result[0]->value ?? null;
    }

    /**
     * {@inheritdoc}
     */
    public function set(int $productId, string $namespace, string $key, string $value): bool
    {
        $data = [
            'product_id' => $productId,
            'namespace' => $namespace,
            'key' => $key,
            'value' => $value
        ];

        // Try to update first
        $query = $this->model
            ->where('product_id', '=', $productId)
            ->where('namespace', '=', $namespace)
            ->where('key', '=', $key);

        if ($query->update(['value' => $value])) {
            return true;
        }

        // If update failed, try to create
        return $this->model->create($data) !== null;
    }

    /**
     * {@inheritdoc}
     */
    public function getMulti(int $productId, string $namespace, array $keys): array
    {
        $query = $this->model->select(['product_id', 'namespace', 'key', 'value'])
            ->where('product_id', '=', $productId)
            ->where('namespace', '=', $namespace);

        if (!empty($keys)) {
            $query->whereIn('key', $keys);
        }

        return $query->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function setMulti(int $productId, string $namespace, array $meta): bool
    {
        try {
            $this->db->beginTransaction();

            foreach ($meta as $key => $value) {
                $data = [
                    'product_id' => $productId,
                    'namespace' => $namespace,
                    'key' => $key,
                    'value' => $value
                ];

                // Try to update first
                $query = $this->model
                    ->where('product_id', '=', $productId)
                    ->where('namespace', '=', $namespace)
                    ->where('key', '=', $key);

                if (!$query->update(['value' => $value])) {
                    // If update failed, try to create
                    if ($this->model->create($data) === null) {
                        throw new \Exception('Failed to create product meta');
                    }
                }
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function importProductMeta(string $csv_file): array
    {
        $reader = Reader::createFromPath($csv_file, 'r');
        $reader->setHeaderOffset(0);
        $headers = $reader->getHeader();
        $records = $reader->getRecords();
        $productMetas = [];
        $validRecords = [];
        $invalid = [];
        $productIdMaps = $this->product->select(['product_id', 'product_code'])->limit(0)->findAll(false);
        $productCodeMaps = array_column($productIdMaps, 'product_code', 'product_id');
        foreach($records as $offset => $record){
            $validated = true;
            $productMeta = [];
            if(!isset($record['product_id'])){
                $validated = false;
                $invalid[] = [
                    'row' => $offset + 1, // +2 because CSV row count starts at 1 and includes header
                    'data' => $record,
                    'errors' => 'product_id is required'
                ];
                continue;
            }
            if(!isset($productCodeMaps[$record['product_id']]) ||
            $productCodeMaps[$record['product_id']] !== $record['product_code']){
                $validated = false;
                $invalid[] = [
                    'row' => $offset + 1, // +2 because CSV row count starts at 1 and includes header
                    'data' => $record,
                    'errors' => 'Product code mismatch or Product not found'
                ];
                continue;
            }
            if($validated){
                $validRecords[] = $record;
                if(isset($record['meta_keywords'])){
                    $productMeta['product_id'] = $record['product_id'];
                    $productMeta['key'] = 'meta_keywords';
                    $productMeta['value'] = $record['meta_keywords'];
                    $productMeta['namespace'] = 'enSeo';
                    $productMetas[] = $productMeta;
                }
                if(isset($record['meta_description'])){
                    $productMeta['product_id'] = $record['product_id'];
                    $productMeta['key'] = 'meta_description';
                    $productMeta['value'] = $record['meta_description'];
                    $productMeta['namespace'] = 'enSeo';
                    $productMetas[] = $productMeta;
                }
                if(isset($record['meta_content'])){
                    $productMeta['product_id'] = $record['product_id'];
                    $productMeta['key'] = 'meta_content';
                    $productMeta['value'] = $record['meta_content'];
                    $productMeta['namespace'] = 'enSeo';
                    $productMetas[] = $productMeta;
                }
            }

        }
        if(count($productMetas) > 0){
            $this->model->upsert($productMetas, ['product_id', 'namespace', 'key']);
        }
        $totalRecords = iterator_count($records);
        $totalInvalidRecords = count($invalid);
        $totalValidRecords = $totalRecords - $totalInvalidRecords;
        return [
            'success' => true,
            'total_records' => $totalRecords,
            'invalid_records' => count($invalid),
            'valid_records' => $totalValidRecords,
            'valid_data' => $validRecords,
            'invalid_data' => $invalid,
            
            'summary' => [
                'success_rate' => iterator_count($records) > 0
                    ? round((count($validRecords) / iterator_count($records)) * 100, 2) . '%'
                    : '0%',
                'errors' => count($invalid),
            ]
        ];
    }

} 