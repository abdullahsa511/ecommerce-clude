<?php

declare(strict_types=1);

namespace App\Core\Models\Option;

use App\Core\Models\Base\Model;

class OptionContent extends Model
{

    protected string $table = 'option_content'; 
    protected string $primaryKey = 'option_id';

    public int $option_id;
    public int $language_id;
    public string $name;

    public function __construct() 
    {
        parent::__construct();
    }
} 