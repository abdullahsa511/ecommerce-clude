<?php

declare(strict_types=1);

namespace App\Core\Repositories\User;

use App\Core\Models\User\DigitalAssetLog;
use App\Core\Repositories\Base\BaseRepository;
use PDO;

class DigitalAssetLogRepository extends BaseRepository implements DigitalAssetLogRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'digital_asset_log', DigitalAssetLog::class);
    }

    /**
     * Get all digital asset logs with optional filters
     * 
     * @param int|null $userId User ID
     * @param int|null $start Pagination start
     * @param int|null $limit Pagination limit
     * @return array{items: array, total: int}
     */
    public function getAll(
        ?int $userId = null,
        ?int $start = null,
        ?int $limit = null
    ): array {
        if ($userId !== null) {
            $this->model->where('user_id', '=', $userId, 'AND');
        }

        $total = $this->model->countAll();

        if ($start !== null && $limit !== null) {
            $this->model->offset($start)->limit($limit);
        }

        $items = $this->model->findAll();

        return [
            'items' => $items,
            'total' => $total
        ];
    }

    /**
     * Get a specific digital asset log
     * 
     * @param int $digitalAssetLogId Digital asset log ID
     * @param int|null $userId User ID
     * @param int|null $siteId Site ID
     * @return DigitalAssetLog|null
     */
    public function get(int $digitalAssetLogId, ?int $userId = null, ?int $siteId = null): ?DigitalAssetLog
    {
        $this->model->where('digital_asset_log_id', '=', $digitalAssetLogId, 'AND');

        if ($userId !== null) {
            $this->model->where('user_id', '=', $userId, 'AND');
        }

        if ($siteId !== null) {
            $this->model->where('site_id', '=', $siteId, 'AND');
        }

        $result = $this->model->findAll();
        return !empty($result) ? $this->model->set($result[0]) : null;
    }
} 