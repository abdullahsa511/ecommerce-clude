<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;

class TaxRateDataValidation extends Validation
{
    public function __construct(array $data, array $requiredFields = [], array $textFields = [], array $existingData = [ 'taxRateIds' => [] ])
    {
        $this->rawData = $data;
        parent::__construct($requiredFields, $textFields);

        if (isset($data['tax_rate_id'])) {
            $this->rawData['tax_rate_id'] = $this->validateInteger($data['tax_rate_id'], 'tax_rate_id', 0, true);
            if (isset($existingData['taxRateIds'][$this->rawData['tax_rate_id']])) {
                $this->isExistingData = true;
            }
        }
        if (isset($data['name']) && !empty($data['name']) && $data['name']) {
            if (isset($existingData['taxRateIds'][$this->rawData['name']])) {
                $this->rawData['name'] = $existingData['taxRateIds'][$this->rawData['name']];
                $this->isExistingData = true;
            } else {
                if (!count($this->errors)) {
                    // name
                    $this->rawData['name'] = $this->validateString($data['name'], 'name', 191);
                    // region_group_id
                    $this->rawData['region_group_id'] = $this->validateInteger($data['region_group_id'], 'region_group_id', 0);
                    // rate
                    $this->rawData['rate'] = $this->validateFloat($data['rate'], 'rate', 0);
                    // type
                    $this->rawData['type'] = $this->validateString($data['type'], 'type', 1);

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
