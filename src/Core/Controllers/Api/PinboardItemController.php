<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\Media\MediaRepositoryInterface;
use App\Core\Repositories\Pinboard\PinboardItemRepositoryInterface;
use App\Core\Models\Pinboard\PinboardResponse;
use App\Core\Models\Pinboard\PinboardItemData;

class PinboardItemController extends ApiController
{
    private PinboardItemRepositoryInterface $pinboardItemRepository;
    private MediaRepositoryInterface $mediaRepository;

    public function __construct(
        PinboardItemRepositoryInterface $pinboardItemRepository,
        MediaRepositoryInterface $mediaRepository
    )
    {
        parent::__construct();
        $this->pinboardItemRepository = $pinboardItemRepository;
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * Get all pinboards
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $pinboards = $this->pinboardItemRepository->findAll();
        return $this->renderResponse($pinboards);
    }

    /**
     * Get pinboard by ID
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function show(Request $request, $id): Response
    {
        $pinboard = $this->pinboardItemRepository->showPinboardItem((int)$id);
        if(!$pinboard){
            return $this->renderError(404, 'Pinboard not found');
        }
        $response = new PinboardResponse($pinboard->data);
        return $this->renderResponse($response);
    }

    /**
     * Create a new pinboard
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        try {
            $pinboardItems = $request->input('pinboardItem');

            // Check if pinboardItems is null or empty
            if (empty($pinboardItems)) {
                return $this->renderError(422, 'Pinboard items data is required');
            }

            // Ensure pinboardItems is an array
            if (!is_array($pinboardItems)) {
                return $this->renderError(422, 'Pinboard items must be an array');
            }

            $pinboardItemResult = $this->pinboardItemRepository->createPinboardItem($pinboardItems);
            if(!$pinboardItemResult){
                return $this->renderError(500, 'Failed to create pinboard');
            }
            
            return $this->renderResponse([
                'message' => 'Pinboard items created successfully',
                'data' => $pinboardItemResult
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        } catch (\Exception $e) {
            return $this->renderError(500, 'Failed to create pinboard items: ' . $e->getMessage());
        }
    }

    /**
     * Update a pinboard
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id): Response
    {
        try {
            $pinboard = $request->input('pinboard_item');
            $pinboardItemData = new PinboardItemData($pinboard);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $pinboard = $this->pinboardItemRepository->updatePinboardItem($pinboardItemData);
        if(!$pinboard){
            return $this->renderError(500, 'Failed to update pinboard');
        }
        $pinboard = new PinboardResponse($pinboard->data);
        return $this->renderResponse($pinboard);
    }

    /**
     * Delete a pinboard
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function delete(Request $request, $id): Response
    {
        // $pinboard = $this->pinboardItemRepository->showPinboardItem((int) $id);
        // if (!$pinboard) {
        //     return $this->renderError(404, 'Pinboard not found');
        // }

        try {
            $this->pinboardItemRepository->deleteByPinboardItemId((int) $id);
            return $this->renderResponse(['status' => true, 'message' => 'Pinboard deleted successfully']);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Failed to delete pinboard: ' . $e->getMessage());
        }
    }

    public function productList(Request $request): Response
    {
        $productList = $this->pinboardItemRepository->productList($request->input('search'));
        return $this->renderResponse($productList);
    }
    public function getPinboard(Request $request, $user_id): Response
    {
        $pinboardIdRaw = $request->input('pinboard_id');
        $pinboardId = ($pinboardIdRaw !== null && $pinboardIdRaw !== '') ? (int) $pinboardIdRaw : null;
        $pinboardItems = $this->pinboardItemRepository->getPinboard((int) $user_id, $pinboardId);
        if (!$pinboardItems) {
            return $this->renderResponse(['data' => []]);
        }
        return $this->renderResponse($pinboardItems);
    }
    public function getProjectItemsByPinboardId(Request $request, $user_id, $pinboard_id): Response
    {
        $projectListPage = $request->query('project_list') ? true : false;
        if ($projectListPage === false) {
            $result = $this->pinboardItemRepository->updateProjectPinboardId((int) $pinboard_id, (int) $user_id);
            if (!$result) {
                return $this->renderError(500, 'Failed to update project pinboard id: ' . $result['message']);
            }

            $pinboardItems = $this->pinboardItemRepository->getPinboard((int) $user_id, (int) $pinboard_id);
            if (!$pinboardItems) {
                return $this->renderResponse(['data' => []]);
            }
        } else {
            $pinboardItems = $this->pinboardItemRepository->getPinboardWithAllStatus((int) $user_id, (int) $pinboard_id);
            if (!$pinboardItems) {
                return $this->renderResponse(['data' => []]);
            }
        }
        return $this->renderResponse($pinboardItems);
    }
    public function reorderPinboardItems(Request $request): Response
    {
        $pinboardItems = $request->all();
        try {
            $pinboardItemResult = $this->pinboardItemRepository->reorderPinboardItems($pinboardItems);
            return $this->renderResponse(['success' => true, 'data' => $pinboardItemResult]);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Failed to reorder pinboard items: ' . $e->getMessage());
        }
    }

    public function addToPinboard(Request $request): Response
    {
        $pinboardItem = $request->all();
        try {
            $pinboardItemResult = $this->pinboardItemRepository->addToPinboard($pinboardItem);
            return $this->renderResponse(['success' => true, 'data' => $pinboardItemResult]);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Failed to add to pinboard: ' . $e->getMessage());
        }
    }

    public function addToPinboardItemImages(Request $request): Response
    {
        $pinboardItem = $request->all();

        // $size = [
        //     'width' => 400,
        //     'height' => 420,
        // ];
        // $folder = 'media/pinboard/';
        // if ($request->files() || isset($_FILES['files'])) {
        //     $files = $request->files() ?? $_FILES['files'];

        //     if (!count($files)) {
        //         return $this->renderError(422, 'No files uploaded');
        //     }
        //     $data = [
        //         'files' => $files,
        //         'upload_dir' => $request->input('upload_dir', $folder)
        //     ];

        //     $result = $this->mediaRepository->upload($data, $size, $folder);
        //     if (!$result || empty($result['files'][0]['objectURL'])) {
        //         return $this->renderError(500, 'Failed to upload media');
        //     }

        //     $pinboardItem['photo'] = $result['files'][0]['objectURL'];
        // }

        try {
            $pinboardItemResult = $this->pinboardItemRepository->addToPinboardItemImages($pinboardItem);
            return $this->renderResponse(['success' => true, 'data' => $pinboardItemResult]);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Failed to add to pinboard: ' . $e->getMessage());
        }
    }

    public function addToPinboardProductConfigurator(Request $request): Response
    {
        $pinboardItem = $request->all();
        try {
            $pinboardItemResult = $this->pinboardItemRepository->addToPinboardProductConfigurator($pinboardItem);
            return $this->renderResponse(['success' => true, 'data' => $pinboardItemResult]);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Failed to add to pinboard: ' . $e->getMessage());
        }
    }
    public function updateCommentDescription(Request $request): Response
    {
        $data = $request->all();
        try {
            $result = $this->pinboardItemRepository->updateCommentDescription($data);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Failed to update comment description: ' . $e->getMessage());
        }
    }
} 