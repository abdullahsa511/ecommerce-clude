<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\Page\PageRepositoryInterface;
use App\Core\Models\Post\PostData;
use App\Core\Models\Post\PostResponse;
use App\Core\Repositories\Page\PageStatusRepositoryInterface;
use App\Core\Repositories\Media\MediaRepositoryInterface;

class PageController extends ApiController
{
    private PageRepositoryInterface $pageRepository;
    private PageStatusRepositoryInterface $pageStatusRepository;
    private MediaRepositoryInterface $mediaRepository;
    public function __construct(
        PageRepositoryInterface $pageRepository,
        PageStatusRepositoryInterface $pageStatusRepository,
        MediaRepositoryInterface $mediaRepository
    ) {
        parent::__construct();
        $this->pageRepository = $pageRepository;
        $this->pageStatusRepository = $pageStatusRepository;
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * Get all posts.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $pages = $this->pageRepository->findAll();
        return $this->renderResponse($pages);
    }

    /**
     * Get a post by ID.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function show(Request $request, $id): Response
    {
        $page = $this->pageRepository->showPage((int)$id);
        if (!$page) {
            return $this->renderError(404, 'Page not found');
        }
        $response = new PostResponse($page->data);
        return $this->renderResponse($response);
    }

    /**
     * Create a new post.
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        try {
            $page = $request->input('page');
            $content = $page['postContent'];
            $data = [
                'name' => $content['name'],
                'slug' => $content['slug'],
            ];
            $request->validate([
                'name' => 'required|string',
                'slug' => 'required|string',
            ], $data);

            $pageData = new PostData($page);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }
        // $page = $this->pageRepository ->createPage($pageData);
        $page = $this->pageRepository->insertPageData($page);
        if (!$page) {
            return $this->renderError(500, 'Failed to create page');
        }
        $page = new PostResponse($page->data);
        return $this->renderResponse($page);
    }

    /**
     * Update a post.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id): Response
    {
        try {
            $page = $request->input('page');
            $content = $page['postContent'];
            $data = [
                'title' => $page['title'],
                'name' => $content['name'],
                'slug' => $content['slug'],
            ];
            $request->validate([
                'title' => 'required|string',
                'name' => 'required|string',
                'slug' => 'required|string',
            ], $data);
            $pageData = new PostData($page);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $page = $this->pageRepository->updatePageData($page);
        // $page = $this->pageRepository->updatePage($pageData);
        if (!$page) {
            return $this->renderError(500, 'Failed to update page');
        }
        $page = new PostResponse($page->data);
        return $this->renderResponse($page);
    }

    /**
     * Delete a post.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function delete(Request $request, $id): Response
    {
        $deleted = $this->pageRepository->deletePage((int) $id);
        return $this->renderResponse(['message' => 'Page deleted successfully']);
    }


    public function getStatuses(Request $request): Response
    {
        $statuses = $this->pageStatusRepository->findAll();
        return $this->renderResponse($statuses);
    }

    // images upload.. 
    public function upload(Request $request, int $page_id): Response
    {
        try {
            $property = $request->input('property');
            if (empty($property)) {
                return $this->renderError(400, 'Missing property parameter');
            }

            $propertyConfig = [
                'image' => ['folder' => 'image', 'size' => ['width' => 1600, 'height' => 657]],
                'image_thumb' => ['folder' => 'thumbnail', 'size' => ['width' => 436, 'height' => 503]],
                'image_banner' => ['folder' => 'banner-image', 'size' => ['width' => 1600, 'height' => 657]],
                'feature_image' => ['folder' => 'featured', 'size' => ['width' => 537, 'height' => 501]],
                'feature_image_thumb' => ['folder' => 'featured-thumbnail', 'size' => ['width' => 537, 'height' => 501]],
                'main_image_one' => ['folder' => 'main-image-one', 'size' => ['width' => 1346, 'height' => 608]],
                'main_image_two' => ['folder' => 'main-image-two', 'size' => ['width' => 670, 'height' => 686]],
            ];

            $config = $propertyConfig[$property] ?? ['folder' => 'test', 'size' => ['width' => 945, 'height' => 630]];
            $uploadFolder = 'media/Pages/' . $config['folder'];

            // Handle files
            if ($request->files() || isset($_FILES['files'])) {
                $files = $request->files() ?? $_FILES['files'];

                if (!count($files)) {
                    return $this->renderError(422, 'No files uploaded');
                }

                $uploadData = ['files' => $files, 'upload_dir' => $uploadFolder];
                $result = $this->mediaRepository->upload($uploadData, $config['size'], $uploadFolder);

                if (empty($result) || empty($result['files'])) {
                    return $this->renderError(500, 'Media upload failed');
                }

                $this->pageRepository->updatePageImges($result['files'], $property, $page_id);

                return $this->renderResponse($result);
            }
            return $this->renderError(422, 'No files uploaded');
        } catch (\Throwable $e) {
            return $this->renderError(500, 'Upload failed: ' . $e->getMessage());
        }
    }

    public function deleteByPath(Request $request): Response
    {
        $path = $request->input('path');
        $property = $request->input('property');
        $pageId = (int) $request->input('page_id');

        // Validate input
        if (empty($path) || empty($property) || empty($pageId)) {
            return $this->renderError(422, 'Path, property, and page_id are required.');
        }

        try {
            // Delete the media file (if exists) from media table / storage
            $deletedFromMedia = $this->mediaRepository->deleteMediaByPath($path);

            // Update project record and remove file from filesystem
            $deletedFromProject = $this->pageRepository->deletePageImage($path, $property, $pageId);

            if ($deletedFromProject) {
                return $this->renderResponse([
                    'message' => 'Media deleted successfully',
                    'path' => $path,
                    'property' => $property,
                    'page_id' => $pageId
                ]);
            }

            return $this->renderError(404, 'Media or project record not found.');
        } catch (Exception $e) {
            return $this->renderError(500, 'An error occurred while deleting media: ' . $e->getMessage());
        }
    }

    public function importPages(Request $request): Response
    {
        $csv_file = $request->file('csv_file');
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';
        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        $result = $this->pageRepository->importPages($csv_file_path);
        return $this->renderResponse(['success' => $result]);
    }
}
