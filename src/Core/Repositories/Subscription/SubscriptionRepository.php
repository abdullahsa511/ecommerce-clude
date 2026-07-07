<?php

namespace App\Core\Repositories\Subscription;

use App\Core\Models\Subscription\Subscription;
use App\Core\Models\Subscription\SubscriptionPlanContent;
use App\Core\Repositories\Base\BaseRepository;
use PDO;

class SubscriptionRepository extends BaseRepository implements SubscriptionRepositoryInterface
{
    private SubscriptionPlanContent $subscriptionPlanContent;

    public function __construct(PDO $db, SubscriptionPlanContent $subscriptionPlanContent) 
    {
        parent::__construct($db, 'subscription', Subscription::class);
        $this->subscriptionPlanContent = $subscriptionPlanContent;
        $this->subscriptionPlanContent->setDb($db);
    }

    public function findByEmail(string $email): ?Subscription
    {
        $model = $this->model->where('email', '=', $email);

        $subscriptions = $model->executeQuery($model->getQuery());

        if (!empty($subscriptions)) {
            $subscriptions = $model->set($subscriptions[0]);
            return $subscriptions;
        }
        return null;
    }

    /**
     * Get all subscriptions with optional filtering and pagination
     * 
     * @param int|null $languageId
     * @param int|null $start
     * @param int|null $limit 
     * @return array
     */
    public function getAll(int | null $languageId = null, int $start = 0, int $limit = 10): array
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

    public function findAll(): array
    {
        // $results = $this->model->with(['subscriptionPlanContent'])->findAll();
        $results = [
            [
                "subscription_id" => "1001",
                "user" => "John Doe",
                "reason" => "Premium Plan Upgrade",
                "status" => "active",
                "action" => "upgrade",
                "date_ordered" => "2024-02-15T10:30:00Z",
                "created_at" => "2024-02-15T10:30:00Z",
                "date_updated" => "2024-02-15T10:30:00Z",
            ],
            [
                "subscription_id" => "1002",
                "user" => "Jane Smith",
                "reason" => "Monthly Renewal",
                "status" => "pending",
                "action" => "renew",
                "date_ordered" => "2024-02-14T15:45:00Z",
                "created_at" => "2024-02-14T15:45:00Z",
                "date_updated" => "2024-02-14T15:45:00Z",
            ],
            [
                "subscription_id" => "1003",
                "user" => "Mike Johnson",
                "reason" => "Plan Downgrade",
                "status" => "processing",
                "action" => "downgrade",
                "date_ordered" => "2024-02-13T09:20:00Z",
                "created_at" => "2024-02-13T09:20:00Z",
                "date_updated" => "2024-02-13T09:20:00Z",
            ],
            [
                "subscription_id" => "1004",
                "user" => "Sarah Wilson",
                "reason" => "Cancellation Request",
                "status" => "cancelled",
                "action" => "cancel",
                "date_ordered" => "2024-02-12T14:15:00Z",
                "created_at" => "2024-02-12T14:15:00Z",
                "date_updated" => "2024-02-12T14:15:00Z",
            ]
        ];
        return $results;
    }


    public function createSubscription(array $data): array
    {
        $response = [];
        try {
            $this->db->beginTransaction();
            $lengthType = $this->model->create([
                'value' => $data['value'],
            ]);
            $data['length_type_id'] = $lengthType->length_type_id;
            // $unit = (string) ($data['unit'] ?? '');
            // if (mb_strlen($unit) > 4) {
            //     throw new \InvalidArgumentException(sprintf("Value for 'unit' is too long (%d). Maximum allowed is 4 characters.", mb_strlen($unit)));
            // }

            $lengthTypeContentCreated = $this->lengthTypeContent->create([
                'length_type_id' => $data['length_type_id'],
                'language_id' => $data['language_id'] ?? 1,
                'name' => $data['name'],
                'unit' => $data['unit'],
            ]);
            $response = (array) $lengthType->data;
            if ($lengthTypeContentCreated) {
                $lengthTypeContentdata = $this->lengthTypeContent->where('length_type_id', '=', $lengthType->length_type_id)
                    ->where('language_id', '=', $data['language_id'])->first();
                $response['lengthTypeContent'] = (array) $lengthTypeContentdata->data;
            }
            $this->db->commit();
            return $response;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update variants: " . $e->getMessage());
        }
    }

    public function subscribeEmail(string $email)
    {
        $this->db->beginTransaction();
        try {
            $result = $this->model->where('email', '=', $email)->first();
            if($result){
                return [
                    'success' => true,
                    'message' => 'Email already subscribed',
                ];
            }
            $this->model->create([  
                'order_id' => 1,
                'order_product_id' => 1,
                'email' => $email,
                'user_id' => 1,
                'site_id' => 1,
                'subscription_status_id' => 1,
                'notes' => 'Subscription created',
                'subscription_plan_id' => 1,
                'payment_address_id' => 1,
                'payment_method' => 'credit_card',
                'shipping_address_id' => 1,
                'shipping_method' => 'standard',
                'product_id' => 1,
                'quantity' => 1,
                'price' => 0,
                'period' => 'month',
                'cycle' => 1,
                'length' => 1,
                'left' => 1,
                'trial_price' => 0,
                'trial_period' => 'month',
                'trial_cycle' => 1,
                'trial_length' => 1,
                'trial_left' => 1,
                'trial_status' => 1,
                'date_next' => date('Y-m-d H:i:s'),
                'notes' => 'Subscription created',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $this->db->commit();
            return [
                'success' => true,
                'message' => 'Subscription created',
            ];
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getSubscribeRequests(): array
    {
        $results = $this->model->where('subscription_status_id', '=', 1)->orderBy('created_at', 'DESC')->findAll(false);
        return $results;
    }

    
} 