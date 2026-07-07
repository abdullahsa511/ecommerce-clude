<?php

declare(strict_types=1);

namespace App\Core\Models\Site;

use App\Core\Models\Base\Model;

class SettingContent extends Model
{
    protected $table = 'setting_content';
    public int|null $setting_id;
    public int $language_id;
    public string|null $namespace;
    public string|null $key;
    public string $value;
    public string|null $created_at;
    public string|null $updated_at;

    public function __construct() 
    {
        parent::__construct();
    }
} 