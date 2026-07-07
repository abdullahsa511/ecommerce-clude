<?php

declare(strict_types=1);

namespace App\Core\Repositories\Project;

use App\Core\Models\Product\ProjectImage;
use App\Core\Models\Project\Project;
use App\Core\Models\Project\ProjectData;
use App\Core\Models\Project\ProjectStatus;
use App\Core\Repositories\Base\BaseRepository;
use Exception;
use PDO;

class ProjectStatusRepository extends BaseRepository implements ProjectStatusRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'project_status', ProjectStatus::class);
    }

    /**
     * {@inheritDoc}
     */
    public function getAll(?int $language_id = null, int $start = 0, int $limit = 10): array
    {
        $query = $this->model;

        if ($language_id !== null) {
            $query->where('language_id', '=', $language_id);
        }

        $query->orderBy('order_status_id', 'DESC')
              ->limit($limit)
              ->offset($start);

        $data = $query->findAll();
        $total = $query->countAll();

        return [
            'data' => $data,
            'total' => $total
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function get(int $order_status_id): ?ProjectStatus
    {
        return $this->find($order_status_id);
    }

    
} 