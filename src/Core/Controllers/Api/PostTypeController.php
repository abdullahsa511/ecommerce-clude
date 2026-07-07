<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\Post\PostTypeRepositoryInterface;
use App\Core\Repositories\Media\MediaRepositoryInterface;

class PostTypeController extends ApiController
{
    private PostTypeRepositoryInterface $postTypeRepository;
    private MediaRepositoryInterface $mediaRepository;
    public function __construct(
        PostTypeRepositoryInterface $postTypeRepository,
        MediaRepositoryInterface $mediaRepository,
    )
    {
        parent::__construct();
        $this->postTypeRepository = $postTypeRepository;
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * Get all post types with optional filtering
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $postTypes = $this->postTypeRepository->findAll();
        return $this->renderResponse($postTypes);
    }

    /**
     * Get a single post type by ID
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function show(Request $request, $id): Response
    {

        $postType = $this->postTypeRepository->find((int)$id);
        if(!$postType){
            return $this->renderError(404, 'Post type not found');
        }
        $data = $postType->data;
        if(!$data->image){
            $data->image = [];
        }else{
            $data->image = [
                [
                    'post_type_image_id' => $id,
                    'image' => $data->image,
                    'objectURL' => $data->image ?? '',
                    'size' => 256,
                    'type' => 'image/jpeg',
                    'status' => [
                        'name' => 'Uploaded',
                        'severity' => 'success'
                    ],
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ];
        }

        return $this->renderResponse($data);
    }

    /**
     * Create a new post type
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        try {
            $data = $request->validate([
                'name' => 'required|string',
                'type' => 'required|string',
                'plural' => 'required|string',
                'icon' => 'required|string',
                'site_id' => 'required|integer',
                'source' => 'string|nullable',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $postType = $this->postTypeRepository->create($data);
        return $this->renderResponse($postType->data);
    }

    /**
     * Update an existing post type
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
                'type' => 'string|required',
                'plural' => 'string|nullable',
                'icon' => 'string|nullable',
                'site_id' => 'integer|nullable',
                'source' => 'string|nullable',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $existingPostType = $this->postTypeRepository->find((int)$id);
        if (!$existingPostType) {
            return $this->renderError(404, 'Post type not found');
        }
        unset($data['image']);
        $postType = $this->postTypeRepository->update((int)$id, $data);
        if (!$postType) {
            return $this->renderError(500, 'Failed to update post type');
        }
        
        return $this->renderResponse($postType->data);
    }

    /**
     * Delete a post type
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function delete(Request $request, $id): Response
    {
        $this->postTypeRepository->delete((int) $id);
        return $this->renderResponse(['message' => 'Post type deleted successfully']);
    }

    // importPostTypes
    public function importPostTypes(Request $request): Response
    {
        $csv_file = $request->file('csv_file');
        
        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';
        
        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->postTypeRepository->importPostTypes($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }

    public function upload(Request $request, int $post_type_id): Response
    {
        // Set default size
        $size = [
            'width' => 400,
            'height' => 420,
        ];

        $folder = 'media/post-types/';
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

            $this->postTypeRepository->updatePostTypeImage($result['files'], $post_type_id);
            return $this->renderResponse($result);
        }

        return $this->renderError(422, 'No files uploaded');
    }

    // delete vendor image
    public function deleteImage(Request $request, int $post_type_id): Response
    {
        $deleted = $this->postTypeRepository->deletePostTypeImage($post_type_id);
        return $this->renderResponse(['deleted' => $deleted]);
    }
    
}
