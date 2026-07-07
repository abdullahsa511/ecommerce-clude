<?php

declare(strict_types=1);

namespace App\Core\Models\Product;

use App\Core\Models\Base\Model;
use App\Core\Models\Option\Option;
use App\Core\Models\Option\OptionValue;
use App\Core\Models\Option\OptionContent;
use App\Core\Models\Option\OptionValueContent;

class ProductOptionValue extends Model
{
    protected string $table = 'product_option_value';
    protected string $tableAlias = 'pov';
    protected string $primaryKey = 'product_option_value_id';
    protected array $fillable = [
        'product_id',
        'product_option_id',
        'option_id',
        'option_value_id',
        'quantity',
        'subtract',
        'price',
        'price_prefix',
        'points',
        'points_prefix',
        'weight',
        'weight_prefix'
    ];

    /**
     * Product Option Value ID
     */
    public int $product_option_value_id;

    /**
     * Product ID
     */
    public int $product_id;

    /**
     * Option ID
     */
    public int $option_id;

    /**
     * Option Value ID
     */
    public int $option_value_id;

    /**
     * Quantity
     */
    public int $quantity;

    /**
     * Subtract from stock
     */
    public bool $subtract;

    /**
     * Price
     */
    public float $price;

    /**
     * Price prefix (+ or -)
     */
    public string $price_prefix;

    /**
     * Points
     */
    public int $points;

    /**
     * Points prefix (+ or -)
     */
    public string $points_prefix;

    /**
     * Weight
     */
    public float $weight;

    /**
     * Weight prefix (+ or -)
     */
    public string $weight_prefix;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Define relationship with Option model
     */
    public function option()
    {
        return $this->belongsTo(Option::class, 'option_id');
    }

    /**
     * Define relationship with OptionValue model
     */
    public function optionValue()
    {
        return $this->belongsTo(OptionValue::class, 'option_value_id');
    }

    /**
     * Define relationship with OptionContent model
     */
    public function optionContent()
    {
        return $this->belongsTo(OptionContent::class, 'option_id', 'option_id');
    }

    /**
     * Define relationship with OptionValueContent model
     */
    public function optionValueContent()
    {
        return $this->belongsTo(OptionValueContent::class, 'option_value_id', 'option_value_id');
    }
} 