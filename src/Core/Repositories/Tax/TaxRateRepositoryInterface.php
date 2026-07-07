<?php

declare(strict_types=1);

namespace App\Core\Repositories\Tax;

use App\Core\Models\Tax\TaxRate;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface TaxRateRepositoryInterface extends BaseRepositoryInterface
{
    public function getAll(): array;
    
    public function findByRegionGroup(int $regionGroupId): array;
    
    public function findByName(string $name): ?TaxRate;
    
    public function findByType(string $type): array;
    public function importTaxRates(string $csv_file): array;
    public function isNameExists(string $name, ?int $id = 0):bool;
} 