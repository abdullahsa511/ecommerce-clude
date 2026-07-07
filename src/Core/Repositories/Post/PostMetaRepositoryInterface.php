<?php

declare(strict_types=1);

namespace App\Core\Repositories\Post;

use App\Core\Models\Post\PostMeta;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface PostMetaRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get a single meta value
     */
    public function get(
        ?int $productId = null,
        ?string $namespace = null,
        ?string $key = null
    ): ?string;

    /**
     * Set a single meta value
     */
    public function set(
        int $productId,
        string $namespace,
        string $key,
        string $value
    ): bool;

    /**
     * Get multiple meta values
     * @return array<PostMeta>
     */
    public function getMulti(
        ?int $productId = null,
        ?string $namespace = null,
        ?array $keys = null
    ): array;

    /**
     * Set multiple meta values
     * @param array<array{key: string, value: string}> $meta
     */
    public function setMulti(
        int $productId,
        string $namespace,
        array $meta
    ): bool;

    /**
     * Delete meta values
     */
    public function delete(
        ?int $productId = null,
        ?string $namespace = null,
        ?array $keys = null
    ): bool;
} 