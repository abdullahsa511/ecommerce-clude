<?php

declare(strict_types=1);

namespace App\Core\Models\Site;

use App\Core\Models\Base\Model;

class SettingContent extends Model
{
    public int $setting_id;
    public int $language_id;
    public string $value;
    public string $created_at;
    public string $updated_at;

    public function __construct() 
    {
        parent::__construct();
    }
} 