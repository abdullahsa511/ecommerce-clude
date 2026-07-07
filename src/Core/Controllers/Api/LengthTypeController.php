<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Http\ApiController;
use App\Core\Repositories\Product\LengthTypeRepositoryInterface;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Exceptions\ValidationException;
use Exception;

class LengthTypeController extends ApiController
{
    private LengthTypeRepositoryInterface $lengthTypeRepository;

    public function __construct(
        LengthTypeRepositoryInterface $lengthTypeRepository,
    )
    {
        parent::__construct();
        $this->lengthTypeRepository = $lengthTypeRepository;
    }

    /**
     * Get all length types.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $lengthTypes = $this->lengthTypeRepository->findAll();
        return $this->renderResponse($lengthTypes);
    }

    /**
     * Get a length type by ID.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function show(Request $request, $id): Response
    {
        $lengthType = $this->lengthTypeRepository->find((int)$id);
        if(!$lengthType){
            return $this->renderError(404, 'Length type not found');
        }
        return $this->renderResponse($lengthType->data);
    }

    /**
     * Create a new length type.
     *
     * @param Request $request
     * @return Response
     */
    public function createLengthType(Request $request): Response
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
            ], $inputData);

            $validatedData['language_id'] = $data['language_id'] ?? $data['lanugage_id'] ?? 1;
            $validatedData['code'] = $data['name'] ?? $data['name'] ?? null;

            $lengthType = $this->lengthTypeRepository->createLenthType($validatedData);
            return $this->renderResponse($lengthType);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    /**
     * Update a length type.
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

        $existingLengthType = $this->lengthTypeRepository->find((int)$id);
        if (!$existingLengthType) {
            return $this->renderError(404, 'Length type not found');
        }

        $lengthType = $this->lengthTypeRepository->updateLengthType((int)$id, $data);
        if (!$lengthType) {
            return $this->renderError(500, 'Failed to update length type');
        }

        return $this->renderResponse($lengthType);
    }

    /**
     * Delete a length type.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function delete(Request $request, $id): Response
    {
        try {
            $variant = $this->lengthTypeRepository->deleteLengthType((int) $id);
            return $this->renderResponse($variant);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    public function importLengthTypes(Request $request): Response
    {
        $csv_file = $request->file('csv_file');
        
        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';
        
        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            // $result = $this->lengthTypeRepository->importStatuses($csv_file_path, 'length_type_id');
            $result = $this->lengthTypeRepository->importCSVs($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }
} 