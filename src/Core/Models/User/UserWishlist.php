<?php

declare(strict_types=1);

namespace App\Core\Models\User;

use App\Core\Models\Base\Model;
use App\Core\Models\Product\Product;

class UserWishlist extends Model
{
    public int $user_id;
    public int $product_id;
    public string $created_at;
    public string $updated_at;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get the product for this wishlist item
     * 
     * @return array
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
} 