<?php

namespace App\Core\Models\Product;

use App\Core\Models\Base\Model;

class ProductAttributeContent extends Model
{
    protected string $primaryKey = 'product_attribute_content_id';

    protected array $fillable = [
        'product_attribute_id',
        'language_id',
        'name',
        'description'
    ];
    
    public function __construct() 
    {
        parent::__construct();
    }
} 