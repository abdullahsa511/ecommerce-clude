<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;

class ProjectDataValidation
{
    public stdClass $project;

    private bool $isValidData = true;
    private array $errors = [];
    private array $rawData = [];

    public function __construct(array $data)
    {
        
        $image_path = '/media/Projects/banner/';
        $image_thumb_path = '/media/Projects/thumbnail/';
        $image_main_one_path = '/media/Projects/main-image-one/';
        $image_main_two_path = '/media/Projects/main-image-two/';

        $this->project = new stdClass();
        $this->rawData = $data;
        
        // Initialize with proper type casting and validation
        $this->project->project_id = isset($data['project_id']) ? $this->validateInteger($data['project_id'], 'project_id') : null;
        // $project_id = !empty($data['project_id']) ? (int)$data['project_id'] : null;
        // $this->project_id = $project_id;
        $this->project->site_id = isset($data['site_id']) ? $this->validateInteger($data['site_id'], 'site_id', 1) : 1;
        $this->project->status_id = isset($data['status_id']) ? $this->validateInteger($data['status_id'], 'status_id', 1) : 1;
        $this->project->customer_id = isset($data['customer_id']) ? $this->validateInteger($data['customer_id'], 'customer_id') : null;
        
        // String validations with length limits
        $this->project->name = isset($data['name']) ? $this->validateString($data['name'], 'name', 191) : null;
        $this->project->slug = isset($data['slug']) ? $this->validateSlug($data['slug']) : null;
        $this->project->description = isset($data['description']) ? $this->validateText($data['description'], 'description') : null;
        $this->project->preview_text = isset($data['preview_text']) ? $this->validateText($data['preview_text'], 'preview_text') : null;
        $this->project->location = isset($data['location']) ? $this->validateString($data['location'], 'location', 191) : null;
        $this->project->designer = isset($data['designer']) ? $this->validateString($data['designer'], 'designer', 191) : null;
        $this->project->photographer = isset($data['photographer']) ? $this->validateString($data['photographer'], 'photographer', 191) : null;
        // $this->project->status = isset($data['status']) ? $this->validateString($data['status'], 'status', 191) : null;
        $statuses = [
            1 => 'Draft',
            2 => 'Future',
            3 => 'Pending',
            4 => 'Published',
            5 => 'Private',
            6 => 'Trash',
        ];
        
        $this->project->status_id = isset($data['status_id']) ? ($this->validateInteger($data['status_id'], 'status_id', 1) ?? 1) : 1;
        $this->project->status = $statuses[$this->project->status_id] ?? 'Draft';
        
        // JSON validations
        $this->project->image = isset($data['image']) ? $this->validateJson($image_path.$data['image'], 'image') : null;
        $this->project->image_thumb = isset($data['image_thumb']) ? $this->validateJson($image_thumb_path.$data['image_thumb'], 'image_thumb') : null;
        
        // Meta fields
        $this->project->meta_title = isset($data['meta_title']) ? $this->validateString($data['meta_title'], 'meta_title', 191) : null;
        $this->project->meta_description = isset($data['meta_description']) ? $this->validateText($data['meta_description'], 'meta_description') : null;
        $this->project->meta_keywords = isset($data['meta_keywords']) ? $this->validateString($data['meta_keywords'], 'meta_keywords', 500) : null;
        
        // Additional fields
        $this->project->title = isset($data['title']) ? $this->validateString($data['title'], 'title', 191, true) : null;
        $this->project->label = isset($data['label']) ? $this->validateString($data['label'], 'label', 191) : null;
        $this->project->keyline_quote = isset($data['keyline_quote']) ? $this->validateString($data['keyline_quote'], 'keyline_quote', 255) : null;
        $this->project->link_text = isset($data['link_text']) ? $this->validateString($data['link_text'], 'link_text', 191) : null;
        $this->project->is_featured = isset($data['is_featured']) ? $this->validateBoolean($data['is_featured'], 'is_featured') : 0;
        
        // Main content fields
        $this->project->main_title = isset($data['main_title']) ? $this->validateString($data['main_title'], 'main_title', 191) : null;
        $this->project->main_description_one = isset($data['main_description_one']) ? $this->validateText($data['main_description_one'], 'main_description_one') : null;
        $this->project->main_description_two = isset($data['main_description_two']) ? $this->validateText($data['main_description_two'], 'main_description_two') : null;
        $this->project->main_description_three = isset($data['main_description_three']) ? $this->validateText($data['main_description_three'], 'main_description_three') : null;
        $this->project->main_description_four = isset($data['main_description_four']) ? $this->validateText($data['main_description_four'], 'main_description_four') : null;
       $this->project->main_image_one = isset($data['main_image_one']) ? $this->validateJson($image_main_one_path.$data['main_image_one'], 'main_image_one') : null;
        $this->project->main_image_two = isset($data['main_image_two']) ? $this->validateJson($image_main_two_path.$data['main_image_two'], 'main_image_two') : null;

    }
    private function validateInteger($value, string $field, ?int $default = null, bool $isMandatory = false): ?int
    {
        if($isMandatory && ($value === null || $value === '') && !isset($this->rawData['post_id'])){
            $this->addError($field, 'is mandatory');
            return null;
        }
        if ($value === null || $value === '') { return $default; }
        if (!is_numeric($value)) { $this->addError($field, 'must be a valid integer'); return $default; }
        $int = (int)$value;
        if ($int < 0) { $this->addError($field, 'must be a positive integer'); return $default; }
        return $int;
    }

    private function validateString($value, string $field, int $maxLength, bool $isMandatory = false): ?string
    {
        if($isMandatory && ($value === null || $value === '') && !isset($this->rawData['post_id'])){
            $this->addError($field, 'is mandatory');
            return null;
        }
        if ($value === null || $value === '') { return null; }
        if (!is_string($value)) { $this->addError($field, 'must be a string'); return null; }
        $s = trim($value);
        //Instead of resizing the string pelase add error
        if (strlen($s) > $maxLength) { $s = substr($s, 0, $maxLength); }
        return $s;
    }

    private function validateText($value, string $field, bool $isMandatory = false): ?string
    {
        if($isMandatory && ($value === null || $value === '') && !isset($this->rawData['post_id'])){
            $this->addError($field, 'is mandatory');
            return null;
        }
        if ($value === null || $value === '') { return null; }
        if (!is_string($value)) { $this->addError($field, 'must be a string'); return null; }
        return trim($value);
    }

    private function validateSlug($value): ?string
    {
        if ($value === null || $value === '') { return null; }
        if (!is_string($value)) { $this->addError('slug', 'must be a string'); return null; }
        $slug = strtolower(trim($value));
        // spaces → -
        $slug = preg_replace('/\s+/', '-', $slug);
        // remove all special characters except -
        $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
        // remove multiple -
        $slug = preg_replace('/-+/', '-', $slug);
        // trim - from start & end
        $slug = trim($slug, '-');
        return substr($slug, 0, 191);
    }

    private function validateJson($imageValue, string $field): ?string
    {
        if ($imageValue === '' || $imageValue === null) { return '[]'; }
        $imageValue = is_string($imageValue) ? $imageValue : (is_array($imageValue) ? json_encode($imageValue) : (string)$imageValue);
        if ($this->isValidJson($imageValue)) { 
            $this->addError($field, 'must be a valid JSON string');
            return $imageValue; 
        }
        // if (!str_contains($imageValue, '/media/Posts/')) { $imageValue = "/media/Posts/{$imageValue}"; }
        $data = [[ 'id'=>null,'file'=>['name'=>basename($imageValue),'size'=>0,'type'=>'image/jpeg','error'=>0,'tmp_name'=>$imageValue,'full_path'=>basename($imageValue)],'name'=>basename($imageValue),'size'=>0,'type'=>'image/jpeg','image'=>$imageValue,'status'=>['name'=>'Uploaded','severity'=>'success'],'media_id'=>null,'objectURL'=>$imageValue,'created_at'=>'','description'=>'','post_image_id'=>null,'project_image_id'=>null ]];
        return json_encode($data) ?: '[]';

        return $value;
    }
    private function isValidJson(string $string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    private function addError(string $field, string $message): void
    {
        $this->errors[$field] = $message;
        $this->isValidData = false;
    }

    public function validate(): bool|self
    {
        if (!$this->isValidData) { return false; }
        return $this;
    }

    public function getErrors(): array
    {
        return $this->errors;
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

    // private function validateText($value, string $field): ?string
    // {
    //     if ($value === null || $value === '') {
    //         return null;
    //     }

    //     if (!is_string($value)) {
    //         $this->addError($field, "must be a string");
    //         return null;
    //     }

    //     return trim($value);
    // }

    // private function validateSlug($value): ?string
    // {
    //     if ($value === null || $value === '') {
    //         return null;
    //     }

    //     if (!is_string($value)) {
    //         $this->addError('slug', "must be a string");
    //         return null;
    //     }

    //     $slug = trim($value);
    //     if (strlen($slug) > 191) {
    //         $this->addError('slug', "must not exceed 191 characters");
    //         return null;
    //     }

    //     // Basic slug validation - alphanumeric, hyphens, underscores
    //     if (!preg_match('/^[a-zA-Z0-9\-_]+$/', $slug)) {
    //         $this->addError('slug', "must contain only letters, numbers, hyphens, and underscores");
    //         return null;
    //     }

    //     return strtolower($slug);
    // }

    // private function validateJson($imageValue, string $field): ?string
    // {
    //     if ($imageValue === '' || $imageValue === null) { return '[]'; }
    //     $imageValue = is_string($imageValue) ? $imageValue : (is_array($imageValue) ? json_encode($imageValue) : (string)$imageValue);
    //     if ($this->isValidJson($imageValue)) { 
    //         $this->addError($field, 'must be a valid JSON string');
    //         return $imageValue; 
    //     }
    //     // if (!str_contains($imageValue, '/media/Posts/')) { $imageValue = "/media/Posts/{$imageValue}"; }
    //     $data = [[ 'id'=>null,'file'=>['name'=>basename($imageValue),'size'=>0,'type'=>'image/jpeg','error'=>0,'tmp_name'=>$imageValue,'full_path'=>basename($imageValue)],'name'=>basename($imageValue),'size'=>0,'type'=>'image/jpeg','image'=>$imageValue,'status'=>['name'=>'Expected','severity'=>'info'],'media_id'=>null,'objectURL'=>$imageValue,'created_at'=>'','description'=>'','post_image_id'=>null,'project_image_id'=>null ]];
    //     return json_encode($data) ?: '[]';

    //     return $value;
    // }
    // private function isValidJson(string $string): bool
    // {
    //     json_decode($string);
    //     return json_last_error() === JSON_ERROR_NONE;
    // }

    private function validateBoolean($value, string $field): bool
    {
        if ($value === null || $value === '') {
            return false;
        }

        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (bool) $value;
        }

        if (is_string($value)) {
            $lowerValue = strtolower(trim($value));
            if (in_array($lowerValue, ['true', '1', 'yes', 'on'])) {
                return true;
            }
            if (in_array($lowerValue, ['false', '0', 'no', 'off'])) {
                return false;
            }
        }

        $this->addError($field, "must be a valid boolean value");
        return false;
    }

    // private function addError(string $field, string $message): void
    // {
    //     $this->errors[$field] = $message;
    //     $this->isValidData = false;
    // }

    // public function validate(): bool|array
    // {
    //     // Additional business logic validations
    //     if (empty($this->name) && empty($this->title)) {
    //         $this->addError('name', "either name or title is required");
    //     }

    //     if ($this->site_id <= 0) {
    //         $this->addError('site_id', "must be greater than 0");
    //     }

    //     if ($this->status_id <= 0) {
    //         $this->addError('status_id', "must be greater than 0");
    //     }

    //     if ($this->customer_id !== null && $this->customer_id <= 0) {
    //         $this->addError('customer_id', "must be greater than 0 when provided");
    //     }

    //     if (!$this->isValidData) {
    //         return false;
    //     }

    //     return $this->toArray();
    // }

    // public function isValid(): bool
    // {
    //     return $this->isValidData;
    // }

    // public function getErrors(): array
    // {
    //     return $this->errors;
    // }


    public function toArray(): array
    {
        // Always return a consistent structure with all fields
        $data = [
            'project_id' => $this->project_id,
            'site_id' => $this->site_id,
            'status_id' => $this->status_id,
            'customer_id' => $this->customer_id,
            'name' => $this->name ?? '',
            'slug' => $this->slug ?? '',
            'description' => $this->description ?? '',
            'preview_text' => $this->preview_text ?? '',
            'location' => $this->location ?? '',
            'designer' => $this->designer ?? '',
            'photographer' => $this->photographer ?? '',
            'status' => $this->status ?? '',
            'image' => $this->image ?? '',
            'image_thumb' => $this->image_thumb ?? '',
            'meta_title' => $this->meta_title ?? '',
            'meta_description' => $this->meta_description ?? '',
            'meta_keywords' => $this->meta_keywords ?? '',
            'title' => $this->title ?? '',
            'label' => $this->label ?? '',
            'keyline_quote' => $this->keyline_quote ?? '',
            'link_text' => $this->link_text ?? '',
            'is_featured' => $this->is_featured ? 1 : 0,
            'main_title' => $this->main_title ?? '',
            'main_description_one' => $this->main_description_one ?? '',
            'main_description_two' => $this->main_description_two ?? '',
            'main_description_three' => $this->main_description_three ?? '',
            'main_description_four' => $this->main_description_four ?? '',
            'main_image_one' => $this->main_image_one ?? '',
            'main_image_two' => $this->main_image_two ?? '',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        return $data;
    }

    public function getUniqueIdentifier(): string
    {
        if ($this->project_id !== null) {
            return "project_id:{$this->project_id}";
        }
        if ($this->slug !== null) {
            return "slug:{$this->slug}";
        }
        if ($this->name !== null) {
            return "name:{$this->name}";
        }
        return "unknown:" . md5(serialize($this->rawData));
    }
}
