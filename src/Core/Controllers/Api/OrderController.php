<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\Order\OrderRepositoryInterface;
use App\Core\Models\Order\OrderData;
use App\Core\Models\Order\OrderResponse;

class OrderController extends ApiController
{
    private OrderRepositoryInterface $orderRepository;

    public function __construct(OrderRepositoryInterface $orderRepository)
    {
        parent::__construct();
        $this->orderRepository = $orderRepository;
    }

    /**
     * Get all orders
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $orders = $this->orderRepository->findAll();
        $orders = array_map(function($order){
            return new OrderResponse((object) $order);
        }, $orders);
        return $this->renderResponse($orders);
    }

    /**
     * Get order by ID
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function show(Request $request, $id): Response
    {
        $order = $this->orderRepository->showOrder((int)$id);
        if(!$order){
            return $this->renderError(404, 'Order not found');
        }
        $response = new OrderResponse($order->data);
        return $this->renderResponse($response);
    }

    /**
     * Create a new order
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        try {
            $order = $request->input('order');
            $orderData = new OrderData($order);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $order = $this->orderRepository->createOrder($orderData);
        if(!$order){
            return $this->renderError(500, 'Failed to create order');
        }
        $order = new OrderResponse($order->data);
        return $this->renderResponse($order);
    }

    /**
     * Update an order
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id): Response
    {
        try {
            $order = $request->input('order');
            $orderData = new OrderData($order);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $order = $this->orderRepository->updateOrder($orderData);
        if(!$order){
            return $this->renderError(500, 'Failed to update order');
        }
        $order = new OrderResponse($order->data);
        return $this->renderResponse($order);
    }

    /**
     * Delete an order
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function delete(Request $request, $id): Response
    {
        $order = $this->orderRepository->showOrder((int) $id);
        if (!$order) {
            return $this->renderError(404, 'Order not found');
        }

        try {
            $this->orderRepository->delete((int) $id);
            return $this->renderResponse(['message' => 'Order deleted successfully']);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Failed to delete order: ' . $e->getMessage());
        }
    }

    /**
     * Get order tracking
     *
     * @param Request $request
     * @return Response
     */
    public function getOrderTracking(Request $request): Response
    {
        $orderNumber = $request->query('order_number');
        if(!$orderNumber){
            return $this->renderError(400, 'Order number is required');
        }
        try {
            $orderTracking = $this->orderRepository->getOrderTracking($orderNumber);
            return $this->renderResponse($orderTracking);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Failed to get order tracking: ' . $e->getMessage());
        }
    }
} 