<?php

declare(strict_types=1);

namespace App\Core\Repositories\Return;

use App\Core\Models\Order\ReturnReason;
use App\Core\Repositories\Base\BaseRepository;
use Illuminate\Support\Collection;
use PDO;

class ReturnReasonRepository extends BaseRepository implements ReturnReasonRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'return_reason', ReturnReason::class);
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

    public function findByName(string $name, int $language_id): ?ReturnReason
    {
        $this->model->where('name', '=', $name);
        $this->model->where('language_id', '=', (string)$language_id);
        $this->model->limit(1);
        
        $results = $this->model->findAll();
        return !empty($results) ? $this->model->set($results[0]) : null;
    }

} 