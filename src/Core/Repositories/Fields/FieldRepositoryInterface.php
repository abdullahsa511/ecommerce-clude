<?php

declare(strict_types=1);

namespace App\Core\Repositories\Fields;

use App\Core\Models\Fields\Field;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface FieldRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all fields with optional filters
     * 
     * @param int|null $fieldItemId Field item ID
     * @param string|null $postType Post type filter
     * @param int|null $start Pagination start
     * @param int|null $limit Pagination limit
     * @return array{items: array, total: int}
     */
    public function getAll(
        ?int $fieldItemId = null,
        ?string $postType = null,
        ?int $start = null,
        ?int $limit = null
    ): array;

    /**
     * Get a specific field
     * 
     * @param int $fieldId Field ID
     * @return Field|null
     */
    public function get(int $fieldId): ?Field;

} 