<?php

declare(strict_types=1);

namespace App\Core\Repositories\Item;

use App\Core\Models\Item\RequestResponse\ItemVariantRequest;
use App\Core\Models\Item\VariantItem;
use App\Core\Repositories\Base\BaseRepositoryInterface;
use Exception;
interface VariantItemRepositoryInterface extends BaseRepositoryInterface
{
    public function getAll(int $languageId, int $start = 0, int $limit = 10): array;
    // public function findByValue(float $value): ?VariantsItem;
    public function get(int $variantItemId, int $languageId): ?VariantItem;

    public function createVariantItem(ItemVariantRequest $itemVariantRequest): array;

    public function updateVariantItem(ItemVariantRequest $itemVariantRequest): array;

    public function deleteVariantItem(int $variantItemId): bool;

    public function importVariantItem(string $csv_file): array;
    // variant 
    public function findByName(array $data, ?int $id = null): bool;

    public function getVariantByItem(int $item_id): array;
    public function getVariantByVariantId(int $variant_id): array;
    public function createProductVariant(array $data): array;
    public function checkDuplicateProudctVariant(int $product_id, string $variant_name): bool;
    public function checkDuplicateVariantItem(int $product_id, int $item_id): bool;
    public function addVariantItem(array $data): array;
    public function editVariantItem(array $data): array;
} 