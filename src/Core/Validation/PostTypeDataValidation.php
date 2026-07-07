<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;

class PostTypeDataValidation extends Validation
{
    public stdClass $postType;

    public function __construct(array $data, array $requiredFields = [], array $textFields = [], array $existingData = [])
    {
        $this->rawData = $data;
        parent::__construct($requiredFields, $textFields);
        $this->postType = new stdClass();
        $image_path = '/media/Post-type/'; // TODO: change to the actual path

        // POST TYPE ID
        if (isset($data['post_type_id'])) {
            $this->postType->post_type_id = $this->validateInteger($data['post_type_id'], 'post_type_id', 0, true);
            if (isset($existingData[$data['name']])) {
                $this->postType->post_type_id = $existingData[$data['name']];
                $this->isExistingData = true;
            }
        }

        // POST TYPE DATA
        if (isset($data['name']) && !empty($data['name']) && $data['name']) {
            // required fields
            $this->postType->name = $this->validateString($data['name'], 'name', 191, true);
            $this->postType->type = $this->validateString($data['type'], 'type', 191, true);
            // optional fields
            $this->postType->plural = $this->validateString($data['plural'], 'plural', 191);
            $this->postType->icon = $this->validateString($data['icon'], 'icon', 191);
            $image = $this->validateString($data['image'], 'image', 191);
            $this->postType->image = $image_path . $image;
            $this->postType->source = $this->validateString($data['source'], 'source', 191);
            $this->postType->site_id = $this->validateInteger($data['site_id'], 'site_id', 0);
            // existing data
            if (isset($existingData[$this->postType->name])) {
                $this->postType->post_type_id = $existingData[$this->postType->name];
                $this->isExistingData = true;
            }
        } else {
            $this->addError('name', 'is mandatory');
        }
    }

    public function toArray(): array
    {
        return (array) $this->postType;
    }
}
