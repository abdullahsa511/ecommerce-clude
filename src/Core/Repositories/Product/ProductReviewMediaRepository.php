<?php

declare(strict_types=1);

namespace App\Core\Repositories\Product;

use PDO;
use App\Core\Models\Product\ProductReviewMedia;
use App\Core\Repositories\Base\BaseRepository;

class ProductReviewMediaRepository extends BaseRepository implements ProductReviewMediaRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'product_review_media', ProductReviewMedia::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getAll(
        int $languageId,
        ?int $siteId = null,
        ?int $productId = null,
        ?int $productReviewId = null,
        ?int $status = null,
        ?int $start = null,
        ?int $limit = null
    ): array {
        $query = $this->model->select(['product_review_media.*'])
            ->with(['review']);

        if ($productId !== null) {
            $query->where('product_review_media.product_id', '=', $productId);
        }

        if ($productReviewId !== null) {
            $query->where('product_review_media.product_review_id', '=', $productReviewId);
        }

        if ($status !== null) {
            $query->join(
                'product_review',
                'product_review.product_review_id = product_review_media.product_review_id',
                'inner'
            )
            ->where('product_review.status', '=', $status);
        }

        // Get total count before pagination
        $total = $query->countAll();

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
    public function get(int $productReviewMediaId): ?array
    {
        $result = $this->model->select(['product_review_media.*'])
            ->with(['review'])
            ->where('product_review_media.product_review_media_id', '=', $productReviewMediaId)
            ->findAll();

        if (empty($result)) {
            return null;
        }

        $item = $result[0];
        $data = get_object_vars($item);
        unset($data['db']);
        return $data;
    }
} 