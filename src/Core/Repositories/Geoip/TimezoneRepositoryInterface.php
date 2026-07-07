<?php

declare(strict_types=1);

namespace App\Core\Repositories\Geoip;

use App\Core\Repositories\Base\BaseRepositoryInterface;
use App\Core\Models\Geoip\Timezone;

interface TimezoneRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all timezones with pagination and filtering
     */
    public function getAll(
        ?int $status = null,
        ?string $search = null,
        int $start = 0,
        int $limit = 10
    ): array;

    /**
     * Get a single timezone by ID
     */
    public function get(int $timezoneId): ?Timezone;
} 