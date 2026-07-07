<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\Geoip\RegionGroupRepositoryInterface;

class RegionGroupController extends ApiController
{
    private RegionGroupRepositoryInterface $regionGroupRepository;

    public function __construct(
        RegionGroupRepositoryInterface $regionGroupRepository,
    )
    {
        parent::__construct();
        $this->regionGroupRepository = $regionGroupRepository;
    }

    /**
     * Create a new client.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $regionGroups = $this->regionGroupRepository->findAll();
        return $this->renderResponse($regionGroups);
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
        $regionGroup = $this->regionGroupRepository->get((int)$id);
        if(!$regionGroup){
            return $this->renderError(404, 'Region group not found');
        }
        return $this->renderResponse($regionGroup->data);
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
                'content' => 'required|string'
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $existingRegionGroup = $this->regionGroupRepository->isExistsName($data['name']);
        // check duplicate name
        if ($existingRegionGroup) {
            throw new ValidationException(['name' => ['Region group name is already in use.']]);
        }

        $regionGroup = $this->regionGroupRepository->create($data);
        return $this->renderResponse($regionGroup->data);
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
                'content' => 'string|nullable'
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $existingRegionGroup = $this->regionGroupRepository->isExistsName($data['name'], (int) $id);
        // check duplicate name
        if ($existingRegionGroup) {
            throw new ValidationException(['name' => ['Region group name is already in use.']]);
        }

        $regionGroup = $this->regionGroupRepository->updateRegionGroup($data, (int) $id);
        if (!$regionGroup) {
            throw new ValidationException(['name' => ['Failed to update region group.']]);
        }
        return $this->renderResponse($regionGroup->data);
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
        $this->regionGroupRepository->delete((int) $id);
        return $this->renderResponse(['message' => 'Region group deleted successfully']);
    }
}
