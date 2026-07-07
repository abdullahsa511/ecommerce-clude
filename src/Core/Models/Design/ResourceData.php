<?php

declare(strict_types=1);

namespace App\Core\Models\Design;

final class ResourceData
{
    private array $data;

    public function __construct(array $result)
    {
        // normalize documents (string → array)
        $documents = $result['design_resource_documents'] ?? [];

        if (is_string($documents)) {
            $decoded = json_decode($documents, true);
            $documents = is_array($decoded) ? $decoded : [];
        }

        // normalize image
        $image = $result['image'] ?? '';

        if ($image === '' && !empty($result['img'])) {
            $imgData = json_decode((string) $result['img'], true);
            if (json_last_error() === JSON_ERROR_NONE && isset($imgData[0]['objectURL'])) {
                $image = (string) $imgData[0]['objectURL'];
            }
        }

        // keep EVERYTHING else untouched
        $this->data = [
            'design_resource_documents' => $documents,
            'design_resource_id'        => (int) ($result['design_resource_id'] ?? 0),
            'title'                     => $result['title'] ?? null,
            'link_text'                 => $result['link_text'] ?? null,
            'img'                       => $result['img'] ?? null,
            'slug'                      => $result['slug'] ?? null,
            'description'               => $result['description'] ?? null,
            'media_id'                  => isset($result['media_id']) ? (int) $result['media_id'] : null,
            'resource_type'             => $result['resource_type'] ?? null,
            'grade'                     => $result['grade'] ?? null,
            'is_featured'               => $result['is_featured'] ?? null,
            'img2'                      => $result['img2'] ?? null,
            'type'                      => $result['type'] ?? null,
            'sort_order'                => (int) ($result['sort_order'] ?? 0),
            'image'                     => $image,
        ];
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
