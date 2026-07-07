<?php

declare(strict_types=1);

namespace App\Core\Models\Attribute;

use App\Core\Models\Base\Model;

class AttributeGroup extends Model
{
    public int $attribute_group_id;
    public int $sort_order;
    public bool $soft_delete = true;
    
    public function __construct() 
    {
        parent::__construct();
    }
    /**
     * Define relationship with Attribute model
     */
    public function attribute()
    {
        return $this->hasMany(Attribute::class, 'attribute_group_id');
    }

    /**
     * Define relationship with AttributeGroupContent model
     */
    public function attributeGroupContent()
    {
        return $this->hasMany(AttributeGroupContent::class, 'attribute_group_id');
    }
} 