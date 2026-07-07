<?php

declare(strict_types=1);

namespace App\Core\Models\Product;

use App\Core\Models\Base\Model;
use App\Core\Models\User;

class ProductReview extends Model
{
    protected string $table = 'product_review';
    protected string $tableAlias = 'pr';
    protected string $primaryKey = 'product_review_id';
    
    protected array $fillable = [
        'product_id',
        'user_id',
        'author',
        'content',
        'rating',
        'status',
        'parent_id',
        'created_at',
        'updated_at'
    ];


    /**
     * Product Review ID
     */
    public int $product_review_id;

    /**
     * Product ID
     */
    public int $product_id;

    /**
     * User ID
     */
    public int $user_id;

    /**
     * Rating (1-5)
     */
    public int $rating;

    /**
     * Review title
     */
    public string $author;

    /**
     * Review text
     */
    public string $content;

    /**
     * Status
     */
    public int $status;

    /**
     * Parent ID
     */
    public ?int $parent_id;

    /**
     * Created at
     */
    public string $created_at;

    /**
     * Updated at
     */
    public string $updated_at;

    /**
     * Review count (number of child reviews)
     */
    public int $review_count = 0;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Define relationship with User model
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Define relationship with Product model
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Define relationship with ProductReviewMedia model
     */
    public function media()
    {
        return $this->hasMany(ProductReviewMedia::class, 'product_review_id');
    }
} 