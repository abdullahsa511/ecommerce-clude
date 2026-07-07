<?php

declare(strict_types=1);

namespace App\Core\Repositories\Geoip;

use PDO;
use App\Core\Models\Geoip\Region;
use App\Core\Repositories\Base\BaseRepository;

class RegionRepository extends BaseRepository implements RegionRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'region', Region::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getAll(
        ?int $countryId = null,
        ?int $status = null,
        ?string $search = null,
        int $start = 0,
        int $limit = 10
    ): array {
        $query = $this->model;

        // Load relationships
        $query->with(['country']);

        // Apply filters
        if ($countryId !== null) {
            $query->where('country_id', '=', $countryId);
        }

        if ($status !== null) {
            $query->where('status', '=', $status);
        }

        if ($search !== null) {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        // Apply ordering
        $query->orderBy('status', 'DESC')
              ->orderBy('region_id', 'ASC');

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

    /**
     * {@inheritdoc}
     */
    public function get(int $regionId): ?Region
    {
        $query = $this->model
            ->where('region_id', '=', $regionId);

        $result = $query->findAll();
        if (empty($result)) {
            return null;
        }
        
        return $this->model->set($result[0]);
    }

    public function updateRegion(array $data, int $id): ?object
    {
        $this->model->clearQuery();
        $this->model->where('region_id', '=', $id);
        $result = $this->model->first();
        if (!$result) {
            return null;
        }
        $result->update($data);
        return $result;
    }

    public function isExistsCode(string $code, ?int $id = 0): bool
    {
        $this->model->clearQuery();
        $this->model->where('code', '=', $code);
        $this->model->limit(1);
        if($id > 0){
            $this->model->where('region_id', '!=', $id);
        }
        $results = $this->model->first();
        if(isset($results->region_id) && $results->region_id > 0){
            return true;
        }else{
            return false;
        }
    }
} 