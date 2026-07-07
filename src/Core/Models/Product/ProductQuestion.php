<?php

declare(strict_types=1);

namespace App\Core\Models\Product;

use App\Core\Models\Base\Model;
use App\Core\Models\User;

class ProductQuestion extends Model
{
    protected string $table = 'product_question';
    protected string $tableAlias = 'pq';
    protected string $primaryKey = 'product_question_id';
    protected array $fillable = [
        'product_id',
        'user_id',
        'author',
        'content',
        'status',
        'parent_id',
        'created_at',
        'updated_at'
    ];

    /**
     * Product Question ID
     */
    public int $product_question_id;

    /**
     * Product ID
     */
    public int $product_id;

    /**
     * User ID
     */
    public int $user_id;

    /**
     * Question text
     */
    public string $author;

    /**
     * Answer text
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
     * Question count (number of child questions)
     */
    public int $question_count = 0;

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
} 