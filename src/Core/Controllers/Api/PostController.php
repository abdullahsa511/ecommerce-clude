<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\Post\PostRepositoryInterface;
use App\Core\Models\Post\PostData;
use App\Core\Models\Post\PostResponse;
use App\Core\Repositories\Media\MediaRepositoryInterface;
use App\Core\Repositories\Post\PostStatusRepositoryInterface;
use App\Core\Utilities\Debug;
use League\Csv\Reader;

class PostController extends ApiController
{
    private PostRepositoryInterface $postRepository;
    private PostStatusRepositoryInterface $postStatusRepository;
    private MediaRepositoryInterface $mediaRepository;
    public function __construct(
        PostRepositoryInterface $postRepository,
        PostStatusRepositoryInterface $postStatusRepository,
        MediaRepositoryInterface $mediaRepository
    ) {
        parent::__construct();
        $this->postRepository = $postRepository;
        $this->postStatusRepository = $postStatusRepository;
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
        $filters = array_filter([
            'type' => $request->query('type'),
            'status' => $request->query('status'),
            'search' => $request->query('search'),
            'site_id' => $request->query('site_id'),
            'taxonomy_item_id' => $request->query('taxonomy_item_id'),
        ], static fn ($value) => $value !== null && $value !== '');

        $start = (int) $request->query('start', 0);
        $limit = (int) $request->query('limit', 200);

        $posts = $this->postRepository->getAll($start, $limit, $filters);
        return $this->renderResponse($posts);
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
        $post = $this->postRepository->showPost((int)$id);
        if (!$post) {
            return $this->renderError(404, 'Post not found');
        }
        $response = new PostResponse($post->data);
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
            $post = $request->input('post');
            $content = $post['postContent'];
            $request->validate([
                'name' => 'required|string',
                'slug' => 'required|string',
            ], $content);
            $postData = new PostData($post);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $post = $this->postRepository->createPost($postData);
        if (!$post) {
            return $this->renderError(500, 'Failed to create post');
        }
        $post = new PostResponse($post->data);
        return $this->renderResponse($post);
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
            $post = $request->input('post');
            if(!$post) return $this->renderError(400, 'Post not found');
            if(isset($post['status_id'])){
                $status = $this->postStatusRepository->findOneBy(['post_status_id' => (int)$post['status_id']]);
                if(!$status) return $this->renderError(400, 'Status not found');
                $post['status'] = $status->name;
            }

            $content = $post['postContent'];
            $request->validate([
                'name' => 'required|string',
                'slug' => 'required|string',
            ], $content);
            $postData = new PostData($post);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $post = $this->postRepository->updatePost($postData);
        if (!$post) {
            return $this->renderError(500, 'Failed to update post');
        }
        $post = new PostResponse($post->data);
        return $this->renderResponse($post);
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
        $this->postRepository->delete((int) $id);
        return $this->renderResponse(['message' => 'Post deleted successfully']);
    }


    public function getStatuses(Request $request): Response
    {
        $statuses = $this->postStatusRepository->findAll();
        return $this->renderResponse($statuses);
    }
    public function upload(Request $request, int $post_id): Response
    {
        $property = $request->input('property');

        // Set default size
        $size = [
            // 'width' => 945,
            // 'height' => 630,
        ];
        $folder = 'Main';
        $is_banner = false;
        // Override size based on property
        if ($property === 'image_banner') {
            // $size = [
            //     'width' => 1600,
            //     'height' => 657,
            // ];
            $folder = 'Banner';
            $is_banner = true;
        } elseif ($property === 'main_image_one') {
            // $size = [
            //     'width' => 1341,
            //     'height' => 608,
            // ];
        } elseif ($property === 'main_image_two') {
            // $size = [
            //     'width' => 670,
            //     'height' => 686,
            // ];
        } elseif ($property === 'feature_image') {
            // $size = [
            //     'width' => 670,
            //     'height' => 447,
            // ];
            // $size['featured_image_one'] = [
            //     'width' => 691,
            //     'height' => 461,
            // ];
            // $size['featured_image_two'] = [
            //     'width' => 537,
            //     'height' => 501,
            // ];
            $folder = 'Feature';
            // $thumbnail = [
            //     'width' => 435,
            //     'height' => 292,
            //     'path' => 'media/Blogs/Thumbnails',
            // ];
            // $size['thumbnail'] = $thumbnail;
        } elseif ($property === 'feature_image_thumb') {
            // $size = [
            //     'width' => 435,
            //     'height' => 292,
            // ];
            $folder = 'Thumbnails';
        } elseif ($property === 'images') {
            $folder = 'Gallery';
        }

        if ($request->files() || isset($_FILES['files'])) {
            $files = $request->files() ?? $_FILES['files'];

            if (!count($files)) {
                return $this->renderError(422, 'No files uploaded');
            }
            $data = [
                'files' => $files,
                'upload_dir' => 'media/Blogs/' . $folder
            ];

            $result = $this->mediaRepository->upload($data, $size, 'media/Blogs/' . $folder, null, $is_banner);
            if (!$result) {
                return $this->renderError(500, 'Failed to upload media');
            }
            if (isset($result['files'])) {
                $thumbnail = null;
                if(isset($size['thumbnail'])){
                    $imagePath = ROOT_DIR . PUBLIC_PATH . $result['files'][0]['image'];
                    $thumbnailDir = ROOT_DIR . PUBLIC_PATH . $size['thumbnail']['path'];
                    $thumbnail = $this->mediaRepository->createThumbnail($imagePath, $thumbnailDir, $size['thumbnail']['width'], $size['thumbnail']['height']);
                    if (!$thumbnail) {
                        return $this->renderError(500, 'Failed to create thumbnail');
                    }
                    $result['files'][0]['thumbnail'] = $thumbnail;
                }
                $post = $this->postRepository->get($post_id);
                if ($post) {
                    if ($property === 'images') {
                        $result = $this->postRepository->insertPostImages($result['files'], $post_id);
                        return $this->renderResponse($result);
                    } else {
                        $postData = new PostData(['post_id' => $post_id, $property => $result['files'], $property.'_thumb' => [$thumbnail]]);
                        $this->postRepository->updatePost($postData);
                    }
                }
            }
            return $this->renderResponse($result);
        }

        return $this->renderError(422, 'No files uploaded');
    }
    
    public function deletePostBannerFeatureImage(Request $request, int $post_id, string $property): Response
    {
        $deleted = $this->postRepository->deletePostBannerFeatureImage($post_id, $property);
        return $this->renderResponse(['message' => 'Media deleted successfully', 'success' => $deleted]);
    }
    public function deletePostImage(Request $request, int $post_image_id): Response
    {
        $deleted = $this->postRepository->deletePostImage($post_image_id);
        return $this->renderResponse(['message' => 'Media deleted successfully', 'deleted' => $deleted]);
    }

    public function importPosts(Request $request): Response
    {
        $csv_file = $request->file('csv_file');
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';
        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        $result = $this->postRepository->importPosts($csv_file_path);
        return $this->renderResponse(['success' => $result]);
    }

    public function importPostImages(Request $request): Response
    {
        $csv_file = $request->file('csv_file');

        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';

        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        $posts = $this->postRepository->importPostImages($csv_file_path);
        return $this->renderResponse(['success' => $posts]);
    }

    public function getBlogPostData(Request $request): Response
    {
        // Dummy blog post data
        $posts = [
            [
                'id' => 1,
                'title' => "Krost's Sydney Office Update With 3D Tour",
                'slug' => 'krost-sydney-office-update',
                'image' => '/img/blog-page/News 1.png',
                'excerpt' => 'Take a virtual tour of our newly updated Sydney office space.',
                'link' => '/blog/krost-sydney-office-update',
            ],
            [
                'id' => 2,
                'title' => "Raising a Cup for a Cause – Krost’s Biggest Morning Tea",
                'slug' => 'krost-biggest-morning-tea',
                'image' => '/img/blog-page/News 2.png',
                'excerpt' => 'Supporting cancer research with a cup of tea and community spirit.',
                'link' => '/blog/krost-biggest-morning-tea',
            ],
            [
                'id' => 3,
                'title' => "New Office Furniture Trends for 2025",
                'slug' => 'office-furniture-trends-2025',
                'image' => '/img/blog-page/News 3.png',
                'excerpt' => 'Explore the top design and functionality trends shaping the modern workplace.',
                'link' => '/blog/office-furniture-trends-2025',
            ],
        ];

        // Wrap response for consistency
        return $this->renderResponse([
            'posts' => $posts,
            'loading' => false
        ]);
    }

    public function getPaginationData(Request $request): Response
    {
            $is_admin = $this->isAdmin();
            $current_page = (int) $_GET['current_page'];
            $per_page =  (int) $_GET['per_page'];

            // var_dump($per_page); exit;

            $posts = $this->postRepository->getBloglistPaginationData($current_page, $per_page, $is_admin);
            return $this->renderResponse($posts);
    }

    public function updateWayPoints(Request $request): Response
    {
        $data = $request->all();
        $this->postRepository->updateWayPoints($data);
        return $this->renderResponse(['message' => 'Way points updated successfully']);
    }

    public function removeWayPoint(Request $request): Response
    {
        $data = $request->all();
        $removed = $this->postRepository->removeWayPoint($data);
        return $this->renderResponse($removed);
    }

    public function reorderImages(Request $request, int $post_id): Response
    {
        // echo 'reorderImages'; exit;
        $data = $request->all();
        $reordered = $this->postRepository->reorderPostImages($data, $post_id);
        return $this->renderResponse($reordered);
    }

    public function deletePostGalleryImage(Request $request): Response
    {
        try {
            $data = $request->validate([
                'post_image_ids' => 'required|array',
                'property' => 'nullable|string',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $ids = array_values(array_filter( // array value reset key value.
            array_map('intval', $data['post_image_ids']), // only keep integer data.
            static fn (int $id): bool => $id > 0, // keep greater than 0
        ));

        if ($ids === []) {
            return $this->renderError(422, 'No valid image ids provided');
        }

        $property = $data['property'] ?? 'images';
        $postGalleryImage = $this->postRepository->deletePostGalleryImageById($ids, $property);
        return $this->renderResponse($postGalleryImage);
    }
}
