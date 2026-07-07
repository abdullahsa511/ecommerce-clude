<?php

declare(strict_types=1);

namespace App\Core\Validation;

use stdClass;
class MediaDataValidation extends Validation
{
    public stdClass $media;

    public function __construct(array $data, array $requiredFields = [])
    {
        $this->rawData = $data;
        $textFields = ['name', 'type', 'path', 'file', 'meta', 'parent_id', 'folder_id'];
        parent::__construct($requiredFields, $textFields);
        $this->media = new stdClass();
        if(isset($data['name'])) $this->media->name = $this->validateString($data['name'], 'name', 191);
        if(isset($data['type'])) $this->media->type = $this->validateString($data['type'], 'type', 191);
        if(isset($data['path'])) $this->media->path = $this->validateString($data['path'], 'path', 191);
        if(isset($data['file'])) $this->media->file = $this->validateJson($data['file'], 'file');
        if(isset($data['meta'])) $this->media->meta = $this->validateText($data['meta'], 'meta');
        if(isset($data['parent_id'])) $this->media->parent_id = $this->validateString($data['parent_id'], 'parent_id', 191);
        if(isset($data['folder_id'])) $this->media->folder_id = $this->validateString($data['folder_id'], 'folder_id', 191);
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

    }

    public function toArray(): array
    {
        return  (array)$this->media;
    }

    
}
