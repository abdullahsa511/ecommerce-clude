<?php

declare(strict_types=1);

namespace App\Core\Repositories\ProductOptionGroup;

use App\Core\Models\ProductOptionGroup\ProductOptionGroup;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface ProductOptionGroupRepositoryInterface extends BaseRepositoryInterface
{
    public function getProductOptionGroups(): array;
    public function getProductOptionGroupById($id);
    public function createProductOptionGroup($data): array;
    public function updateProductOptionGroup($data, $id): array;
    public function deleteProductOptionGroup(int $product_option_group_id): bool;
    public function importProductOptionGroups(string $csv_file): array;
    public function searchProductOptionGroups(string $name, int $product_id): array;
    public function findProductOptionGroupsByNames(array $names, int $product_id, int $product_variant_id): array;
    public function searchItemOptionGroups(string $name, int $product_id, $product_variant_id = null): array;
    public function searchItemOptionGroupsByQuery(string $name, int $product_id, $product_variant_id = null): array;
} 