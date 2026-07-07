<?php

declare(strict_types=1);

namespace App\Core\Models\Pinboard;

use App\Core\Models\Base\Model;

class PinboardTempItemAccessories extends Model
{
    protected string $table = 'pinboard_item_accessories';

    protected int $pinboard_temp_item_accessories_id;
    protected int $pinboard_temp_id;
    protected int $pinboard_temp_item_id;
    protected int $accessories_product_id;
    protected int $accessories_item_id;

    protected string $created_at;
    protected string $updated_at;
    protected string $deleted_at;

    public function __construct() 
    {
        parent::__construct();
    }

} 
