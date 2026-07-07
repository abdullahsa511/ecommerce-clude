<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;
class DesignResourceDocumentDataValidation extends Validation
{
    public stdClass $design_resource_document;

    public function __construct(array $data, array $requiredFields = [])
    {
        $this->rawData = $data;
        $textFields = ['name', 'type', 'path', 'file', 'meta', 'parent_id', 'folder_id'];
        parent::__construct($requiredFields, $textFields);
        $this->design_resource_document = new stdClass();
        if(isset($data['name'])) $this->design_resource_document->name = $this->validateString($data['name'], 'name', 191);
        if(isset($data['description'])) $this->design_resource_document->description = $this->validateText($data['description'], 'description');
        if(isset($data['type'])) $this->design_resource_document->format = $this->validateString($data['type'], 'format', 191);
        if(isset($data['path'])) $this->design_resource_document->url = $this->validateString($data['path'], 'url', 500);
    }

    public function toArray(): array
    {
        return  (array)$this->design_resource_document;
    }

    
}
