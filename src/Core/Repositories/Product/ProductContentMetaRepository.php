<?php

declare(strict_types=1);

namespace App\Core\Repositories\Product;

use PDO;
use App\Core\Models\Product\ProductContentMeta;
use App\Core\Repositories\Base\BaseRepository;

class ProductContentMetaRepository extends BaseRepository implements ProductContentMetaRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'product_content_meta', ProductContentMeta::class);
    }

    /**
     * Get a single product content meta value
     *
     * @param int $productId Product ID
     * @param string $namespace Namespace
     * @param string $key Key
     * @param int|null $languageId Language ID
     * @return string|null
     */
    public function get(int $productId, string $namespace, string $key, ?int $languageId = null): ?string
    {
        $query = $this->model->select(['value']);
        
        $query->where('product_id', '=', $productId)
              ->where('namespace', '=', $namespace)
              ->where('key', '=', $key);

        if ($languageId !== null) {
            $query->where('language_id', '=', $languageId);
        }

        $result = $query->findAll();
        return $result[0]->value ?? null;
    }

    /**
     * Set a single product content meta value
     *
     * @param int $productId Product ID
     * @param string $namespace Namespace
     * @param string $key Key
     * @param string $value Value
     * @param int|null $languageId Language ID
     * @return bool
     */
    public function set(int $productId, string $namespace, string $key, string $value, ?int $languageId = null): bool
    {
        $data = [
            'product_id' => $productId,
            'namespace' => $namespace,
            'key' => $key,
            'value' => $value,
            'language_id' => $languageId
        ];

        // Try to update first
        $query = $this->model
            ->where('product_id', '=', $productId)
            ->where('namespace', '=', $namespace)
            ->where('key', '=', $key);

        if ($languageId !== null) {
            $query->where('language_id', '=', $languageId);
        }

        if ($query->update(['value' => $value])) {
            return true;
        }

        // If update failed, try to create
        return $this->model->create($data) !== null;
    }

    /**
     * Get multiple product content meta values
     *
     * @param int $productId Product ID
     * @param string $namespace Namespace
     * @param array $keys Array of keys
     * @param int|null $languageId Language ID
     * @return array
     */
    public function getMulti(int $productId, string $namespace, array $keys, ?int $languageId = null): array
    {
        $query = $this->model->select(['product_id', 'namespace', 'key', 'value', 'language_id']);
        
        $query->where('product_id', '=', $productId)
              ->where('namespace', '=', $namespace)
              ->whereIn('key', $keys);

        if ($languageId !== null) {
            $query->where('language_id', '=', $languageId);
        }

        return $query->findAll();
    }

    /**
     * Set multiple product content meta values
     *
     * @param int $productId Product ID
     * @param array $meta Array of meta data [['namespace' => string, 'key' => string, 'value' => string, 'language_id' => int], ...]
     * @return bool
     */
    public function setMulti(int $productId, array $meta): bool
    {
        try {
            $this->db->beginTransaction();

            foreach ($meta as $item) {
                $item['product_id'] = $productId;
                
                // Try to update first
                $query = $this->model
                    ->where('product_id', '=', $productId)
                    ->where('namespace', '=', $item['namespace'])
                    ->where('key', '=', $item['key']);

                if (isset($item['language_id'])) {
                    $query->where('language_id', '=', $item['language_id']);
                }

                if (!$query->update(['value' => $item['value']])) {
                    // If update failed, try to create
                    if ($this->model->create($item) === null) {
                        throw new \Exception('Failed to create product content meta');
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
} 