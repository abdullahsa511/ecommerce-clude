<?php

declare(strict_types=1);

namespace App\Core\Models\Design;

final class ResourceImageData
{
    public string $type;
    public string $dataSrc;
    public string $dataBgSrc;
    public string $title;
    public string $gridArea;
    public string $class;
    public string $path;
    public string $context;
    public string|null|int $context_reference;


    private static array $gridAreaPattern = ['8-4', '8-6', '8-6', '8-4', '16-6'];

    private static array $mediaTabGridAreaPattern = ['6-6', '6-6', '6-6', '6-6', '12-6'];

    public function __construct(array $result, int $index = 0, string $patternType = 'default')
    {
        // normalize and decode any stored JSON that may contain image data
        $imageData = json_decode((string) ($result['file'] ?? '[]'), true);
        $path = $result['path'] ?? '';

        if (!is_array($imageData)) {
            $imageData = [];
        }

        // Attempt to locate an objectURL in several common shapes:
        // - $imageData['objectURL']
        // - $imageData[0]['objectURL']
        // - decoded $result['img'] / $result['image']
        // - fallback to $result['path']
        $imageUrl = null;
        if (isset($imageData['objectURL'])) {
            $imageUrl = $imageData['objectURL'];
        } elseif (isset($imageData[0]['objectURL'])) {
            $imageUrl = $imageData[0]['objectURL'];
        } else {
            // try img or image fields from the result (may be JSON or a plain string)
            $imgField = $result['img'] ?? $result['image'] ?? null;
            if (is_string($imgField) && $imgField !== '') {
                $decodedImgField = json_decode($imgField, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decodedImgField) && isset($decodedImgField[0]['objectURL'])) {
                    $imageUrl = $decodedImgField[0]['objectURL'];
                } else {
                    // treat the string itself as a potential URL
                    $imageUrl = $imgField;
                }
            } else {
                // ultimate fallback to provided path
                $imageUrl = $path;
            }
        }

        // Resolve a string URL from various possible shapes (string, nested array, numeric index)
        $resolvedImageUrl = '';
        if (is_string($imageUrl) && $imageUrl !== '') {
            $resolvedImageUrl = $imageUrl;
        } elseif (is_array($imageUrl)) {
            if (isset($imageUrl['objectURL'])) {
                if (is_string($imageUrl['objectURL'])) {
                    $resolvedImageUrl = $imageUrl['objectURL'];
                } elseif (is_array($imageUrl['objectURL']) && isset($imageUrl['objectURL'][0])) {
                    $resolvedImageUrl = (string) $imageUrl['objectURL'][0];
                }
            } elseif (isset($imageUrl[0]) && is_string($imageUrl[0])) {
                $resolvedImageUrl = $imageUrl[0];
            }
        }
        // Fallback to provided path when no image URL resolved
        if ($resolvedImageUrl === '') {
            $resolvedImageUrl = is_string($path) ? $path : (string) ($path ?? '');
        }

        // Choose pattern
        $pattern = self::$gridAreaPattern;

        if ($patternType === 'media') {
            $pattern = self::$mediaTabGridAreaPattern;
        }

        $i = $index % count($pattern);
        $gridArea = $pattern[$i];

        // $i = ($index % count(self::$gridAreaPattern));
        // $gridArea = self::$gridAreaPattern[$i];

        $this->type      = 'th-masonry-img-item-' . $gridArea;
        $this->dataSrc   = $resolvedImageUrl;
        $this->dataBgSrc = $resolvedImageUrl;
        $this->path      = is_string($path) ? $path : (string) ($path ?? '');
        $this->title     = (string) ($result['name'] ?? 'Image');
        $this->gridArea  = $gridArea;
        $this->class     = 'th-masonry-img-item th-masonry-img-item-' . $gridArea;
        if(isset($result['product_id'])){
            $this->context = 'Product';
            $this->context_reference = $result['product_title'] ?? '';
            $this->class .= ' th-product';
        }else if(isset($result['project_id'])){
            $this->context = 'Project';
            $this->context_reference = $result['project_name'] ?? '';
        }else if(isset($result['section_id'])){
            $this->context = 'Showroom';
            $this->context_reference = $result['title'] ?? '';
        }else if(isset($result['post_id'])){
            $this->context = 'Post';
            $this->context_reference = $result['post_title'] ?? '';
        }else{
            $this->context = '';
            $this->context_reference = '';
        }
        // $this->media_id  = isset($result['media_id']) ? (int) $result['media_id'] : 0;

    }

    public function toArray(): array
    {
        return [
            'type'      => $this->type,
            'dataSrc'   => $this->dataSrc,
            'dataBgSrc' => $this->dataBgSrc,
            'path'      => $this->path,
            'title'     => $this->title,
            'gridArea'  => $this->gridArea,
            'class'     => $this->class,
            'context'   => $this->context,
            'context_reference' => $this->context_reference
        ];
    }
}
