<?php

declare(strict_types=1);

namespace App\Core\Models\Cart;

use App\Core\Models\Base\Model;

class CouponTaxonomy extends Model
{
    public int $coupon_id;
    public int $taxonomy_item_id;

    public function __construct() 
    {
        parent::__construct();
    }
} 