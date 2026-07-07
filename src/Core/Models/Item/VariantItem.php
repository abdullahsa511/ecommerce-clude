<?php

declare(strict_types=1);

namespace App\Core\Models\Item;

use App\Core\Models\Base\Model;
use App\Core\Models\Item\Item;
use App\Core\Models\Variant\Variant;

class VariantItem extends Model
{
    protected string $table = 'variant_item';
    protected string $primaryKey = 'variant_item_id';
    protected array $fillable = [
        'product_variant_id',
        'product_id',
        'item_id',
        // 'km_item_id',
        'sort_order',
    ];

    public int|null $variant_item_id;
    public int|null $product_variant_id;
    public int|null $product_id;
    public int|null $item_id;
    public int|null $km_item_id;
    public int|null $sort_order;

    public function __construct() 
    {
        parent::__construct();
    }

    /**
     * Define relationship with Item model
     */
    public function item()
    {
        return $this->hasMany(Item::class, 'item_id');
    }

    /**
     * Define relationship with Variant model
     */
    public function variant()
    {
        return $this->hasMany(Variant::class, 'product_variant_id');
    }

} 