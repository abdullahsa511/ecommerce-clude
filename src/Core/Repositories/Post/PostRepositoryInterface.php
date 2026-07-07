<?php

declare(strict_types=1);

namespace App\Core\Repositories\Post;

use App\Core\Models\Post\Post;
use App\Core\Models\Post\PostContent;
use App\Core\Models\Post\PostContentRevision;
use App\Core\Models\Post\PostData;
use App\Core\ModelsFilters\PostFilter;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface PostRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all posts with filtering and pagination
     *
     * @param int $start
     * @param int $limit
     * @param array $filters Array of filters (search, like, username, status, etc.)
     * @param array $options Array of options (comment_count, categories, tags, etc.)
     * @param string|null $orderBy
     * @param string $direction
     * @return array
     */
    public function getAll(
        int $start = 0,
        int $limit = 10,
        array $filters = [],
        array $options = [],
        ?string $orderBy = null,
        string $direction = 'DESC'
    ): array;

    /**
     * Get a single post by ID or slug
     *
     * @param int|null $postId
     * @param string|null $slug
     * @param array $options Array of options (comment_count, language_id, etc.)
     * @return Post|null
     */
    public function get(?int $postId = null, ?string $slug = null, array $options = []): ?Post;

    
    /**
     * Edit post content for a specific language
     *
     * @param int $postId
     * @param array $postContent
     * @param int $languageId
     * @return bool
     */
    public function editContent(int $postId, array $postContent, int $languageId): bool;


    /**
     * Set post taxonomy items
     *
     * @param int $postId
     * @param array $taxonomyItems
     * @return bool
     */
    public function setPostTaxonomy(int $postId, array $taxonomyItems): bool;

    /**
     * Get post archives
     *
     * @param int $start
     * @param int $limit
     * @param string|null $interval
     * @param string|null $type
     * @return array
     */
    public function getArchives(int $start = 0, int $limit = 10, ?string $interval = null, ?string $type = null): array;

    /**
     * Record a revision for a post content
     *
     * @param PostContent $postContent
     * @return PostContentRevision
     */
    public function recordRevision(PostContent $postContent): PostContentRevision;

    public function getBlogSlider(int $postId, array $fields, int $limit = 4): array;

    public function createPost(PostData $postData): Post;
    public function updatePost(PostData $postData): Post;

    public function showPost(int $postId): Post;
    public function getTags(): array;
    public function insertPosts(array $data): bool;

    /**
     * Get blog gallery component data
     *
     * @param array $param
     * @return array
     */
    public function getBlogGalleryComponentData(array $param): array;

    public function getBlogMainComponentData(array $param);

    public function getLatestNewsComponentData(array $param);
    public function insertPostImages(array $data, int $post_id): array;

    /**
     * Import posts from a CSV file.
     *
     * @param string $csv_file
     * @return array
     */
    public function importPosts(string $csv_file): array;

    public function deletePostBannerFeatureImage(int $post_id, string $property): bool;

    public function deletePostImage(int $post_image_id): bool;

    public function getBlogDetailHeroComponentData(array $params): array;
    // public function getPageHeroComponentData(array $params): array;
    public function getBloglistPaginationData(int $current_page, int $per_page);

    public function updateWayPoints(array $data): array;

    public function removeWayPoint(array $data): array;
    public function reorderPostImages(array $data, int $post_id): array;
    public function getPostIdBySlug(string $slug): int;

    public function deletePostGalleryImageById(array $ids, string $property = 'images'): array;

    public function getRelatedArticlesSliderComponentData(array $params): array;
} 