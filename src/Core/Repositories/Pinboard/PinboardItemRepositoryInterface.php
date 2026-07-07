<?php

declare(strict_types=1);

namespace App\Core\Repositories\Pinboard;

use App\Core\Models\Pinboard\PinboardItem;
use App\Core\Models\Pinboard\PinboardItemData;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface PinboardItemRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Create a new pinboard item
     *
     * @param PinboardData $pinboardData
     * @return PinboardItem
     */
    public function createPinboardItem(array $pinboardItems): array;

    /**
     * Update an existing pinboard item
     *
     * @param PinboardData $pinboardData
     * @return PinboardItem
     */
    public function updatePinboardItem(PinboardItemData $pinboardItemData): PinboardItem;

    /**
     * Get a pinboard item by ID
     *
     * @param int $pinboardId
     * @return PinboardItem
     */
    public function showPinboardItem(int $pinboardId): PinboardItem;

    public function productList(string $search): array;

    public function deleteByPinboardId(int $pinboardId): bool;

    public function deleteByPinboardItemId(int $pinboardItemId): bool;

    public function createPinboardItems(array $pinboardItems): array;
    public function getPinboard(?int $userId = null, ?int $pinboardId = null, ?int $customerId = null): array;
    public function getPinboardWithAllStatus(?int $userId = null, ?int $pinboardId = null): array;
    public function reorderPinboardItems(array $pinboardItems): array;
    public function getPinboardItemsByPinboardId(int $pinboardId): array;
    public function addToPinboard(array $pinboardItems): array;
    public function addToPinboardItemImages(array $pinboardItems): array;
    public function addToPinboardProductConfigurator(array $pinboardItems): array;
    public function authUser(): array;
    public function pinboardItemForComponent(int $pinboardId): array;
    public function updateCommentDescription(array $data): array;
    public function updateProjectPinboardId(int $pinboardId, int $userId): array;
    // public function getCommentPhotoForComponent(int $pinboardId): array;
    // public function getPinboardBookingVirtualMeetingComponent(int $pinboardId): array;
    // public function getPinboardBookingShowroomVisitComponent(int $pinboardId): array;
} 