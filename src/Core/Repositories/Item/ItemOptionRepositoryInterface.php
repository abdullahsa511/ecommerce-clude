<?php

declare(strict_types=1);

namespace App\Core\Repositories\Item;

use App\Core\Models\Base\Model;
use App\Core\Models\Item\Item;
use App\Core\Models\Item\ItemData;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface ItemOptionRepositoryInterface extends BaseRepositoryInterface
{
    public function getItemOptions(): array;
    public function getItemOptionById(int $itemOptionId): ?array;
    public function createItemOption(array $itemOptionData): ?array;
    public function updateItemOption(int $id, array $itemOptionData): ?array;
    public function deleteItemOption(int $id): bool;
    public function importItemOptions(string $csvFilePath): array;
    public function deleteItemOptionGroup(array $ids): bool;
    public function updateItemOptionImage(array $images, int $item_option_id): bool;
    public function deleteItemOptionImage(int $item_option_id): bool;
} 