<?php

declare(strict_types=1);

namespace App\Core\Repositories\Plugin;

use App\Core\Repositories\Base\BaseRepository;
use App\Core\Models\Plugin\Plugin;
use App\Core\Models\Base\Model;
use PDO;

class PluginRepository extends BaseRepository implements PluginRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'plugin', Plugin::class);
    }

    public function getAll(
        ?int $languageId =null,
        ?int $postId = null,
        ?int $userId = null,
        ?int $status = null,
        bool $postTitle = false,
        int $start = 0,
        int $limit = 10
    ): array {
        $query = $this->model;

        if ($postId !== null) {
            $query->where('post_id', '=', $postId);
        }

        if ($userId !== null) {
            $query->where('user_id', '=', $userId);
        }

        if ($status !== null) {
            $query->where('status', '=', $status);
        }

        if ($postTitle) {
            $query->with(['pluginContent' => function($model) use ($languageId) {
                $model->where('language_id', '=', $languageId);
            }]);
        }


        $query->orderBy('plugin_id', 'ASC');


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

    public function get(int $pluginId): ?object
    {
        $query = $this->model
            ->with(['user'])
            ->where('plugin_id', '=', $pluginId);

        $result = $query->findAll();
        if (empty($result)) {
            return null;
        }
        
        // Create a new Comment instance and set its data
        $plugin = new Plugin();
        $plugin->setDb($this->db);
        return $plugin->set($result[0]);
    }

} 