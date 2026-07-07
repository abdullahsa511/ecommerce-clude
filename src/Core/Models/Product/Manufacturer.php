<?php

declare(strict_types=1);

namespace App\Core\Models\Product;

use App\Core\Models\Base\Model;

class Manufacturer extends Model
{
    protected string $table = 'manufacturer';
    protected string $tableAlias = 'manufacturer';

    public int $manufacturer_id;
    public string $manufacturer_code;
    public int $admin_id;
    public string $name;
    public string $slug;
    public json| string|null $image;
    public int $sort_order;

    /**
     * Define the relationship with manufacturer_to_site
     */
    public function sites()
    {
        return $this->hasMany(ManufacturerToSite::class, 'manufacturer_id');
    }

    /**
     * Define the relationship with manufacturer_option
     */
    public function options()
    {
        return $this->hasMany(ManufacturerOption::class, 'manufacturer_id');
    }

    public function __construct() 
    {
        parent::__construct();
    }
} 