<?php

declare(strict_types=1);

namespace App\Core\Models\User;

use App\Core\Models\Base\Model;

class DigitalAsset extends Model
{
    public int $digital_asset_id;
    public string $file;
    public string $public;
    public string $created_at;

    public function __construct() 
    {
        parent::__construct();
    }
} 