<?php

declare(strict_types=1);

namespace App\Core\Repositories\Post;

use App\Core\Repositories\Base\BaseRepositoryInterface;
use App\Core\Models\Post\Post;

interface PostBlogSliderRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all post types with optional filtering
     */
    public function getAll(
        ?int $siteId = null,
        ?string $type = null,
        ?string $source = null,
        int $start = 0,
        int $limit = 10
    ): array;

    /**
     * Get a single post type by ID
     */
    public function get(int $postId): ?Post;

    public function getBlogSlider(int $postId, array $fields, int $limit = 4): array;
} 