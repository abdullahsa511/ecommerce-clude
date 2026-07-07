<?php

namespace App\Core\Repositories\Subscription;

use App\Core\Models\Subscription\SubscriptionStatus;
use App\Core\Repositories\Base\BaseRepository;
use PDO;

class SubscriptionStatusRepository extends BaseRepository implements SubscriptionStatusRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'subscription_status', SubscriptionStatus::class);
    }

    /**
     * Get all subscription statuses with optional filtering and pagination
     * 
     * @param int|null $languageId
     * @param int|null $start
     * @param int|null $limit 
     * @return array
     */
    public function getAll(?int $languageId = null, ?int $start = null, ?int $limit = null): array
    {
        $query = $this->model->select(['subscription_status.*']);

        if ($languageId !== null) {
            $query->where('language_id', '=', $languageId);
        }

        if ($start !== null) {
            $query->offset($start);
        }

        if ($limit !== null) {
            $query->limit($limit);
        }

        $results = $query->findAll();
        $totalRecords = $query->countAll();

        return [
            'data' => $results,
            'total' => $totalRecords
        ];
    }

    /**
     * Get a single subscription status by ID
     * 
     * @param int $subscriptionStatusId
     * @return array|null
     */
    public function get(int $subscriptionStatusId): ?array
    {
        $result = $this->model->find($subscriptionStatusId);
        return $result ? $result->findAll() : null;
    }

    
} 