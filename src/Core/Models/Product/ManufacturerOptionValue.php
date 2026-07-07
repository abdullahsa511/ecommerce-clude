<?php

declare(strict_types=1);

namespace App\Core\Models\Product;

use App\Core\Models\Base\Model;

class ManufacturerOptionValue extends Model
{
    protected string $table = 'manufacturer_option_value';
    protected string $tableAlias = 'manufacturer_option_value';

    /**
     * Define the relationship with manufacturer_option
     */
    public function option()
    {
        return $this->belongsTo(ManufacturerOption::class, 'manufacturer_option_id');
    }

    public function __construct() 
    {
        parent::__construct();
    }
} 