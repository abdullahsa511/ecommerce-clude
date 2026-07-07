<?php

declare(strict_types=1);

namespace App\Core\Models\Product;

use App\Core\Models\Base\Model;
use App\Core\Models\User\UserGroup;

class ProductPromotion extends Model
{
    public int $product_promotion_id;
    public int $product_id;
    public int $user_group_id;
    public string $user_group_name;
    public int $priority;
    public float $price;
    public string $from_date;
    public string $to_date;

    public function __construct() 
    {
        parent::__construct();
    }

    public function userGroup()
    {
        return $this->belongsTo(UserGroup::class, 'user_group_id');
    }
} 