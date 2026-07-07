<?php

declare(strict_types=1);

namespace App\Core\Models\Product;

use App\Core\Models\Base\Model;
use App\Core\Models\Site\Site;

class ProductType extends Model
{
    // Core properties
    public int $product_type_id;
    public string $name = '';
    public string $type = '';
    public string $plural = '';
    public string $icon = '';
    public string $image = '';
    public string $source = '';
    public int $site_id;

    public string $table = 'product_type';
    public string $primaryKey = 'product_type_id';

    public function site(): array
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function __construct() 
    {
        parent::__construct();
    }
} 