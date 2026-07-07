<?php

declare(strict_types=1);

namespace App\Core\Repositories\Fields;

use App\Core\Models\Fields\FieldGroup;
use App\Core\Models\Fields\FieldGroupContent;
use App\Core\Repositories\Base\BaseRepository;
use PDO;

class FieldGroupRepository extends BaseRepository implements FieldGroupRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'field_group', FieldGroup::class);
    }

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
    ): array {
        // Join with field_group_content
        $this->model->join(
            'field_group_content',
            'field_group.field_group_id',
            '=',
            'field_group_content.field_group_id',
            'INNER'
        );

        // Add language_id filter if provided
        if ($languageId !== null) {
            $this->model->where('field_group_content.language_id', '=', $languageId, 'AND');
        }

        // Get total count before pagination
        $total = $this->model->countAll();

        // Add pagination if provided
        if ($start !== null && $limit !== null) {
            $this->model->offset($start)->limit($limit);
        }

        // Execute query and get results
        $items = $this->model->findAll();

        return [
            'items' => $items,
            'total' => $total
        ];
    }

    /**
     * Get a specific field group
     * 
     * @param int $fieldGroupId Field group ID
     * @param int|null $languageId Language ID
     * @return FieldGroup|null
     */
    public function get(int $fieldGroupId, ?int $languageId = null): ?FieldGroup
    {
        // Join with field_group_content
        $this->model->join(
            'field_group_content',
            'field_group.field_group_id',
            '=',
            'field_group_content.field_group_id',
            'INNER'
        );

        // Add field_group_id filter
        $this->model->where('field_group.field_group_id', '=', $fieldGroupId, 'AND');

        // Add language_id filter if provided
        if ($languageId !== null) {
            $this->model->where('field_group_content.language_id', '=', $languageId, 'AND');
        }

        $result = $this->model->findAll();
        return !empty($result) ? $this->model->set($result[0]) : null;
    }
} 