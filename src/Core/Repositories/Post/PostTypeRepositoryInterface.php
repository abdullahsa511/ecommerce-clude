<?php

declare(strict_types=1);

namespace App\Core\Repositories\Post;

use App\Core\Repositories\Base\BaseRepositoryInterface;
use App\Core\Models\Post\PostType;

interface PostTypeRepositoryInterface extends BaseRepositoryInterface
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
    public function get(int $postTypeId): ?PostType;
    public function importPostTypes(string $csv_file): array;
    public function updatePostTypeImage(array $data, int $post_type_id): bool;
    public function deletePostTypeImage(int $post_type_id): bool;
} 