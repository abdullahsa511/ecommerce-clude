<?php

declare(strict_types=1);

namespace App\Core\Repositories\Product;

use App\Core\Models\Product\ProductDiscount;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface ProductDiscountRepositoryInterface extends BaseRepositoryInterface
{

    
    public function getAll(int $languageId, int $start = 0, int $limit = 10): array;
    // public function findByValue(float $value): ?ProductDiscount;

    public function get(int $productDiscountId, int $languageId): ?ProductDiscount;

    public function createProductDiscount(array $data): array;

    public function updateProductDiscount(int $id, array $data): array;

    public function deleteProductDiscount(int $variant_id): ?ProductDiscount;

    public function importCSVs(string $csv_file): array;


} 