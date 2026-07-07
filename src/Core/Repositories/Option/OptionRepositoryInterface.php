<?php

declare(strict_types=1);

namespace App\Core\Repositories\Option;

use App\Core\Models\Option\Option;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface OptionRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all options with pagination
     * 
     * @param int $language_id Language ID
     * @param int $start Start offset
     * @param int $limit Number of records to return
     * @return array{data: array, total: int}
     */
    public function getAll(int $language_id, int $start = 0, int $limit = 10): array;

    /**
     * Get a single option by ID
     * 
     * @param int $option_id Option ID
     * @param int $language_id Language ID
     * @return Option|null
     */
    public function get(int $option_id, int $language_id): ?Option;

    /**
     * Add new option
     * 
     * @param array $option Option data including content
     * @return int New option ID
     */
    public function add(array $option): int;

    /**
     * Edit option
     * 
     * @param int $option_id Option ID
     * @param array $option Option data including content
     * @return bool
     */
    public function edit(int $option_id, array $option): bool;
    public function getAllOptions(): array;
    public function getOptionById($id);
    public function findByCode(string $code, ?int $id = null): ?Option;
    public function importOptions(string $csv_file): array;
    public function createOptions($data): array;
    public function updateOptions($data, $id): array;
    public function deleteOptions(int $attribute_id): bool;
    public function deleteMultipleOptions(array $attribute_ids): bool;
    public function getAllOptionTypes(): array;
    
} 