<?php

declare(strict_types=1);

namespace App\Core\Repositories\Order;

use App\Core\Models\Order\PaymentStatus;
use App\Core\Repositories\Base\BaseRepository;
use PDO;

class PaymentStatusRepository extends BaseRepository implements PaymentStatusRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'payment_status', PaymentStatus::class);
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

        $query->orderBy('payment_status_id', 'DESC')
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
    public function get(int $payment_status_id): ?PaymentStatus
    {
        return $this->find($payment_status_id);
    }

    
} 