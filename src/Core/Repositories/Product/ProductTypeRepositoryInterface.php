<?php

declare(strict_types=1);

namespace App\Core\Repositories\Product;

use App\Core\Repositories\Base\BaseRepositoryInterface;
use App\Core\Models\Product\ProductType;

interface ProductTypeRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all product types with optional filtering
     */
    public function getAll(
        ?int $siteId = null,
        ?string $type = null,
        ?string $source = null,
        int $start = 0,
        int $limit = 10
    ): array;

    /**
     * Get a single product type by ID
     */
    public function get(int $productTypeId): ?ProductType;
    public function importProductTypes(string $csv_file): array;
    public function updateProductTypeImage(array $data, int $product_type_id): bool;
    public function deleteProductTypeImage(int $product_type_id): bool;
} 