<?php

declare(strict_types=1);

namespace App\Core\Models\Product;

use App\Core\Models\Base\Model;

class LengthType extends Model
{
    public int $length_type_id;
    public float $value;

    public function __construct() 
    {
        parent::__construct();
    }

    public function lengthTypeContent()
    {
        return $this->hasOne(LengthTypeContent::class, 'length_type_id', 'length_type_id');
    }
} 