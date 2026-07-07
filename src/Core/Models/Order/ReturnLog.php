<?php

declare(strict_types=1);

namespace App\Core\Models\Order;

use App\Core\Models\Base\Model;

class ReturnLog extends Model
{
    public int $return_log_id;
    public int $return_id;
    public int $return_status_id;
    public int $notify;
    public string $note;
    public string $created_at;

    public function __construct() 
    {
        parent::__construct();
    }
} 