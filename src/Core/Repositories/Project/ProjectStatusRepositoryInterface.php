<?php

declare(strict_types=1);

namespace App\Core\Repositories\Project;

use App\Core\Models\Project\ProjectStatus;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface ProjectStatusRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all order statuses
     *
     * @param int|null $language_id
     * @param int $start
     * @param int $limit
     * @return array
     */
    public function getAll(?int $language_id = null, int $start = 0, int $limit = 10): array;

    /**
     * Get a single order status
     *
     * @param int $project_status_id
     * @return ProjectStatus|null
     */
    public function get(int $project_status_id): ?ProjectStatus;

    
} 