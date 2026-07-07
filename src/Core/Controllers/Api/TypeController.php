<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\Type\TypeRepositoryInterface;
use App\Core\Utilities\Debug;

/**
 * All option setup related data control from here.
 */
class TypeController extends ApiController
{
    private TypeRepositoryInterface $typeRepository;
    public function __construct(
        TypeRepositoryInterface $typeRepository
    ) {
        parent::__construct();
        $this->typeRepository = $typeRepository;
    }

    /***
     * 
     * start type trancation methods
     *  */

    public function getTypes(Request $request): Response
    {
        $data = $this->typeRepository->getTypes();
        return $this->renderResponse($data);
    }

    public function createType(Request $request): Response
    {
        try {
            $data = $request->all();
            if ($data instanceof Response) {
                return $data;
            }

           $validData = $request->validate([
                'type' => 'required|string',
                'sort_order' => 'nullable|int',
            ]);

            // check duplicate name
            $existing = $this->typeRepository->findTypeByName($data['type']);
            if ($existing) {
                return $this->renderError(400, 'Type name is already in use.');
            }

            $type = $this->typeRepository->createType($validData);
            return $this->renderResponse($type);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    public function getTypeById(Request $request, $id): Response
    {
        $data = $this->typeRepository->getTypeById($id);
        return $this->renderResponse($data);
    }

    public function updateType(Request $request, $id): Response
    {
        $data = $request->all();
        if ($data instanceof Response) {
            return $data;
        }

        $inputData = [
            'type' => $data['type'] ?? null,
            'sort_order' => $data['sort_order'] ?? null,
        ];

        try {
            $validatedData = $request->validate([
                'type' => 'required|string|notExists:type,type,type_id,'.(int) $id,
                'sort_order' => 'nullable|int'
            ], $inputData);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        }

        // $this->typeRepository->clearQuery();
        $existing = $this->typeRepository->getTypeById($id);
        if (!$existing) {
            return $this->renderError(400, 'Type with the id ' . $id . ' not found.');
        }

        $type = $this->typeRepository->updateType($validatedData, $id);
        return $this->renderResponse($type);
    }

    public function deleteType(Request $request, $id): Response
    {
        try {
            $result = $this->typeRepository->deleteType((int)$id);
            return $this->renderResponse(['success' => $result]);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    public function importTypes(Request $request): Response
    {
        $csv_file = $request->file('csv_file');
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';

        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->typeRepository->importTypes($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }
}
