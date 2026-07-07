<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\Tax\TaxRateRepositoryInterface;
use App\Core\Repositories\Tax\TaxRuleRepositoryInterface;
use App\Core\Repositories\Tax\TaxTypeRepositoryInterface;

class TaxController extends ApiController
{
    private TaxRateRepositoryInterface $taxRateRepository;
    private TaxRuleRepositoryInterface $taxRuleRepository;
    private TaxTypeRepositoryInterface $taxTypeRepository;

    public function __construct(
        TaxRateRepositoryInterface $taxRateRepository,
        TaxRuleRepositoryInterface $taxRuleRepository,
        TaxTypeRepositoryInterface $taxTypeRepository,
    )
    {
        parent::__construct();
        $this->taxRateRepository = $taxRateRepository;
        $this->taxRuleRepository = $taxRuleRepository;
        $this->taxTypeRepository = $taxTypeRepository;
    }

    /**
     * Get all tax rates.
     *
     * @param Request $request
     * @return Response
     */
    public function rateIndex(Request $request): Response
    {
        $taxRates = $this->taxRateRepository->findAll();
        return $this->renderResponse($taxRates);
    }

    /**
     * Get all tax rules.
     *
     * @param Request $request
     * @return Response
     */
    public function ruleIndex(Request $request): Response
    {
        $result = $this->taxRuleRepository->findAll();
        return $this->renderResponse($result);
    }
    

    public function typeIndex(Request $request): Response
    {
        $taxTypes = $this->taxTypeRepository->getAll();
        return $this->renderResponse($taxTypes);
    }


    public function rateShow(Request $request, int $id): Response
    {
        $taxRate = $this->taxRateRepository->find($id);
        if (!$taxRate) {
            return $this->renderError(404, 'Tax rate not found');
        }
        return $this->renderResponse($taxRate->data);
    }
    /**
     * Get a specific tax rule.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function ruleShow(Request $request, int $id): Response
    {
        $taxRule = $this->taxRuleRepository->find($id);
        if (!$taxRule) {
            return $this->renderError(404, 'Tax rule not found');
        }
        return $this->renderResponse($taxRule->data);
    }

    /**
     * Get all tax types.
     *
     * @param Request $request
     * @return Response
     */
    public function typeShow(Request $request, int $id): Response
    {
        $taxType = $this->taxTypeRepository->find($id);
        if (!$taxType) {
            return $this->renderError(404, 'Tax type not found');
        }
        return $this->renderResponse($taxType->data);
    }

    /**
     * Create a new tax rate.
     *
     * @param Request $request
     * @return Response
     */
    public function rateCreate(Request $request): Response
    {
        try {
            $data = $request->validate([
                'region_group_id' => 'required|integer',
                'name' => 'required|string',
                'rate' => 'required|numeric',
                'type' => 'required|string',
            ]);
            $existingTaxRate = $this->taxRateRepository->isNameExists($data['name']);
            if ($existingTaxRate) {
                throw new ValidationException(['name' => ['Tax rate name is already in use.']]);
            }
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $taxRate = $this->taxRateRepository->create($data);
        return $this->renderResponse($taxRate->data);
    }

    /**
     * Create a new tax rule.
     *
     * @param Request $request
     * @return Response
     */
    public function ruleCreate(Request $request): Response
    {
        try {
            $data = $request->validate([
                'tax_type_id' => 'required|integer',
                'tax_rate_id' => 'required|integer',
                'based' => 'required|string',
                'priority' => 'required|integer',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $taxRule = $this->taxRuleRepository->create($data);
        return $this->renderResponse($taxRule->data);
    }

    /**
     * Create a new tax type.
     *
     * @param Request $request
     * @return Response
     */
    public function typeCreate(Request $request): Response
    {
        try {
            $data = $request->validate([
                'name' => 'required|string',
                'content' => 'required|string',
            ]);
            // duplicate name check
            $existingTaxType = $this->taxTypeRepository->isNameExists($data['name']);
            if ($existingTaxType) {
                throw new ValidationException(['name' => ['Tax type name is already in use.']]);
            }
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $taxType = $this->taxTypeRepository->create($data);
        return $this->renderResponse($taxType->data);
    }

    /**
     * Update a tax rate.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function rateUpdate(Request $request, int $id): Response
    {
        try {
            $data = $request->validate([
                'name' => 'required|string',
                'region_group_id' => 'integer|nullable',
                'rate' => 'numeric|nullable',
                'type' => 'string|nullable',
            ]);
            $existingTaxRate = $this->taxRateRepository->isNameExists($data['name'], (int) $id);
            if ($existingTaxRate) {
                throw new ValidationException(['name' => ['Tax rate name is already in use.']]);
            }
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $existingRate = $this->taxRateRepository->find($id);
        if (!$existingRate) {
            return $this->renderError(404, 'Tax rate not found');
        }

        $taxRate = $this->taxRateRepository->update($id, $data);
        if (!$taxRate) {
            return $this->renderError(500, 'Failed to update tax rate');
        }
        
        return $this->renderResponse($taxRate->data);
    }

    /**
     * Update a tax rule.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function ruleUpdate(Request $request, int $id): Response
    {
        try {
            $data = $request->validate([
                'tax_type_id' => 'integer|nullable',
                'tax_rate_id' => 'integer|nullable',
                'based' => 'string|nullable',
                'priority' => 'integer|nullable',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $existingRule = $this->taxRuleRepository->find($id);
        if (!$existingRule) {
            return $this->renderError(404, 'Tax rule not found');
        }

        $taxRule = $this->taxRuleRepository->update($id, $data);
        if (!$taxRule) {
            return $this->renderError(500, 'Failed to update tax rule');
        }
        
        return $this->renderResponse($taxRule->data);
    }

    /**
     * Update a tax type.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function typeUpdate(Request $request, int $id): Response
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|notExists:`tax_type`,name,tax_type_id,' . (int) $id,
                'name' => 'required|string',
                'content' => 'string|nullable',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $existingType = $this->taxTypeRepository->find($id);
        if (!$existingType) {
            return $this->renderError(404, 'Tax type not found');
        }

        $taxType = $this->taxTypeRepository->update($id, $data);
        if (!$taxType) {
            return $this->renderError(500, 'Failed to update tax type');
        }
        
        return $this->renderResponse($taxType->data);
    }

    /**
     * Delete a tax rate.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function rateDelete(Request $request, int $id): Response
    {
        $this->taxRateRepository->delete($id);
        return $this->renderResponse(['message' => 'Tax rate deleted successfully']);
    }

    /**
     * Delete a tax rule.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function ruleDelete(Request $request, int $id): Response
    {
        $this->taxRuleRepository->delete($id);
        return $this->renderResponse(['message' => 'Tax rule deleted successfully']);
    }

    /**
     * Delete a tax type.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function typeDelete(Request $request, int $id): Response
    {
        $this->taxTypeRepository->delete($id);
        return $this->renderResponse(['message' => 'Tax type deleted successfully']);
    }

    // importTaxTypes
    public function importTaxTypes(Request $request): Response
    {
        $csv_file = $request->file('csv_file');
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';
        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }
        $result = $this->taxTypeRepository->importTaxTypes($csv_file_path);
        return $this->renderResponse($result);
    }

    public function importTaxRates(Request $request): Response
    {
        $csv_file = $request->file('csv_file');
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';
        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }
        try {
            $result = $this->taxRateRepository->importTaxRates($csv_file_path);
            return $this->renderResponse($result);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }
}