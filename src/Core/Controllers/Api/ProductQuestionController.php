<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\Product\ProductQuestionRepositoryInterface;

class ProductQuestionController extends ApiController
{
    private ProductQuestionRepositoryInterface $productQuestionRepository;

    public function __construct(
        ProductQuestionRepositoryInterface $productQuestionRepository,
    )
    {
        parent::__construct();
        $this->productQuestionRepository = $productQuestionRepository;
    }


    public function index(Request $request): Response
    {
        $result = $this->productQuestionRepository->findAll();
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

        $existingProductQuestion = $this->productQuestionRepository->find((int)$id);
        if (!$existingProductQuestion) {
            return $this->renderError(404, 'Product question not found');
        }

        $productQuestion = $this->productQuestionRepository->update((int) $id, $data);
        if (!$productQuestion) {
            return $this->renderError(500, 'Failed to update product question');
        }
        
        return $this->renderResponse($productQuestion->data);
    }

    public function delete(Request $request, $id): Response
    {
        $this->productQuestionRepository->delete((int) $id);
        return $this->renderResponse(['message' => 'Product question deleted successfully']);
    }

} 