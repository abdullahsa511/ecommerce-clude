<?php

declare(strict_types=1);

namespace App\Core\Repositories\Product;

use App\Core\Models\Product\WeightType;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface WeightTypeRepositoryInterface extends BaseRepositoryInterface
{

    
    public function getAll(int $languageId, int $start = 0, int $limit = 10): array;
    // public function findByValue(float $value): ?WeightType;

    public function get(int $weightTypeId, int $languageId): ?WeightType;

    public function createWeightType(array $data): array;

    public function updateWeightType(int $id, array $data): array;

    public function deleteWeightType(int $weightTypeId): ?WeightType;

    public function importCSVs(string $csv_file): array;


} 