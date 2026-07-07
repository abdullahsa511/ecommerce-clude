<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\Order\OrderItemRepositoryInterface;
use App\Core\Models\Order\OrderResponse;
use App\Core\Models\Order\OrderItemData;

class OrderItemController extends ApiController
{
    private OrderItemRepositoryInterface $orderItemRepository;

    public function __construct(OrderItemRepositoryInterface $orderItemRepository)
    {
        parent::__construct();
        $this->orderItemRepository = $orderItemRepository;
    }

    /**
     * Get all quotes
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $orders = $this->orderItemRepository->findAll();
        return $this->renderResponse($orders);
    }

    /**
     * Get quote by ID
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function show(Request $request, $id): Response
    {
        $order = $this->orderItemRepository->showOrderItem((int)$id);
        if(!$order){
            return $this->renderError(404, 'Order not found');
        }
        $response = new OrderResponse($order->data);
        return $this->renderResponse($response);
    }

    /**
     * Create a new quote
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        try {
            $orderItems = $request->input('orderItem');

            $orderItemResult = $this->orderItemRepository->createOrderItem($orderItems);
            if(!$orderItemResult){
                return $this->renderError(500, 'Failed to create quote');
            }
            
            return $this->renderResponse([
                'message' => 'Order items created successfully',
                'data' => $orderItemResult
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        } catch (\Exception $e) {
            return $this->renderError(500, 'Failed to create order items: ' . $e->getMessage());
        }
    }

    /**
     * Update a quote
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id): Response
    {
        try {
            $order = $request->input('order_item');
            $orderItemData = new OrderItemData($order);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $order = $this->orderItemRepository->updateOrderItem($orderItemData);
        if(!$order){
            return $this->renderError(500, 'Failed to update order');
        }
        $order = new OrderResponse($order->data);
        return $this->renderResponse($order);
    }

    /**
     * Delete a quote
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function delete(Request $request, $id): Response
    {
        $order = $this->orderItemRepository->showOrderItem((int) $id);
        if (!$order) {
            return $this->renderError(404, 'Order not found');
        }

        try {
            $this->orderItemRepository->delete((int) $id);
            return $this->renderResponse(['message' => 'Order deleted successfully']);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Failed to delete order: ' . $e->getMessage());
        }
    }

    public function productList(Request $request): Response
    {
        $productList = $this->orderItemRepository->productList($request->input('search'));
        return $this->renderResponse($productList);
    }
} 