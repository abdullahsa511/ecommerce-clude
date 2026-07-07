<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;

class TaxonomyDataValidation extends Validation
{
    public stdClass $taxonomy;

    public function __construct(array $data, array $requiredFields = [], array $textFields = [], array $existingData = [])
    {
        $this->rawData = $data;
        parent::__construct($requiredFields, $textFields);
        $this->taxonomy = new stdClass();

        // TAXONOMY ID
        if (isset($data['taxonomy_id'])) {
            $this->taxonomy->taxonomy_id = $this->validateInteger($data['taxonomy_id'], 'taxonomy_id', 0, true);
            if (isset($existingData[$data['name']])) {
                $this->taxonomy->taxonomy_id = $existingData[$data['name']];
                $this->isExistingData = true;
            }
        }

        // TAXONOMY DATA
        if (isset($data['name']) && !empty($data['name']) && $data['name']) {
            // required fields
            $this->taxonomy->name = $this->validateString($data['name'], 'name', 191, true);
            $this->taxonomy->post_type = $this->validateString($data['post_type'], 'post_type', 191, true);
            $this->taxonomy->type = $this->validateString($data['type'], 'type', 191, true);
            $this->taxonomy->site_id = $this->validateInteger($data['site_id'], 'site_id', 0);
            // existing data
            if (isset($existingData[$this->taxonomy->name])) {
                $this->taxonomy->taxonomy_id = $existingData[$this->taxonomy->name];
                $this->isExistingData = true;
            }
        } else {
            $this->addError('name', 'is mandatory');
        }
    }

    public function toArray(): array
    {
        return (array) $this->taxonomy;
    }
}
