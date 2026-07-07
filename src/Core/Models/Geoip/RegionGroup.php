<?php

declare(strict_types=1);

namespace App\Core\Models\Geoip;

use App\Core\Models\Base\Model;

class RegionGroup extends Model
{
    protected string $table = 'region_group';
    // protected string $tableAlias = 'rg';
    protected string $primaryKey = 'region_group_id';

    public function __construct() 
    {
        parent::__construct();
    }

    /**
     * Region Group ID
     */
    public int $region_group_id;

    /**
     * Name
     */
    public string $name;
    
    public string $content;

    /**
     * Created At
     */
    public string $created_at;

    /**
     * Updated At
     */
    public string $updated_at;

    /**
     * Define relationship with regions through pivot table
     */
    public function regions()
    {
        return $this->belongsToMany(
            Region::class,
            'region_to_region_group',
            'region_group_id',
            'region_id',
            'region_group_id',
            'region_id'
        );
    }

   
} 