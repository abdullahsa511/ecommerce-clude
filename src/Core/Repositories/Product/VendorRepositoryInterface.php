<?php

namespace App\Core\Repositories\Product;

use App\Core\Repositories\Base\BaseRepositoryInterface;
use PhpParser\Node\Expr\NullsafeMethodCall;

interface VendorRepositoryInterface extends BaseRepositoryInterface
{
  
    public function getAllVendors(): array;

    public function getVendorById(int $id): ?array;
    public function createVendor(array $data): ?array;
    public function updateVendor(int $id, array $data): ?array;
    public function deleteVendor(int $id): bool;
    public function importVendors(string $csv_file): array;
    public function updateVendorImage(array $data, int $vendor_id): bool;
    public function deleteVendorImage(int $vendor_id): bool;
    public function searchVendors(string $query): array;

    // public function createLenthType(array $data): array;

    // public function updateVendor(int $id, array $data): array;

    // public function deleteVendor(int $variant_id): ?Vendor;

    // public function importCSVs(string $csv_file): array;
    
} 