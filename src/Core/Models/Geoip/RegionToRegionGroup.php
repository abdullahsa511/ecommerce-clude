<?php

declare(strict_types=1);

namespace App\Core\Models\Geoip;

use App\Core\Models\Base\Model;

class RegionToRegionGroup extends Model
{
    protected string $table = 'region_to_region_group';
    protected string $tableAlias = 'rtrg';
    protected array $fillable = [
        'region_group_id',
        'region_id',
        'country_id'
    ];
    public function __construct() 
    {
        parent::__construct();
    }

    /**
     * Region Group ID
     */
    public int $region_group_id;

    /**
     * Region ID
     */
    public int $region_id;

    /**
     * Country ID
     */
    public int $country_id;

    /**
     * Define relationship with Region model
     */
    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id', 'region_id');
    }

    /**
     * Define relationship with Country model
     */
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'country_id');
    }

    /**
     * Define relationship with RegionGroup model
     */
    public function regionGroup()
    {
        return $this->belongsTo(RegionGroup::class, 'region_group_id', 'region_group_id');
    }
} 