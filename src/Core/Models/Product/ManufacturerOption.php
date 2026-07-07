<?php

declare(strict_types=1);

namespace App\Core\Models\Product;

use App\Core\Models\Base\Model;

class ManufacturerOption extends Model
{
    protected string $table = 'manufacturer_option';
    protected string $tableAlias = 'manufacturer_option';

    /**
     * Define the relationship with manufacturer
     */
    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class, 'manufacturer_id');
    }

    /**
     * Define the relationship with manufacturer_option_value
     */
    public function values()
    {
        return $this->hasMany(ManufacturerOptionValue::class, 'manufacturer_option_id');
    }

    public function __construct() 
    {
        parent::__construct();
    }
} 