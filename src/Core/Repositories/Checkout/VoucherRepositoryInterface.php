<?php

namespace App\Core\Repositories\Checkout;

use App\Core\Repositories\Base\BaseRepositoryInterface;

interface VoucherRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all vouchers with optional filtering and pagination
     * 
     * @param int $languageId
     * @param int|null $start
     * @param int|null $limit
     * @return array
     */
    public function getAll(int $languageId, ?int $start = null, ?int $limit = null): array;

    /**
     * Get a single voucher by ID
     * 
     * @param int $voucherId
     * @return array|null
     */
    public function get(int $voucherId): ?array;

    
} 