<?php

declare(strict_types=1);

namespace App\Core\Models\User;

use App\Core\Models\Base\Model;
use App\Core\Models\Product\Product;

class CustomerSegment extends Model
{
    public int $customer_segment_id;
    public string $customer_segment_name;
    public string $created_at;


    public function __construct()
    {
        parent::__construct();
    }
} 