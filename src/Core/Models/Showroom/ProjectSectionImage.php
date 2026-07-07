<?php

declare(strict_types=1);

namespace App\Core\Models\Showroom;

use App\Core\Models\Base\Model;

class ProjectSectionImage extends Model
{
    protected string $table = 'project_section_images';
    protected string $primaryKey = 'project_section_images_id';

    public int $section_image_id;
    public int $section_id;
    public string $image_link;
    public ?int $media_id;
    public ?string $image;
    public ?string $status;
    public ?int $sort_order;

    /**
     * Belongs to a section
     */
    public function sectionImages()
    {
        return $this->belongsTo(ProjectSection::class, 'section_id');
    }

    public function __construct()
    {
        parent::__construct();
    }
}
