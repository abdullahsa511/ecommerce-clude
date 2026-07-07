<?php

declare(strict_types=1);

namespace App\Core\Models\Product;

use App\Core\Models\Base\Model;

class ProductReviewMedia extends Model
{
    protected string $table = 'product_review_media';
    protected string $tableAlias = 'prm';
    protected string $primaryKey = 'product_review_media_id';
    protected array $fillable = [
        'product_id',
        'product_review_id',
        'image',
        'sort_order',
        'created_at',
        'updated_at'
    ];

    /**
     * Product Review Media ID
     */
    public int $product_review_media_id;

    /**
     * Product ID
     */
    public int $product_id;

    /**
     * Product Review ID
     */
    public int $product_review_id;

    /**
     * Image path
     */
    public string $image;

    /**
     * Sort order
     */
    public int $sort_order;

    /**
     * Created at
     */
    public string $created_at;

    /**
     * Updated at
     */
    public string $updated_at;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Define relationship with ProductReview model
     */
    public function review()
    {
        return $this->belongsTo(ProductReview::class, 'product_review_id');
    }
} 