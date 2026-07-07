<?php

declare(strict_types=1);

namespace App\Core\Repositories\Post;

use App\Core\Repositories\Base\BaseRepositoryInterface;
use App\Core\Models\Post\PostTag;

interface PostTagRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get a single post by ID or slug
     *
     * @param int|null $posttagId
     * @param string|null $slug
     * @param array $options Array of options (comment_count, language_id, etc.)
     * @return Post|null
     */
    public function get(?int $posttagId = null, ?string $slug = null, array $options = []): ?PostTag;
    public function insertPostTagImages(array $data, int $posttagId): bool;

    public function getAllPostTags(): array;

    public function add(array $data): array;
    public function findByName(string $name): ?PostTag;
    public function getPostTagById($id);

    public function findPostTags(): array;

    public function updatePostTags($id, $data): array;

    public function updatePostTag(PostTag $posttagData): PostTag;

    public function findWithImages(int $id): ?array;

    public function searchPostTags(string $search): array;

    public function deletePostTag(int $post_tag_id): ?PostTag;

    public function importPostTags(string $csv_file): array;
} 