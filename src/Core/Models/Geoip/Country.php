<?php

declare(strict_types=1);

namespace App\Core\Models\Geoip;

use App\Core\Models\Base\Model;
use function App\Core\System\utils\session;

class Country extends Model
{
    public int $country_id;
    public string $name;
    public string $iso_code_2;
    public string $iso_code_3;
    public int $status;
    public string $created_at;
    public string $updated_at;

        
    public function __construct() 
    {
        parent::__construct();
    }

    /**
     * Define relationship with Region model
     */
    public function region()
    {
        return $this->hasMany(Region::class, 'country_id');
    }

    /**
     * Define relationship with RegionGroup model through RegionToRegionGroup
     */
    public function regionGroup()
    {
        return $this->belongsToMany(RegionGroup::class, 'region_to_region_group', 'country_id', 'region_group_id');
    }
}
