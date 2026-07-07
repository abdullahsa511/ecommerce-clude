<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\Localisation\CurrencyRepositoryInterface;

class CurrencyController extends ApiController
{
    private CurrencyRepositoryInterface $currencyRepository;

    public function __construct(
        CurrencyRepositoryInterface $currencyRepository,
    )
    {
        parent::__construct();
        $this->currencyRepository = $currencyRepository;
    }

    /**
     * Get all currencies.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $currencies = $this->currencyRepository->findAll();
        return $this->renderResponse($currencies);
    }

    /**
     * Show a currency.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function show(Request $request, $id): Response
    {
        $currency = $this->currencyRepository->find((int)$id);
        if(!$currency){
            return $this->renderError(404, 'Currency not found');
        }
        return $this->renderResponse($currency->data);
    }

    /**
     * Create a new currency.
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        try {
            $data = $request->validate([
                'name' => 'required|string',
                'code' => 'required|string|size:3',
                'value' => 'required|numeric',
                'sign_start' => 'required|string',
                'sign_end' => 'required|string',
                'decimal_place' => 'required|integer|in:0,1',
                'status' => 'required|integer|in:0,1',
            ]);
            // code must be supported less than 3 characters
            if (strlen($data['code']) > 3) {
                throw new ValidationException(['code' => ['The currency code must be less than 3 characters long']]);
            }
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        if ($this->currencyRepository->findOneBy(['code' => $data['code']])) {
            throw new ValidationException(['code' => ['Currency code is already in use.']]);
        }

        $currency = $this->currencyRepository->create($data);
        return $this->renderResponse($currency->data);
    }

    /**
     * Update a currency.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id): Response
    {
        try {
            $data = $request->validate([
                'name' => 'string|required',
                'code' => 'string|required',
                'value' => 'numeric|nullable',
                'sign_start' => 'string|nullable',
                'sign_end' => 'string|nullable',
                'decimal_place' => 'integer|nullable',
                'status' => 'integer|nullable',
            ]);
            // 3 character code supported only
            if (strlen($data['code']) > 3) {
                throw new ValidationException(['code' => ['The currency code must be less than 3 characters long']]);
            }
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        if (isset($data['code'])) {
            $currencyWithSameCode = $this->currencyRepository->isExistsCode($data['code'], (int) $id);
            if ($currencyWithSameCode) {
                throw new ValidationException(['code' => ['Currency code is already in use.']]);
            }
        }
        $existingCurrency = $this->currencyRepository->get((int)$id);
        if (!$existingCurrency) {
            return $this->renderError(404, 'Currency not found');
        }

        $currency = $this->currencyRepository->update((int) $id, $data);
        if (!$currency) {
            return $this->renderError(500, 'Failed to update currency');
        }
        
        return $this->renderResponse($currency->data);
    }

    /**
     * Delete a currency.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function delete(Request $request, $id): Response
    {
        $this->currencyRepository->delete((int) $id);
        return $this->renderResponse(['message' => 'Currency deleted successfully']);
    }
} 