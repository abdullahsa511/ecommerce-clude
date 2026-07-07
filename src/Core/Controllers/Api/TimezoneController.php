<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\Geoip\TimezoneRepositoryInterface;

class TimezoneController extends ApiController
{
    private TimezoneRepositoryInterface $timezoneRepository;

    public function __construct(
        TimezoneRepositoryInterface $timezoneRepository,
    )
    {
        parent::__construct();
        $this->timezoneRepository = $timezoneRepository;
    }

    /**
     * Create a new client.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $countries = $this->timezoneRepository->findAll();
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

        $timezone = $this->timezoneRepository->find((int)$id);
        if(!$timezone){
            return $this->renderError(404, 'Timezone not found');
        }
        return $this->renderResponse($timezone->data);
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
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }


        $timezone = $this->timezoneRepository->create($data);
        return $this->renderResponse($timezone->data);
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

        $existingTimezone = $this->timezoneRepository->find((int)$id);
        if (!$existingTimezone) {
            return $this->renderError(404, 'Timezone not found');
        }


        $timezone = $this->timezoneRepository->update((int) $id, $data);
        if (!$timezone) {
            return $this->renderError(500, 'Failed to update timezone');
        }
        
        return $this->renderResponse($timezone->data);
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
        $this->timezoneRepository->delete((int) $id);
        return $this->renderResponse(['message' => 'Timezone deleted successfully']);
    }
    
    
}
