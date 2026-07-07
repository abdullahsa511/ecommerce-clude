<?php

declare(strict_types=1);

namespace App\Core\Models\Product;

use App\Core\Models\Base\Model;

class ProductCertificate extends Model
{
    public int $product_certificate_id;
    public int $product_id;
    public int $media_id;
    public ?string $logo;
    public ?string $certificate_file;
    public string $certificate_provider;
    public string $title;
    public string $description;
    public int $sort_order;
    public string $created_at;
    public string $updated_at;
    public string $deleted_at;

    public function __construct() 
    {
        parent::__construct();
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'product_id', 'product_id');
    }

    public function media()
    {
        return $this->hasOne(Media::class, 'media_id', 'media_id');
    }
} 