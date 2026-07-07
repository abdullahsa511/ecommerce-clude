<?php

declare(strict_types=1);

namespace App\Core\Models\Project;

use App\Core\Models\Base\Model;

class ProjectStatus extends Model
{
    public int $project_status_id;
    public int $language_id;
    public string $name;

    public function __construct() 
    {
        parent::__construct();
    }
}