<?php

declare(strict_types=1);

namespace App\Core\Repositories\Product;

use App\Core\Models\Product\LengthType;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface LengthTypeRepositoryInterface extends BaseRepositoryInterface
{

    
    public function getAll(int $languageId, int $start = 0, int $limit = 10): array;
    // public function findByValue(float $value): ?LengthType;

    public function get(int $lengthTypeId, int $languageId): ?LengthType;

    public function createLenthType(array $data): array;

    public function updateLengthType(int $id, array $data): array;

    public function deleteLengthType(int $variant_id): ?LengthType;

    public function importCSVs(string $csv_file): array;


} 