<?php

declare(strict_types=1);

namespace App\Core\Repositories\Plugin;

use App\Core\Repositories\Base\BaseRepositoryInterface;
use App\Core\Models\Plugin\Plugin;

interface PluginRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all plugins with pagination and filtering
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

    /**
     * Get a single plugin by ID
     */
    public function get(int $pluginId): ?object;


} 