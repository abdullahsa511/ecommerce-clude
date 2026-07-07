<?php

declare(strict_types=1);

namespace App\Core\Models\Product;

use App\Core\Models\Base\Model;

class ProductMeta extends Model
{
    protected string $table = 'product_content_meta';
    protected array $fillable = [
        'product_id',
        'namespace',
        'key',
        'value'
    ];

    public int $product_id;
    public string $namespace;
    public string $key;
    public string $value;

    /**
     * Get the product that owns this meta
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