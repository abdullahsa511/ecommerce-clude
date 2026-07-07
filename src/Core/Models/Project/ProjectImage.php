<?php

declare(strict_types=1);

namespace App\Core\Models\Project;

use App\Core\Models\Base\Model;

class ProjectImage extends Model
{
    public int|string $project_image_id;
    public int $project_id;
    public string $image_link;
    public string $image;
    public int $sort_order;
    public string $status;
    public string $way_points;
    public string|null $created_at;
    public string|null $updated_at;

    public function __construct() 
    {
        parent::__construct();
    }
}