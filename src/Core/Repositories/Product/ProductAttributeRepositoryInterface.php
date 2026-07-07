<?php

declare(strict_types=1);

namespace App\Core\Repositories\Product;

use App\Core\Repositories\Base\BaseRepositoryInterface;

interface ProductAttributeRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all product attributes with optional pagination
     *
     * @param int|null $languageId
     * @param int|null $start
     * @param int|null $limit
     * @return array{items: array, total: int}
     */
    public function getAll(?int $languageId = null, ?int $start = null, ?int $limit = null): array;

    /**
     * Get a single product attribute by ID
     *
     * @param int $productAttributeId
     * @return array|null
     */
    public function get(int $productAttributeId): ?array;

    
} 