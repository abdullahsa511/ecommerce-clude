<?php

declare(strict_types=1);

namespace App\Core\Repositories\Fields;

use App\Core\Models\Fields\Field;
use App\Core\Repositories\Base\BaseRepository;
use PDO;

class FieldRepository extends BaseRepository implements FieldRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'field', Field::class);
    }

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
    ): array {
        // Add field_item_id filter if provided
        if ($fieldItemId !== null) {
            $this->model->where('field_item_id', '=', $fieldItemId, 'AND');
        }

        // Add post_type filter if provided
        if ($postType !== null) {
            $this->model->where('post_type', '=', $postType, 'AND');
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
     * Get a specific field
     * 
     * @param int $fieldId Field ID
     * @return Field|null
     */
    public function get(int $fieldId): ?Field
    {
        $this->model->where('field_id', '=', $fieldId, 'AND');
        $result = $this->model->findAll();
        return !empty($result) ? $this->model->set($result[0]) : null;
    }

} 