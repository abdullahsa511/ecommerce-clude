<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Components\Site;
use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Models\Site\SiteData;
use App\Core\Models\Site\SiteResponse;
use App\Core\Repositories\Site\SiteRepositoryInterface;
use App\Core\Repositories\Site\SettingRepositoryInterface;

class SettingController extends ApiController
{
    private SiteRepositoryInterface $siteRepository;
    private SettingRepositoryInterface $settingRepository;

    public function __construct(
        SiteRepositoryInterface $siteRepository,
        SettingRepositoryInterface $settingRepository,
    )
    {
        parent::__construct();
        $this->siteRepository = $siteRepository;
        $this->settingRepository = $settingRepository;
    }

    /**
     * Get all sites.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $sites = $this->siteRepository->findAll();
        return $this->renderResponse($sites);
    }

    /**
     * Show a site.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function show(Request $request, $id): Response
    {
        $site = $this->siteRepository->find((int)$id);
        if(!$site){
            return $this->renderError(404, 'Site not found');
        }
        $response = new SiteResponse($site->data);
        return $this->renderResponse($response);
    }

    /**
     * Create a new site.
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        try {
            $site = $request->input('site');
            $siteData = new SiteData($site);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $site = $this->siteRepository->createSite($siteData);
        if(!$site){
            return $this->renderError(500, 'Failed to create site');
        }
        $site = new SiteResponse($site->data);
        return $this->renderResponse($site);
    }

    /**
     * Update a site.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */

    public function update(Request $request, int $id): Response
    {
        try {
            $site = $request->input('site');
            $siteData = new SiteData($site);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $site = $this->siteRepository->update($id, $siteData->toArray());
        if(!$site){
            return $this->renderError(500, 'Failed to update site');
        }
        $site = new SiteResponse($site->data);
        return $this->renderResponse($site);
    }

    /**
     * Delete a site.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function delete(Request $request, $id): Response
    {
        $this->siteRepository->delete((int) $id);
        return $this->renderResponse(['message' => 'Site deleted successfully']);
    }

    /**
     * Get email settings.
     *
     * @param Request $request
     * @return Response
     */
    public function getEmailSettings(Request $request): Response
    {
        $data = $request->all();
        $setting = $this->settingRepository->getEmailSettings();
        return $this->renderResponse($setting);
    }

    public function createEmailSettings(Request $request): Response
    {
        try {
            $data = $request->all();
            $request->validate([
                'mail_engine' => 'required|string',
                'test_email' => 'required|email',
                'mail_parameters' => 'required|string',
            ]);
            $setting = $this->settingRepository->createEmailSettings($data);
            if(!$setting){
                return $this->renderError(500, 'Update email settings failed');
            }
            return $this->renderResponse($setting);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }
    }


} 