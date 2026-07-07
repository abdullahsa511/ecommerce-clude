<?php

declare(strict_types=1);

namespace App\Core\Models\Product;

use App\Core\Models\Base\Model;

class ProductRelatedProject extends Model
{
    public int $project_id;
    public int $product_id;
    public ?int $sort_order;
    public ?string $created_at;
    public ?string $updated_at;
    public ?string $deleted_at;

    public function __construct() 
    {
        parent::__construct();
    }
} 