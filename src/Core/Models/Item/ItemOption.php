<?php

declare(strict_types=1);

namespace App\Core\Models\Item;

use App\Core\Models\Base\Model;
use App\Core\Models\Product\ProductVariant;
use App\Core\Models\Product\ProductOptionGroup;
use App\Core\Models\Product\Type;
use App\Core\Models\Option\Option;

class ItemOption extends Model
{
    protected string $table = 'item_option';
    protected string $primaryKey = 'item_option_id';
    
    public int $item_option_id;
    public int $item_id;
    public int $product_id;
    public int $product_variant_id;
    public int $product_option_group_id;
    public int $product_option_id;
    public int $type_id;
    public string|null $name;
    public string|array|object|null $value;
    public string|null $meta_description;
    public int $required;
    public string|null $item_code;
    public string|null $description;
    public string|null|array $quote_image;
    public bool|int|null|string $is_default;
    public string|null $hex_color;
    public string|null|array $option_image;
    /**
     * Define relationship with Item model
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
    public function productOptionGroup()
    {
        return $this->belongsTo(ProductOptionGroup::class, 'product_option_group_id');
    }
    public function type()
    {
        return $this->belongsTo(Type::class, 'type_id');
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