<?php

declare(strict_types=1);

namespace App\Core\Repositories\Item;

use App\Core\Models\Base\Model;
use App\Core\Models\Item\Item;
use App\Core\Models\Item\ItemData;
use App\Core\ModelsFilters\RequestUri;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface ItemRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get a single product with all its related data
     * @return array{
     *  item: Item,
     *  content: array,
     *  images: array,
     *  related: array,
     *  variant: array,
     *  subscription: array,
     *  attribute: array,
     *  digital_asset: array,
     *  discount: array,
     *  promotion: array,
     *  points: array,
     *  option: array,
     *  option_value: array,
     *  option_value_content: array,
     *  sites: array
     * }
     */
    public function get(
        ?int $itemId = null,
        ?int $productId = null,
        ?string $slug = null,
        ?int $languageId = null,
        bool $includePromotion = false,
        bool $includePoints = false,
        bool $includeStockStatus = false,
        bool $includeWeightType = false,
        bool $includeLengthType = false,
        bool $includeRating = false,
        bool $includeReviews = false
    ): ?array;
    public function getItems(RequestUri &$requestUri) : ?array;
    public function getItemById(int $itemId);
    public function createItem(ItemData $itemData): ?Item;
    public function updateItem(ItemData $itemData): ?Item;
    // public function getItemById(int $itemId, int $languageId = 1): Item|null;
    // public function itemList(): array;
    public function importItems(string $csvFilePath): array;
    public function importDimensions(string $csvFilePath): array;
    public function searchItemOptions(string $name, int $product_id): array;
    public function getItemByItemCode(string $itemCode): ?bool;
    public function searchItems(string $name, int $product_id = null): array;
    public function insertItemTableImageFile(array $data, string $property, int $item_id): bool;
    public function deleteMediaByPath(string $property, int $item_id): bool;
} 