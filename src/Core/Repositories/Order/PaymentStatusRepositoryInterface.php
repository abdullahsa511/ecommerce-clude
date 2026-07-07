<?php

declare(strict_types=1);

namespace App\Core\Repositories\Order;

use App\Core\Models\Order\PaymentStatus;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface PaymentStatusRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all payment statuses
     *
     * @param int|null $language_id
     * @param int $start
     * @param int $limit
     * @return array
     */
    public function getAll(?int $language_id = null, int $start = 0, int $limit = 10): array;

    /**
     * Get a single payment status
     *
     * @param int $payment_status_id
     * @return PaymentStatus|null
     */
    public function get(int $payment_status_id): ?PaymentStatus;

    
} 