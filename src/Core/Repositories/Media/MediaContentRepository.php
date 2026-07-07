<?php

declare(strict_types=1);

namespace App\Core\Repositories\Media;

use App\Core\Models\Media\MediaContent;
use App\Core\Repositories\Base\BaseRepository;
use PDO;

class MediaContentRepository extends BaseRepository implements MediaContentRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'media_content', MediaContent::class);
    }

    /**
     * Get all media content with pagination and filtering
     */
    public function getAll(
        ?int $media_id = null,
        ?int $language_id = null,
        int $start = 0,
        int $limit = 10
    ): array {
        $query = $this->model;

        if ($media_id !== null) {
            $query->where('media_id', '=', $media_id);
        }

        if ($language_id !== null) {
            $query->where('language_id', '=', $language_id);
        }

        $query->orderBy('media_id', 'ASC')
              ->orderBy('language_id', 'ASC');

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

    /**
     * Get media content by ID
     */
    public function get(int $mediaContentId): ?MediaContent
    {
        $query = $this->model
            ->where('media_content_id', '=', $mediaContentId);

        $result = $query->findAll();
        if (empty($result)) {
            return null;
        }
        
        return $this->model->set($result[0]);
    }

    /**
     * Get media content by media ID and language ID
     */
    public function getContent(?int $media_id = null, ?int $language_id = null): ?MediaContent
    {
        $query = $this->model;

        if ($media_id !== null) {
            $query->where('media_id', '=', $media_id);
        }

        if ($language_id !== null) {
            $query->where('language_id', '=', $language_id);
        }

        $result = $query->findAll();
        if (empty($result)) {
            return null;
        }
        
        return $this->model->set($result[0]);
    }

    /**
     * Edit media and its content
     */
    public function edit(array $media, array $media_content, ?int $media_id = null, ?string $file = null): bool
    {
        $query = $this->model;

        if ($media_id !== null) {
            $query->where('media_id', '=', $media_id);
        }

        if ($file !== null) {
            $query->where('file', '=', $file);
        }

        $result = $query->findAll();
        if (empty($result)) {
            return false;
        }

        $media_id = $result[0]['media_id'];

        // Update media record
        if (!empty($media)) {
            $this->model->update($media_id, $media);
        }

        // Update or insert media content records
        foreach ($media_content as $content) {
            $content['media_id'] = $media_id;
            $this->model->upsert([$content], ['media_id', 'language_id']);
        }

        return true;
    }

    public function create(array $data): ?MediaContent
    {
        return $this->model->createWithCompositeKey(['media_id', 'language_id'], $data);
    }
} 