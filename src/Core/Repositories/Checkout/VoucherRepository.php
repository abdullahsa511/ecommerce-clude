<?php

namespace App\Core\Repositories\Checkout;

use App\Core\Models\Checkout\Voucher;
use App\Core\Repositories\Base\BaseRepository;
use PDO;
class VoucherRepository extends BaseRepository implements VoucherRepositoryInterface
{

    public function __construct(PDO $db) 
    {
        parent::__construct($db, 'voucher', Voucher::class);
    }

    /**
     * Get all vouchers with optional filtering and pagination
     * 
     * @param int $languageId
     * @param int|null $start
     * @param int|null $limit
     * @return array
     */
    public function getAll(int $languageId, ?int $start = null, ?int $limit = null): array
    {
        $query = $this->model->select(['voucher.*']);

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
     * Get a single voucher by ID
     * 
     * @param int $voucherId
     * @return array|null
     */
    public function get(int $voucherId): ?array
    {
        $result = $this->model->find($voucherId);
        return $result ? $result->findAll() : null;
    }

    
} 