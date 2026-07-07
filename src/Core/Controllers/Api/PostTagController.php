<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Http\ApiController;
use App\Core\Repositories\Post\PostTagRepositoryInterface;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Exceptions\ValidationException;
use App\Core\Models\Post\PostTag;
use App\Core\Repositories\Media\MediaRepositoryInterface;

class PostTagController extends ApiController
{
    private PostTagRepositoryInterface $postTagRepository;
    private MediaRepositoryInterface $mediaRepository;


    public function __construct(
        PostTagRepositoryInterface $postTagRepository,
        MediaRepositoryInterface $mediaRepository,

    ) {
        parent::__construct();
        $this->postTagRepository = $postTagRepository;
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * Get all post tags.
     *
     * @param Request $request
     * @return Response
     */
    public function getAllPostTags(Request $request): Response
    {
        // $postTags = $this->postTagRepository->findPostTags();
        $data = $this->postTagRepository->getAllPostTags();
        return $this->renderResponse($data);
    }

    /**
     * Get a post tag by ID.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function show(Request $request, $id): Response
    {
        $postTag = $this->postTagRepository->find((int)$id);
        if (!$postTag) {
            return $this->renderError(404, 'Post tag not found');
        }

        return $this->renderResponse($postTag->data);
    }

    /**
     * Create a new post tag.
     *
     * @param Request $request
     * @return Response
     */
    // public function create(Request $request): Response
    // {
    //     $data = $request->validate([
    //         'name' => 'required|string',
    //         'slug' => 'required|string',
    //         'description' => 'string|nullable',
    //         'status' => 'required|integer',
    //         'thumbnail' => 'string|nullable',
    //         'image' => 'string|nullable',
    //     ]);
    //     if($data instanceof Response){
    //         return $data;
    //     }
    //     // Create the post tag
    //     $postTag = $this->postTagRepository->create($data);
    //     if (!$postTag) {
    //         return $this->renderError(500, 'Failed to create post tag');
    //     }
    //     return $this->renderResponse($postTag->data);

    // ============ back up
    // $data = $request->all();
    // try {
    //     // Debug::dd($data, true);
    //     if ($data instanceof Response) {
    //         return $data;
    //     }
    //     if (!isset($data['name']) || empty($data['name'])) {
    //         return $this->renderError(400, 'Name is required');
    //     }

    //     $existingData = $this->postTagRepository->findByName($data['name']);
    //     if ($existingData) {
    //         return $this->renderError(400, 'Post Tag name is already in use');
    //     }
    //     $this->postTagRepository->clearQuery();
    //     $postTag = $this->postTagRepository->add($data);
    //     return $this->renderResponse($postTag);
    // } catch (ValidationException $e) {
    //     return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
    // } catch (Exception $e) {
    //     return $this->renderError(500, $e->getMessage());
    // }
    // }

    public function create(Request $request): Response
    {
        try {
            $data = $request->all();
            if ($data instanceof Response) {
                return $data;
            }
            // $inputData = [
            //     'name' => $data['name'] ?? null,
            //     'slut' => $data['slut'] ?? null,
            //     'description' => $data['description'] ?? null,
            //     'status' => $data['status'] ?? null,
            //     'image' => $data['image'] ?? null,
            //     'post_id' => $data['post_id'] ?? null
            // ];

            // $request->validate([
            //     'name' => 'required|string',
            //     'slug' => 'required|string',
            //     'description' => 'string|nullable',
            //     'status' => 'required|integer',
            //     'image' => 'string|nullable',
            //     'post_id' => 'required|int',
            // ], $inputData);


            $validData = $request->validate([
                'name' => 'required|string',
                'slug' => 'required|string',
                'description' => 'string|nullable',
                'status' => 'required|integer',
                'image' => 'string|nullable',
                'post_id' => 'required|int',
            ]);




            $existingData = $this->postTagRepository->findByName($data['name']);
            if ($existingData) {
                return $this->renderError(400, 'Post Tag name is already in use');
            }
            // if (!isset($data['name']) || empty($data['name'])) {
            //     return $this->renderError(400, 'Name is required');
            // }
            // $postTag = $this->postTagRepository->add($data);
            $postTag = $this->postTagRepository->add($validData);
            return $this->renderResponse($postTag);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    /**
     * Update a post tag.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id): Response
    {
        $data = $request->all();
        if ($data instanceof Response) {
            return $data;
        }
        $inputData = [
            'name' => $data['name'] ?? null,
            'slug' => $data['slug'] ?? null,
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? null,
            'image' => $data['image'] ?? null,
            'post_id' => $data['post_id'] ?? null
        ];
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|notExists:post_tag,name,post_tag_id,'.(int) $id,
                'slug' => 'required|string|notExists:post_tag,slug,post_tag_id,'.(int) $id,
                'description' => 'string|nullable',
                'status' => 'required|integer',
                'image' => 'string|nullable',
                'post_id' => 'required|int',
                ], $inputData);
            } catch (ValidationException $e) {
                return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
            }
            // $this->postTagRepository->clearQuery();

            $updateData = $validatedData;
            

            
            $existingData = $this->postTagRepository->getPostTagById($id);
            if (!$existingData) {
                return $this->renderError(400, 'Posttag with the id '.$id.' not found.');
            }
           

            $postTag = $this->postTagRepository->updatePostTags($id, $updateData);
            return $this->renderResponse($postTag);
       
    }

    public function upload(Request $request, int $id): Response
    {
        $property = $request->input('property');

        // Override size based on property
        if ($property === 'image') {
            $size = [
                'width' => 1600,
                'height' => 657,
            ];
            $folder = 'image';
        }

        if ($request->files() || isset($_FILES['files'])) {
            $files = $request->files() ?? $_FILES['files'];

            if (!count($files)) {
                return $this->renderError(422, 'No files uploaded');
            }
            $data = [
                'files' => $files,
                'upload_dir' => 'media/post-tag/' . $folder
            ];

            $result = $this->mediaRepository->upload($data, $size, 'media/post-tag/' . $folder);
            if (!$result) {
                return $this->renderError(500, 'Failed to upload media');
            }
            if (isset($result['files'])) {
                $postTag = $this->postTagRepository->get($id);
                if ($postTag) {
                    if ($property === 'image') {
                        $this->postTagRepository->insertPostTagImages($result['files'], $id);

                        // $posttagData = new PostTag(['post_tag_id' => $id, $property => $result['files']]);
                        // $this->postTagRepository->updatePostTag($posttagData);
                    }
                }
            }
            return $this->renderResponse($result);
        }

        return $this->renderError(422, 'No files uploaded');
    }



    /**
     * Delete a post tag.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function delete(Request $request, $id): Response
    {
        // Delete the post tag (this will cascade to related images if foreign key constraints are set up)
        // $this->postTagRepository->delete((int) $id);
        // return $this->renderResponse(['message' => 'Post tag deleted successfully']);
        try {
            $postTag = $this->postTagRepository->deletePostTag((int) $id);
            return $this->renderResponse($postTag);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }


    public function importPostTags(Request $request): Response
    {
        $csv_file = $request->file('csv_file');
        
        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';
        
        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->postTagRepository->importPostTags($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }
}
