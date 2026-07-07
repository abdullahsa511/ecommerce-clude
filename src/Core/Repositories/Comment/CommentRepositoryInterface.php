<?php

declare(strict_types=1);

namespace App\Core\Repositories\Comment;

use App\Core\Repositories\Base\BaseRepositoryInterface;

interface CommentRepositoryInterface extends BaseRepositoryInterface
{
    public function getAll(): array;
    public function findByPostId(int $post_id): array;
    public function findByUserId(int $user_id): array;
    public function findByParentId(int $parent_id): array;
    public function findByEmail(string $email): array;
    public function findByStatus(int $status): array;
} 