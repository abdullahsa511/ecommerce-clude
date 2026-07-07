<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;

class ShowroomContactDataValidation extends Validation
{
    public stdClass $showroomContact;

    public function __construct(
        array $data, 
        array $requiredFields = [], 
        array $textFields = [], 
        array $existingShowroomContactIds = []
    ){
        $showroomIds = [
            'Sydney' => 1, 
            'Melbourne' => 2, 
            'Brisbane' => 3, 
            // lowercase showroom names
            'sydney' => 1, 
            'melbourne' => 2, 
            'brisbane' => 3
        ];

        $path = '/media/Showroom-contacts/';
        $this->rawData = $data;
        parent::__construct($requiredFields, $textFields);
        $this->showroomContact = new stdClass();

        if(isset($data['showroom_contact_id'])){
            $this->showroomContact->showroom_contact_id = $this->validateInteger($data['showroom_contact_id'], 'showroom_contact_id', 0, true);
            if(isset($existingShowroomContactIds[$data['showroom_contact_id']])){
                $this->showroomContact->showroom_contact_id = $existingShowroomContactIds[$data['showroom_contact_id']];
                $this->isExistingData = true;
            }
        }

        if(isset($data['name']) && isset($existingShowroomContactIds[trim($data['name'])])){
            $this->showroomContact->showroom_contact_id = $existingShowroomContactIds[trim($data['name'])];
            $this->isExistingData = true;
        }

        if(!isset($data['showroom'])){
            $this->addError('showroom', 'Showroom is required');
        }

        if(!isset($data['name'])){
            $this->addError('name', 'Name is required');
        }

        //Set manufacturer properties
        $this->showroomContact->name = $this->validateString(trim($data['name']), 'name', 191, true);
        $this->showroomContact->email = $this->validateString($data['email'], 'email', 191, true);
        $this->showroomContact->phone = $this->validateString($data['phone'], 'phone', 191, true);
        $this->showroomContact->designation = $this->validateString($data['designation'], 'designation', 191, true);
        $this->showroomContact->message = $this->validateString($data['message'], 'message', 191, false);
        $this->showroomContact->image = $this->validateJson($data['image'], 'image', $path);
        $this->showroomContact->sort_order = $this->validateInteger($data['sort_order'] ?? 0, 'sort_order');
        $this->showroomContact->status = $this->validateInteger($data['status'] ?? 1, 'status');
        $this->showroomContact->showroom_id = $this->validateInteger($showroomIds[$data['showroom']], 'showroom_id', 0, true);
    }

    public function toArray(): array
    {
        return (array) $this->showroomContact;
    }
}
