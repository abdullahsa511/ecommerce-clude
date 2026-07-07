<?php

declare(strict_types=1);

namespace App\Core\Repositories\Type;

use App\Core\Models\Type\Type;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface TypeRepositoryInterface extends BaseRepositoryInterface
{
    public function getTypes(): array;
    public function getTypeById($id);
    public function findTypeByName(string $name);
    public function createType(array $data): array;
    public function updateType(array $data, $id): array;
    public function deleteType(int $type_id): bool;
    public function importTypes(string $csv_file): array;
} 