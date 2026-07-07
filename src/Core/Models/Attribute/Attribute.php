<?php

declare(strict_types=1);

namespace App\Core\Models\Attribute;

use App\Core\Models\Base\Model;
use App\Core\Models\Product\ProductAttribute;

class Attribute extends Model
{

    protected string $table = 'attribute'; 
    protected string $primaryKey = 'attribute_id';

    public int $attribute_id;
    public int $attribute_group_id;
    public int $sort_order;
    // public string $name;
    public string $code;
    public string|null $description;
    public string|null $metadata;
    public string|null $type;
    public string|null $value;
    public string|null $image;
    
    public function __construct() 
    {
        parent::__construct();
    }

    /**
     * Define relationship with ProductAttribute model
     */
    public function productAttributes()
    {
        return $this->hasMany(ProductAttribute::class, 'attribute_id');
    }

    /**
     * Get attribute content for specific language
     */
    public function content()
    {
        return $this->hasOne(AttributeContent::class, 'attribute_id');
    }

    /**
     * Get attribute group content for specific language
     */
    public function groupContent()
    {
        return $this->hasOne(AttributeGroupContent::class, 'attribute_group_id', 'attribute_group_id');
    }
} 