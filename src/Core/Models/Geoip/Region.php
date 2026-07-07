<?php

declare(strict_types=1);

namespace App\Core\Models\Geoip;

use App\Core\Models\Base\Model;

class Region extends Model
{
    public int $region_id;
    public int $country_id;
    public string $name;
    public string $code;
    public int $status;

    public function __construct() 
    {
        parent::__construct();
    }

    /**
     * Define relationship with Country model
     */
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'country_id');
    }

    /**
     * Define relationship with RegionGroup model through RegionToRegionGroup
     */
    public function regionGroup()
    {
        return $this->belongsToMany(RegionGroup::class, 'region_to_region_group', 'region_id', 'region_group_id');
    }
} 