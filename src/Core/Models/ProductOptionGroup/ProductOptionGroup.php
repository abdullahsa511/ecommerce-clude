<?php

declare(strict_types=1);

namespace App\Core\Models\ProductOptionGroup;

use App\Core\Models\Base\Model;
use App\Core\Models\Variant\Variant;
use App\Core\Models\Product\ProductOption;

class ProductOptionGroup extends Model
{
    protected string $table = 'product_option_group';
    protected string $primaryKey = 'product_option_group_id';

    public int|null $product_option_group_id;
    public int|null $product_id;
    public int|null $product_variant_id;
    public string|null $option_group_name;
    public string|null $option_group_description;
    public int|null $sort_order;
    public int|null $active_status;
    public string|null $created_at;
    public string|null $updated_at;
    public string|null $deleted_at;

    public function __construct() 
    {
        parent::__construct();
    }

    /**
     * Define relationship with variant model
     */
    public function productVariant()
    {
        return $this->belongsTo(Variant::class, 'product_variant_id');
    }

    // with option group to product option
    public function productOptions()
    {
        return $this->hasMany(ProductOption::class, 'product_option_group_id');
    }

    
} 