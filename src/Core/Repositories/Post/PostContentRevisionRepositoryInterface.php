<?php

declare(strict_types=1);

namespace App\Core\Repositories\Post;

use App\Core\Models\Post\PostContentRevision;

interface PostContentRevisionRepositoryInterface
{
    /**
     * Get all revisions with pagination
     * @return array{items: array<PostContentRevision>, total: int}
     */
    public function getAll(
        ?int $postId = null,
        ?int $languageId = null,
        ?string $createdAt = null,
        bool $includeContent = false,
        int $start = 0,
        int $limit = 10
    ): array;

    /**
     * Get a specific revision
     */
    public function get(
        int $postId,
        int $languageId,
        string $createdAt
    ): ?PostContentRevision;

    

    /**
     * Delete a specific revision
     */
    public function deleteRevision(
        int $postId,
        int $languageId,
        string $createdAt
    ): bool;
} 