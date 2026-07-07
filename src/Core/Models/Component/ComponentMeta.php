<?php

declare(strict_types=1);

namespace App\Core\Models\Component;

use App\Core\Models\Base\Model;
use function App\Core\System\utils\session;

class ComponentMeta extends Model
{
    public int $component_meta_id;
    public int $component_id;
    public string $property;
    public string $value;
        
    public function __construct() 
    {
        parent::__construct();
    }


}
