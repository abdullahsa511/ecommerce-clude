<?php

declare(strict_types=1);

namespace App\Core\Repositories\Post;

use PDO;
use App\Core\Models\Post\Post;
use App\Core\Repositories\Base\BaseRepository;
use function App\Core\System\utils\htmlToPlainText;

class PostBlogSliderRepository extends BaseRepository implements PostBlogSliderRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'post', Post::class);
    }

    protected function getPrimaryKeyColumn(): string
    {
        return 'post_tag_id';
    }

    public function getAll(
        ?int $siteId = null,
        ?string $type = null,
        ?string $source = null,
        int $start = 0,
        int $limit = 10
    ): array {
        $query = $this->model;


        // Apply ordering
        $query->orderBy('post_tag_id', 'ASC');

        // Apply pagination
        if ($limit !== null) {
            $query->limit($limit);
        }

        if ($start !== null) {
            $query->offset($start);
        }

        // Get results
        $results = $query->findAll() ?? [];
        $total = $query->countAll();
        $perPage = $limit ?? $this->model->limitValue;

        return [
            'items' => collect($results),
            'total' => $total,
            "total_pages" => (int)ceil($total / $perPage),
            "current_page" => (int)($start / $perPage + 1),
            "per_page" => $perPage
        ];
    }

    public function get(int $postId): ?Post
    {
        $query = $this->model;
        return $query->find($postId);
    }

    public function getBlogSlider(int $postId, array $fields, int $limit = 4): array
    {
        $result = $this->model
        ->join('post_content', 'post_content.post_id', '=', 'post.post_id')
        ->where('post.is_featured', '>', 0)
  
        ->select($fields)
        ->limit($limit)
        ->orderBy('post.post_id', 'DESC');
        
        $results = $result->findAll(false);

        foreach ($results as $key => &$row) {
            $row['description'] = htmlToPlainText($row['description']);
        }
        
        return $results;
    }
    
} 