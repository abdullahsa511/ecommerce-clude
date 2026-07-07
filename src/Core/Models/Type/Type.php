<?php

declare(strict_types=1);

namespace App\Core\Models\Type;

use App\Core\Models\Base\Model;


class Type extends Model
{
    public int $type_id;
    public string $type;
    public int $sort_order;
    

    
    public function __construct() 
    {
        parent::__construct();
    }

} 