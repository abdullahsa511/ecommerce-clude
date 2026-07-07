<?php

declare(strict_types=1);

namespace App\Core\Models\Fields;

use App\Core\Models\Base\Model;

class Field extends Model
{
    public int $field_id;
    public int $field_group_id;
    public string $type;
    public string $value;
    public int $status;
    public int $sort_order;
        
    public function __construct() 
    {
        parent::__construct();
    }

    /**
     * Define relationship with FieldGroup model
     */
    public function fieldGroup()
    {
        return $this->hasOne(FieldGroup::class, 'field_group_id');
    }

    /**
     * Define relationship with FieldContent model
     */
    public function fieldContent()
    {
        return $this->hasMany(FieldContent::class, 'field_id');
    }

    /**
     * Define relationship with FieldValue model
     */
    public function fieldValue()
    {
        return $this->hasMany(FieldValue::class, 'field_id');
    }
} 