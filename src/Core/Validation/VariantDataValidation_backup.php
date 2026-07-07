<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;

class VariantDataValidation extends Validation
{
    public stdClass $variant;
    // public stdClass $variant_content;

    public function __construct(array $data, array $requiredFields = [], array $textFields = [], array $existingData = ["variantContentMap" => [], 'variantIds' => [], 'languageMap' => []])
    {
        $this->rawData = $data;
        parent::__construct($requiredFields, $textFields);
        $this->variant = new stdClass();
        $this->variant_content = new stdClass();

        if(isset($data['variant_id'])) {
            $this->variant_content->variant_id = $this->validateInteger($data['variant_id'], 'variant_id', 0, true);
            if(isset($existingData['variantIds'][$data['variant_id']])) {
                $this->isExistingData = true;
            }
        }
        // VARIANT CONTENT TABLE
        if(isset($data['name']) && !empty($data['name']) && $data['name']){
            $this->variant_content->name = $this->validateString($data['name'], 'name', 191);
            $this->variant_content->language_id = isset($data['language_code']) ? $existingData['languageMap'][$data['language_code']] : 1;
            $code = str_replace(' ', '-', strtolower(trim($this->variant_content->name)));
            if(isset($existingData['variantContentMap'][$code])){
                $this->variant_content->variant_id = $existingData['variantContentMap'][$code];
                $this->isExistingData = true;
            }else{
                $this->variant->sort_order = $this->validateInteger($data['sort_order'] ?? 0, 'sort_order', 0, true);
                if(!count($this->errors)){
                    // VARIANT CODE
                    $this->variant->code = $code;
                    if(!$this->isExistingData){
                        $this->variant_content->code = $this->variant->code;
                    }
                }
            };
        }else{
            $this->addError('name', 'is mandatory');
        }
    }

    public function toArray(): array
    {
        return [
            'variant' => (array)$this->variant,
            'variant_content' => (array)$this->variant_content,
        ];
    }
}
