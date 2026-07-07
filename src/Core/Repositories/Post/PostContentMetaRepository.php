<?php

declare(strict_types=1);

namespace App\Core\Repositories\Post;

use PDO;
use App\Core\Models\Post\PostContentMeta;
use App\Core\Repositories\Base\BaseRepository;

class PostContentMetaRepository extends BaseRepository implements PostContentMetaRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'post_content_meta', PostContentMeta::class);
    }

    protected function getPrimaryKeyColumn(): string
    {
        return 'meta_id';
    }

    public function getAll(
        ?int $postId = null,
        ?string $namespace = null,
        ?array $keys = null,
        ?int $languageId = null,
        int $start = 0,
        int $limit = 10
    ): array {
        $query = $this->model;

        // Load relationships
        $query->with(['post', 'language']);

        // Apply filters
        if ($postId !== null) {
            $query->where('post_id', '=', $postId);
        }

        if ($namespace !== null) {
            $query->where('namespace', '=', $namespace);
        }

        if ($keys !== null && !empty($keys)) {
            $query->whereIn('key', $keys);
        }

        if ($languageId !== null) {
            $query->where('language_id', '=', $languageId);
        }

        // Apply ordering by composite key
        $query->orderBy('post_id', 'ASC')
              ->orderBy('language_id', 'ASC')
              ->orderBy('namespace', 'ASC')
              ->orderBy('key', 'ASC');

        // Apply pagination
        if ($limit !== null) {
            $query->limit($limit);
        }

        if ($start !== null) {
            $query->offset($start);
        }

        // Get results
        $results = $query->findAll() ?? [];
        $total = $query->countAll();
        $perPage = $limit ?? $this->model->limitValue;

        return [
            'items' => collect($results),
            'total' => $total,
            "total_pages" => (int)ceil($total / $perPage),
            "current_page" => (int)($start / $perPage + 1),
            "per_page" => $perPage
        ];
    }

    /**
     * Get a single meta value
     */
    public function get(
        int $postId,
        ?string $namespace = null,
        ?string $key = null,
        ?int $languageId = null
    ): ?PostContentMeta {
        $query = $this->model;

        $query->where('post_id', '=', $postId);

        if ($namespace !== null) {
            $query->where('namespace', '=', $namespace);
        }

        if ($key !== null) {
            $query->where('key', '=', $key);
        }

        if ($languageId !== null) {
            $query->where('language_id', '=', $languageId);
        }

        $result = $query->findAll();
        if (empty($result)) {
            return null;
        }
        
        return $query->find($postId);
    }

    public function create(array $data): ?PostContentMeta
    {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                INSERT INTO post_content_meta 
                    (post_id, language_id, namespace, `key`, value)
                VALUES 
                    (:post_id, :language_id, :namespace, :key, :value)
            ");

            // $query = $this->model->select(['*'])
            //     ->where('post_id', '=', $data['post_id'])
            //     ->where('language_id', '=', $data['language_id'])
            //     ->where('namespace', '=', $data['namespace'])
            //     ->where('`key`', '=', $data['key']);

            $result = $stmt->execute([
                'post_id' => $data['post_id'],
                'language_id' => $data['language_id'],
                'namespace' => $data['namespace'],
                'key' => $data['key'],
                'value' => $data['value']
            ]);

            if ($result) {
                $this->db->commit();
                return $this->model->set($data);
            }

            $this->db->rollBack();
            return null;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
} 