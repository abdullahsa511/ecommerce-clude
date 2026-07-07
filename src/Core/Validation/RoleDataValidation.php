<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;

class RoleDataValidation extends Validation
{
    public function __construct(array $data, array $requiredFields = [], array $textFields = [], array $existingData = [ 'roleIds' => [] ])
    {
        $this->rawData = $data;
        parent::__construct($requiredFields, $textFields);

        if (isset($data['role_id'])) {
            $this->rawData['role_id'] = $this->validateInteger($data['role_id'], 'role_id', 0, true);
            if (isset($existingData['roleIds'][$this->rawData['role_id']])) {
                $this->isExistingData = true;
            }
        }
        if (isset($data['name']) && !empty($data['name']) && $data['name']) {
            if (isset($existingData['roleIds'][$this->rawData['name']])) {
                $this->rawData['name'] = $existingData['roleIds'][$this->rawData['name']];
                $this->isExistingData = true;
            } else {
                if (!count($this->errors)) {
                    // name
                    $this->rawData['name'] = $this->validateString($data['name'], 'name', 191);
                    // display_name
                    $this->rawData['display_name'] = $this->validateString($data['display_name'], 'display_name', 191);
                    // permissions
                    $this->rawData['permissions'] = $this->validateString($data['permissions'], 'permissions', 191);

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
