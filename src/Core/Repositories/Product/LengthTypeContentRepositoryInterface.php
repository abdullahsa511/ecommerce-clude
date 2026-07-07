<?php

declare(strict_types=1);

namespace App\Core\Repositories\Product;

use App\Core\Models\Product\LengthTypeContent;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface LengthTypeContentRepositoryInterface extends BaseRepositoryInterface
{
    public function getAll(): array;
    public function deleteWhere(array $conditions): bool;
    public function findByLengthTypeAndLanguage(int $lengthTypeId, int $languageId): ?LengthTypeContent;

} 