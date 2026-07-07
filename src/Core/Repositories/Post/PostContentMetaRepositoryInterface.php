<?php

declare(strict_types=1);

namespace App\Core\Repositories\Post;

use App\Core\Repositories\Base\BaseRepositoryInterface;
use App\Core\Models\Post\PostContentMeta;
interface PostContentMetaRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get a single meta value
     */
    public function getAll(
        ?int $postId = null,
        ?string $namespace = null,
        ?array $keys = null,
        ?int $languageId = null,
        int $start = 0,
        int $limit = 10
    ): array;

    public function get(
        int $postId,
        ?string $namespace = null,
        ?string $key = null,
        ?int $languageId = null
    ): ?PostContentMeta;

} 