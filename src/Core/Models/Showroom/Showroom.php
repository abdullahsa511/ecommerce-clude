<?php

declare(strict_types=1);

namespace App\Core\Models\Showroom;

use App\Core\Models\Base\Model;
use PDO;

class Showroom extends Model
{
    protected string $table = 'showrooms';
    protected string $primaryKey = 'showrooms_id';

    public int $showrooms_id;
    public string $title;
    public string $slug;
    public ?string $description;
    public ?string $address;
    public ?string $image;
    public ?string $banner_image;
    public ?string $overview_image;
    public ?string $phone;
    public ?string $email;
    public ?string $mobile;
    public ?string $opening_hours;
    public string $status;
    public ?string $google_map_link;
    public ?int $sort_order;
    public array|string|null $banner_way_points;
    public int $is_section_active;
    
    public function __construct()
    {
        parent::__construct();
    }

    public function projectSection()
    {
        return $this->hasMany(ProjectSection::class, 'showrooms_id', 'showroom_id');

    }
}
