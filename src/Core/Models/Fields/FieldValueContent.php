<?php

declare(strict_types=1);

namespace App\Core\Models\Fields;

use App\Core\Models\Base\Model;

class FieldValueContent extends Model
{
    public int $field_value_id;
    public int $language_id;
    public int $field_id;
    public string $name;

    public function __construct() 
    {
        parent::__construct();
    }
} 