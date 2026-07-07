<?php

declare(strict_types=1);

namespace App\Core\Models\Product;

use App\Core\Models\Base\Model;
use App\Core\Models\Admin\Admin;

class ProductContentRevision extends Model
{
    protected string $table = 'product_content_revision';
    protected array $fillable = [
        'product_id',
        'language_id',
        'created_at',
        'admin_id',
        'content'
    ];

    public int $product_id;
    public int $language_id;
    public string $created_at;
    public int $admin_id;
    public ?string $content;
    public ?string $display_name; // from admin join
    public ?string $username; // from admin join

    public function __construct() 
    {
        parent::__construct();
    }

    /**
     * Get the admin that owns this revision
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
} 