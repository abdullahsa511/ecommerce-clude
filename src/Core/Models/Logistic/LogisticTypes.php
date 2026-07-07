<?php

declare(strict_types=1);

namespace App\Core\Models\Logistic;

use App\Core\Models\Base\Model;

class LogisticTypes extends Model
{
    public int $logistic_types_id;
    public string $uuid;
    public string $name;
    public string $short;
    public string $type;
    public bool $track_resource;
    public float $forecasted_rate;
    public bool $is_active;
    public string $created_at;
    public string $updated_at;

    protected string $table = 'logistic_types';
    protected string $primaryKey = 'logistic_types_id';
    
    public function __construct() 
    {
        parent::__construct();
    }
} 