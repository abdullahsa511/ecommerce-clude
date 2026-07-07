<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\Option\OptionRepositoryInterface;
use App\Core\Utilities\Debug;

/**
 * All option setup related data control from here.
 */
class OptionController extends ApiController
{
    private OptionRepositoryInterface $optionRepository;
    public function __construct(
        OptionRepositoryInterface $optionRepository
    ) {
        parent::__construct();
        $this->optionRepository = $optionRepository;
    }

    /**
     * Fetch all options
     */
    public function getAllOptions(Request $request): Response
    {
        $data = $this->optionRepository->getAllOptions();
        return $this->renderResponse($data);
    }

    public function getAllOptionTypes(Request $request): Response
    {
        $data = $this->optionRepository->getAllOptionTypes();
        return $this->renderResponse($data);
    }

    public function getOptionById(Request $request, $id): Response
    {
        $data = $this->optionRepository->getOptionById($id);
        return $this->renderResponse($data);
    }

    public function create(Request $request): Response
    {
        try {
            $data = $request->all();
            // Debug::dd($data, true);
            if ($data instanceof Response) {
                return $data;
            }
            $inputData = [
                'name' => $data['content']['name'] ?? null,
                'type_id' => $data['type_id'] ?? null,
                'sort_order' => $data['sort_order'] ?? null,
            ];
            $request->validate([
                'name' => 'required|string',
                'type_id' => 'required|int',
                'sort_order' => 'nullable|int',
            ], $inputData);
            // validation function
            // $validatedData = $this->validateOptionData($data);
            // Generate code check duplicate
            $existingName = $this->optionRepository->findByName($data['content']['name']);
            if ($existingName) {
                return $this->renderError(400, 'Option name is already in use.');
            }
            $code = str_replace(' ', '-', strtolower($inputData['name']));
            $existingCode = $this->optionRepository->findByCode($code);
            if ($existingCode) {
                // my user did not know about code. code only handle from backend.
                return $this->renderError(400, 'Option Name is already in use.');
            }
            // Proceed to create option
            $option = $this->optionRepository->createOptions($data);
            return $this->renderResponse($option);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    public function update(Request $request, $id): Response
    {
        $data = $request->all();

        if ($data instanceof Response) {
            return $data;
        }
        $inputData = [
            'name' => $data['content']['name'] ?? null,
            'type_id' => $data['type_id'] ?? null,
            'sort_order' => $data['sort_order'] ?? null,
            'code' => str_replace(' ', '-', strtolower($data['content']['name'] ?? null))
        ];
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|notExists:`option_content`,name,option_id,' . (int) $id,
                'type_id' => 'nullable|int',
                'sort_order' => 'nullable|int',
                'code' => 'required|string|notExists:`option`,code,option_id,' . (int) $id
            ], $inputData);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        }

        $updateData['option'] = $validatedData;
        $updateData['option']['code'] = str_replace(' ', '-', strtolower($validatedData['name']));
        unset($updateData['option']['name']);

        $updateData['content'] = [
            'option_id' => $id,
            'name' => $validatedData['name'],
            'language_id' => $data['content']['language_id'] ?? 1,
        ];
        $this->optionRepository->clearQuery();
        $existingOption = $this->optionRepository->getOptionById($id);
        if (!$existingOption) {
            return $this->renderError(400, 'Option with the id ' . $id . ' not found.');
        }
        $option = $this->optionRepository->updateOptions($updateData, $id);
        return $this->renderResponse($option);
    }
    public function deleteOption(Request $request, $id): Response
    {
        try {
            $option = $this->optionRepository->deleteOptions((int) $id);
            return $this->renderResponse($option);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    public function deleteMultiple(Request $request): Response
    {
        $option_ids = [2, 4];
        try {
            $option = $this->optionRepository->deleteMultipleOptions($option_ids);
            return $this->renderResponse($option);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    public function importOptions(Request $request): Response
    {
        $csv_file = $request->file('csv_file');

        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';

        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->optionRepository->importOptions($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }

    
}
