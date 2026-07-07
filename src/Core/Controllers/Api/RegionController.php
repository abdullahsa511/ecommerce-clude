<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\Geoip\RegionRepositoryInterface;

class RegionController extends ApiController
{
    private RegionRepositoryInterface $regionRepository;

    public function __construct(
        RegionRepositoryInterface $regionRepository,
    )
    {
        parent::__construct();
        $this->regionRepository = $regionRepository;
    }

    /**
     * Create a new client.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $regions = $this->regionRepository->findAll();
        return $this->renderResponse($regions);
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

        $region = $this->regionRepository->find((int)$id);
        if(!$region){
            return $this->renderError(404, 'Region not found');
        }
        return $this->renderResponse($region->data);
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
                'country_id' => 'required|integer',
                'name' => 'required|string',
                'code' => 'required|string',
                'status' => 'required|integer',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $existingRegion = $this->regionRepository->isExistsCode($data['code']);
        if ($existingRegion) {
            throw new ValidationException(['code' => ['Region code is already in use.']]);
        }

        $region = $this->regionRepository->create($data);
        return $this->renderResponse($region->data);
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
                'country_id' => 'integer|nullable',
                'name' => 'string|required',
                'code' => 'string|required',
                'status' => 'integer|nullable'
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        // Check if region exists
        $existingRegion = $this->regionRepository->find((int)$id);
        if (!$existingRegion) {
            throw new ValidationException(['name' => ['Region not found.']]);
        }

        $existingRegion = $this->regionRepository->isExistsCode($data['code'], (int) $id);
        if ($existingRegion) {
            throw new ValidationException(['code' => ['Region code is already in use.']]);
        }

        $region = $this->regionRepository->updateRegion($data, (int) $id);
        if (!$region) {
            throw new ValidationException(['name' => ['Failed to update region.']]);
        }
        
        return $this->renderResponse($region->data);
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
        $this->regionRepository->delete((int) $id);
        return $this->renderResponse(['message' => 'Region deleted successfully']);
    }
    
    
}
