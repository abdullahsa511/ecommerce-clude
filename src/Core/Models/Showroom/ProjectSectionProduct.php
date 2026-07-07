<?php

declare(strict_types=1);

namespace App\Core\Models\Showroom;

use App\Core\Models\Base\Model;
use App\Core\Models\Product\Product;

class ProjectSectionProduct extends Model
{
    protected string $table = 'project_section_products';
    protected string $primaryKey = 'project_section_products_id';

    public int $section_product_id;
    public int $section_id;
    public int $product_id;
    public ?string $finish_material;
    public ?string $status;
    public ?int $sort_order;
    public $product;

    /**
     * Belongs to a section
     */
    public function sectionProducts()
    {
        return $this->hasMany(ProjectSectionProduct::class, 'section_id', 'project_sections_id');
    }
    /**
     * Belongs to a product
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function __construct()
    {
        parent::__construct();
    }
}
