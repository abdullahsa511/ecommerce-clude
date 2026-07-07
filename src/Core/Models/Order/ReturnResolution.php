<?php

declare(strict_types=1);

namespace App\Core\Models\Order;

use App\Core\Models\Base\Model;

class ReturnResolution extends Model
{
    public int $return_resolution_id;
    public int $language_id;
    public string $name;

    public function __construct() 
    {
        parent::__construct();
    }
}