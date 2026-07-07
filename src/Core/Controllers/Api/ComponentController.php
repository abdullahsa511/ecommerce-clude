<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Models\Component\ComponentItem;
use App\Core\Models\Component\ComponentItemData;
use App\Core\Repositories\Component\ComponentRepositoryInterface;
use App\Core\Repositories\Component\ComponentItemRepositoryInterface;
use App\Core\Repositories\Media\MediaRepositoryInterface;

use function App\Core\System\utils\htmlToPlainText;

class ComponentController extends ApiController
{
    private ComponentRepositoryInterface $componentRepository;
    private ComponentItemRepositoryInterface $componentItemRepository;
    private MediaRepositoryInterface $mediaRepository;

    public function __construct(
        ComponentRepositoryInterface $componentRepository,
        ComponentItemRepositoryInterface $componentItemRepository,
        MediaRepositoryInterface $mediaRepository,
    )
    {
        parent::__construct();
        $this->componentRepository = $componentRepository;
        $this->componentItemRepository = $componentItemRepository;
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * Get all components
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $components = $this->componentRepository->findAll();
        return $this->renderResponse($components);
    }

    /**
     * Show a component by ID
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function show(Request $request, $id): Response
    {
        $component = $this->componentRepository->getComponentById((int)$id);
        if(!$component){
            return $this->renderError(404, 'Component not found');
        }
        return $this->renderResponse($component);
    }

    /**
     * Create a new component
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        try {
            $data = $request->validate([
                'name' => 'required|string',
                'section_title' => 'nullable|string',
                'section_subtitle' => 'nullable|string',
                'section_link' => 'nullable|string',
                'title' => 'nullable|string',
                'subtitle' => 'nullable|string',
                'description' => 'nullable|string',
                'image' => 'nullable|string',
                'mobile_banner' => 'nullable|string',
                'images' => 'nullable|string',
                'links' => 'nullable|string',
                'buttons' => 'nullable|string',
                'template' => 'nullable|string',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $component = $this->componentRepository->createComponent($data);
        return $this->renderResponse($component->data);
    }

    /**
     * Update a component
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
                'section_title' => 'string|nullable',
                'section_subtitle' => 'string|nullable',
                'section_link' => 'string|nullable',
                'title' => 'string|nullable',
                'subtitle' => 'string|nullable',
                'description' => 'string|nullable',
                'image' => 'string|nullable',
                'mobile_banner' => 'string|nullable',
                'images' => 'string|nullable',
                'links' => 'string|nullable',
                'buttons' => 'string|nullable',
                'template' => 'string|nullable',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $existingComponent = $this->componentRepository->find((int)$id);
        if (!$existingComponent) {
            return $this->renderError(404, 'Component not found');
        }
        if(isset($data['buttons']) && is_array($data['buttons'])){
            $data['buttons'] = json_encode($data['buttons']);
        }
        if(isset($data['links']) && is_array($data['links'])){
            $data['links'] = json_encode($data['links']);
        }
        if(isset($data['images']) && is_array($data['images'])){
            $data['images'] = json_encode($data['images']);
        }
        if(isset($data['image']) && is_array($data['image'])){
            $data['image'] = json_encode($data['image']);
        }
        if(isset($data['mobile_banner']) && is_array($data['mobile_banner'])){
            $data['mobile_banner'] = json_encode($data['mobile_banner']);
        }
        $data['description'] = $data['description'];

        $component = $this->componentRepository->update((int) $id, $data);
        if (!$component) {
            return $this->renderError(500, 'Failed to update component');
        }
        
        return $this->renderResponse($component->data);
    }

    /**
     * Delete a component
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function delete(Request $request, $id): Response
    {
        $this->componentRepository->delete((int) $id);
        return $this->renderResponse(['message' => 'Component deleted successfully']);
    }

    public function indexItem(Request $request): Response
    {
        $items = $this->componentItemRepository->findAll();
        return $this->renderResponse($items);

    }

    public function showItem(Request $request, $id): Response
    {
        $item = $this->componentItemRepository->find((int) $id);
        return $this->renderResponse($item);
    }

    public function addComponentItem(Request $request): Response
    {
         $items = [];
         $error = [];
        try {
            $data = $request->validate([
               'component_id' => 'required|integer',
               'fields' => 'required|array',
               'related_models' => 'array|nullable',
               'description' => 'string|nullable',
               'is_featured' => 'boolean|nullable',
               'is_recent' => 'boolean|nullable',
               'model' => 'string|nullable',
               'property_name' => 'string|required',
               'title' => 'string|nullable',
               'subtitle' => 'string|nullable',
               'link_text' => 'string|nullable',
               'item_count' => 'integer|nullable',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }
        $item = new ComponentItemData($data, true); 
        $createdItem = $this->componentItemRepository->addComponentItem($item->toArray());
        
        // return $this->renderResponse(['item' => $createdItem, 'error' => $error]);
        return $this->renderResponse($createdItem[0]);
    }

    public function updateComponentItem(Request $request, $id): Response
    {
        // return $this->renderResponse("You hit it");
        try {
            $data = $request->validate([
                'component_id' => 'required|integer',
                'fields' => 'required|array',
                'related_models' => 'array|nullable',
                'description' => 'string|nullable',
                'is_featured' => 'boolean|nullable',
                'is_recent' => 'boolean|nullable',
                'item_count' => 'integer|nullable',
                'model' => 'string|nullable',
                'property_name' => 'string|required',
                'title' => 'string|nullable',
                'subtitle' => 'string|nullable',
                'link_text' => 'string|nullable',
             ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        try {
            $item = new ComponentItemData($data, true); 
            $updatedItem = $this->componentItemRepository->updateComponentItems($item->toArray(), (int)$id);
            return $this->renderResponse($updatedItem[0]);
        } catch (\Exception $e) {
            return $this->renderError(404, $e->getMessage());
        }
    }

    /**
     * Delete a component item
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function deleteItem(Request $request, $id): Response
    {
        $this->componentItemRepository->delete((int) $id);
        return $this->renderResponse(['message' => 'Component Item deleted successfully']);
    }


    //Create a comprehensive model list with their assoicated join models - Done

    //Join models must have the join statement [jointable=> 'product, join => {'model' => 'product.product_id', 'operator' => '=', 'mainmodel' => 'mainmodel.mainmodel_id'}] - Done

    //On select the main model return associated join models with statements api (return static array) - Done

    // Update get fields api and prefix the tablename. - Done

    // Merge associate model fields with main model fields - Done

    // Now storing the data in the database, we need to store the data in the database in the correct format - Done

    // Get data from the frontend and send it to the model to make in syncronized. - Done

    // After syncronising the data, we need to pass this data to the repository to store. - Done

    // In repository, check model is null or not. - Done

    // If model is null, then store the data and fields column will be an object having key and values. - Done

    // If model is not null, then store the data and fields column will be an array having key/fields. - Done

    // Check there is any items are available or not for that specific component. if available then send items data for that component to show. - Done

    // User can delete any item or add or edit any item then make a function for that to haldle those things and send back to the new response.

    

    public function updateWayPoints(Request $request): Response
    {
        $data = $request->all();
        $this->componentRepository->updateWayPoints($data);
        return $this->renderResponse(['message' => 'Way points updated successfully']);
    }

    public function upload(Request $request, $component_id): Response
    {
        $property = $request->input('property');
        if($request->files() || isset($_FILES['files'])){
          $files = $request->files() ?? $_FILES['files'];
          
          if(!count($files)){
            return $this->renderError(422, 'No files uploaded');
          }
          $uploadDir = 'media/Components';
          $data = [
            'files' => $files,
            // 'upload_dir' => $request->input('upload_dir', 'media/uploads' . date('Y/m')) 
            'upload_dir' => $uploadDir
          ];
          $size = json_decode($request->input('size', "[]"), true);

          $result = $this->mediaRepository->upload($data, $size, 'media/Components');
          if($component_id > 0 && isset($result['files'])){
            $this->componentRepository->uploadImage($result['files'], $property, $component_id);
          }
          if(!$result){
            return $this->renderError(500, 'Failed to upload media');
          }
          return $this->renderResponse($result);
        }

        return $this->renderError(422, 'No files uploaded');
    }

    public function deleteByPath(Request $request, $component_id): Response
    {
        $objectUrl = $request->input('path');
        $property = $request->input('property');
        if (!$objectUrl) {
            return $this->renderError(422, 'Path is required');
        }
        $this->componentRepository->deleteImage($objectUrl, $property, $component_id);
        return $this->renderResponse(['message' => 'Media deleted successfully']);
    }


}
