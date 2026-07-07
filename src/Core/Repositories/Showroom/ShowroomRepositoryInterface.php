<?php

declare(strict_types=1);

namespace App\Core\Repositories\Showroom;

use App\Core\Repositories\Base\BaseRepositoryInterface;

interface ShowroomRepositoryInterface extends BaseRepositoryInterface
{
    public function insertShowroomData(array $data): void; // Insert showroom data
    public function createShowroom($data);
    public function deleteShowroom(int $id): bool;
    public function getShowroomData(): ?array;
    public function sectionDetails($showroom_slug, $slug);
    public function getShowroom($option = []);
    public function findById($id);
    public function findBySlug($slug);
    public function findSectionById($id);
    public function findShowroomSectionById($id);
    public function showroomDetails();
    public function updateShowroom(int $id, array $data, string $property = 'image'): ?array;
    public function addSection(string $id, array $data): ?array;
    public function updateSection(int $id, array $data): ?array;
    public function deleteSection(int $id): bool;
    public function sectionImages(int $id): ?array;
    public function addSectionImages(array $data): bool;
    public function addSectionImage(int $id, array $data): ?array;
    public function updateSectionImage(int $id, array $data): ?array;
    public function deleteSectionImage(int $id, int $imageId): bool;
    public function sectionProducts(int $id): ?array;
    public function addSectionProduct(int $id, int $productId): ?array;
    public function updateSectionProduct(int $id, array $data): ?array;
    public function deleteSectionProduct(int $id, int $project_section_products_id): bool;
    public function deleteImageByProperty(int $post_id, string $property, string $type='showroom'): bool;

    public function getShowroomsComponentData($param = [], $links = []);
    // import csv file
    public function importSections(string $csvFilePath): array;
    public function importSectionsImages(string $csvFilePath): array;
    public function importSectionProducts(string $csvFilePath): array;
    public function getShowroomComponentData($param = []) :array;
    public function getShowroomForPinboard(): ?array;

    public function updateWayPoints(array $data): array;
    // showroom contact
    public function getShowroomContactList(): array;
    public function getShowroomContactById(int $showroom_contact_id): array;
    public function deleteShowroomContactById(int $showroom_contact_id): bool;
    public function createShowroomContact(array $data): array;
    public function updateShowroomContactById(int $showroom_contact_id, array $data): array;
    public function importShowroomContact(string $csvFilePath): array;
    public function getShowroomContactForComponent(int $showroom_id): array;
    public function updateShowroomContactImage(int $showroom_contact_id, array $data): bool;
    public function deleteShowroomContactImage(int $showroom_contact_id): bool;

    public function getMembersData($showroom_id = 1): array;
    public function updateSlot(int $contact_id, array $data): array;
    public function getSlot(int $contact_id): array;
}