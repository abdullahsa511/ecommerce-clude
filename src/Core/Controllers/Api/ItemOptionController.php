<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Components\Site;
use App\Core\Exceptions\ValidationException;
use App\Core\Http\ApiController;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Models\Item\Item;
use App\Core\Models\Item\ItemData;
use App\Core\Models\Item\ItemResponse;
use App\Core\Repositories\Item\ItemOptionRepositoryInterface;
use Exception;
use League\Csv\Reader;
use App\Core\Repositories\Media\MediaRepositoryInterface;

class ItemOptionController extends ApiController
{
    private ItemOptionRepositoryInterface $itemOptionRepository;
    private MediaRepositoryInterface $mediaRepository;

    public function __construct(
        ItemOptionRepositoryInterface $itemOptionRepository,
        MediaRepositoryInterface $mediaRepository
    ) {
        parent::__construct();
        $this->itemOptionRepository = $itemOptionRepository;
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * Get all sites.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $items = $this->itemOptionRepository->getItemOptions();
        return $this->renderResponse($items);
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
        $item = $this->itemOptionRepository->getItemOptionById((int)$id);
        if (!$item) {
            return $this->renderError(404, 'Item not found');
        }
        return $this->renderResponse($item);
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
            $itemOption = $request->all();
            $request->validate([
                'item_id' => 'required|int',
                'product_variant_id' => 'required|int',
                'product_id' => 'required|int',
                'product_option_group_id' => 'required|int',
                'product_option_id' => 'required|int',
                'type_id' => 'required|int',
                'option_name' => 'required|string',
                'meta_description' => 'nullable|string',
                'price' => 'nullable|float',
                'sort_order' => 'nullable|int',
                'option_description' => 'nullable|string',
                'active_status' => 'nullable|int',
                'required' => 'nullable|int|max:1'
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $itemOption = $this->itemOptionRepository->createItemOption($itemOption);
        if (!$itemOption) {
            return $this->renderError(500, 'Failed to create item option');
        }
        return $this->renderResponse($itemOption);
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
        // return $this->renderError(500, 'Failed to update product');
        try {
            $itemOption = $request->all();
            $validateData = $request->validate([
                'item_id' => 'required|int',
                'product_variant_id' => 'required|int',
                'product_id' => 'required|int',
                'product_option_group_id' => 'required|int',
                'product_option_id' => 'required|int',
                'type_id' => 'required|int',
                'option_name' => 'required|string',
                'meta_description' => 'nullable|string',
                'price' => 'nullable|float',
                'sort_order' => 'nullable|int',
                'option_description' => 'nullable|string',
                'active_status' => 'nullable|int',
                'required' => 'nullable|int|max:1',
                'hex_color' => 'nullable|string',
            ], $itemOption);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $itemOption = $this->itemOptionRepository->updateItemOption($id, $validateData);

        if (!$itemOption) {
            return $this->renderError(500, 'Failed to update item option');
        }

        return $this->renderResponse($itemOption);
    }

    public function delete(Request $request, int $id): Response
    {
        $itemOption = $this->itemOptionRepository->deleteItemOption($id);

        // if (!$itemOption) {
        //     return $this->renderError(500, 'Failed to delete item optionaaa');
        // }
        return $this->renderResponse($itemOption);
    }
    
    public function deleteItemOptionGroup(Request $request): Response
    {
        $data = $request->all();
        $ids = [
            'item_id' => (int) $data['item_id'],
            'product_id' => (int) $data['product_id'],
            'product_variant_id' => (int) $data['product_variant_id'],
            'product_option_group_id' => (int) $data['product_option_group_id'],
        ];
        $itemOptionGroup = $this->itemOptionRepository->deleteItemOptionGroup($ids);

        // if (!$itemOption) {
        //     return $this->renderError(500, 'Failed to delete item optionaaa');
        // }
        return $this->renderResponse($itemOptionGroup);
    }

    public function importItemOptions(Request $request): Response
    {
        $csv_file = $request->file('csv_file');

        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';

        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->itemOptionRepository->importItemOptions($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }

    // 
    public function upload(Request $request, int $item_option_id): Response
    {
        // Set default size
        $size = [
            'width' => 400,
            'height' => 420,
        ];

        $folder = 'media/item-options/';
        if ($request->files() || isset($_FILES['files'])) {
            $files = $request->files() ?? $_FILES['files'];

            if (!count($files)) {
                return $this->renderError(422, 'No files uploaded');
            }
            $data = [
                'files' => $files,
                'upload_dir' => $request->input('upload_dir', $folder)
            ];

            $result = $this->mediaRepository->upload($data, $size, $folder);
            if (!$result) {
                return $this->renderError(500, 'Failed to upload media');
            }

            $this->itemOptionRepository->updateItemOptionImage($result['files'], $item_option_id);
            return $this->renderResponse($result);
        }

        return $this->renderError(422, 'No files uploaded');
    }

    // delete vendor image
    public function deleteImage(Request $request, int $item_option_id): Response
    {
        $deleted = $this->itemOptionRepository->deleteItemOptionImage($item_option_id);
        return $this->renderResponse(['deleted' => $deleted]);
    }
}
