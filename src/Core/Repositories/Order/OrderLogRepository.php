<?php

declare(strict_types=1);

namespace App\Core\Repositories\Order;

use App\Core\Models\Base\Model;
use App\Core\Models\Order\OrderLog;
use App\Core\Repositories\Base\BaseRepository;
use PDO;

class OrderLogRepository extends BaseRepository implements OrderLogRepositoryInterface
{
    protected Model $model;
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->model = new OrderLog();
        $this->model->setDb($db);
    }

    /**
     * {@inheritDoc}
     */
    public function getAll(?int $order_id = null, int $start = 0, int $limit = 10): array
    {
        $this->model->select([
            'ol.order_log_id',
            'ol.order_id',
            'ol.status',
            'ol.comment',
            'ol.created_at'
        ]);

        if ($order_id !== null) {
            $this->model->where('order_id', '=', $order_id);
        }

        $this->model->limit($limit)->offset($start);

        return [
            'data' => $this->model->findAll(),
            'total' => $this->model->countAll()
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function get(int $order_log_id): ?OrderLog
    {
        /** @var OrderLog|null */
        return $this->model->select([
            'ol.order_log_id',
            'ol.order_id',
            'ol.status',
            'ol.comment',
            'ol.created_at'
        ])
        ->find($order_log_id);
    }

    
} 