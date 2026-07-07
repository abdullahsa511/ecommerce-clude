<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Http\ApiController;
use App\Core\Http\Request;
use App\Core\Http\Response;
use function App\Core\System\utils\app;

use App\Core\Repositories\Return\ReturnResolutionRepositoryInterface;
use App\Core\Repositories\Status\OrderStatusRepositoryInterface;
use App\Core\Repositories\Status\PaymentStatusRepositoryInterface;
use App\Core\Repositories\Status\ReturnStatusRepositoryInterface;
use App\Core\Repositories\Status\ShippingStatusRepositoryInterface;
use App\Core\Repositories\Status\StockStatusRepositoryInterface;
use App\Core\Repositories\Status\SubscriptionStatusRepositoryInterface;
use App\Core\Repositories\Status\ReturnResolutionStatusRepositoryInterface;
use App\Core\Repositories\Status\ReturnReasonStatusRepositoryInterface;
use App\Core\Exceptions\ValidationException;
use PDO;

class StatusController extends ApiController
{
    private StockStatusRepositoryInterface $stockStatusRepository;
    private SubscriptionStatusRepositoryInterface $subscriptionStatusRepository;
    private OrderStatusRepositoryInterface $orderStatusRepository;
    private PaymentStatusRepositoryInterface $paymentStatusRepository;
    private ShippingStatusRepositoryInterface $shippingStatusRepository;
    private ReturnStatusRepositoryInterface $returnStatusRepository;
    private ReturnResolutionStatusRepositoryInterface $returnResolutionRepository;
    private ReturnReasonStatusRepositoryInterface $returnReasonRepository;

    public function __construct(
        StockStatusRepositoryInterface $stockStatusRepository,
        SubscriptionStatusRepositoryInterface $subscriptionStatusRepository,
        OrderStatusRepositoryInterface $orderStatusRepository,
        PaymentStatusRepositoryInterface $paymentStatusRepository,
        ShippingStatusRepositoryInterface $shippingStatusRepository,
        ReturnStatusRepositoryInterface $returnStatusRepository,
        ReturnResolutionStatusRepositoryInterface $returnResolutionRepository,
        ReturnReasonStatusRepositoryInterface $returnReasonRepository
    )
    {
        parent::__construct();
        $this->stockStatusRepository = $stockStatusRepository;
        $this->subscriptionStatusRepository = $subscriptionStatusRepository;
        $this->orderStatusRepository = $orderStatusRepository;
        $this->paymentStatusRepository = $paymentStatusRepository;
        $this->shippingStatusRepository = $shippingStatusRepository;
        $this->returnStatusRepository = $returnStatusRepository;
        $this->returnResolutionRepository = $returnResolutionRepository;
        $this->returnReasonRepository = $returnReasonRepository;
    }

    /**
     * Get all subscriptions.
     *
     * @param Request $request
     * @return Response
     */
    public function stockStatusIndex(Request $request): Response
    {
        $stockStatuses = $this->stockStatusRepository->findAll();
        return $this->renderResponse($stockStatuses);
    }

    /**
     * Get a subscription by ID.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function stockStatusShow(Request $request, $id): Response
    {
        $stockStatus = $this->stockStatusRepository->find((int)$id);
        if(!$stockStatus){
            return $this->renderError(404, 'Stock status not found');
        }
        return $this->renderResponse($stockStatus->data);
    }

    /**
     * Create a new stock status.
     *
     * @param Request $request
     * @return Response
     */
    public function stockStatusCreate(Request $request): Response
    {
        $data = $this->validate([
            'name' => 'required|string|max:255',
            'language_id' => 'required|integer|exists:language,language_id',
        ]);
        if($data instanceof Response){
            return $data;
        }
        $this->isExistsName($data['name'], 'stock_status');
        $stockStatus = $this->stockStatusRepository->create($data);
        return $this->renderResponse($stockStatus->data);
    }

    /**
     * Update a stock status.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function stockStatusUpdate(Request $request, $id): Response
    {
        $data = $this->validate([
            'name' => 'required|string|max:255',
            'language_id' => 'integer|exists:language,language_id',
        ]);
        if($data instanceof Response){
            return $data;
        }
        $this->isExistsName($data['name'], 'stock_status', (int)$id);
        $stockStatus = $this->stockStatusRepository->update((int)$id, $data);
        return $this->renderResponse($stockStatus->data);
    }
    /**
     * Delete a subscription.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function stockStatusDelete(Request $request, $id): Response
    {
        $this->stockStatusRepository->delete((int) $id);
        return $this->renderResponse(['message' => 'Stock status deleted successfully']);
    }

    public function importStockStatuses(Request $request): Response
    {
        $csv_file = $request->file('csv_file');
        
        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';
        
        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->stockStatusRepository->importStatuses($csv_file_path, 'stock_status_id');
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }
    public function importSubscriptionStatuses(Request $request): Response
    {
        $csv_file = $request->file('csv_file');
        
        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';
        
        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->subscriptionStatusRepository->importStatuses($csv_file_path, 'subscription_status_id');
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }
    public function importOrderStatuses(Request $request): Response
    {
        $csv_file = $request->file('csv_file');
        
        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';
        
        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->orderStatusRepository->importStatuses($csv_file_path, 'order_status_id');
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }
    public function importPaymentStatuses(Request $request): Response
    {
        $csv_file = $request->file('csv_file');
        
        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';
        
        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->paymentStatusRepository->importStatuses($csv_file_path, 'payment_status_id');
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }
    // shipping statuses import api
    public function importShippingStatuses(Request $request): Response
    {
        $csv_file = $request->file('csv_file');
        
        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';
        
        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->shippingStatusRepository->importStatuses($csv_file_path, 'shipping_status_id');
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }
    // return statuses import api
    public function importReturnStatuses(Request $request): Response
    {
        $csv_file = $request->file('csv_file');
        
        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';
        
        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->returnStatusRepository->importStatuses($csv_file_path, 'return_status_id');
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }
    // return resolutions import api
    public function importReturnResolutions(Request $request): Response
    {
        $csv_file = $request->file('csv_file');
        
        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';
        
        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->returnResolutionRepository->importStatuses($csv_file_path, 'return_resolution_id');
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }
    // return reasons import api
    public function importReturnReasons(Request $request): Response
    {
        $csv_file = $request->file('csv_file');
        
        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';
        
        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->returnReasonRepository->importStatuses($csv_file_path, 'return_reason_id');
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Get all subscription statuses.
     *
     * @param Request $request
     * @return Response
     */
    public function subscriptionStatusIndex(Request $request): Response
    {
        $subscriptionStatuses = $this->subscriptionStatusRepository->findAll();
        return $this->renderResponse($subscriptionStatuses);
    }

    /**
     * Get a subscription status by ID.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function subscriptionStatusShow(Request $request, $id): Response
    {
        $subscriptionStatus = $this->subscriptionStatusRepository->find((int)$id);
        if(!$subscriptionStatus){
            return $this->renderError(404, 'Subscription status not found');
        }
        return $this->renderResponse($subscriptionStatus->data);
    }

    /**
     * Create a new subscription status.
     *
     * @param Request $request
     * @return Response
     */
    public function subscriptionStatusCreate(Request $request): Response
    {
        $data = $this->validate([
            'name' => 'required|string|max:32',
            'language_id' => 'required|integer|exists:language,language_id',
        ]);
        if($data instanceof Response){
            return $data;
        }
        $this->isExistsName($data['name'], 'subscription_status');
        $subscriptionStatus = $this->subscriptionStatusRepository->create($data);
        return $this->renderResponse($subscriptionStatus->data);
    }

    /**
     * Update a subscription status.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function subscriptionStatusUpdate(Request $request, $id): Response
    {   
        $data = $request->all();
        if($data instanceof Response){
            return $data;
        }   
        try {
          $request->validate([
            'name' => 'required|string|max:32',
            'language_id' => 'integer|exists:language,language_id',
         ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        }
        $this->isExistsName($data['name'], 'subscription_status', (int)$id);
        unset($data['value']); // remove value from data
        $subscriptionStatus = $this->subscriptionStatusRepository->updateSubscriptionStatus($data, (int)$id);
        return $this->renderResponse($subscriptionStatus);
    }

    /**
     * Delete a subscription status.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function subscriptionStatusDelete(Request $request, $id): Response
    {
        $this->subscriptionStatusRepository->delete((int) $id);
        return $this->renderResponse(['message' => 'Subscription status deleted successfully']);
    }

    /**
     * Get all order statuses.
     *
     * @param Request $request
     * @return Response
     */
    public function orderStatusIndex(Request $request): Response
    {
        $orderStatuses = $this->orderStatusRepository->findAll();
        return $this->renderResponse($orderStatuses);
    }

    /**
     * Get an order status by ID.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function orderStatusShow(Request $request, $id): Response
    {
        $orderStatus = $this->orderStatusRepository->find((int)$id);
        if(!$orderStatus){
            return $this->renderError(404, 'Order status not found');
        }
        return $this->renderResponse($orderStatus->data);
    }

    /**
     * Create a new order status.
     *
     * @param Request $request
     * @return Response
     */
    public function orderStatusCreate(Request $request): Response
    {
        $data = $this->validate([
            'name' => 'required|string|max:32',
            'language_id' => 'required|integer|exists:language,language_id',
        ]);
        if($data instanceof Response){
            return $data;
        }
        $this->isExistsName($data['name'], 'order_status');
        $orderStatus = $this->orderStatusRepository->create($data);
        return $this->renderResponse($orderStatus->data);
    }

    /**
     * Update an order status.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function orderStatusUpdate(Request $request, $id): Response
    {
        $data = $request->all();
        if($data instanceof Response){
            return $data;
        }
        try {
          $request->validate([
            'name' => 'required|string|max:32',
            'language_id' => 'integer|exists:language,language_id',
         ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        }
        $this->isExistsName($data['name'], 'order_status', (int)$id);
        $orderStatus = $this->orderStatusRepository->update((int)$id, $data);
        return $this->renderResponse($orderStatus->data);
    }

    /**
     * Delete an order status.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function orderStatusDelete(Request $request, $id): Response
    {
        // $this->orderStatusRepository->delete((int) $id);
        // return $this->renderResponse(['message' => 'Order status deleted successfully']);
        try {
            $orderStatus = $this->orderStatusRepository->delete((int) $id);
            return $this->renderResponse($orderStatus);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Failed to delete order status: ' . $e->getMessage());
        }catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage());
        }
    }

    /**
     * Get all payment statuses.
     *
     * @param Request $request
     * @return Response
     */
    public function paymentStatusIndex(Request $request): Response
    {
        $paymentStatuses = $this->paymentStatusRepository->findAll();
        return $this->renderResponse($paymentStatuses);
    }

    /**
     * Get a payment status by ID.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function paymentStatusShow(Request $request, $id): Response
    {
        $paymentStatus = $this->paymentStatusRepository->find((int)$id);
        if(!$paymentStatus){
            return $this->renderError(404, 'Payment status not found');
        }
        return $this->renderResponse($paymentStatus->data);
    }

    /**
     * Create a new payment status.
     *
     * @param Request $request
     * @return Response
     */
    public function paymentStatusCreate(Request $request): Response
    {
        $data = $this->validate([
            'name' => 'required|string|max:32',
            'language_id' => 'required|integer|exists:language,language_id',
        ]);
        if($data instanceof Response){
            return $data;
        }
        $this->isExistsName($data['name'], 'payment_status');
        $paymentStatus = $this->paymentStatusRepository->create($data);
        return $this->renderResponse($paymentStatus->data);
    }

    /**
     * Update a payment status.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function paymentStatusUpdate(Request $request, $id): Response
    {
        $data = $request->all();
        if($data instanceof Response){
            return $data;
        }
        try {
          $request->validate([
            'name' => 'required|string|max:32',
            'language_id' => 'integer|exists:language,language_id',
         ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        }
        $this->isExistsName($data['name'], 'payment_status', (int)$id);
        $paymentStatus = $this->paymentStatusRepository->update((int)$id, $data);
        return $this->renderResponse($paymentStatus->data);
    }

    /**
     * Delete a payment status.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function paymentStatusDelete(Request $request, $id): Response
    {
        $this->paymentStatusRepository->delete((int) $id);
        return $this->renderResponse(['message' => 'Payment status deleted successfully']);
    }

    /**
     * Get all shipping statuses.
     *
     * @param Request $request
     * @return Response
     */
    public function shippingStatusIndex(Request $request): Response
    {
        $shippingStatuses = $this->shippingStatusRepository->findAll();
        return $this->renderResponse($shippingStatuses);
    }

    /**
     * Get a shipping status by ID.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function shippingStatusShow(Request $request, $id): Response
    {
        $shippingStatus = $this->shippingStatusRepository->find((int)$id);
        if(!$shippingStatus){
            return $this->renderError(404, 'Shipping status not found');
        }
        return $this->renderResponse($shippingStatus->data);
    }

    /**
     * Create a new shipping status.
     *
     * @param Request $request
     * @return Response
     */
    public function shippingStatusCreate(Request $request): Response
    {
        $data = $request->all();
        if($data instanceof Response){
            return $data;
        }
        try {
          $request->validate([
            'name' => 'required|string|max:32',
            'language_id' => 'integer|exists:language,language_id',
        ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        }
        $this->isExistsName($data['name'], 'shipping_status');
        $shippingStatus = $this->shippingStatusRepository->create($data);
        return $this->renderResponse($shippingStatus->data);
    }

    /**
     * Update a shipping status.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function shippingStatusUpdate(Request $request, $id): Response
    {
        $data = $request->all();
        if($data instanceof Response){
            return $data;
        }
        $inputData = [
            'name' => $data['name'] ?? null,
            'language_id' => $data['language_id'] ?? null,
        ];
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:32',
                'language_id' => 'integer|exists:language,language_id',
            ], $inputData);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        }
        $this->isExistsName($data['name'], 'shipping_status', (int)$id);
        $shippingStatus = $this->shippingStatusRepository->update((int)$id, $data);
        return $this->renderResponse($shippingStatus->data);
    }

    /**
     * Delete a shipping status.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function shippingStatusDelete(Request $request, $id): Response
    {
        $this->shippingStatusRepository->delete((int) $id);
        return $this->renderResponse(['message' => 'Shipping status deleted successfully']);
    }

    /**
     * Get all return statuses.
     *
     * @param Request $request
     * @return Response
     */
    public function returnStatusIndex(Request $request): Response
    {
        $returnStatuses = $this->returnStatusRepository->findAll();
        return $this->renderResponse($returnStatuses);
    }

    /**
     * Get a return status by ID.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function returnStatusShow(Request $request, $id): Response
    {
        $returnStatus = $this->returnStatusRepository->find((int)$id);
        if(!$returnStatus){
            return $this->renderError(404, 'Return status not found');
        }
        return $this->renderResponse($returnStatus->data);
    }

    /**
     * Create a new return status.
     *
     * @param Request $request
     * @return Response
     */
    public function returnStatusCreate(Request $request): Response
    {
        $data = $this->validate([
            'name' => 'required|string|max:32',
            'language_id' => 'required|integer|exists:language,language_id',
        ]);
        if($data instanceof Response){
            return $data;
        }
        $this->isExistsName($data['name'], 'return_status');
        $returnStatus = $this->returnStatusRepository->create($data);
        return $this->renderResponse($returnStatus->data);
    }

    /**
     * Update a return status.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function returnStatusUpdate(Request $request, $id): Response
    {
        $data = $this->validate([
            'name' => 'required|string|max:32',
            'language_id' => 'integer|exists:language,language_id',
        ]);
        if($data instanceof Response){
            return $data;
        }
        $this->isExistsName($data['name'], 'return_status', (int)$id);
        $returnStatus = $this->returnStatusRepository->update((int)$id, $data);
        return $this->renderResponse($returnStatus->data);
    }

    /**
     * Delete a return status.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function returnStatusDelete(Request $request, $id): Response
    {
        $this->returnStatusRepository->delete((int) $id);
        return $this->renderResponse(['message' => 'Return status deleted successfully']);
    }

    /**
     * Get all return resolutions.
     *
     * @param Request $request
     * @return Response
     */
    public function returnResolutionIndex(Request $request): Response
    {
        $returnResolutions = $this->returnResolutionRepository->findAll();
        return $this->renderResponse($returnResolutions);
    }

    /**
     * Get a return resolution by ID.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function returnResolutionShow(Request $request, $id): Response
    {
        $returnResolution = $this->returnResolutionRepository->find((int)$id);
        if(!$returnResolution){
            return $this->renderError(404, 'Return resolution not found');
        }
        return $this->renderResponse($returnResolution->data);
    }

    /**
     * Create a new return resolution.
     *
     * @param Request $request
     * @return Response
     */
    public function returnResolutionCreate(Request $request): Response
    {
        $data = $this->validate([
            'name' => 'required|string|max:64',
            'language_id' => 'required|integer|exists:language,language_id',
        ]);
        if($data instanceof Response){
            return $data;
        }
        $this->isExistsName($data['name'], 'return_resolution');
        $returnResolution = $this->returnResolutionRepository->create($data);
        return $this->renderResponse($returnResolution->data);
    }

    /**
     * Update a return resolution.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function returnResolutionUpdate(Request $request, $id): Response
    {
        $data = $this->validate([
            'name' => 'required|string|max:64',
            'language_id' => 'integer|exists:language,language_id',
        ]);
        if($data instanceof Response){
            return $data;
        }
        $this->isExistsName($data['name'], 'return_resolution', (int)$id);
        $returnResolution = $this->returnResolutionRepository->update((int)$id, $data);
        return $this->renderResponse($returnResolution->data);
    }

    /**
     * Delete a return resolution.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function returnResolutionDelete(Request $request, $id): Response
    {
        $this->returnResolutionRepository->delete((int) $id);
        return $this->renderResponse(['message' => 'Return resolution deleted successfully']);
    }

    /**
     * Get all return reasons.
     *
     * @param Request $request
     * @return Response
     */
    public function returnReasonIndex(Request $request): Response
    {
        $returnReasons = $this->returnReasonRepository->findAll();
        return $this->renderResponse($returnReasons);
    }

    /**
     * Get a return reason by ID.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function returnReasonShow(Request $request, $id): Response
    {
        $returnReason = $this->returnReasonRepository->find((int)$id);
        if(!$returnReason){
            return $this->renderError(404, 'Return reason not found');
        }
        return $this->renderResponse($returnReason->data);
    }

    /**
     * Create a new return reason.
     *
     * @param Request $request
     * @return Response
     */
    public function returnReasonCreate(Request $request): Response
    {
        $data = $this->validate([
            'name' => 'required|string|max:128',
            'language_id' => 'required|integer|exists:language,language_id',
        ]);
        if($data instanceof Response){
            return $data;
        }
        $this->isExistsName($data['name'], 'return_reason');
        $returnReason = $this->returnReasonRepository->create($data);
        return $this->renderResponse($returnReason->data);
    }

    /**
     * Update a return reason.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function returnReasonUpdate(Request $request, $id): Response
    {
        $data = $this->validate([
            'name' => 'required|string|max:128',
            'language_id' => 'integer|exists:language,language_id',
        ]);
        if($data instanceof Response){
            return $data;
        }
        $this->isExistsName($data['name'], 'return_reason', (int)$id);
        $returnReason = $this->returnReasonRepository->update((int)$id, $data);
        return $this->renderResponse($returnReason->data);
    }

    /**
     * Delete a return reason.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function returnReasonDelete(Request $request, $id): Response
    {
        $this->returnReasonRepository->delete((int) $id);
        return $this->renderResponse(['message' => 'Return reason deleted successfully']);
    }

    // is exists name
    public function isExistsName(string $name, string $table, int $id = 0): void
    {
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
        $idColumn = $table . '_id';
        $whereCondition = $id > 0 ? "AND {$idColumn} != :id" : '';
        $sql = "SELECT COUNT(*) AS total FROM `{$table}` WHERE name = :name {$whereCondition} LIMIT 1;";

        $db = app(PDO::class);
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':name', $name);
        if ($id > 0) {
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        }
        $stmt->execute();
        $results = $stmt->fetchColumn();
        if ($results > 0) {
            throw new ValidationException(['name' => ['Status name is already in use.']]);
        }
    }
} 