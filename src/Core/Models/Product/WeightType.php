<?php

declare(strict_types=1);

namespace App\Core\Models\Product;

use App\Core\Models\Base\Model;

class WeightType extends Model
{
    public int $weight_type_id;
    public float $value;

    public function __construct() 
    {
        parent::__construct();
    }
    

    public function weightTypeContent()
    {
        return $this->hasOne(WeightTypeContent::class, 'weight_type_id', 'weight_type_id');
    }
} 