<?php

declare(strict_types=1);

namespace App\Core\Models\Product;

use App\Core\Models\Base\Model;

class ProductDigitalAsset extends Model
{
    public int $product_id;
    public int $digital_asset_id;

    public function __construct() 
    {
        parent::__construct();
    }
} 