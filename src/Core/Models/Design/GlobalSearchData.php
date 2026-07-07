<?php

declare(strict_types=1);

namespace App\Core\Models\Design;

use function App\Core\System\utils\env;

final class GlobalSearchData
{
    public string $title;
    public string $dataSrc;
    public string $dataBgSrc;
    public int|string|null $reference;
    public string $description;
    public string $href;
    public string $slug;
    public string $product_url;
    public string $model_type;

    public function __construct(array $item)
    {
        // base url
        $base_url = env('APP_URL');

        // $name = isset($item['name']) ? ucwords(str_replace(['_', '-'], ' ', strtolower($item['name']))) : '';
        $name = isset($item['name']) ? str_replace(['_', '-'], ' ', $item['name']) : '';
        $title = isset($item['title']) ? $item['title'] : $name;
        $this->title = empty($title) ? $name : $title;
        $this->reference = $item['reference'] ?? null;
        $this->description = $item['description'] ?? '';
        $imageData = $item['image'] ?? null;
        $this->href = isset($item['href']) ? $base_url . '/' . $item['href'] : '';
        $this->product_url = isset($item['href']) ? '/' . $item['href'] : '';
        $this->slug = $item['slug'] ?? '';
        $this->model_type = $item['model_type'] ?? '';
        // Decode JSON if string
        if (is_string($imageData)) {
            $decoded = json_decode($imageData, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $imageData = $decoded;
            } else {
                $imageData = [$imageData];
            }
        }

        // Get objectURL
        $objectURL = null;
        if (is_array($imageData)) {
            if (isset($imageData[0]['objectURL'])) {
                $objectURL = $imageData[0]['objectURL'];
            } elseif (isset($imageData['objectURL'])) {
                $objectURL = $imageData['objectURL'];
            }
        } elseif (is_string($imageData)) {
            $objectURL = $imageData;
        }

        $this->dataSrc = $objectURL ?? '';
        $this->dataBgSrc = $objectURL ?? '';
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'dataSrc' => $this->dataSrc,
            'dataBgSrc' => $this->dataBgSrc,
            'reference' => $this->reference,
            'description' => $this->description,
            'href' => $this->href,
            'product_url' => $this->product_url,
            'slug' => $this->slug,
            'model_type' => $this->model_type,
        ];
    }
}
