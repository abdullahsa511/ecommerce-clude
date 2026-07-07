<?php

declare(strict_types=1);

namespace App\Core\Models\Fields;

use App\Core\Models\Base\Model;

class FieldContent extends Model
{
    public int $field_id;
    public int $language_id;
    public string $name;

    public function __construct() 
    {
        parent::__construct();
    }
} 