<?php

declare(strict_types=1);

namespace App\Core\Repositories\Option;

use App\Core\Models\Option\OptionValue;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface OptionValueRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all option values with pagination
     * 
     * @param int $language_id Language ID
     * @param int|null $option_id Option ID (optional)
     * @param int $start Start offset
     * @param int $limit Number of records to return
     * @return array{data: array, total: int}
     */
    public function getAll(int $language_id, ?int $option_id = null, int $start = 0, int $limit = 10): array;

    /**
     * Get a single option value by ID
     * 
     * @param int $option_value_id Option Value ID
     * @param int $language_id Language ID
     * @return OptionValue|null
     */
    public function get(int $option_value_id, int $language_id): ?OptionValue;

    /**
     * Add new option value
     * 
     * @param array $option_value Option value data including content
     * @return int New option value ID
     */
    public function add(array $option_value): int;

    /**
     * Edit option value
     * 
     * @param int $option_value_id Option Value ID
     * @param array $option_value Option value data including content
     * @return bool
     */
    public function edit(int $option_value_id, array $option_value): bool;

    


} 