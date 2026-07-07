<?php

declare(strict_types=1);

namespace App\Core\Repositories\Post;

use PDO;
use App\Core\Models\Post\PostContentRevision;
use App\Core\Repositories\Base\BaseRepository;

class PostContentRevisionRepository extends BaseRepository implements PostContentRevisionRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'post_content_revision', PostContentRevision::class);
    }

    /**
     * Get all revisions with pagination
     * @return array{items: array<PostContentRevision>, total: int}
     */
    public function getAll(
        ?int $postId = null,
        ?int $languageId = null,
        ?string $createdAt = null,
        bool $includeContent = false,
        int $start = 0,
        int $limit = 10
    ): array {
        $query = $this->model;

        // Select specific columns
        $columns = [
            'post_content_revision.post_id',
            'post_content_revision.language_id',
            'post_content_revision.created_at',
            'post_content_revision.admin_id',
            'admin.display_name',
            'admin.username'
        ];

        if ($includeContent) {
            $columns[] = 'post_content_revision.content';
        }

        $query->select($columns)
              ->with(['admin']);

        // Apply filters
        if ($postId !== null) {
            $query->where('post_content_revision.post_id', '=', $postId);
        }

        if ($languageId !== null) {
            $query->where('post_content_revision.language_id', '=', $languageId);
        }

        if ($createdAt !== null) {
            $query->where('post_content_revision.created_at', '=', $createdAt);
        }

        // Apply ordering and pagination
        $query->orderBy('post_content_revision.created_at', 'DESC')
              ->limit($limit)
              ->offset($start);

        // Get results
        $items = $query->findAll();
        $total = $query->countAll();

        return [
            'items' => $items,
            'total' => $total
        ];
    }

    /**
     * Get a specific revision
     */
    public function get(
        int $postId,
        int $languageId,
        string $createdAt
    ): ?PostContentRevision {
        $query = $this->model;

        $query->with(['admin'])
              ->where('post_id', '=', $postId)
              ->where('language_id', '=', $languageId)
              ->where('created_at', '=', $createdAt);

        return $query->findAll()[0] ?? null;
    }

    /**
     * Delete a specific revision
     */
    public function deleteRevision(
        int $postId,
        int $languageId,
        string $createdAt
    ): bool {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("
                DELETE FROM post_content_revision 
                WHERE post_id = :post_id 
                AND language_id = :language_id 
                AND created_at = :created_at
            ");

            $result = $stmt->execute([
                'post_id' => $postId,
                'language_id' => $languageId,
                'created_at' => $createdAt
            ]);

            $this->db->commit();
            return $result;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    
} 