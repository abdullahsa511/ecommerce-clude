<?php

declare(strict_types=1);

namespace App\Core\Repositories\Localisation;

use App\Core\Models\Localisation\Language;
use App\Core\Repositories\Base\BaseRepository;
use PDO;

class LanguageRepository extends BaseRepository implements LanguageRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'language', Language::class);
    }

    /**
     * Get all languages with optional filters
     * 
     * @param int|null $status Language status
     * @param string|null $search Search term
     * @param int $start Pagination start
     * @param int $limit Pagination limit
     * @return array{items: array, total: int}
     */
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
              ->orderBy('language_id', 'ASC');

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
     * Get a specific language by ID or code
     * 
     * @param int|null $languageId Language ID
     * @param string|null $code Language code
     * @return Language|null
     */
    public function get(?int $languageId = null, ?string $code = null): ?Language
    {
        if ($languageId !== null) {
            $this->model->where('language_id', '=', $languageId);
        }

        if ($code !== null) {
            $this->model->where('code', '=', $code);
        }

        $this->model->limit(1);
        $result = $this->model->findAll();
        return !empty($result) ? $this->model->set($result[0]) : null;
    }

    
} 