<?php

declare(strict_types=1);

namespace App\Core\Repositories\Product;

use App\Core\Repositories\Base\BaseRepositoryInterface;

interface ProductAccessoriesRepositoryInterface extends BaseRepositoryInterface
{
    public function findAll(): array;

    public function getAccessoriesData();

    public function importAccessories(string $csv_file): array;

    public function getAccessoriesByProductId(int $product_id): array;
    public function getAccessoriesById(int $id);

    public function deleteAccessories(int $id): bool;

} 