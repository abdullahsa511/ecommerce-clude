<?php

namespace App\Core\Repositories\Subscription;

use App\Core\Models\Subscription\SubscriptionPlanContent;
use App\Core\Repositories\Base\BaseRepository;
use PDO;

class SubscriptionPlanContentRepository extends BaseRepository implements SubscriptionPlanContentRepositoryInterface
{

    public function __construct(PDO $db, SubscriptionPlanContent $subscriptionPlanContent) 
    {
        parent::__construct($db, 'subscription_plan_content', SubscriptionPlanContent::class);
    }

    /**
     * Get all subscriptions with optional filtering and pagination
     * 
     * @param int|null $languageId
     * @param int|null $start
     * @param int|null $limit 
     * @return array
     */
    public function getAll(?int $languageId = null, int $start = 0, int $limit = 10): array
    {
        $query = $this->model->with(['subscriptionPlanContent']);

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

        return $results;
        // return [
        //     'data' => $results,
        //     'total' => $totalRecords
        // ];
    }

    /**
     * Get a single subscription by ID
     * 
     * @param int $subscriptionId
     * @return array|null
     */
    public function get(int $subscriptionId): ?array
    {
        $result = $this->model->find($subscriptionId);
        return $result ? $result->findAll() : null;
    }

    
} 