<?php

declare(strict_types=1);

namespace App\Core\Models\Product;

use App\Core\Models\Base\Model;
use App\Core\Models\Design\DesignResource;
use App\Core\Models\Media\Media;
use App\Core\Models\Product\Product;

class ProductResource extends Model
{
    protected string $table = 'product_resource';
    protected string $primaryKey = 'product_resource_id';

    public int $product_resource_id;
    public int $product_id;
    public int $design_resource_id;
    public string $resource_type;
    public int $sort_order;
    public int $active_status;

    public function __construct() 
    {
        parent::__construct();
    }
} 