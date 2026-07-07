<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\Plugin\PluginRepositoryInterface;

class PluginController extends ApiController
{
    private PluginRepositoryInterface $pluginRepository;

    public function __construct(
        PluginRepositoryInterface $pluginRepository,
    )
    {
        parent::__construct();
        $this->pluginRepository = $pluginRepository;
    }

    /**
     * Get all currencies.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $plugins = $this->pluginRepository->findAll();
        return $this->renderResponse($plugins);
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
        $plugin = $this->pluginRepository->find((int)$id);
        if(!$plugin){
            return $this->renderError(404, 'Plugin not found');
        }
        return $this->renderResponse($plugin->data);
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
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $plugin = $this->pluginRepository->create($data);
        return $this->renderResponse($plugin->data);
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
                'name' => 'string|nullable',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $existingPlugin = $this->pluginRepository->find((int)$id);
        if (!$existingPlugin) {
            return $this->renderError(404, 'Plugin not found');
        }


        $plugin = $this->pluginRepository->update((int) $id, $data);
        if (!$plugin) {
            return $this->renderError(500, 'Failed to update plugin');
        }
        
        return $this->renderResponse($plugin->data);
    }

    /**
     * Delete a plugin.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function delete(Request $request, $id): Response
    {
        $this->pluginRepository->delete((int) $id);
        return $this->renderResponse(['message' => 'Plugin deleted successfully']);
    }
} 