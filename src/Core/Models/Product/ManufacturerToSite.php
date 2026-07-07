<?php

declare(strict_types=1);

namespace App\Core\Models\Product;

use App\Core\Models\Base\Model;

class ManufacturerToSite extends Model
{
    protected string $table = 'manufacturer_to_site';
    protected string $tableAlias = 'manufacturer_to_site';

    /**
     * Define the relationship with manufacturer
     */
    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class, 'manufacturer_id');
    }

    public function __construct() 
    {
        parent::__construct();
    }
} 