<?php

declare(strict_types=1);

namespace App\Core\Models\User;

use App\Core\Models\Base\Model;

class DigitalAssetLog extends Model
{
    public int $digital_asset_log_id;
    public int $digital_asset_id;
    public int $user_id;
    public int $site_id;
    public string $ip;
    public string $country;
    public string $created_at;
    public string $updated_at;

    public function __construct() 
    {
        parent::__construct();
    }
} 