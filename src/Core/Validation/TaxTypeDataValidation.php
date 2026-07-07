<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;

class TaxTypeDataValidation extends Validation
{
    public function __construct(array $data, array $requiredFields = [], array $textFields = [], array $existingData = [])
    {
        $this->rawData = $data;
        parent::__construct($requiredFields, $textFields);

        if (isset($data['tax_type_id'])) {
            $this->rawData['tax_type_id'] = $this->validateInteger($data['tax_type_id'], 'tax_type_id', 0, true);
            if (isset($existingData[$this->rawData['tax_type_id']])) {
                $this->isExistingData = true;
            }
        }
        if (isset($data['name']) && !empty($data['name']) && $data['name']) {
            if (isset($existingData[$this->rawData['name']])) {
                $this->rawData['name'] = $existingData[$this->rawData['name']];
                $this->isExistingData = true;
            } else {
                if (!count($this->errors)) {
                    // name
                    $this->rawData['name'] = $this->validateString($data['name'], 'name', 191);
                    // content
                    $this->rawData['content'] = isset($data['content']) ? $this->validateString($data['content'], 'content', 191) : null;

                    if (!$this->isExistingData) {
                        $this->isExistingData = false;
                    }
                }
            };
        } else {
            $this->addError('name', 'is mandatory');
        }
    }

    public function toArray(): array
    {
        return [
            'data' => (array)$this->rawData,
        ];
    }
}
