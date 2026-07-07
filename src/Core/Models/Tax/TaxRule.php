<?php

declare(strict_types=1);

namespace App\Core\Models\Tax;

use App\Core\Models\Base\Model;

class TaxRule extends Model
{
    public int $tax_rule_id;
    public int $tax_type_id;
    public int $tax_rate_id;
    public string $based;
    public int $priority;

    public function __construct() 
    {
        parent::__construct();
    }
} 