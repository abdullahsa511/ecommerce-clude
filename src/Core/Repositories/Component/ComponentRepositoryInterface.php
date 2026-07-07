<?php

declare(strict_types=1);

namespace App\Core\Repositories\Component;

use App\Core\Models\Component\Component;
use App\Core\Models\Component\ComponentData;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface ComponentRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all countries with pagination and filtering
     */
    public function getAll(
        ?int $status = null,
        ?string $search = null,
        int $start = 0,
        int $limit = 10
    ): array;

    /**
     * Get a single country by ID
     */
    public function get(int $componentId): ?Component;

    public function getComponentByName(string $name): ?ComponentData;
    public function getComponentById(int $id): ?ComponentData;

    public function createComponent(array $data): ?Component;

    public function seedData(array $data): void;

    public function getComponentItems(ComponentData $component): ?array;

    public function updateWayPoints(array $data): array;
    public function uploadImage(array $data, int|string $component_id, ?string $property = 'image'): bool;
    public function deleteImage(string $objectUrl, int|string $component_id, ?string $property = 'image'): bool;
} 