<?php

declare(strict_types=1);

namespace App\Core\Models\Product;

use App\Core\Models\Base\Model;

class ProductContentMeta extends Model
{
    protected string $table = 'product_content_meta';
    protected string $primaryKey = 'product_content_meta_id';
    protected array $fillable = [
        'product_id',
        'namespace',
        'key',
        'value',
        'language_id'
    ];

    public int $product_content_meta_id;
    public int $product_id;
    public string $namespace;
    public string $key;
    public string $value;
    public ?int $language_id;
    public string $created_at;
    public string $updated_at;

    public function getId(): int
    {
        return $this->product_content_meta_id;
    }

    public function __construct() 
    {
        parent::__construct();
    }
} 