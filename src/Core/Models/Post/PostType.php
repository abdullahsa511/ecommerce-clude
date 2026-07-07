<?php

declare(strict_types=1);

namespace App\Core\Models\Post;

use App\Core\Models\Base\Model;
use App\Core\Models\Site\Site;

class PostType extends Model
{
    // Core properties
    public int $post_type_id;
    public string $name = '';
    public string $type = '';
    public string $plural = '';
    public string $icon = '';
    public string $image = '';
    public string $source = '';
    public int $site_id;

    public string $table = 'post_type';
    public string $primaryKey = 'post_type_id';

    public function site(): array
    {
        return $this->belongsTo(Site::class, 'site_id');
    }

    public function __construct() 
    {
        parent::__construct();
    }
} 