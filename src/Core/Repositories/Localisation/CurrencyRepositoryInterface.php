<?php

declare(strict_types=1);

namespace App\Core\Repositories\Localisation;

use App\Core\Models\Localisation\Currency;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface CurrencyRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all currencies with optional filtering
     * 
     * @param int|null $currencyId Optional currency ID to filter by
     * @param int|null $status Optional status to filter by
     * @return array Array containing 'items' and 'total' count
     */
    public function getAll(?int $currencyId = null, ?int $status = null): array;

    /**
     * Get a single currency by ID
     * 
     * @param int $currencyId The currency ID to find
     * @return Currency|null The found currency or null
     */
    public function get(int $currencyId): ?Currency;
    public function isExistsCode(string $code, ?int $id = 0): bool;
} 