<?php

declare(strict_types=1);

namespace App\Core\Models\Design;


use App\Core\Models\Base\Model;
use App\Core\Models\Media\Media;

class DesignResourceDocument extends Model
{
    protected string $table = 'design_resource_document';
    protected string $primaryKey = 'design_resource_document_id';

    public int $design_resource_document_id;
    public int $design_resource_id;
    public ?int $media_id;
    public ?string $name;
    public ?string $description;
    public ?string $format;
    public ?string $url;
    public ?string $created_at;
    public ?string $updated_at;

    public function media() 
    {
        return $this->hasOne(Media::class, 'media_id', 'media_id');
    }
    
}
