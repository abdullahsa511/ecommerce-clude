<?php

declare(strict_types=1);

namespace App\Core\Repositories\User;

use App\Core\Models\User\DigitalAssetLog;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface DigitalAssetLogRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all digital asset logs with optional filters
     * 
     * @param int|null $userId User ID
     * @param int|null $start Pagination start
     * @param int|null $limit Pagination limit
     * @return array{items: array, total: int}
     */
    public function getAll(
        ?int $userId = null,
        ?int $start = null,
        ?int $limit = null
    ): array;

    /**
     * Get a specific digital asset log
     * 
     * @param int $digitalAssetLogId Digital asset log ID
     * @param int|null $userId User ID
     * @param int|null $siteId Site ID
     * @return DigitalAssetLog|null
     */
    public function get(int $digitalAssetLogId, ?int $userId = null, ?int $siteId = null): ?DigitalAssetLog;
} 