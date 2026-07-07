<?php

declare(strict_types=1);

namespace App\Core\Repositories\Page;

use App\Core\Models\Post\Post;
use App\Core\Models\Post\PostData;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface PageRepositoryInterface extends BaseRepositoryInterface
{
    public function getBlogSlider(int $postId, array $fields, int $limit = 4): array;
    public function get(?int $postId = null, ?string $slug = null, array $options = []): ?Post;

    public function createPage(PostData $postData): Post;
    public function insertPageData(array $data);
    public function updatePage(PostData $postData): Post;
    public function updatePageData($data);

    public function showPage(int $postId): Post;

    /**
     * Delete a page and all its related data.
     *
     * @param int $postId
     * @return bool
     */
    public function deletePage(int $postId): bool;

    /**
     * Delete multiple pages and all their related data.
     *
     * @param array $postIds
     * @return int Number of deleted posts
     */
    public function deleteMultiplePages(array $postIds): int;
    public function updatePageImges(array $data, string $property, int $project_id): bool;
    public function deletePageImage(string $path, string $property, int $pageId): bool;


    public function getPageHeaderForComponent(array $params) : array;
    public function getPageBodyForComponent(array $params): array;

    public function importPages(string $csv_file): array;

} 