<?php

declare(strict_types=1);

namespace App\Core\Repositories\Product;

use PDO;
use App\Core\Models\Product\ProductAttribute;
use App\Core\Models\Product\ProductAttributeContent;
use App\Core\Repositories\Base\BaseRepository;

class ProductAttributeRepository extends BaseRepository implements ProductAttributeRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'product_attribute', ProductAttribute::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getAll(?int $languageId = null, ?int $start = null, ?int $limit = null): array
    {
        $query = $this->model;

        if ($languageId !== null) {
            $query->where('language_id', '=', $languageId);
        }

        // Get total count before pagination
        $total = $query->countAll();

        // Add pagination if provided
        if ($start !== null && $limit !== null) {
            $query->offset($start)->limit($limit);
        }

        // Execute query and get results
        $items = $query->findAll();

        return [
            'items' => $items,
            'total' => $total
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function get(int $productAttributeId): ?array
    {
        $result = $this->model->find($productAttributeId);
        if ($result) {
            $data = get_object_vars($result);
            unset($data['db']);
            return $data;
        }
        return null;
    }

} 