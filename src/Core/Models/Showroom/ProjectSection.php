<?php

declare(strict_types=1);

namespace App\Core\Models\Showroom;

use App\Core\Models\Product\Product;

use App\Core\Models\Base\Model;
use PDO;

class ProjectSection extends Model
{
    protected string $table = 'project_sections';
    protected string $primaryKey = 'project_sections_id';

    public int $section_id;
    public int $project_sections_id;
    public int $showroom_id;
    public string $section_code;
    public string $title;
    public string $slug;
    public ?string $image;
    public ?string $description;
    public ?string $status;
    public ?int $sort_order;

    /**
     * Related products through the pivot table
     */
    public function sectionProducts()
    {
        return $this->hasMany(ProjectSectionProduct::class, 'project_sections_id', 'section_id');
    }

    /**
     * Related images
     */
    public function sectionImages()
    {
        return $this->hasMany(ProjectSectionImage::class, 'project_sections_id', 'section_id');
    }

    // public function getProductsWithDetails(): array
    // {
    //     $db = $this; // Assuming Base\Model provides this
    //     $sql = "
    //         SELECT 
    //             p.product_id, 
    //             p.title, 
    //             p.description, 
    //             p.price
    //         FROM project_section_products psp
    //         INNER JOIN products p ON p.product_id = psp.product_id
    //         WHERE psp.section_id = :section_id
    //     ";
    //     $stmt = $db->prepare($sql);
    //     $stmt->execute(['section_id' => $this->project_sections_id]);
    //     return $stmt->fetchAll(PDO::FETCH_ASSOC);
    // }

    public function __construct()
    {
        parent::__construct();
    }
}
