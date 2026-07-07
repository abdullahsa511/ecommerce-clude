<?php

declare(strict_types=1);

namespace App\Core\Repositories\Geoip;

use App\Core\Models\Geoip\Country;
use App\Core\Repositories\Base\BaseRepository;

use PDO;

class CountryRepository extends BaseRepository implements CountryRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'country', Country::class);
    }

    public function getAll(
        ?int $status = null,
        ?string $search = null,
        int $start = 0,
        int $limit = 10
    ): array {
        $query = $this->model;

        if ($status !== null) {
            $query->where('status', '=', $status);
        }

        if ($search !== null) {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $query->orderBy('status', 'DESC')
              ->orderBy('country_id', 'ASC');

        if ($limit !== null) {
            $query->limit($limit);
        }

        if ($start !== null) {
            $query->offset($start);
        }

        // Get results
        $results = $query->findAll() ?? [];
        $total = $query->countAll();
        $perPage = $limit ?? $this->model->limitValue;

        return [
            'items' => collect($results),
            'total' => $total,
            "total_pages" => (int)ceil($total / $perPage),
            "current_page" => (int)($start / $perPage + 1),
            "per_page" => $perPage
        ];
    }

    public function get(int $countryId): ?Country
    {
        $query = $this->model
            ->where('country_id', '=', $countryId);

        $result = $query->findAll();
        if (empty($result)) {
            return null;
        }
        
        return $this->model->set($result[0]);
    }

    public function updateCountry(array $data, int $id): ?object
    {
        $this->model->clearQuery();
        $this->model->where('country_id', '=', $id);
        $result = $this->model->find($id);
        if (!$result) {
            return null;
        }
        $result->update($data);
        return $result;
    }

    public function isExistsName(string $name, ?int $id = 0): bool
    {
        $this->model->clearQuery();
        $this->model->where('name', '=', $name);
        $this->model->limit(1);
        if($id > 0){
            $this->model->where('country_id', '!=', $id);
        }
        $results = $this->model->first();
        if(isset($results->country_id) && $results->country_id > 0){
            return true;
        }else{
            return false;
        }
    }
} 