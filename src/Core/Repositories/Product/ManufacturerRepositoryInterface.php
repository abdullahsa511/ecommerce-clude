<?php

declare(strict_types=1);

namespace App\Core\Repositories\Product;

use App\Core\Models\Product\Manufacturer;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface ManufacturerRepositoryInterface extends BaseRepositoryInterface
{
    public function getAllManufacturers(): array;

    public function getManufacturerById(int $id): ?array;
    public function createManufacturer(array $data): ?array;
    public function updateManufacturer(int $id, array $data): ?array;
    public function deleteManufacturer(int $id): bool;
    public function importManufacturers(string $csv_file): array;
    public function updateManufacturerImage(array $data, int $manufacturer_id): bool;
    public function deleteManufacturerImage(int $manufacturer_id): bool;
    public function getManufacturingProcessComponentData();
} 