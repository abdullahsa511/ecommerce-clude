<?php

declare(strict_types=1);

namespace App\Core\Models;

use App\Core\Models\Base\Model;

class TaxRate extends Model
{
    public int $tax_rate_id;
    public int $region_group_id;
    public string $name;
    public string $rate;
    public string $type;
    public string $created_at;
    public string $updated_at;

    public function __construct() 
    {
        parent::__construct();
    }

} 