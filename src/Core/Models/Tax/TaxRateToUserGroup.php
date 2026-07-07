<?php

declare(strict_types=1);

namespace App\Core\Models\Tax;

use App\Core\Models\Base\Model;

class TaxRateToUserGroup extends Model
{
    public int $tax_rate_id;
    public int $user_group_id;
    public string $created_at;
    public string $updated_at;

    public function __construct() 
    {
        parent::__construct();
    }
} 