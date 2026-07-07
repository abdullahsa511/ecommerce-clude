<?php

declare(strict_types=1);

namespace App\Core\Repositories\Order;

use PDO;
use App\Core\Models\Order\ReturnObject;
use App\Core\Repositories\Base\BaseRepository;

class ReturnRepository extends BaseRepository implements ReturnRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'return', ReturnObject::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getAll(
        int $languageId,
        ?int $start = null,
        ?int $limit = null
    ): array {
        $query = $this->model->select(['return.*'])
            ->with(['returnResolution' => function($query) use ($languageId) {
                $query->select(['return_resolution.return_resolution_id', 'return_resolution.name as return_resolution'])
                    ->where('return_resolution.language_id', '=', $languageId);
            }])
            ->with(['returnReason' => function($query) use ($languageId) {
                $query->select(['return_reason.return_reason_id', 'return_reason.name as return_reason'])
                    ->where('return_reason.language_id', '=', $languageId);
            }])
            ->with(['returnStatus' => function($query) use ($languageId) {
                $query->select(['return_status.return_status_id', 'return_status.name as return_status'])
                    ->where('return_status.language_id', '=', $languageId);
            }]);

        // Get total count before pagination
        $total = $query->countAll();

        // Apply pagination
        if ($start !== null && $limit !== null) {
            $query->offset($start)->limit($limit);
        }

        $results = $query->findAll();

        // Transform the results to include the related data
        $items = [];
        foreach ($results as $result) {
            $item = get_object_vars($result);
            unset($item['db']);
            
            // Add related data
            if (isset($result->returnResolution_data)) {
                $item['return_resolution'] = $result->returnResolution_data->return_resolution ?? null;
            }
            if (isset($result->returnReason_data)) {
                $item['return_reason'] = $result->returnReason_data->return_reason ?? null;
            }
            if (isset($result->returnStatus_data)) {
                $item['return_status'] = $result->returnStatus_data->return_status ?? null;
            }

            $items[] = $item;
        }

        return [
            'items' => $items,
            'total' => $total
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function get(int $returnId): ?array
    {
        $result = $this->model->select(['return.*'])
            ->where('return_id', '=', $returnId)
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