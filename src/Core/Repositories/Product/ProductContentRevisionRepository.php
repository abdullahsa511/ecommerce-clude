<?php

declare(strict_types=1);

namespace App\Core\Repositories\Product;

use PDO;
use App\Core\Models\Product\ProductContentRevision;
use App\Core\Repositories\Base\BaseRepository;

class ProductContentRevisionRepository extends BaseRepository implements ProductContentRevisionRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'product_content_revision', ProductContentRevision::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getAll(
        ?int $productId = null,
        ?int $languageId = null,
        ?string $createdAt = null,
        bool $includeContent = false,
        ?int $start = null,
        ?int $limit = null
    ): array {
        $fields = [
            'product_content_revision.product_id',
            'product_content_revision.language_id',
            'product_content_revision.created_at',
            'product_content_revision.admin_id'
        ];

        if ($includeContent) {
            $fields[] = 'content';
        }

        $query = $this->model->select($fields)->with(['admin']);

        if ($productId !== null) {
            $query->where('product_content_revision.product_id', '=', $productId);
        }

        if ($languageId !== null) {
            $query->where('product_content_revision.language_id', '=', $languageId);
        }

        if ($createdAt !== null) {
            $query->where('product_content_revision.created_at', '=', $createdAt);
        }

        // Get total count before pagination
        $total = $query->countAll();

        $query->orderBy('product_content_revision.created_at', 'DESC');

        if ($start !== null && $limit !== null) {
            $query->offset($start)->limit($limit);
        }

        return [
            'items' => $query->findAll(),
            'total' => $total
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function get(int $productId, int $languageId, string $createdAt): ?array
    {
        $result = $this->model->select(['product_content_revision.*'])
            ->with(['admin'])
            ->where('product_content_revision.product_id', '=', $productId)
            ->where('product_content_revision.language_id', '=', $languageId)
            ->where('product_content_revision.created_at', '=', $createdAt)
            ->findAll();

        if (empty($result)) {
            return null;
        }

        $data = get_object_vars($result[0]);
        unset($data['db']);
        return $data;
    }
} 