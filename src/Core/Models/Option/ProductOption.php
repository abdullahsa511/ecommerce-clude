<?php

declare(strict_types=1);

namespace App\Core\Models\Option;

use App\Core\Models\Base\Model;

class ProductOption extends Model
{
    public int $product_option_id;
    public int $product_id;
    public int $option_id;
    public string $option_name;
    public string $option_description;
    public float $price;
    public int $required;
    public int $type_id;
    public string $type;
    public int $sort_order;
    public int $active_status;
    public string $created_at;
    public string $updated_at;
    public string $deleted_at;

    /**
     * Define relationship with Option model
     */
    public function option()
    {
        return $this->hasOne(Option::class, 'option_id');
    }

    /**
     * Define relationship with ProductOptionValue model
     */
    public function productOptionValue()
    {
        return $this->hasMany(ProductOptionValue::class, 'product_option_id');
    }

    public function __construct() 
    {
        parent::__construct();
    }
} 