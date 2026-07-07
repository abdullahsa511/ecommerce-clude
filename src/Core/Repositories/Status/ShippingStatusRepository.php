<?php

declare(strict_types=1);

namespace App\Core\Repositories\Status;

use App\Core\Models\Localisation\Language;
use App\Core\Models\Order\ShippingStatus;
use PDO;

class ShippingStatusRepository extends StatusRepository implements ShippingStatusRepositoryInterface
{
    public Language $language;

    public function __construct(PDO $db, Language $language)
    {
        $this->language = $language;
        $this->language->setDb($db);
        parent::__construct($db, 'shipping_status', ShippingStatus::class);
    }


    public function getAll(int $language_id): array
    {
        $this->model->where('language_id', '=', (string)$language_id);
        $this->model->orderBy('name', 'ASC');
        
        $result = $this->model->findAll();
        $items = collect($result);
        $totalRecords = $this->model->countAll();

        return [
            'items' => $items,
            'total' => $totalRecords
        ];
    }

    public function findByName(string $name, int $language_id): ?ShippingStatus
    {
        $this->model->where('name', '=', $name);
        $this->model->where('language_id', '=', (string)$language_id);
        $this->model->limit(1);
        
        $results = $this->model->findAll();
        return !empty($results) ? $this->model->set($results[0]) : null;
    }

} 