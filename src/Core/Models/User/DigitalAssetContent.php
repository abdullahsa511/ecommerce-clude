<?php

declare(strict_types=1);

namespace App\Core\Models\User;

use App\Core\Models\Base\Model;

class DigitalAssetContent extends Model
{
    public int $digital_asset_id;
    public int $language_id;
    public string $name;
    public string $created_at;
    public string $updated_at;

    public function __construct() 
    {
        parent::__construct();
    }
} 