<?php

declare(strict_types=1);

namespace App\Core\Repositories\Product;

use App\Core\Repositories\Base\BaseRepositoryInterface;

interface ProductQuestionRepositoryInterface extends BaseRepositoryInterface
{
    public function findAll(): array;
} 