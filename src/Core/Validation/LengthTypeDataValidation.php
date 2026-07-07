<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;

class LengthTypeDataValidation
{
    private bool $isValidData = true;
    public bool $isExistingData = false;
    private array $errors = [];
    private array $rawData = [];

    public stdClass $lengthType;

    private array $nullableIntegerFields = [
        'length_type_id',
    ];

    private array $requiredFields = [
        'name',
        'unit',
        'value',
    ];

    public function __construct(array $data,  array $existingData = ["lengthTypeMap" => [], 'lengthTypeIds' => []])
    {
        $this->rawData = $data;

        $this->lengthType = new stdClass();


        if(isset($data['length_type_id'])) {
            $this->lengthType->length_type_id = $this->validateInteger($data['length_type_id'], 'value', 0);
            if(isset($existingData['lengthTypeIds'][$data['length_type_id']])) {
                $this->isExistingData = true;
            }
        }
        // ATTRIBUTE TABLE
        if(isset($data['name']) && !empty($data['name']) && $data['name']){
            $this->lengthType->name = $this->validateString($data['name'], 'name', 191);
            $this->lengthType->unit = $this->validateString($data['unit'], 'unit', 20, true);
            $this->lengthType->value = $this->validateInteger($data['value'], 'value');
            
            
            $name = $this->lengthType->name;
            if(isset($existingData['lengthTypeMap'][$name])){
                $this->lengthType->length_type_id = $existingData['lengthTypeMap'][$name];
                $this->isExistingData = true;
            }
            else{
                $this->lengthType->length_type_id = $this->validateInteger($data['length_type_id'] ?? Null, 'length_type_id', 0);
                if(!count($this->errors)){
                    $this->lengthType->name = $name;
                    if(!$this->isExistingData){
                        $this->lengthType->name = $this->lengthType->name;
                    }
                }
            };
        }else{
            $this->addError('name', 'Length Type name is required');
        }
    }

    private function validateInteger($value, string $field, ?int $default = 0, bool $isMandatory = false): ?int
    {
        $value = $this->fixTextEncoding($value, $field);
        // Nullable integer fields
        if (in_array($field, $this->nullableIntegerFields) && ($value == null || empty($value))) {
            return 0;
        }
        // Mandatory check
        if ($isMandatory && ($value === null || $value === '') && !isset($this->rawData['product_id'])) {
            $this->addError($field, 'is mandatory');
            return null;
        }

        // Default value if empty
        if ($value === null || $value === '') {
            return $default;
        }
        // Numeric check
        if (!is_numeric($value)) {
            $this->addError($field, 'must be a valid integer');
            return $default;
        }
        // Positive integer check
        $int = (int)$value;
        if ($int < 0) {
            $this->addError($field, 'must be a positive integer');
            return $default;
        }
        // Required field check
        if (in_array($field, $this->requiredFields) && (($value == '') || $value === null)) {
            $this->addError($field, 'is mandatory');
            return null;
        }
        return $int;
    }

    private function validateString($value, string $field, int $maxLength, bool $isMandatory = false): ?string
    {
        $value = $this->fixTextEncoding($value, $field);
        if ($isMandatory && ($value === null || $value === '') && !isset($this->rawData['product_id'])) {
            $this->addError($field, 'is mandatory');
            return null;
        }
        if ($value === null || $value === '') {
            return null;
        }
        if (!is_string($value)) {
            $this->addError($field, 'must be a string');
            return null;
        }
        $s = trim($value);
        if (strlen($s) > $maxLength) {
            $s = substr($s, 0, $maxLength);
        }
        if (in_array($field, $this->requiredFields) && (($value == '') || $value === null)) {
            $this->addError($field, 'is mandatory');
            return null;
        }
        return $s;
    }

    private function addError(string $field, string $message): void
    {
        $this->errors[$field] = $message;
        $this->isValidData = false;
    }

    public function validate(): bool|self
    {
        // If the data has already been marked as invalid, return false
        if (!$this->isValidData) {
            return false;
        }
        // If data is valid, return the current object instance for chaining
        return $this;
    }

    public function getErrors(): array
    {
        // Return the array of validation errors collected so far
        return $this->errors;
    }

    public function toArray(): array
    {
        return [
            'lengthType' => (array)$this->lengthType,
        ];
    }
    // public function attributeGroupToArray(): array
    // {
    //     return [
    //         'attribute_group' => (array)$this->attribute_group,
    //         'attribute_group_content' => (array)$this->attribute_group_content,
    //     ];
    // }

    public function getUniqueIdentifier(): string
    {
        if (!empty($this->lengthType->name)) {
            return 'length_type_' . $this->lengthType->name;
        }

        // Fallback to a generated unique ID
        return 'unique_' . uniqid();
    }

    public function getLengthTypeUniqueIdentifier(): string
    {
        // unique identifi group name and language code.
        if (!empty($this->lengthType->name)) {
            return 'lengthType_' . $this->lengthType->name;
        }

        // Fallback to a generated unique ID
        return 'unique_' . uniqid();
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
        $data = [[ 'id'=>null,'file'=>['name'=>basename($imageValue),'size'=>0,'type'=>'image/jpeg','error'=>0,'tmp_name'=>$imageValue,'full_path'=>basename($imageValue)],'name'=>basename($imageValue),'size'=>0,'type'=>'image/jpeg','image'=>$imageValue,'status'=>['name'=>'Expected','severity'=>'info'],'media_id'=>null,'objectURL'=>$imageValue,'created_at'=>'','description'=>'','post_image_id'=>null,'project_image_id'=>null ]];
        return json_encode($data) ?: '[]';

        return $value;
    }

    private function isValidJson(string $string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    private function validateSlug($value): ?string
    {
        if ($value === null || $value === '') { return null; }
        if (!is_string($value)) { $this->addError('slug', 'must be a string'); return null; }
        $slug = strtolower(trim($value));
        $slug = preg_replace('/[^a-z0-9\-_]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        return substr(trim($slug, '-'), 0, 191);
    }


    private function fixTextEncoding(string|int|float|null $value, string $field): string|int|float|null
    {
        $textFields = [
            'model',
            'description',
            'specifications',
            'warranty_period',
            'product_code',
            'factory_code',
            'sku',
            'isbn',
            'barcode',
            'material',
            'out_of_stock_status',
            'size',
            'date_available',
            'template',
            'video_link',
            'name',
            'slug',
            'meta_title',
            'meta_description',
            'meta_keywords',
            'main_image_one_title',
            'main_image_one_description',
            'main_image_two_title',
            'main_image_two_description',
            'feature_description',
            'feature_image_one_title',
            'feature_image_one_description',
            'feature_image_two_title',
            'feature_image_two_description',
            'feature_image_three_title',
            'feature_image_three_description'
        ];

        if (in_array($field, $textFields)) {
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
