<?php

declare(strict_types=1);

namespace App\Core\Repositories\Geoip;

use PDO;
use App\Core\Models\Geoip\RegionGroup;
use App\Core\Models\Geoip\RegionToRegionGroup;
use App\Core\Repositories\Base\BaseRepository;
use Illuminate\Support\Collection;

class RegionGroupRepository extends BaseRepository implements RegionGroupRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, '', RegionGroup::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getAll(
        ?int $languageId = null,
        int $start = 0,
        int $limit = 10
    ): array {
        $query = $this->model;

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
    public function get(int $regionGroupId): ?RegionGroup
    {
        $query = $this->model
            ->where('region_group_id', '=', $regionGroupId);
            // ->with([
            //     'regions' => function($regionQuery) {
            //         // Only select specific columns from region (flat array of column names)
            //         $regionQuery->select([
            //             'region.region_id',
            //             'region.name',
            //             'region.code',
            //             'region.status',
            //             'region.country_id' // Needed to join with country relation
            //         ]);
            //         // Eager-load the country relationship with specific column(s)
            //         $regionQuery->with(['country' => function($countryQuery) {
            //             $countryQuery->select(['country.country_id', 'country.name']);
            //         }]);
            //         return $regionQuery;
            //     }
            // ]);

       return $query->first();
    }

    /**
     * {@inheritdoc}
     */
    public function getRegions(
        int $regionGroupId,
        ?int $countryId = null,
        ?int $start = null,
        ?int $limit = null
    ): array {
        $query = $this->model->select(['region_group.*'])
            ->where('region_group_id', '=', $regionGroupId);

        // Add relationships
        $query->with(['regionPivot']);
        $query->with(['regionPivot.region']);
        $query->with(['regionPivot.country']);

        if ($countryId !== null) {
            $query->where('country_id', '=', $countryId);
        }

        // Get total count before pagination
        $total = $query->countAll();

        // Apply pagination
        if ($start !== null && $limit !== null) {
            $query->offset($start)->limit($limit);
        }

        $results = $query->findAll();

        // Transform the results to match the expected format
        $items = [];
        foreach ($results as $result) {
            foreach ($result->regionPivot_data as $pivot) {
                $items[] = [
                    'region_group_id' => $pivot->region_group_id,
                    'region_id' => $pivot->region_id,
                    'country_id' => $pivot->country_id,
                    'region' => $pivot->region_data->name ?? null,
                    'country' => $pivot->country_data->name ?? null
                ];
            }
        }

        return [
            'items' => $items,
            'total' => $total
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function isRegion(
        int $regionGroupId,
        int $countryId,
        int $regionId,
        ?int $start = null,
        ?int $limit = null
    ): array {
        $pivotModel = new RegionToRegionGroup();
        $pivotModel->setDb($this->db);

        $query = $pivotModel->select(['region_to_region_group.*'])
            ->where('region_group_id', '=', $regionGroupId)
            ->where('country_id', '=', $countryId)
            ->where('region_id', '=', $regionId)
            ->orWhere('region_id', '=', 0);

        // Get total count before pagination
        $total = $query->countAll();

        // Apply pagination
        if ($start !== null && $limit !== null) {
            $query->offset($start)->limit($limit);
        }

        return [
            'items' => $query->findAll(),
            'total' => $total
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function addRegions(array $data, int $regionGroupId): bool
    {
        try {
            $this->db->beginTransaction();

            $pivotModel = new RegionToRegionGroup();
            $pivotModel->setDb($this->db);

            // Delete existing regions for this group
            $stmt = $this->db->prepare("DELETE FROM region_to_region_group WHERE region_group_id = :region_group_id");
            $stmt->execute(['region_group_id' => $regionGroupId]);

            // Add new regions
            foreach ($data as $regionData) {
                $regionData['region_group_id'] = $regionGroupId;
                $pivotModel->create($regionData);
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function find(int $id): ?object
    {
        $result = $this->model
            ->with(['regions', 'regionPivot'])
            ->with(['regionPivot.region', 'regionPivot.country'])
            ->find($id);

        if ($result) {
            // Process the regions data
            $regions = [];
            if (isset($result->regionPivot)) {
                foreach ($result->regionPivot as $pivot) {
                    $regions[] = [
                        'region_id' => $pivot->region_id,
                        'country_id' => $pivot->country_id,
                        'region_name' => $pivot->region->name ?? null,
                        'country_name' => $pivot->country->name ?? null
                    ];
                }
                $result->regions = $regions;
            }
        }

        return $result;
    }
    // update region group
    public function updateRegionGroup(array $data, int $id): ?object
    {
        $this->model->clearQuery();
        $this->model->where('region_group_id', '=', $id);
        $result = $this->model->first();
        if (!$result) {
            return null;
        }
        $result->update($data);
        return $result;
    }
    // exists name
    public function isExistsName(string $name, ?int $id = 0): bool
    {
        $this->model->clearQuery();
        $this->model->where('name', '=', $name);
        $this->model->limit(1);
        if($id > 0){
            $this->model->where('region_group_id', '!=', $id);
        }
        $results = $this->model->first();
        if(isset($results->region_group_id) && $results->region_group_id > 0){
            return true;
        }else{
            return false;
        }
    }
} 