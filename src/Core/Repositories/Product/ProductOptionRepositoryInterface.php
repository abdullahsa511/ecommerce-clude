<?php

declare(strict_types=1);

namespace App\Core\Repositories\Product;

use App\Core\Repositories\Base\BaseRepositoryInterface;

interface ProductOptionRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all product options with pagination
     *
     * @param int $languageId Language ID
     * @param int|null $productId Product ID
     * @param int|null $start Start offset for pagination
     * @param int|null $limit Limit for pagination
     * @return array{items: array, total: int}
     */
    public function getAll(
        int $languageId,
        ?int $productId = null,
        ?int $start = null,
        ?int $limit = null
    ): array;

    /**
     * Get a single product option
     *
     * @param int $productOptionId Product option ID
     * @param int $languageId Language ID
     * @return array|null
     */
    public function get(int $productOptionId, int $languageId): ?array;
    public function getProductOptions(): array;
    public function getProductOptionById(int $productOptionId): ?array;
    public function createProductOption(array $productOptionData): ?array;
    public function updateProductOption(int $productOptionId, array $productOptionData): ?array;
    public function isProductOptionUnique(array $productOptionData, ?int $id = null): bool;
    public function deleteProductOption(int $id): bool;
    /**
     * Import product options from CSV file
     *
     * @param string $csv_file Path to the CSV file
     * @return array
     */
    public function importProductOptions(string $csv_file): array;
    public function searchProductOptions(string $name, int $product_id): array;
    public function findProductOptionsByGroupIds(array $groupIds): array;
    public function searchItemOptionsByQuery(string $name, int $product_id, int $product_variant_id, int $product_option_group_id): array;

}