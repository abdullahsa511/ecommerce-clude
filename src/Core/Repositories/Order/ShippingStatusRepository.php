<?php

namespace App\Core\Repositories\Order;

use App\Core\Models\Order\ShippingStatus;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Repositories\Status\StatusRepository;
use App\Core\Repositories\Order\ShippingStatusRepositoryInterface;
use PDO;

class ShippingStatusRepository extends StatusRepository implements ShippingStatusRepositoryInterface
{

    public function __construct(PDO $db) 
    {
        parent::__construct($db, 'shipping_status', ShippingStatus::class);
    }

    /**
     * Get all shipping statuses with optional filtering and pagination
     * 
     * @param int|null $languageId
     * @param int|null $start
     * @param int|null $limit 
     * @return array
     */
    public function getAll(?int $languageId = null, ?int $start = null, ?int $limit = null): array
    {
        $query = $this->model->select(['shipping_status.*']);

        if ($languageId !== null) {
            $query->where('language_id', '=', $languageId);
        }

        if ($start !== null) {
            $query->offset($start);
        }

        if ($limit !== null) {
            $query->limit($limit);
        }

        $results = $query->findAll();
        $totalRecords = $query->countAll();

        return [
            'data' => $results,
            'total' => $totalRecords
        ];
    }

    /**
     * Get a single shipping status by ID
     * 
     * @param int $shippingStatusId
     * @return array|null
     */
    public function get(int $shippingStatusId): ?array
    {
        $result = $this->model->find($shippingStatusId);
        return $result ? $result->findAll() : null;
    }

    
} 