<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\Product\ProductReviewRepositoryInterface;

class ProductReviewController extends ApiController
{
    private ProductReviewRepositoryInterface $productReviewRepository;

    public function __construct(
        ProductReviewRepositoryInterface $productReviewRepository,
    )
    {
        parent::__construct();
        $this->productReviewRepository = $productReviewRepository;
    }


    public function index(Request $request): Response
    {
        $result = $this->productReviewRepository->findAll();
        return $this->renderResponse($result);
    }

    public function update(Request $request, $id): Response
    {
        try {
            $data = $request->validate([
                'status' => 'integer|nullable',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $existingProductReview = $this->productReviewRepository->find((int)$id);
        if (!$existingProductReview) {
            return $this->renderError(404, 'Product review not found');
        }

        $productReview = $this->productReviewRepository->update((int) $id, $data);
        if (!$productReview) {
            return $this->renderError(500, 'Failed to update product review');
        }
        
        return $this->renderResponse($productReview->data);
    }

    public function delete(Request $request, $id): Response
    {
        $this->productReviewRepository->delete((int) $id);
        return $this->renderResponse(['message' => 'Product review deleted successfully']);
    }

} 