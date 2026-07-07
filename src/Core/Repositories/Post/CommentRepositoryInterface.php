<?php

declare(strict_types=1);

namespace App\Core\Repositories\Post;

use App\Core\Repositories\Base\BaseRepositoryInterface;
use App\Core\Models\Post\Comment;

interface CommentRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all comments with pagination and filtering
     */
    public function getAll(
        ?int $languageId =null,
        ?int $postId = null,
        ?int $userId = null,
        ?int $status = null,
        bool $postTitle = false,
        int $start = 0,
        int $limit = 10
    ): array;

    public function findCommentsById(int $id, string $modelType, ?int $userId = null): array;
    public function createComment(array $data ,array $files):array;
    public function deleteCommentById(int $id): bool;
    public function upvoteComment(int $comment_id, int $user_id);
    public function checkedComment(int $comment_id, int $user_id);
    public function createReplyComment(array $data):array;
} 