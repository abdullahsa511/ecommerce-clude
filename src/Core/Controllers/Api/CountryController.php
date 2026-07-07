<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\Geoip\CountryRepositoryInterface;

class CountryController extends ApiController
{
    private CountryRepositoryInterface $countryRepository;

    public function __construct(
        CountryRepositoryInterface $countryRepository,
    )
    {
        parent::__construct();
        $this->countryRepository = $countryRepository;
    }

    /**
     * Create a new client.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $countries = $this->countryRepository->findAll();
        return $this->renderResponse($countries);
    }

    /**
     * Show a language.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function show(Request $request, $id): Response
    {

        $country = $this->countryRepository->find((int)$id);
        if(!$country){
            return $this->renderError(404, 'Country not found');
        }
        return $this->renderResponse($country->data);
    }

    /**
     * Create a new language.
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        try {
            $data = $request->validate([
                'name' => 'required|string',
                'iso_code_2' => 'required|string',
                'iso_code_3' => 'required|string',
                'status' => 'required|integer',
            ]);
            // check duplicate name
            $existingCountry = $this->countryRepository->isExistsName($data['name']);
            if ($existingCountry) {
                throw new ValidationException(['name' => ['Country name is already in use.']]);
            }
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $country = $this->countryRepository->create($data);
        return $this->renderResponse($country->data);
    }

    /**
     * Update a language.
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
                'iso_code_2' => 'string|nullable',
                'iso_code_3' => 'string|nullable',
                'status' => 'integer|nullable',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        // check duplicate name
        $existingCountry = $this->countryRepository->isExistsName($data['name'], (int) $id);
        if ($existingCountry) {
            throw new ValidationException(['name' => ['Country name is already in use.']]);
        }

        $country = $this->countryRepository->updateCountry($data, (int) $id);
        if (!$country) {
            throw new ValidationException(['name' => ['Failed to update country.']]);
        }
        
        return $this->renderResponse($country->data);
    }

    /**
     * Delete a language.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function delete(Request $request, $id): Response
    {
        $this->countryRepository->delete((int) $id);
        return $this->renderResponse(['message' => 'Country deleted successfully']);
    }
    
    
}
