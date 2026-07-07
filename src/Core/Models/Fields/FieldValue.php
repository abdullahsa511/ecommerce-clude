<?php

declare(strict_types=1);

namespace App\Core\Models\Fields;

use App\Core\Models\Base\Model;

class FieldValue extends Model
{
    public int $field_value_id;
    public int $field_id;
    public int $sort_order;

    public function __construct() 
    {
        parent::__construct();
    }
} 