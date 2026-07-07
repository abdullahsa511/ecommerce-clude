<?php

declare(strict_types=1);

namespace App\Core\Models\Site;

use App\Core\Models\Base\Model;

class Setting extends Model
{
    protected $table = 'setting';
    
    public int|null $setting_id;
    public int $site_id;
    public string|null $code;
    public string|null $namespace;
    public string $key;
    public string $value;
    public int|null $serialized;
    public string|null $created_at;
    public string|null $updated_at;

    public function __construct() 
    {
        parent::__construct();
    }
    public function site(): array
    {
        return $this->belongsTo(Site::class, 'site_id');
    }
    public function settingContent(): array
    {
        return $this->hasMany(SettingContent::class, 'setting_id');
    }
} 