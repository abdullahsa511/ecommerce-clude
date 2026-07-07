<?php

declare(strict_types=1);

namespace App\Core\Models\Localisation;

use App\Core\Models\Base\Model;

class Currency extends Model
{
    public int $currency_id;
    public string $name;
    public string $code;
    public float $value;
    public string $sign_start;
    public string $sign_end;
    public int $decimal_place;
    public int $status;
    public string $updated_at;
    
    public function __construct()
    {}
   
}
