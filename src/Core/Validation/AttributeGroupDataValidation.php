<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;

class AttributeGroupDataValidation
{
    private bool $isValidData = true;
    public bool $isExistingData = false;
    private array $errors = [];
    private array $rawData = [];

    public stdClass $attribute_group;
    public stdClass $attribute_group_content;

    private array $nullableIntegerFields = [
        'attribute_group_id',
        'sort_order',
    ];

    private array $requiredFields = [
        'name',
        'language_id',
    ];

    public function __construct(array $data, array $existingData = ["attributeGroupContentMap" => [], 'attributeGroupIds' => [], 'languageMap' => []])
    {
        $this->rawData = $data;

        $this->attribute_group = new stdClass();
        $this->attribute_group_content = new stdClass();

        if(isset($data['attribute_group_id'])) {
            $this->attribute_group_content->attribute_group_id = $this->validateInteger($data['attribute_group_id'], 'attribute_group_id', 0);
            if(isset($existingData['attributeGroupIds'][$data['attribute_group_id']])) {
                $this->isExistingData = true;
            }
        }
        // ATTRIBUTE TABLE
        if(isset($data['name']) && !empty($data['name']) && $data['name']){
            $this->attribute_group_content->name = $this->validateString($data['name'], 'name', 191);
            $this->attribute_group_content->language_id = $existingData['languageMap'][$data['language_code']]??1;
            $code = str_replace(' ', '-', strtolower(trim($this->attribute_group_content->name)));
            if(isset($existingData['attributeGroupContentMap'][$code])){
                $this->attribute_group_content->attribute_group_id = $existingData['attributeGroupContentMap'][$code];
                $this->isExistingData = true;
            }else{
                $this->attribute_group->sort_order = $this->validateInteger($data['sort_order'] ?? 0, 'sort_order', 0);
                if(!count($this->errors)){
                    $this->attribute_group->code = $code;
                    if(!$this->isExistingData){
                        $this->attribute_group_content->code = $this->attribute_group->code;
                    }
                }
            };
        }else{
            $this->addError('name', 'Attribute group name is required');
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
            'attribute_group' => (array)$this->attribute_group,
            'attribute_group_content' => (array)$this->attribute_group_content,
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
        if (!empty($this->attribute_content->name)) {
            return 'attribute_' . $this->attribute_content->name;
        }

        // Fallback to a generated unique ID
        return 'unique_' . uniqid();
    }

    public function getAttributeGroupUniqueIdentifier(): string
    {
        // unique identifi group name and language code.
        if (!empty($this->attribute_group_content->name)) {
            return 'attribute_' . $this->attribute_group_content->name;
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
