<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\Model\ModelRepositoryInterface;

class ModelController extends ApiController
{
    private ModelRepositoryInterface $modelRepository;

    public function __construct(
        ModelRepositoryInterface $modelRepository,
    )
    {
        parent::__construct();
        $this->modelRepository = $modelRepository;
    }

    /**
     * Create a new client.
     *
     * @param Request $request
     * @return Response
     */
    public function getModels(Request $request): Response
    {
        $models = $this->modelRepository->getModels();
        return $this->renderResponse($models);
    }

    public function getFields(Request $request): Response
    {
        $data = $request->validate([
            'name' => 'required|string',
        ]);
        $fields = $this->modelRepository->getFields($data['name']);
        return $this->renderResponse($fields);
    }
    
    /**
     * Get table columns for a model
     * 
     * @param Request $request
     * @return Response
     */
    public function getTableColumns(Request $request): Response
    {
        $data = $request->validate([
            'name' => 'required|string',
        ]);
        $columns = $this->modelRepository->getTableColumns($data['name']);
        return $this->renderResponse(['table' => $data['name'], 'columns' => $columns]);
    }

    /**
     * Get table columns for a model and its related models
     * 
     * @param Request $request
     * @return Response
     */
    public function getRelatedModelTableColumns(Request $request): Response
    {
        $data = $request->validate([
            'name' => 'required|string',
        ]);
        $result = $this->modelRepository->getRelatedModelTableColumns($data['name']);
        return $this->renderResponse($result);
    }

    /**
     * Get the primary related model for a given model name
     *
     * @param Request $request
     * @return Response
     */
    public function getRelatedModels(Request $request): Response
    {
        $data = $request->validate([
            'name' => 'required|string',
        ]);
        $result = $this->modelRepository->getRelatedModels($data['name']);
        return $this->renderResponse($result);
    }

    public function getJoinedTableColumns(Request $request): Response
    {
        $data = $request->validate([
            'name' => 'required|string',
            'related_model' => 'required|string',
        ]);
        $result = $this->modelRepository->getJoinedTableColumns($data['name'], $data['related_model']);
        return $this->renderResponse($result);
    }
}
