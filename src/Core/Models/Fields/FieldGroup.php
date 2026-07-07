<?php

declare(strict_types=1);

namespace App\Core\Models\Fields;

use App\Core\Models\Base\Model;

class FieldGroup extends Model
{
    public int $field_group_id;
    public string $type;
    public int $status;
    public int $sort_order;
        
    public function __construct() 
    {
        parent::__construct();
    }

    /**
     * Define relationship with Field model
     */
    public function field()
    {
        return $this->hasMany(Field::class, 'field_group_id');
    }

    /**
     * Define relationship with FieldGroupContent model
     */
    public function fieldGroupContent()
    {
        return $this->hasMany(FieldGroupContent::class, 'field_group_id');
    }
} 