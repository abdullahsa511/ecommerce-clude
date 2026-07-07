<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;

class TypeDataValidation
{
    private bool $isValidData = true;
    public bool $isExistingData = false;
    private array $errors = [];
    private array $rawData = [];

    public stdClass $type;

    private array $nullableIntegerFields = [
        'type_id',
        'sort_order',
    ];

    private array $requiredFields = [
        'type',
        'type_id',
    ];

    public function __construct(array $data, array $existingData = ["typeContentMap" => [], 'typeIds' => []])
    {
        $this->rawData = $data;

        $this->type = new stdClass();

        if(isset($data['type_id'])) {
            $this->type->type_id = $this->validateInteger($data['type_id'], 'type_id', 0);
            if(isset($existingData['typeIds'][$data['type_id']])) {
                $this->isExistingData = true;
            }
        }

        if(isset($data['type']) && !empty($data['type']) && $data['type']){
            $this->type->type = $this->validateString($data['type'], 'type', 191);
            $this->type->sort_order = $this->validateInteger($data['sort_order'] ?? 0, 'sort_order', 0);
            
            $type = $this->type->type;
            if(isset($existingData['typeContentMap'][$type])){
                $this->type->type_id = $existingData['typeContentMap'][$type];
                $this->isExistingData = true;
            }else{
                $this->type->sort_order = $this->validateInteger($data['sort_order'] ?? 0, 'sort_order', 0);
                if(!count($this->errors)){
                    $this->type->type = $type;
                    if(!$this->isExistingData){
                        $this->type->type = $this->type->type;
                    }
                }
            };
        }else{
            $this->addError('type', 'Type is required');
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
            'type' => (array)$this->type,
        ];
    }

    public function getUniqueIdentifier(): string
    {
        // unique identifi group name and language code.
        if (!empty($this->type->type)) {
            return 'type_' . $this->type->type;
        }

        // Fallback to a generated unique ID
        return 'unique_' . uniqid();
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
