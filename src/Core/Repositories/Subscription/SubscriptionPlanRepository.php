<?php

declare(strict_types=1);

namespace App\Core\Repositories\Subscription;

use App\Core\Models\Subscription\SubscriptionPlan;
use App\Core\Models\Subscription\SubscriptionPlanContent;
use App\Core\Repositories\Base\BaseRepository;
use PDO;
use App\Core\Models\Base\Model;

class SubscriptionPlanRepository extends BaseRepository implements SubscriptionPlanRepositoryInterface
{
    private SubscriptionPlanContent $subscriptionPlanContent;

    public function __construct(PDO $db, SubscriptionPlanContent $subscriptionPlanContent) 
    {
        parent::__construct($db, 'subscription_plan', SubscriptionPlan::class);
        $this->subscriptionPlanContent = $subscriptionPlanContent;
        $this->subscriptionPlanContent->setDb($db);
    }

    public function getAll(int $languageId = null, int $start = 0, int $limit = 10): array
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

        // $queryCheck = $query->getQuery();
        $results = $query->findAll();
        $totalRecords = $query->countAll();

        return [
            'data' => $results,
            'total' => $totalRecords
        ];
    }

    public function get(int $subscriptionId): ?array
    {
        $result = $this->model->find($subscriptionId);
        return $result ? $result->findAll() : null;
    }

    public function getActivePlans(): array
    {
        $this->model->where('status', '=', '1');
        $this->model->orderBy('sort_order', 'ASC');
        return $this->findAll();
    }

    public function getPlanWithTrial(): ?SubscriptionPlan
    {
        $this->model->where('trial_status', '=', '1');
        $this->model->where('status', '=', '1');
        $this->model->orderBy('sort_order', 'ASC');
        $this->model->limit(1);
        
        $results = $this->model->findAll();
        return !empty($results['items']) ? $results['items'][0] : null;
    }

    public function findAll(): array
    {
        $results = $this->model->with(['subscriptionPlanContent'])->findAll();
        foreach ($results as &$result) {
            if(isset($result['subscription_plan_content_data'])) {
                $result['subscription_plan_content_data'] = json_decode($result['subscription_plan_content_data'], true);
            }
        }

        return $results ?? [];
    }

    public function find(int $id): ?object
    {
        $result = $this->model->with(['subscriptionPlanContent'])->find($id);
        if ($result && isset($result->subscription_plan_content_data)) {
            $result->subscription_plan_content_data = json_decode($result->subscription_plan_content_data, true);
        }
        return $result;
    }

    public function create(array $data): object
    {
        $this->db->beginTransaction();

        try {
            // Validate required fields
            $requiredFields = [
                'period', 'length', 'cycle', 
                'trial_period', 'trial_length', 'trial_cycle',
                'name', 'language_id'
            ];
            
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    throw new \InvalidArgumentException("Missing required field: {$field}");
                }
            }

            // Separate subscription plan content data
            $subscriptionPlanContent = [
                'name' => $data['name'],
                'language_id' => (int)$data['language_id']
            ];
            
            // Remove content data from main subscription plan data
            // unset($data['name']);
            // unset($data['language_id']);
            
            // Prepare subscription plan data
            $subscriptionPlanData = [
                'period' => $data['period'],
                'length' => (int)$data['length'],
                'cycle' => (int)$data['cycle'],
                'trial_period' => $data['trial_period'],
                'trial_length' => (int)$data['trial_length'],
                'trial_cycle' => (int)$data['trial_cycle'],
                'trial_status' => isset($data['trial_status']) ? (int)$data['trial_status'] : 0,
                'status' => isset($data['status']) ? (int)$data['status'] : 1,
                'sort_order' => isset($data['sort_order']) ? (int)$data['sort_order'] : 0
            ];

            // Insert subscription plan
            $newSubscriptionPlan = parent::create($subscriptionPlanData);
            $subscriptionPlanId = $newSubscriptionPlan->subscription_plan_id;

            // Add subscription plan ID to content data
            $subscriptionPlanContent['subscription_plan_id'] = $subscriptionPlanId;
            
            // Insert subscription plan content
            $this->subscriptionPlanContent->create($subscriptionPlanContent);

            $this->db->commit();
            return $this->find($subscriptionPlanId);
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function update(int $id, array $data): ?Model
    {
        $this->db->beginTransaction();

        try {
            // Separate subscription plan content data
            $subscriptionPlanContent = [];
            $contentFields = ['name', 'language_id'];
            
            foreach ($contentFields as $field) {
                if (isset($data[$field])) {
                    $subscriptionPlanContent[$field] = $field === 'language_id' ? (int)$data[$field] : $data[$field];
                    unset($data[$field]);
                }
            }

            // Update subscription plan
            if (!empty($data)) {
                // Convert numeric fields to integers
                $numericFields = ['length', 'cycle', 'trial_length', 'trial_cycle', 'trial_status', 'status', 'sort_order'];
                foreach ($numericFields as $field) {
                    if (isset($data[$field])) {
                        $data[$field] = (int)$data[$field];
                    }
                }

                // Handle period fields
                $periodFields = ['period', 'trial_period'];
                foreach ($periodFields as $field) {
                    if (isset($data[$field])) {
                        $data[$field] = (string)$data[$field];
                    }
                }

                $this->model->where('subscription_plan_id', '=', $id);
                $this->model->update($data);
            }

            // Update subscription plan content if provided
            if (!empty($subscriptionPlanContent)) {
                // Set subscription_plan_id for the content model
                $this->subscriptionPlanContent->subscription_plan_id = $id;
                
                $this->subscriptionPlanContent->where('subscription_plan_id', '=', $id);
                // Only add language_id condition if it's provided
                if (isset($subscriptionPlanContent['language_id'])) {
                    $this->subscriptionPlanContent->where('language_id', '=', $subscriptionPlanContent['language_id']);
                }
                $this->subscriptionPlanContent->update($subscriptionPlanContent);
            }

            $this->db->commit();
            return $this->find($id);
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        $this->db->beginTransaction();

        try {
            // First delete the subscription plan content
            $this->subscriptionPlanContent->where('subscription_plan_id', '=', $id);
            $this->subscriptionPlanContent->delete($id);

            // Then delete the subscription plan
            $this->model->where('subscription_plan_id', '=', $id);
            $result = $this->model->delete($id);

            $this->db->commit();
            return $result;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    public function deletePlan(int $id): bool
    {
        $this->db->beginTransaction();
        try {
            $this->model->where('subscription_plan_id', '=', $id);
            $result = $this->model->delete($id);
            $this->db->commit();
            return $result;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
} 