<?php

declare(strict_types=1);

namespace App\Core\Models\Geoip;

use App\Core\Models\Base\Model;
use function App\Core\System\utils\session;

class Timezone extends Model
{
    public int $timezone_id;
    public string $country_code;
    public string $timezone;
    public float $gmt_offset;
    public float $dst_offset;
    public float $raw_offset;
    public string $created_at;
    public string $updated_at;

        
    public function __construct() 
    {
        parent::__construct();
    }

    /**
     * Define relationship with Region model
     */
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_code', 'country_code');
    }
}
