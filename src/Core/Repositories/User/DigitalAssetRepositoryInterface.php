<?php

declare(strict_types=1);

namespace App\Core\Repositories\User;

use App\Core\Models\User\DigitalAsset;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface DigitalAssetRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all digital assets with optional filters
     * 
     * @param int|null $languageId Language ID
     * @param int|null $userId User ID
     * @param int|null $productId Product ID
     * @param int|null $orderStatusId Order status ID
     * @param int|null $start Pagination start
     * @param int|null $limit Pagination limit
     * @return array{items: array, total: int}
     */
    public function getAll(
        ?int $languageId = null,
        ?int $userId = null,
        ?int $productId = null,
        ?int $orderStatusId = null,
        ?int $start = null,
        ?int $limit = null
    ): array;

    /**
     * Get a specific digital asset
     * 
     * @param int $digitalAssetId Digital asset ID
     * @param int|null $userId User ID
     * @param int|null $languageId Language ID
     * @return DigitalAsset|null
     */
    public function get(int $digitalAssetId, ?int $userId = null, ?int $languageId = null): ?DigitalAsset;

} 