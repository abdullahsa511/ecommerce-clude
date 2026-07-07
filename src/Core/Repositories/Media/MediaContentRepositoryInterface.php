<?php

declare(strict_types=1);

namespace App\Core\Repositories\Media;

use App\Core\Models\Media\Media;
use App\Core\Models\Media\MediaContent;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface MediaContentRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all media content with pagination and filtering
     * 
     * @param int|null $media_id Filter by media ID
     * @param int|null $language_id Filter by language ID
     * @param int $start Starting offset
     * @param int $limit Number of records per page
     * @return array{data: array, total: int}
     */
    public function getAll(?int $media_id = null, ?int $language_id = null, int $start = 0, int $limit = 10): array;

    /**
     * Get media content by various criteria
     * 
     * @param int|null $media_id Filter by media ID
     * @param int|null $language_id Filter by language ID
     * @param string|null $file Filter by file name
     * @return Media|null
     */
    public function get(int $mediaContentId): ?MediaContent;


} 