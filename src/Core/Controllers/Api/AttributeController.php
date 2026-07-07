<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\Attribute\AttributeRepositoryInterface;
use App\Core\Utilities\Debug;
use App\Core\Exceptions\ValidationException;
/**
 * All attribute setup related data control from here.
 */
class AttributeController extends ApiController
{
    private AttributeRepositoryInterface $attributeRepository;
    public function __construct(
        AttributeRepositoryInterface $attributeRepository
    ) {
        parent::__construct();
        $this->attributeRepository = $attributeRepository;
    }

    /**
     * Fetch all attributes
     */
    public function getAllAttributes(Request $request): Response
    {
        $data = $this->attributeRepository->getAllAttributes();
        return $this->renderResponse($data);
    }

    public function getAllAttributeGroups(Request $request): Response
    {
        $data = $this->attributeRepository->getAllAttributeGroups();
        return $this->renderResponse($data);
    }

    public function getAllAttributeById(Request $request, $id): Response
    {
        $data = $this->attributeRepository->getAllAttributeById($id);
        return $this->renderResponse($data);
    }

    public function create(Request $request): Response
    {
        $data = $request->all();
        $inputData = [
            'name' => $data['content']['name'] ?? null,
            'sort_order' => $data['sort_order'] ?? null,
            'attribute_group_id' => $data['group_content']['attribute_group_id'] ?? null,
        ];
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'sort_order' => 'required|integer',
            'attribute_group_id' => 'required|integer',
        ], $inputData);

        try {
            // Debug::dd($data, true);
            if ($data instanceof Response) {
                return $data;
            }
            if(!isset($data['content']['name']) || empty($data['content']['name'])){
                throw new ValidationException(['name' => ['Name is required']]);
            }
            // group id is required
            if(!isset($data['attribute_group_id']) || empty($data['attribute_group_id'])){
                throw new ValidationException(['attribute_group_id' => ['Attribute group ID is required']]);
            }

            $attribute = $this->attributeRepository->createAttributes($data);
            return $this->renderResponse($attribute);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    public function update(Request $request, $id): Response
    {
        $data = $request->all();
        $inputData = [
            'name' => $data['content']['name'] ?? null,
            'sort_order' => $data['sort_order'] ?? null,
            'attribute_group_id' => $data['group_content']['attribute_group_id'] ?? null,
        ];
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'sort_order' => 'required|integer',
            'attribute_group_id' => 'required|integer',
        ], $inputData);
        try {
            // Debug::dd($data, true);
            if ($data instanceof Response) {
                return $data;
            }
           if(!isset($data['content']['name']) || empty($data['content']['name'])){
                throw new ValidationException(['name' => ['Name is required']]);
            }
            $attribute = $this->attributeRepository->updateAttributes($data, $id);
            return $this->renderResponse($attribute);

        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }
    
    public function delete(Request $request, $id): Response
    {
        try {
            $attribute = $this->attributeRepository->deleteAttributes((int) $id);
            return $this->renderResponse($attribute);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    public function deleteMultiple(Request $request): Response
    {
        $attribute_ids = [2, 4];
        try {
            $attribute = $this->attributeRepository->deleteMultipleAttributes($attribute_ids);
            return $this->renderResponse($attribute);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    public function importAttributes(Request $request): Response
    {
        $csv_file = $request->file('csv_file');
        
        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';
        
        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->attributeRepository->importAttributes($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }

}
