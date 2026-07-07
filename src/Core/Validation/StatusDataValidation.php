<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;

class StatusDataValidation extends Validation
{

    public stdClass $status;

    private array $nullableIntegerFields = [
        'sort_order',
    ];
    public function __construct(array $data, array $requiredFields = [], array $textFields = [], string $primaryKey, array $existingData = ['statusIds' => [], 'languageMap' => []])
    {
        $this->rawData = $data;
        parent::__construct($requiredFields, $textFields);
        $this->status = new stdClass();

        $this->nullableIntegerFields[] = $primaryKey;

        $this->status = new stdClass();

        if(isset($data[$primaryKey])) {
            if(isset($existingData['statusIds'][$data[$primaryKey]])) {
                $this->isExistingData = true;
            }
        }
        // ATTRIBUTE TABLE
        if(isset($data['name']) && !empty($data['name']) && $data['name']){
            $this->status->name = $this->validateString($data['name'], 'name', 191);
        }else{
            $this->addError('name', 'Status name is required');
        }
        if(isset($data['language_code']) && !empty($data['language_code']) && $data['language_code']){
            if(isset($existingData['languageMap'][$data['language_code']])){
                $language_id = $existingData['languageMap'][$data['language_code']];
                $this->status->language_id = $language_id;
            }else{
                $this->addError('language_code', 'Invalid Language Code');
            }
        }
    }

    public function toArray(): array
    {
        return (array)$this->status;
    }
}
