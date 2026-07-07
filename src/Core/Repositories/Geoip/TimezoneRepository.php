<?php

declare(strict_types=1);

namespace App\Core\Repositories\Geoip;

use App\Core\Models\Geoip\Timezone;
use App\Core\Repositories\Base\BaseRepository;

use PDO;

class TimezoneRepository extends BaseRepository implements TimezoneRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'timezone', Timezone::class);
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
              ->orderBy('timezone_id', 'ASC');

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

    public function get(int $timezoneId): ?Timezone
    {
        $query = $this->model
            ->where('timezone_id', '=', $timezoneId);

        return $query->first() ?? null;
    }
    public function create(array $data): ?Timezone
    {
        $timezone = $this->model->create($data);
        return $timezone;
    }
    public function update(int $id, array $data): ?Timezone
    {
        $timezone = $this->model->update($data, $id);
        return $timezone;
    }
    public function delete(int $id): bool
    {
        $timezone = $this->model->delete($id);
        return $timezone;
    }
} 