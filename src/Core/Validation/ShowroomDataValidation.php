<?php

declare(strict_types=1);

namespace App\Core\Validation;
use stdClass;

class ShowroomDataValidation
{
    private bool $isValidData = true;
    private array $errors = [];
    private array $rawData = [];
    private array $requiredFields = [];
    private array $textFields = [];
    public bool $isExistingData = false;
    private stdClass $section;

    /**
     * @param array $categories = [ 'Workstations' => 1, 'Screens' => 2, 'Gaming' => 3, ... ]
     */

    public function __construct(array $data, array $textFields = [], array $sectionMaps = [])
    {
        $this->section = new stdClass();
        // Clean up nullable integer fields to ensure they are either valid integers or null
        $nullableIntegerFields = [
            
        ];
         // Clean up nullable decimal fields to ensure they are either valid decimals or null
         $nullableDecimalFields = [
           
        ];

          // Ensure required fields that cannot be null are properly set
        $this->requiredFields = [
            
        ];

        $imageFields = [
            
        ];

        $sectionIds = array_values($sectionMaps);
    
      
        if(isset($data['showroom_id'])) $this->section->showroom_id = $this->validateInteger($data['showroom_id'], 'showroom_id') ?? null;
        if(isset($data['section_id'])) {
            $this->section->project_sections_id = $this->validateInteger($data['section_id'], 'section_id') ?? null;
            if(in_array($this->section->project_sections_id, $sectionIds)){
                $this->isExistingData = true;
            }
        }
        // String fields
        if(isset($data['title'])) $this->section->title = $this->validateString($data['title'], 'title', 500) ?? '';
        if(isset($data['description'])) $this->section->description = $this->validateString($data['description'], 'description', 500) ?? '';
        // JSON fields

        $showroom_id = isset($data['showroom_id']) ? $data['showroom_id'] : 1;
        $imagePath = match ((int) $showroom_id) {
            1 => '/media/showrooms/sections/sydney',
            2 => '/media/showrooms/sections/melbourne',
            3 => '/media/showrooms/sections/brisbane',
            default => '/media/showrooms/sections/sydney',
        };


        if(isset($data['image'])) $this->section->image = $this->validateJson($data['image'], 'image', $imagePath) ?? null;
        $this->section->slug = $this->generateSlugFromName($data['title'], 'slug') ?? '';
        if(isset($data['status'])) $this->section->status = $this->validateString($data['status'], 'status', 50) ?? 'Active';
        if(isset($data['section_code'])) {
            $this->section->section_code = $this->validateString($data['section_code'], 'section_code', 50) ?? 'Active';
            if(isset($sectionMaps[$this->section->section_code.'-'.$this->section->showroom_id])){
                $this->isExistingData = true;
            }
        }
    }


    private function generateSlugFromName(string $value, string $field): string
    {
        $value = $this->fixTextEncoding($value, $field);
        if (!$value) return '';
        return $this->validateSlug($value, $field) ?? '';
    }

    private function validateInteger($value, string $field, ?int $default = null, bool $isMandatory = false): ?int
    {
        $value = $this->fixTextEncoding($value, $field);
        if($isMandatory && ($value === null || $value === '') && !isset($this->rawData['product_id'])){
            $this->addError($field, 'is mandatory');
            return null;
        }
        if ($value === null || $value === '') { return $default; }
        if (!is_numeric($value)) { $this->addError($field, 'must be a valid integer'); return $default; }
        $int = (int)$value;
        if ($int < 0) { $this->addError($field, 'must be a positive integer'); return $default; }
        if(in_array($field, $this->requiredFields) && (($value == '') || $value === null)){
            $this->addError($field, 'is mandatory');
            return null;
        }
        return $int;
    }

    private function validateString($value, string $field, int $maxLength, bool $isMandatory = false): ?string
    {
        $value = $this->fixTextEncoding($value, $field);
        if($isMandatory && ($value === null || $value === '') && !isset($this->rawData['product_id'])){
            $this->addError($field, 'is mandatory');
            return null;
        }
        if ($value === null || $value === '') { return null; }
        if (!is_string($value)) { $this->addError($field, 'must be a string'); return null; }
        $s = trim($value);
        if (strlen($s) > $maxLength) { $s = substr($s, 0, $maxLength); }
        if(in_array($field, $this->requiredFields) && (($value == '') || $value === null)){
            $this->addError($field, 'is mandatory');
            return null;
        }
        return $s;
    }

    // private function validateSlug($value, string $field): ?string
    // {
    //     $value = $this->fixTextEncoding($value, $field);
    //     if ($value === null || $value === '') { return null; }
    //     if (!is_string($value)) { $this->addError($field, 'must be a string'); return null; }
    //     $slug = strtolower(trim($value));
    //     $slug = preg_replace('/[^a-z0-9\-_]/', '-', $slug);
    //     $slug = preg_replace('/-+/', '-', $slug);
    //     if(in_array($field, $this->requiredFields) && (($value == '') || $value === null)){
    //         $this->addError($field, 'is mandatory');
    //         return null;
    //     }
    //     return substr(trim($slug, '-'), 0, 191);
    // }
    
    private function validateSlug($value, string $field): ?string
    {
        $value = $this->fixTextEncoding($value, $field);

        if ($value === null || $value === '') {
            if (in_array($field, $this->requiredFields)) {
                $this->addError($field, 'is mandatory');
            }
            return null;
        }

        if (!is_string($value)) {
            $this->addError($field, 'must be a string');
            return null;
        }

        $slug = strtolower(trim($value));
        // spaces → -
        $slug = preg_replace('/\s+/', '-', $slug);
        // remove all special characters except -
        $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
        // remove duplicate -
        $slug = preg_replace('/-+/', '-', $slug);
        // trim - from start & end
        $slug = trim($slug, '-');

        return substr($slug, 0, 191);
    }

    private function validateJson($value, string $field, string $path = ''): ?string
    {
        $value = $this->fixTextEncoding($value, $field);
        if ($value === '' || $value === null) { return '[]'; }
        $value = is_string($value) ? $value : (is_array($value) ? json_encode($value) : (string)$value);
        if (!$this->isValidJson($value)) { 
            // If not JSON, create a simple JSON structure for products
            if (!str_contains($value, $path)) { $value = "{$path}/{$value}"; }
            $data = [[ 'id'=>null,'file'=>['name'=>basename($value),'size'=>0,'type'=>'image/jpeg','error'=>0,'tmp_name'=>$value,'full_path'=>basename($value)],'name'=>basename($value),'size'=>0,'type'=>'image/jpeg','image'=>$value,'status'=>['name'=>'Uploaded','severity'=>'success'],'media_id'=>null,'objectURL'=>$value,'created_at'=>'','description'=>'','product_image_id'=>null ]];
            return json_encode($data) ?: '[]';
        }
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

    public function toArray(): array
    {
        return (array)$this->section;
    }

    public function getUniqueIdentifier(): string
    {
        
        return 'unique_' . uniqid();
    }


    private function fixTextEncoding(string|int|float|null $value, string $field): string|int|float|null
    {
        $textFields = $this->textFields;
        if(in_array($field, $textFields)){
            if (isset($value) && is_string($value) && $value !== '') {
                if (mb_check_encoding($value, 'UTF-8')) {
                    $replacements = [
                        "\x92" => "'",
                        "\x93" => '"',
                        "\x94" => '"',
                        "\x96" => "–",
                        "\x97" => "—",
                        "\x85" => "…",
                        "\x91" => "'",
                        "\x82" => ",",
                        "\x84" => "„",
                        "\x8B" => "‹",
                        "\x9B" => "›",
                    ];
                    $value = strtr($value, $replacements);
                }
            }
            $value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
        }
        return $value;
    }
}
