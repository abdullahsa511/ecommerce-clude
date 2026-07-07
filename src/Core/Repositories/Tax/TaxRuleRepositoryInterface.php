<?php

declare(strict_types=1);

namespace App\Core\Repositories\Tax;

use App\Core\Models\Tax\TaxRule;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface TaxRuleRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all tax rules with pagination
     * 
     * @param int|null $taxTypeId
     * @param int $start
     * @param int $limit
     * @return array{list: array<TaxRule>, total: int}
     */
    public function getAll(?int $taxTypeId, int $start, int $limit): array;

    /**
     * Get a single tax rule by ID
     * 
     * @param int $taxRuleId
     * @return TaxRule|null
     */
    public function get(int $taxRuleId): ?TaxRule;

    
} 