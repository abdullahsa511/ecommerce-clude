<?php

declare(strict_types=1);

namespace App\Core\Models\Logistic;

use App\Core\Models\Base\Model;

class LogisticStatus extends Model
{
    public int $logistic_statuses_id;
    public int $language_id;
    public string $name;

    protected string $table = 'logistic_statuses';
    protected string $primaryKey = 'logistic_statuses_id';

    public function __construct() 
    {
        parent::__construct();
    }
} 