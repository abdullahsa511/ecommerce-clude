<?php

declare(strict_types=1);

namespace App\Core\Repositories\Pinboard;

use App\Core\Models\Pinboard\Pinboard;
use App\Core\Models\Pinboard\PinboardData;
use App\Core\Models\Pinboard\PinboardResponse;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface PinboardRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all pinboards
     *
     * @return array
     */
    public function all(): array;

    /**
     * Get all pinboards
     *
     * @return array
     */
    public function findAll(): array;

    /**
     * Get pinboards by company ID
     *
     * @param int $companyId
     * @return array
     */
    public function findByCompanyId(int $companyId): array;

    /**
     * Get pinboards by user ID
     *
     * @param int $userId
     * @return array
     */
    public function findByUserId(int $userId): array;

    /**
     * Get pinboard by reference number
     *
     * @param string $referenceNumber
     * @return Pinboard|null
     */
    public function findByReferenceNumber(string $referenceNumber): ?Pinboard;

    /**
     * Create a new pinboard
     *
     * @param PinboardData $pinboardData
     * @return Pinboard
     */
    public function createPinboard(PinboardData $pinboardData): Pinboard;

    /**
     * Update an existing pinboard
     *
     * @param PinboardData $pinboardData
     * @return Pinboard
     */
    public function updatePinboard(PinboardData $pinboardData, array $fields = []): Pinboard;

    /**
     * Get a pinboard by ID
     *
     * @param int $pinboardId
     * @return Pinboard
     */
    public function showPinboard(int $pinboardId): Pinboard;

    public function getCustomerPinboard(int $customerId): ?Pinboard;
    
    public function getUserPinboard(int $userId): ?Pinboard;

    /**
     * Delete a pinboard
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Insert pinboards
     *
     * @param array $data
     * @return bool
     */
    public function insertPinboards(array $data): bool;

    /**
     * Get virtual pinboard data for virtual pinboard component
     *
     * @param array $param Optional parameters for filtering and limiting
     * @return array The virtual pinboard data with items
     */
    public function getVirtualPinboardComponentData(array $param = []);
    /**
     * Get revenue card widget data
     *
     * @return array
     */
    public function getPinboardWidget(int $limit = 14): array;
    public function savePinboard(array $data): array;

    /**
     * Get pinboard list component data
     *
     * @param array $param Optional parameters for filtering and limiting
     * @return array The pinboard list component data
     */
    public function getPinboardListComponentData(array $param = []): array;

    /**
     * Save pinboard comment
     *
     * @param array $data
     * @return array
     */
    public function savePinboardComment(array $data, array $files): array;
    public function bookingPhoneCall(array $data): array;
    public function getPinboardFinalBookingComponent(int|string $pinboardIdentifier, string $type = 'phone_call'): array;
    public function updatePinboardStatus(int $pinboardId, int $pinboardStatusId = 2): array;
    public function getBookingComponentContactSales(int $visitShowroomId): array;
    public function searchPinboardProducts(string $queryString);
    public function updateProjectTitle(array $data): array;

    public function updatePinboardVisibility(array $data): array;
    public function getPinboardIdByUuid(string $uuid): int;
    public function allPinboards(): array;
    public function createLead(int $pinboard_id): array;

    public function updatePinboardAfterLeadCreated(int $pinboardId, int $leadId): array;

    public function updatePinboardStatusByName(int $pinboardId, string $statusName): array;

    public function attachPinboardStatusToResponse(PinboardResponse $pinboard): PinboardResponse;
    public function automaticSendEmailClient(): array;
    public function countComment(int $pinboardId): array;
} 