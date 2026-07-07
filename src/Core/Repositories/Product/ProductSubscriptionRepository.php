<?php

declare(strict_types=1);

namespace App\Core\Repositories\Product;

use PDO;
use App\Core\Models\Product\ProductSubscription;
use App\Core\Repositories\Base\BaseRepository;

class ProductSubscriptionRepository extends BaseRepository implements ProductSubscriptionRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'product_subscription', ProductSubscription::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getAll(
        int $languageId,
        ?int $productId = null,
        ?int $subscriptionPlanId = null,
        ?int $start = null,
        ?int $limit = null
    ): array {
        $query = $this->model->select(['product_subscription.*'])
            ->with(['product', 'userGroup'])
            ->with(['subscriptionPlan' => function($query) use ($languageId) {
                $query->select(['subscription_plan.*'])
                    ->leftJoin(
                        'subscription_plan_content',
                        'subscription_plan_content.subscription_plan_id = subscription_plan.subscription_plan_id',
                        'inner'
                    )
                    ->where('subscription_plan_content.language_id', '=', $languageId);
            }]);

        if ($productId !== null) {
            $query->where('product_subscription.product_id', '=', $productId);
        }

        if ($subscriptionPlanId !== null) {
            $query->where('product_subscription.subscription_plan_id', '=', $subscriptionPlanId);
        }

        // Get total count before pagination
        $total = $query->countAll();

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
    public function get(int $productSubscriptionId): ?array
    {
        $result = $this->model->select(['product_subscription.*'])
            ->with(['product', 'subscriptionPlan', 'userGroup'])
            ->where('product_subscription.product_subscription_id', '=', $productSubscriptionId)
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