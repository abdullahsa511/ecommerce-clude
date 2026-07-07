<?php

declare(strict_types=1);

namespace App\Core\Repositories\Tax;

use App\Core\Models\Tax\TaxType;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface TaxTypeRepositoryInterface extends BaseRepositoryInterface
{
    public function isNameExists(string $name):bool;
    public function getAll(): array;
    
    public function findByName(string $name): ?TaxType;
    
    public function findByContent(string $content): ?TaxType;
    public function importTaxTypes(string $csv_file): array;
} 