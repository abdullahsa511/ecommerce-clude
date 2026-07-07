<?php

declare(strict_types=1);

namespace App\Core\Repositories\Comment;

use App\Core\Models\Post\Comment;
use App\Core\Repositories\Base\BaseRepository;
use PDO;

class CommentRepository extends BaseRepository implements CommentRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'comment', Comment::class);
    }

    public function getAll(): array
    {
        $this->model->orderBy('created_at', 'DESC');
        
        $result = $this->model->findAll();
        $items = collect($result);
        $totalRecords = $this->model->countAll();

        return [
            'items' => $items,
            'total' => $totalRecords
        ];
    }

    public function findByPostId(int $post_id): array
    {
        $this->model->where('post_id', '=', (string)$post_id);
        $this->model->orderBy('created_at', 'ASC');

        // $this->model->set
        
        $result = $this->model->findAll();
        $items = collect($result);
        $totalRecords = $this->model->countAll();

        return [
            'items' => $items,
            'total' => $totalRecords
        ];
    }

    public function findByUserId(int $user_id): array
    {
        $this->model->where('user_id', '=', (string)$user_id);
        $this->model->orderBy('created_at', 'DESC');
        
        $result = $this->model->findAll();
        $items = collect($result);
        $totalRecords = $this->model->countAll();

        return [
            'items' => $items,
            'total' => $totalRecords
        ];
    }

    public function findByParentId(int $parent_id): array
    {
        $this->model->where('parent_id', '=', (string)$parent_id);
        $this->model->orderBy('created_at', 'ASC');
        
        $result = $this->model->findAll();
        $items = collect($result);
        $totalRecords = $this->model->countAll();

        return [
            'items' => $items,
            'total' => $totalRecords
        ];
    }

    public function findByEmail(string $email): array
    {
        $this->model->where('email', '=', $email);
        $this->model->orderBy('created_at', 'DESC');
        
        $result = $this->model->findAll();
        $items = collect($result);
        $totalRecords = $this->model->countAll();

        return [
            'items' => $items,
            'total' => $totalRecords
        ];
    }

    public function findByStatus(int $status): array
    {
        $this->model->where('status', '=', (string)$status);
        $this->model->orderBy('created_at', 'DESC');
        
        $result = $this->model->findAll();
        $items = collect($result);
        $totalRecords = $this->model->countAll();

        return [
            'items' => $items,
            'total' => $totalRecords
        ];
    }

} 