<?php

declare(strict_types=1);

namespace App\Core\Models\Product;

use App\Core\Models\Base\Model;
use App\Core\Models\Site\Site;

class Vendor extends Model
{
    public int $vendor_id;
    public ?string $vendor_code = null;
    public int $admin_id;
    public string $name;
    public string $slug;
    public json|string|null $image;
    public int $sort_order;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get the sites this vendor belongs to
     * 
     * @return array
     */
    public function sites()
    {
        return $this->belongsToMany(Site::class, 'vendor_to_site', 'vendor_id', 'site_id', 'vendor_id', 'site_id');
    }
} 