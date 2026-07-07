<?php

declare(strict_types=1);

namespace App\Core\Models\Localisation;

use App\Core\Models\Base\Model;

class Language extends Model
{
    // Core properties
    public int $language_id;
    public string $name = '';
    public string $code = '';
    public string $locale = '';
    public int $rtl = 0;
    public int $sort_order = 0;
    public int $status = 0;
    public int $default = 0;

    public function __construct() 
    {
        parent::__construct();
    }
} 