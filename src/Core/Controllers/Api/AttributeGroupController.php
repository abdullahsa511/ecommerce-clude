<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\Attribute\AttributeGroupRepositoryInterface;
use App\Core\Repositories\Attribute\AttributeGroupRepository;
use App\Core\Utilities\Debug;

/**
 * All attribute setup related data control from here.
 */
class AttributeGroupController extends ApiController
{
    private AttributeGroupRepositoryInterface $attributeGroupRepository;
    public function __construct(
        AttributeGroupRepositoryInterface $attributeGroupRepository
    ) {
        parent::__construct();
        $this->attributeGroupRepository = $attributeGroupRepository;
    }

    public function getAllAttributeGroups(Request $request): Response
    {
        $data = $this->attributeGroupRepository->getAllAttributeGroups();
        return $this->renderResponse($data);
    }

    public function getAllAttributeGroupById(Request $request, $id): Response
    {
        $data = $this->attributeGroupRepository->getAllAttributeGroupById($id);
        return $this->renderResponse($data);
    }

    public function create(Request $request): Response
    {
        $data = $request->all();

        $inputData = [
            'name' => $data['content']['name'] ?? null,
        ];
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ], $inputData);

    

        try {
            // Debug::dd($data, true);
            if ($data instanceof Response) {
                return $data;
            }
            

            if(!isset($data['content']['name']) || empty($data['content']['name'])){
                throw new ValidationException(['name' => ['Name is required']]);
            }
            
            if(!isset($data['content']['language_id']) || empty($data['content']['language_id'])){
                throw new ValidationException(['language_id' => ['Language ID is required']]);
            }

            $existingData = $this->attributeGroupRepository->findByName($data['content']['name']);
            if($existingData){
                throw new ValidationException(['name' => ['Attribute group name is already in use']]);
            }

            $data['code'] = str_replace(' ', '-', strtolower(trim($data['content']['name'])));
            $this->attributeGroupRepository->clearQuery();

            $existingData = $this->attributeGroupRepository->findByCode($data['code']);

            if($existingData){
                throw new ValidationException(['code' => ['Attribute group code is already in use']]);
            }

            $this->attributeGroupRepository->clearQuery();
            $attributeGroup = $this->attributeGroupRepository->add($data);
            return $this->renderResponse($attributeGroup);
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
        ];
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ], $inputData);

        try {
            // Debug::dd($data, true);
            if ($data instanceof Response) {
                return $data;
            }
            if(!isset($data['content']['name']) || empty($data['content']['name'])){
                throw new ValidationException(['name' => ['Name is required']]);
            }
            $attributeGroup = $this->attributeGroupRepository->updateAttributeGroups($id, $data);
            return $this->renderResponse($attributeGroup);

        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }
    public function delete(Request $request, $id): Response
    {
        try {
            $attributeGroup = $this->attributeGroupRepository->deleteAttributeGroup((int) $id);
            return $this->renderResponse($attributeGroup);
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
            $attribute = $this->attributeGroupRepository->deleteMultiple($attribute_ids);
            return $this->renderResponse($attribute);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    public function importAttributeGroups(Request $request): Response
    {
        $csv_file = $request->file('csv_file');
        
        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';
        
        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->attributeGroupRepository->importAttributeGroups($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }

}
