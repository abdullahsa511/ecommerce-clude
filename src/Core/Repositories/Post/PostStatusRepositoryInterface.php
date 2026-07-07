<?php

declare(strict_types=1);

namespace App\Core\Repositories\Post;

use App\Core\Models\Post\PostStatus;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface PostStatusRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all order statuses
     *
     * @param int|null $language_id
     * @param int $start
     * @param int $limit
     * @return array
     */
    public function getAll(?int $language_id = null, int $start = 0, int $limit = 10): array;

    /**
     * Get a single order status
     *
     * @param int $post_status_id
     * @return PostStatus|null
     */
    public function get(int $post_status_id): ?PostStatus;

    
} 