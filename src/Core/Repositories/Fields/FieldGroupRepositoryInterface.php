<?php

declare(strict_types=1);

namespace App\Core\Repositories\Fields;

use App\Core\Models\Fields\FieldGroup;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface FieldGroupRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all field groups with optional filters
     * 
     * @param int|null $languageId Language ID
     * @param int|null $start Pagination start
     * @param int|null $limit Pagination limit
     * @return array{items: array, total: int}
     */
    public function getAll(
        ?int $languageId = null,
        ?int $start = null,
        ?int $limit = null
    ): array;

    /**
     * Get a specific field group
     * 
     * @param int $fieldGroupId Field group ID
     * @param int|null $languageId Language ID
     * @return FieldGroup|null
     */
    public function get(int $fieldGroupId, ?int $languageId = null): ?FieldGroup;
} 