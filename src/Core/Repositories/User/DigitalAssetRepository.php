<?php

declare(strict_types=1);

namespace App\Core\Repositories\User;

use App\Core\Models\User\DigitalAsset;
use App\Core\Repositories\Base\BaseRepository;
use PDO;

class DigitalAssetRepository extends BaseRepository implements DigitalAssetRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'digital_asset', DigitalAsset::class);
    }

    public function getAll(
        ?int $languageId = null,
        ?int $userId = null,
        ?int $productId = null,
        ?int $orderStatusId = null,
        ?int $start = null,
        ?int $limit = null
    ): array {
        if ($languageId !== null) {
            $this->model->where('language_id', '=', $languageId, 'AND');
        }

        if ($userId !== null) {
            $this->model->where('user_id', '=', $userId, 'AND');
        }

        if ($productId !== null) {
            $this->model->where('product_id', '=', $productId, 'AND');
        }

        if ($orderStatusId !== null) {
            $this->model->where('order_status_id', '=', $orderStatusId, 'AND');
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

    public function get(int $digitalAssetId, ?int $userId = null, ?int $languageId = null): ?DigitalAsset
    {
        $this->model->where('digital_asset_id', '=', $digitalAssetId, 'AND');

        if ($userId !== null) {
            $this->model->where('user_id', '=', $userId, 'AND');
        }

        if ($languageId !== null) {
            $this->model->where('language_id', '=', $languageId, 'AND');
        }

        $result = $this->model->findAll();
        return !empty($result) ? $this->model->set($result[0]) : null;
    }
} 