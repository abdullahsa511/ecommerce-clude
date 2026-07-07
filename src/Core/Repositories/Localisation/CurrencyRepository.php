<?php

declare(strict_types=1);

namespace App\Core\Repositories\Localisation;

use App\Core\Models\Localisation\Currency;
use App\Core\Repositories\Base\BaseRepository;
use PDO;

class CurrencyRepository extends BaseRepository implements CurrencyRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'currency', Currency::class);
    }

    public function getAll(?int $currencyId = null, ?int $status = null, ?int $limit = null, ?int $offset = null): array
    {
        $query = $this->model;

        if ($currencyId !== null) {
            $query->where('currency_id', '=', $currencyId, 'AND');
        }

        if ($status !== null) {
            $query->where('status', '=', $status, 'AND');
        }

        if ($limit !== null) {
            $query->limit($limit);
        }

        if ($offset !== null) {
            $query->offset($offset);
        }

        $query->orderBy('code', 'ASC');
        $results = $query->findAll() ?? [];
        $total = $query->countAll();

        return [
            'items' => collect($results),
            'total' => $total,
            "total_pages" => (int) ceil($total / ($limit ?? $this->model->limitValue)),
            "current_page" => (int) ceil($offset / ($limit ?? $this->model->limitValue)) + 1,
            "per_page" => $limit ?? $this->model->limitValue
        ];
    }

    public function get(int $currencyId): ?Currency
    {
        $this->model->clearQuery();
        $this->model->where('currency_id', '=', $currencyId, 'AND');
        $result = $this->model->findAll();
        return !empty($result) ? $this->model->set($result[0]) : null;
    }

    public function isExistsCode(string $code, ?int $id = 0): bool
    {
        $this->model->clearQuery();
        $this->model->where('code', '=', $code);
        $this->model->limit(1);
        if($id > 0){
            $this->model->where('currency_id', '!=', $id);
        }
        $results = $this->model->first();
        if(isset($results->currency_id) && $results->currency_id > 0){
            return true;
        }else{
            return false;
        }
    }
} 