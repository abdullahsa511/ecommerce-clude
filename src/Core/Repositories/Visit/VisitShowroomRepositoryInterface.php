<?php

declare(strict_types=1);

namespace App\Core\Repositories\Visit;

use App\Core\Repositories\Base\BaseRepositoryInterface;
use App\Core\Models\Visit\VisitShowroom;
use DateTimeImmutable;

interface VisitShowroomRepositoryInterface extends BaseRepositoryInterface
{
    public function bookNow(array $data): array;
    public function fetchBookedDataByShowroomId(int $showroom_id, string $date, $visit_showroom_id = null, $tour_type = 'physicalTour'): array;
    public function bookingManagement( $userId, $showroom_id): array;

    public function getContactRequestList(): array;
    public function isExistsContactRequest(string $meeting_time, int $id): bool;
    public function getContactRequestById(int $id): array;
    public function updateContactRequest(array $data, int $id): ?object;
    public function deleteContactRequest(int $visit_showroom_id): bool;
    public function sendMessage(array $data): array;
    public function addVisitShowroom(array $data): array;
    public function updateVisitShowroom(array $data, int $visit_showroom_id): array;
    public function checkExistingData(array $data, $visit_showroom_id = null): array;
    public function checkExistingDataByCustomerId(array $data): array;
    public function rescheduleBooking(array $data): array;
    // public function cancelBooking_old(array $data): array;
    public function cancelBooking(int $visit_showroom_id): array;
    public function oneDayPriorVisitShowroomNotification(): array;
    public function oneDayPriorOnlineMeetingNotification(): array;
    public function absentCustomerNotification(int $visit_showroom_id): array;

    /**
     * Sydney / Melbourne / Brisbane rows (same payload as ShowroomDateTimeRepository).
     *
     * @return list<array<string, mixed>>
     */
    public function getShowroomDateTimes(DateTimeImmutable|string|null $at = null): array;
    public function getVisitShowroomIdByUuid(string $uuid): int;
}
