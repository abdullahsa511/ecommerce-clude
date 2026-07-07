<?php

declare(strict_types=1);

namespace App\Core\Models\Attribute;

use App\Core\Models\Base\Model;

class AttributeContent extends Model
{
    protected string $table = 'attribute_content'; 
    protected string $primaryKey = 'attribute_id';

    public int $attribute_id;
    public int $language_id;
    public string $name;
    public string $created_at;
    public string $updated_at;

    public function __construct()
    {
        parent::__construct();
    }
} 