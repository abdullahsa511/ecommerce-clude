<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Http\ApiController;
use App\Core\Repositories\Post\PostTagRepositoryInterface;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Exceptions\ValidationException;

class TagController extends ApiController
{
    private PostTagRepositoryInterface $postTagRepository;

    public function __construct(
        PostTagRepositoryInterface $postTagRepository,
    )
    {
        parent::__construct();
        $this->postTagRepository = $postTagRepository;
    }

    /**
     * Get all post tags.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $postTags = $this->postTagRepository->findAll();
        return $this->renderResponse($postTags);
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
        if(!$postTag){
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
    public function create(Request $request): Response
    {
        $data = $request->validate([
            'post_id' => 'integer|nullable',
            'name' => 'required|string',
            'slug' => 'required|string',
            'description' => 'required|string',
            'image' => 'nullable|string',
            'status' => 'required|integer',
        ]);

        if($data instanceof Response){
            return $data;
        }

        $postTag = $this->postTagRepository->create($data);
        return $this->renderResponse($postTag->data);
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
        $data = $request->validate([
            'post_id' => 'integer|nullable',
            'name' => 'string|nullable',
            'slug' => 'string|nullable',
            'description' => 'string|nullable',
            'image' => 'string|nullable',
            'status' => 'integer|nullable',
        ]);

        if($data instanceof Response){
            return $data;
        }

        $existingPostTag = $this->postTagRepository->find((int)$id);
        if (!$existingPostTag) {
            return $this->renderError(404, 'Post tag not found');
        }

        $postTag = $this->postTagRepository->update((int)$id, $data);
        if (!$postTag) {
            return $this->renderError(500, 'Failed to update post tag');
        }

        return $this->renderResponse($postTag->data);
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
        $existingPostTag = $this->postTagRepository->find((int)$id);
        if (!$existingPostTag) {
            return $this->renderError(404, 'Post tag not found');
        }

        if (!$this->postTagRepository->delete((int)$id)) {
            return $this->renderError(500, 'Failed to delete post tag');
        }

        return $this->renderResponse(['message' => 'Post tag deleted successfully']);
    }
} 