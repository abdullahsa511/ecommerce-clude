<?php

declare(strict_types=1);

namespace App\Core\Models\Variant;

use App\Core\Models\Base\Model;
use App\Core\Models\Product\Product;
use App\Core\Models\Product\ProductOption;
use App\Core\Models\ProductOptionGroup\ProductOptionGroup;
use App\Core\Models\Variant\VariantContent;

class ProductVariant extends Model
{
    protected string $table = 'product_variant';
    protected string $primaryKey = 'product_variant_id';
    protected array $fillable = [
        // 'product_id',
        'variant_name',
    ];

    public int|null $product_variant_id;
    public int|null $product_id;
    public string|null $variant_name;
    // image
    public array|null|string $image;
    public int|null $sort_order;
    public int|null $active_status;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Define relationship with Product model
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // option group relation varaint id
    public function productOptionGroups()
    {
        return $this->hasMany(ProductOptionGroup::class, 'product_variant_id', 'product_variant_id');
    }

    // option relation
    public function productOptions()
    {
        return $this->hasMany(ProductOption::class, 'product_variant_id');
    }

    /**
     * Define relationship with VariantContent model
     */
    // public function content()
    // {
    //     return $this->hasMany(VariantContent::class, 'variant_id');
    // }

}
