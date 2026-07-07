<?php

declare(strict_types=1);

namespace App\Core\Models\Product;

use App\Core\Models\Base\Model;
use App\Core\Models\Option\Option;

class ProductOption extends Model
{
    protected string $table = 'product_option';
    protected string $primaryKey = 'product_option_id';

    public int $product_option_id;
    public int $product_id;
    public int $product_variant_id;
    public int $product_option_group_id;
    public string $option_name;
    public int $sort_order;
    public int $active_status;
    public string|null $hex_color;
    public string|null|array $option_image;
    public string $created_at;

    /**
     * Get the product that owns this option
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get the option that this product option belongs to
     */
    public function option()
    {
        return $this->belongsTo(Option::class, 'option_id');
    }

    public function __construct() 
    {
        parent::__construct();
    }
} 