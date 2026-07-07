<?php

declare(strict_types=1);

namespace App\Core\Repositories\Page;

use App\Core\Models\Post\PostStatus;
use App\Core\Repositories\Base\BaseRepository;
use PDO;

class PageStatusRepository extends BaseRepository implements PageStatusRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'post_status', PostStatus::class);
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

        $query->orderBy('post_status_id', 'DESC')
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
    public function get(int $post_status_id): ?PostStatus
    {
        return $this->find($post_status_id);
    }

   
} 