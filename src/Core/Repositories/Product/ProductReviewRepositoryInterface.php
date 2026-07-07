<?php

declare(strict_types=1);

namespace App\Core\Repositories\Product;

use App\Core\Repositories\Base\BaseRepositoryInterface;

interface ProductReviewRepositoryInterface extends BaseRepositoryInterface
{
    public function findAll(): array;
} 