<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\Product\WeightTypeRepositoryInterface;

class WeightController extends ApiController
{
    private WeightTypeRepositoryInterface $weightTypeRepository;

    public function __construct(
        WeightTypeRepositoryInterface $weightTypeRepository,
    )
    {
        parent::__construct();
        $this->weightTypeRepository = $weightTypeRepository;
    }

    /**
     * Get all weight types.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $weightTypes = $this->weightTypeRepository->getAll(1);
        return $this->renderResponse($weightTypes);
    }

    /**
     * Show a weight type.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function show(Request $request, $id): Response
    {
        $weightType = $this->weightTypeRepository->find((int)$id);
        if(!$weightType){
            return $this->renderError(404, 'Weight type not found');
        }
        return $this->renderResponse($weightType->data);
    }

    /**
     * Create a new weight type.
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        try {
            $data = $request->validate([
                'value' => 'required|numeric',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $weightType = $this->weightTypeRepository->create($data);
        return $this->renderResponse($weightType->data);
    }

    /**
     * Update a weight type.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id): Response
    {
        try {
            $data = $request->validate([
                'value' => 'numeric|nullable',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $existingWeightType = $this->weightTypeRepository->find((int)$id);
        if (!$existingWeightType) {
            return $this->renderError(404, 'Weight type not found');
        }

        $weightType = $this->weightTypeRepository->update((int) $id, $data);
        if (!$weightType) {
            return $this->renderError(500, 'Failed to update weight type');
        }
        
        return $this->renderResponse($weightType->data);
    }

    /**
     * Delete a weight type.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function delete(Request $request, $id): Response
    {
        $this->weightTypeRepository->delete((int) $id);
        return $this->renderResponse(['message' => 'Weight type deleted successfully']);
    }
} 