<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Http\ApiController;
use App\Core\Repositories\Subscription\SubscriptionRepositoryInterface;
use App\Core\Repositories\Subscription\SubscriptionPlanRepositoryInterface;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Exceptions\ValidationException;

class SubscriptionController extends ApiController
{
    private SubscriptionRepositoryInterface $subscriptionRepository;
    private SubscriptionPlanRepositoryInterface $subscriptionPlanRepository;

    public function __construct(
        SubscriptionRepositoryInterface $subscriptionRepository,
        SubscriptionPlanRepositoryInterface $subscriptionPlanRepository,
    )
    {
        parent::__construct();
        $this->subscriptionRepository = $subscriptionRepository;
        $this->subscriptionPlanRepository = $subscriptionPlanRepository;
    }

    /**
     * Get all subscriptions.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $subscriptions = $this->subscriptionRepository->findAll();
        return $this->renderResponse($subscriptions);
    }

    public function subscribeRequests(Request $request): Response
    {
        $subscriptions = $this->subscriptionRepository->getSubscribeRequests();
        return $this->renderResponse($subscriptions);
    }

    /**
     * Get a subscription by ID.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function show(Request $request, $id): Response
    {
        $subscription = $this->subscriptionRepository->find((int)$id);
        if(!$subscription){
            return $this->renderError(404, 'Subscription not found');
        }
        return $this->renderResponse($subscription->data);
    }

    /**
     * Create a new subscription.
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        try {
            $data = $request->validate([
                'order_id' => 'required|integer',
                'order_product_id' => 'required|integer',
                'site_id' => 'required|integer',
                'user_id' => 'required|integer',
                'payment_method' => 'required|string',
                'shipping_method' => 'required|string',
                'product_id' => 'required|integer',
                'quantity' => 'required|integer',
                'price' => 'required|numeric',
                'period' => 'required|string|in:day,week,month,year',
                'cycle' => 'required|integer',
                'length' => 'required|integer',
                'left' => 'required|integer',
                'trial_price' => 'required|numeric',
                'trial_period' => 'required|string|in:day,week,month,year',
                'trial_cycle' => 'required|integer',
                'trial_length' => 'required|integer',
                'trial_left' => 'required|integer',
                'trial_status' => 'required|integer',
                'date_next' => 'required|date',
                'subscription_status_id' => 'required|integer',
                'notes' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $subscription = $this->subscriptionRepository->createSubscription($data);
        return $this->renderResponse($subscription);
    }
    



    /**
     * Update a subscription.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id): Response
    {
        try {
            $data = $request->validate([
                'order_id' => 'integer|nullable',
                'order_product_id' => 'integer|nullable',
                'site_id' => 'integer|nullable',
                'user_id' => 'integer|nullable',
                'payment_method' => 'string|nullable',
                'shipping_method' => 'string|nullable',
                'product_id' => 'integer|nullable',
                'quantity' => 'integer|nullable',
                'price' => 'numeric|nullable',
                'period' => 'string|in:day,week,month,year|nullable',
                'cycle' => 'integer|nullable',
                'length' => 'integer|nullable',
                'left' => 'integer|nullable',
                'trial_price' => 'numeric|nullable',
                'trial_period' => 'string|in:day,week,month,year|nullable',
                'trial_cycle' => 'integer|nullable',
                'trial_length' => 'integer|nullable',
                'trial_left' => 'integer|nullable',
                'trial_status' => 'integer|nullable',
                'date_next' => 'date|nullable',
                'subscription_status_id' => 'integer|nullable',
                'notes' => 'string|nullable',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $existingSubscription = $this->subscriptionRepository->find((int)$id);
        if (!$existingSubscription) {
            return $this->renderError(404, 'Subscription not found');
        }

        $subscription = $this->subscriptionRepository->update((int) $id, $data);
        if (!$subscription) {
            return $this->renderError(500, 'Failed to update subscription');
        }
        
        return $this->renderResponse($subscription->data);
    }

    /**
     * Delete a subscription.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function delete(Request $request, $id): Response
    {
        $this->subscriptionRepository->delete((int) $id);
        return $this->renderResponse(['message' => 'Subscription deleted successfully']);
    }

    /**
     * Get all subscription plans.
     *
     * @param Request $request
     * @return Response
     */
    public function planIndex(Request $request): Response
    {
        $plans = $this->subscriptionPlanRepository->findAll();
    
        foreach ($plans as &$plan) {
           
            $content = $plan['subscriptionPlanContent'];
            
            if (!empty($content)) {
                // Convert JSON string to array
                $decodedContent = json_decode($content, true);
                if ($decodedContent !== null && json_last_error() === JSON_ERROR_NONE) {
                    $plan['name'] = $decodedContent['name'];
                    $plan['subscriptionPlanContent'] = $decodedContent;
                }
            }
        }
    
        return $this->renderResponse($plans);
    }
    

    /**
     * Get a subscription plan by ID.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function planShow(Request $request, $id): Response
    {
        $plan = $this->subscriptionPlanRepository->find((int)$id);
        if(!$plan){
            return $this->renderError(404, 'Subscription plan not found');
        }
        $content = $plan->data->subscriptionPlanContent;
        if (!empty($content)) {
            // Convert JSON string to array
            $decodedContent = json_decode($content, true);
            if ($decodedContent !== null && json_last_error() === JSON_ERROR_NONE) {
                $plan->data->name = $decodedContent['name'];
                $plan->data->subscriptionPlanContent = $decodedContent;
            }
        }
        return $this->renderResponse($plan->data);
    }

    /**
     * Create a new subscription plan.
     *
     * @param Request $request
     * @return Response
     */
    public function planCreate(Request $request): Response
    {
        try {
            $data = $request->validate([
                'period' => 'required|string|in:day,week,month,year',
                'length' => 'required|integer',
                'cycle' => 'required|integer',
                'trial_period' => 'required|string|in:day,week,month,year',
                'trial_length' => 'required|integer',
                'trial_cycle' => 'required|integer',
                'trial_status' => 'required|integer|in:0,1',
                'status' => 'required|integer|in:0,1',
                'sort_order' => 'integer|nullable',
                'language_id' => 'required|integer',
                'name' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $plan = $this->subscriptionPlanRepository->create($data);
        return $this->renderResponse($plan->data);
    }

    /**
     * Update a subscription plan.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function planUpdate(Request $request, $id): Response
    {
        try {
            $data = $request->validate([
                'period' => 'string|in:day,week,month,year|nullable',
                'length' => 'integer|required',
                'cycle' => 'integer|nullable',
                'trial_period' => 'string|in:day,week,month,year|nullable',
                'trial_length' => 'integer|nullable',
                'trial_cycle' => 'integer|nullable',
                'trial_status' => 'integer|in:0,1|nullable',
                'status' => 'integer|in:0,1|nullable',
                'sort_order' => 'integer|nullable',
                'language_id' => 'integer|nullable',
                'name' => 'string|required',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $existingPlan = $this->subscriptionPlanRepository->find((int)$id);
        if (!$existingPlan) {
            return $this->renderError(404, 'Subscription plan not found');
        }

        $plan = $this->subscriptionPlanRepository->update((int)$id, $data);
        if (!$plan) {
            return $this->renderError(500, 'Failed to update subscription plan');
        }

        return $this->renderResponse($plan->data);
    }

    /**
     * Delete a subscription plan.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function planDelete(Request $request, $id): Response
    {
        if (!$this->subscriptionPlanRepository->deletePlan((int)$id)) {
            return $this->renderError(500, 'Failed to delete subscription plan');
        }

        return $this->renderResponse(['message' => 'Subscription plan deleted successfully']);
    }

    /**
     * Subscribe email.
     *
     * @param Request $request
     * @return Response
     */
    public function subscribeEmail(Request $request): Response
    {
        $email = $request->all();
        if(!$email['email']){
            return $this->renderError(422, 'Email is required');
        }
        try {
            $data = $request->validate([
                'email' => 'required|email',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }
        $subscription = $this->subscriptionRepository->subscribeEmail($data['email']);
        if(!$subscription){
            return $this->renderError(500, 'Failed to subscribe email');
        }
        return $this->renderResponse($subscription);
    }
} 