<?php

declare(strict_types=1);

namespace App\Core\Repositories\Variant;

use App\Core\Models\Variant\ProductVariant;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface ProductVariantRepositoryInterface extends BaseRepositoryInterface
{
    public function getVariants(): array;
    public function getVariantById($id);
    public function createVariant($data): array;
    public function updateProductVariant($data, $id): array;
    public function deleteVariant(int $variant_id): bool;
    public function importVariants(string $csv_file): array;
    public function searchVariants(int $product_id, string|null $name = null): array;
    public function searchVariantItems(int $product_id, string|null $name = null): array;
    public function getProductVariantById(int $product_variant_id): array;
    public function getVariantsByProductId(int $product_id): array;
    public function findByName(array $data, ?int $id = null): bool;
    public function searchItemOptionVariants(string $name, int $product_id): array;

    public function uploadProductOptionImage(array $data, int $product_option_id): bool;
    public function deleteVariantImage(int $product_variant_id): bool;
    public function deleteVariantOptionImage(int $product_variant_option_id): bool;
    public function getKmItemIdsByProductId(array $product_ids): array;
    public function getDefaultItemByProductId(int $product_id): array;
} 