<?php

declare(strict_types=1);

namespace App\Core\Repositories\Product;

use PDO;
use App\Core\Models\Product\ProductOptionValue;
use App\Core\Repositories\Base\BaseRepository;

class ProductOptionValueRepository extends BaseRepository implements ProductOptionValueRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'product_option_value', ProductOptionValue::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getAll(
        int $languageId,
        ?int $optionId = null,
        ?int $productId = null,
        ?array $productOptionValueIds = null,
        ?int $start = null,
        ?int $limit = null
    ): array {
        $query = $this->model->select(['product_option_value.*'])
            ->with([
                'optionValue',
                'option',
                'optionContent' => function($query) use ($languageId) {
                    $query->where('language_id', '=', $languageId);
                },
                'optionValueContent' => function($query) use ($languageId) {
                    $query->where('language_id', '=', $languageId);
                }
            ]);

        if ($optionId !== null) {
            $query->where('product_option_value.option_id', '=', $optionId);
        }

        if ($productId !== null) {
            $query->where('product_option_value.product_id', '=', $productId);
        }

        if ($productOptionValueIds !== null) {
            $query->whereIn('product_option_value.product_option_value_id', $productOptionValueIds);
        }

        // Get total count before pagination
        $total = $query->countAll();

        if ($start !== null && $limit !== null) {
            $query->offset($start)->limit($limit);
        }

        $items = $query->findAll();

        // Map the results to include name and option from relationships
        $mappedItems = array_map(function($item) {
            $item->name = $item->optionValueContent->name ?? null;
            $item->option = $item->optionContent->name ?? null;
            $item->image = $item->optionValue->image ?? null;
            $item->array_key = $item->product_option_value_id;
            return $item;
        }, $items);

        return [
            'items' => $mappedItems,
            'total' => $total
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function get(int $productOptionValueId, int $languageId): ?array
    {
        $result = $this->model->select(['product_option_value.*'])
            ->with([
                'optionValue',
                'optionValueContent' => function($query) use ($languageId) {
                    $query->where('language_id', '=', $languageId);
                }
            ])
            ->where('product_option_value.product_option_value_id', '=', $productOptionValueId)
            ->findAll();

        if (empty($result)) {
            return null;
        }

        $item = $result[0];
        $data = get_object_vars($item);
        unset($data['db']);
        return $data;
    }

} 