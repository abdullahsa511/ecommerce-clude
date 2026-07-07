<?php

declare(strict_types=1);

namespace App\Core\Models\Admin;

use App\Core\Models\Base\Model;
use function App\Core\System\utils\session;

class AdminFailedLogin extends Model
{
    public int $admin_id;
    public int $count;
    public string $last_ip;
    public string $updated_at;

    public function __construct()
    {
        parent::__construct();
    }
    public function admin(){
        return $this->belongsTo(Admin::class, 'admin_id', 'admin_id');
    }
} 