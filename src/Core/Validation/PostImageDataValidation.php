<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;

class PostImageDataValidation extends Validation
{
    public stdClass $postImage;
    public stdClass $media;
    public bool $isExistingData = false;
    public function __construct(array $data, array $requiredFields = [], array $textFields = [], array $allPostImagesMap = [])
    {
        $this->rawData = $data;
        parent::__construct($requiredFields, $textFields);
        $this->postImage = new stdClass();
        $path = '/media/Blogs/Gallery/';   

        // if (isset($allPostImagesMap[$data['image_link']])) {
        //     $this->isExistingData = true;
        //     $this->postImage->post_image_id = $allPostImagesMap[$data['image_link']];
        // }
        
        // if (isset($data['post_image_id'])) {
        //     $this->isExistingData = true;
        //     $this->postImage->post_image_id = $this->validateInteger($data['post_image_id'], 'post_image_id');
        // }

        // Initialize with proper type casting and validation
        // $this->postImage->post_image_id = isset($data['post_image_id']) ? $this->validateInteger($data['post_image_id'], 'post_image_id') : null;
        $this->postImage->post_id = isset($data['post_id']) ? $this->validateInteger($data['post_id'], 'post_id') : null;
        $this->postImage->image_link = isset($data['image_link']) ? $this->validateString($data['image_link'], 'image_link', 191) : null;
        $this->postImage->image = isset($data['image_link']) ? $this->validateJson($data['image_link'], 'image', $path) : null;
        $this->postImage->sort_order = isset($data['sort_order']) ? $this->validateInteger($data['sort_order'], 'sort_order', 0) : 0;
        $this->postImage->status = isset($data['status']) ? $this->validateJson($data['status'], 'status') : null;
        $this->postImage->way_points = isset($data['way_points']) ? $this->validateJson($data['way_points'], 'way_points') : null;

        if(isset($this->postImage->image) && !empty($this->postImage->image)){
            $image = json_decode($this->postImage->image, true);
            $image = $image[0]??[];
            if(isset($image) && !empty($image['objectURL']) && isset($image['file'])){
                $this->media = new stdClass();
                $this->media->file = json_encode([
                    'name' => $image['name'],
                    'size' => $image['size'],
                    'type' => $image['type'],
                    'objectURL' => $image['objectURL'],
                    'tmp_name' => $image['file']['tmp_name'],
                    'full_path' => $image['file']['full_path'],
                ]);
                $this->media->path = $image['objectURL'];
                $this->media->name = $image['name'];
                $this->media->meta = $this->postImage->image_link;
            }
        }
    }

    // private function validateInteger($value, string $field, ?int $default = null): ?int
    // {
    //     if ($value === null || $value === '') {
    //         return $default;
    //     }

    //     if (!is_numeric($value)) {
    //         $this->addError($field, "must be a valid integer");
    //         return $default;
    //     }

    //     $intValue = (int) $value;
    //     if ($intValue < 0) {
    //         $this->addError($field, "must be a positive integer");
    //         return $default;
    //     }

    //     return $intValue;
    // }

    // private function validateString($value, string $field, int $maxLength): ?string
    // {
    //     if ($value === null || $value === '') {
    //         return null;
    //     }

    //     if (!is_string($value)) {
    //         $this->addError($field, "must be a string");
    //         return null;
    //     }

    //     $stringValue = trim($value);
    //     if (strlen($stringValue) > $maxLength) {
    //         $this->addError($field, "must not exceed {$maxLength} characters");
    //         return null;
    //     }

    //     return $stringValue;
    // }

    // private function validateJson($value, string $field): ?string
    // {
    //     if ($value === null || $value === '') {
    //         return null;
    //     }

    //     if (is_array($value)) {
    //         return json_encode($value);
    //     }

    //     if (!is_string($value)) {
    //         $this->addError($field, "must be a valid JSON string or array");
    //         return null;
    //     }

    //     // Test if it's valid JSON
    //     json_decode($value);
    //     if (json_last_error() !== JSON_ERROR_NONE) {
    //         $this->addError($field, "must be a valid JSON string");
    //         return null;
    //     }

    //     return $value;
    // }

    // private function addError(string $field, string $message): void
    // {
    //     $this->errors[$field] = $message;
    //     $this->isValidData = false;
    // }

    // public function validate(): bool|array
    // {
    //     // Additional business logic validations
    //     $this->validateBusinessRules();

    //     if (!$this->isValidData) {
    //         return false;
    //     }

    //     return $this->toArray();
    // }

    private function validateBusinessRules(): void
    {
        // Check required fields for import
        if (empty($this->post_id)) {
            $this->addError('post_id', "post_id is required");
        }

        if (empty($this->image_link)) {
            $this->addError('image_link', "image_link is required");
        }

        // Validate project_id is positive
        if ($this->post_id !== null && $this->post_id <= 0) {
            $this->addError('post_id', "must be greater than 0");
        }

        // Validate sort_order is non-negative
        if ($this->sort_order < 0) {
            $this->addError('sort_order', "must be 0 or greater");
        }
    }

    public function isValid(): bool
    {
        return $this->isValidData;
    }

    // public function getErrors(): array
    // {
    //     return $this->errors;
    // }

    public function getRawData(): array
    {
        return $this->rawData;
    }

    public function getRawDataSafe(): array
    {
        // Safely retrieve raw data by checking isset for each property
        $safeData = [];
        
        if (isset($this->rawData['post_image_id'])) {
            $safeData['post_image_id'] = $this->rawData['post_image_id'];
        }
        if (isset($this->rawData['post_id'])) {
            $safeData['post_id'] = $this->rawData['post_id'];
        }
        if (isset($this->rawData['image_link'])) {
            $safeData['image_link'] = $this->rawData['image_link'];
        }
        if (isset($this->rawData['image'])) {
            $safeData['image'] = $this->rawData['image'];
        }
        if (isset($this->rawData['sort_order'])) {
            $safeData['sort_order'] = $this->rawData['sort_order'];
        }
        if (isset($this->rawData['status'])) {
            $safeData['status'] = $this->rawData['status'];
        }
        if (isset($this->rawData['way_points'])) {
            $safeData['way_points'] = $this->rawData['way_points'];
        }
        
        return $safeData;
    }

    public function toArray(): array
    {
        // Always return a consistent structure with all fields
        $data = (array) $this->postImage;

        return $data;
    }

    // public function getUniqueIdentifier(): string
    // {
    //     // For duplicate checking - use project_image_id if available, otherwise use project_id + image_link combination
    //     if ($this->post_image_id !== null) {
    //         return "post_image_id:{$this->post_image_id}";
    //     }
    //     if ($this->post_id !== null && $this->image_link !== null) {
    //         return "post_id:{$this->post_id}_image_link:{$this->image_link}";
    //     }
    //     return "unknown:" . md5(serialize($this->rawData));
    // }
}
