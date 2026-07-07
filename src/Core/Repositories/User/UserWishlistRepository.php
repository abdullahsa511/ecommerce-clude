<?php

namespace App\Core\Repositories\User;

use App\Core\Models\User\UserWishlist;
use App\Core\Models\Base\Model;
use App\Core\Repositories\Base\BaseRepository;

class UserWishlistRepository extends BaseRepository implements UserWishlistRepositoryInterface
{
    protected Model $model;

    public function __construct(UserWishlist $model) 
    {
        parent::__construct($model);
        $this->model = $model;
    }

    /**
     * Get all products in user's wishlist
     * 
     * @param int $userId
     * @param int $languageId
     * @param int|null $start
     * @param int|null $limit
     * @return array
     */
    public function getAll(int $userId, int $languageId, ?int $start = null, ?int $limit = null): array
    {
        $query = $this->model->select(['user_wishlist.*'])
            ->with(['product' => function($query) use ($languageId) {
                $query->with(['content' => function($query) use ($languageId) {
                    $query->where('language_id', '=', $languageId);
                    return $query;
                }]);
                return $query;
            }])
            ->where('user_id', '=', $userId);

        if ($start !== null) {
            $query->offset($start);
        }

        if ($limit !== null) {
            $query->limit($limit);
        }

        $results = $query->findAll();
        $totalRecords = $query->countAll();

        return [
            'data' => $results,
            'total' => $totalRecords
        ];
    }

    /**
     * Get a specific product from user's wishlist
     * 
     * @param int $userId
     * @param int $productId
     * @param int $languageId
     * @return array|null
     */
    public function get(int $userId, int $productId, int $languageId): ?array
    {
        $result = $this->model->with(['product' => function($query) use ($languageId) {
                $query->with(['content' => function($query) use ($languageId) {
                    $query->where('language_id', '=', $languageId);
                    return $query;
                }]);
                return $query;
            }])
            ->where('user_id', '=', $userId)
            ->where('product_id', '=', $productId)
            ->limit(1)
            ->findAll();

        return !empty($result) ? $result[0] : null;
    }

    /**
     * Add a product to user's wishlist
     * 
     * @param int $userId
     * @param int $productId
     * @return int
     */
    public function add(int $userId, int $productId): int
    {
        // Using upsert to handle the IGNORE functionality from the SQL
        $data = [
            'user_id' => $userId,
            'product_id' => $productId
        ];

        $result = $this->model->upsert([$data], ['user_id', 'product_id']);
        return $result ? $productId : 0;
    }

    /**
     * Delete products from user's wishlist
     * 
     * @param int $userId
     * @param array|null $productIds
     * @return bool
     */
    public function delete(int $userId, ?array $productIds = null): bool
    {
        $query = $this->model->where('user_id', '=', $userId);

        if ($productIds !== null) {
            $query->whereIn('product_id', $productIds);
        }

        $records = $query->findAll();
        if (empty($records)) {
            return false;
        }

        $ids = array_column($records, 'user_id');
        return $this->model->deleteMultiple($ids);
    }
}