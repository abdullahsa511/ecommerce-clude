<?php

declare(strict_types=1);

namespace App\Core\Repositories\Product;

use App\Core\Models\Product\LengthTypeContent;
use App\Core\Repositories\Base\BaseRepository;
use PDO;

class LengthTypeContentRepository extends BaseRepository implements LengthTypeContentRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'length_type_content', LengthTypeContent::class);
    }

    /**
     * Get all length type content
     * 
     * @return array
     */
    public function getAll(): array
    {
        return $this->model->findAll() ?? [];
    }

    /**
     * Delete content by where conditions
     * 
     * @param array $conditions Where conditions
     * @return bool
     */
    public function deleteWhere(array $conditions): bool
    {
        $query = $this->model;
        
        foreach ($conditions as $field => $value) {
            $query->where($field, '=', $value);
        }

        $results = $query->findAll();
        if (empty($results)) {
            return true;
        }

        return $query->delete($results[0]['length_type_content_id']);
    }

    /**
     * Find content by length type ID and language ID
     * 
     * @param int $lengthTypeId Length type ID
     * @param int $languageId Language ID
     * @return LengthTypeContent|null
     */
    public function findByLengthTypeAndLanguage(int $lengthTypeId, int $languageId): ?LengthTypeContent
    {
        $query = $this->model;
        
        $query->where('length_type_id', '=', $lengthTypeId)
              ->where('language_id', '=', $languageId)
              ->limit(1);

        $results = $query->findAll();
        return !empty($results) ? $this->model->set($results[0]) : null;
    }
} 