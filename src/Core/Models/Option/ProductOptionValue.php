<?php

declare(strict_types=1);

namespace App\Core\Models\Option;

use App\Core\Models\Base\Model;

class ProductOptionValue extends Model
{
    public int $product_option_value_id;
    public int $product_option_id;
    public int $product_id;
    public int $option_id;
    public int $option_value_id;
    public int $quantity;
    public int $subtract;
    public string $price_operator;
    public float $price;
    public string $points_operator;
    public int $points;
    public string $weight_operator;
    public float $weight;

    /**
     * Define relationship with ProductOption model
     */
    public function productOption()
    {
        return $this->hasOne(ProductOption::class, 'product_option_id');
    }
    public function optionValue()
    {
        return $this->hasOne(OptionValue::class, 'option_value_id');
    }

    /**
     * Define relationship with Option model
     */
    public function option()
    {
        return $this->hasOne(Option::class, 'option_id');
    }

    public function __construct() 
    {
        parent::__construct();
    }
} 