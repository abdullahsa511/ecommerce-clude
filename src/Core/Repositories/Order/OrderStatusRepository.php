<?php

declare(strict_types=1);

namespace App\Core\Repositories\Order;

use App\Core\Models\Order\OrderStatus;
use App\Core\Repositories\Base\BaseRepository;
use PDO;

class OrderStatusRepository extends BaseRepository implements OrderStatusRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'order_status', OrderStatus::class);
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
    public function get(int $order_status_id): ?OrderStatus
    {
        return $this->find($order_status_id);
    }

   
} 