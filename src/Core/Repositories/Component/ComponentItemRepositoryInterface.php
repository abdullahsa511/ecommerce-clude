<?php

declare(strict_types=1);

namespace App\Core\Repositories\Component;

use App\Core\Models\Component\Component;
use App\Core\Models\Component\ComponentData;
use App\Core\Models\Component\ComponentItem;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface ComponentItemRepositoryInterface extends BaseRepositoryInterface
{
    public function addComponentItem(array $data): array;
    public function updateComponentItems(array $data, int $id): array;
} 