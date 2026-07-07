<?php

declare(strict_types=1);

namespace App\Core\Repositories\Post;

use PDO;
use App\Core\Models\Post\PostMeta;
use App\Core\Repositories\Base\BaseRepository;

class PostMetaRepository extends BaseRepository implements PostMetaRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'product_content_meta', PostMeta::class);
    }

    /**
     * Get a single meta value
     */
    public function get(
        ?int $productId = null,
        ?string $namespace = null,
        ?string $key = null
    ): ?string {
        if ($key === null) {
            return null;
        }

        $query = $this->model;
        $query->where('key', '=', $key);

        if ($productId !== null) {
            $query->where('product_id', '=', $productId);
        }

        if ($namespace !== null) {
            $query->where('namespace', '=', $namespace);
        }

        $result = $query->findAll();
        if (empty($result)) {
            return null;
        }

        $meta = reset($result);
        return $meta instanceof PostMeta ? $meta->value : null;
    }

    /**
     * Set a single meta value
     */
    public function set(
        int $productId,
        string $namespace,
        string $key,
        string $value
    ): bool {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                INSERT INTO product_content_meta 
                    (product_id, namespace, `key`, value)
                VALUES 
                    (:product_id, :namespace, :key, :value)
                ON DUPLICATE KEY UPDATE 
                    value = VALUES(value)
            ");

            $result = $stmt->execute([
                'product_id' => $productId,
                'namespace' => $namespace,
                'key' => $key,
                'value' => $value
            ]);

            $this->db->commit();
            return $result;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Get multiple meta values
     * @return array<PostMeta>
     */
    public function getMulti(
        ?int $productId = null,
        ?string $namespace = null,
        ?array $keys = null
    ): array {
        $query = $this->model;

        if ($productId !== null) {
            $query->where('product_id', '=', $productId);
        }

        if ($namespace !== null) {
            $query->where('namespace', '=', $namespace);
        }

        if ($keys !== null && !empty($keys)) {
            $query->whereIn('key', $keys);
        }

        return $query->findAll();
    }

    /**
     * Set multiple meta values
     * @param array<array{key: string, value: string}> $meta
     */
    public function setMulti(
        int $productId,
        string $namespace,
        array $meta
    ): bool {
        if (empty($meta)) {
            return true;
        }

        try {
            $this->db->beginTransaction();

            $values = [];
            $params = [];
            foreach ($meta as $index => $item) {
                if (!isset($item['key']) || !isset($item['value'])) {
                    throw new \InvalidArgumentException('Meta item must contain key and value');
                }
                $values[] = "(:product_id, :namespace, :key{$index}, :value{$index})";
                $params["key{$index}"] = $item['key'];
                $params["value{$index}"] = $item['value'];
            }

            $stmt = $this->db->prepare("
                INSERT INTO product_content_meta 
                    (product_id, namespace, `key`, value)
                VALUES 
                    " . implode(', ', $values) . "
                ON DUPLICATE KEY UPDATE 
                    value = VALUES(value)
            ");

            $result = $stmt->execute(array_merge($params, [
                'product_id' => $productId,
                'namespace' => $namespace
            ]));

            $this->db->commit();
            return $result;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Delete meta values
     */
    public function delete(
        ?int $productId = null,
        ?string $namespace = null,
        ?array $keys = null
    ): bool {
        try {
            $this->db->beginTransaction();

            $conditions = [];
            $params = [];

            if ($namespace !== null) {
                $conditions[] = "namespace = :namespace";
                $params['namespace'] = $namespace;
            }

            if ($keys !== null && !empty($keys)) {
                $placeholders = str_repeat('?,', count($keys) - 1) . '?';
                $conditions[] = "`key` IN ($placeholders)";
                $params = array_merge($params, $keys);
            }

            if ($productId !== null) {
                $conditions[] = "product_id = :product_id";
                $params['product_id'] = $productId;
            }

            $sql = "DELETE FROM product_content_meta";
            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(' AND ', $conditions);
            }

            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute($params);

            $this->db->commit();
            return $result;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
} 