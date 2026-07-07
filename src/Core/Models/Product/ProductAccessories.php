<?php

declare(strict_types=1);

namespace App\Core\Models\Product;

use App\Core\Models\Base\Model;
use App\Core\Models\Item\Item;
use App\Core\Models\User;

class ProductAccessories extends Model
{
    protected string $table = 'product_accessories';
    protected string $tableAlias = 'product_accessories';
    protected string $primaryKey = 'product_accessories_id';
    protected array $fillable = [
        'parent_product_id',
        'product_id',
        'item_id',
        'price',
        'active_status',
        'created_at',
        'updated_at'
    ];

    public ?int $product_accessories_id;
    public ?int $parent_product_id;
    public ?int $product_id;
    public ?int $item_id;
    public ?int $price;
    public ?bool $active_status;

    
    public int $accessories_count = 0;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Define relationship with User model
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Define relationship with Product model
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
} 