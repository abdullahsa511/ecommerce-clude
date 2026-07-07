<?php

declare(strict_types=1);

namespace App\Core\Models\Product;

use App\Core\Models\Base\Model;


class ProductCategory extends Model
{
    protected $table = 'taxonomy_item';
    
    public int $product_id;
    public int $category_id;
    public string $slug;
    public string $created_at;
    public string $updated_at;
    public array|string|null $banner_way_points;

    public function __construct() 
    {
        parent::__construct();
    }
    public function subcategories(){
        return $this->hasMany(ProductCategory::class, 'parent_id', 'taxonomy_item_id');
    }
} 