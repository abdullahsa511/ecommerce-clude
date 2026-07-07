<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Http\ApiController;
use App\Core\Repositories\Product\WeightTypeRepositoryInterface;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Exceptions\ValidationException;

class WeightTypeController extends ApiController
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
        $weightTypes = $this->weightTypeRepository->findAll();
        return $this->renderResponse($weightTypes);
    }

    /**
     * Get a weight type by ID.
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
    public function createWeightType(Request $request): Response
    {
        try {
            $data = $request->all();
            if ($data instanceof Response) {
                return $data;
            }
            $inputData = [
                'value' => $data['value'] ?? null,
                'name' => $data['name'] ?? null,
                'unit' => $data['unit'] ?? null,
            ];
            $validatedData = $request->validate([
                'value' => 'required|numeric|decimal:15,8',
                'name' => 'required|string|max:30',
                'unit' => 'required|string|max:4',
            ],$inputData);
            $validatedData['language_id'] = $data['language_id'] ?? $data['lanugage_id'] ?? 1;
            $validatedData['code'] = $data['name'] ?? $data['name'] ?? null;

            $weightType = $this->weightTypeRepository->createWeightType($validatedData);
            return $this->renderResponse($weightType);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
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
        $data = $request->all();
        if($data instanceof Response){
            return $data;
        }
        $inputData = [
            'name' => $data['name'] ?? null,
            'unit' => $data['unit'] ?? null,
        ];
        try {
            $validatedData = $request->validate([
                'value' => 'required|numeric|decimal:15,8',
                'name' => 'required|string|max:30',
                'unit' => 'required|string|max:4',
            ], $inputData);
            $data['code'] = $data['name'] ?? null;
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        }

        $existingWeightType = $this->weightTypeRepository->find((int)$id);
        if (!$existingWeightType) {
            return $this->renderError(404, 'Weight type not found');
        }

        $weightType = $this->weightTypeRepository->updateWeightType((int)$id, $data);
        if (!$weightType) {
            return $this->renderError(500, 'Failed to update weight type');
        }

        return $this->renderResponse($weightType);
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
        try {
            $variant = $this->weightTypeRepository->deleteWeightType((int) $id);
            return $this->renderResponse($variant);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    public function importWeightTypes(Request $request): Response
    {
        $csv_file = $request->file('csv_file');
        
        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';
        
        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
           $result = $this->weightTypeRepository->importCSVs($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }
} 