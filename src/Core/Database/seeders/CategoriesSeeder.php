<?php

declare(strict_types=1);

namespace App\Core\Database\Seeders;

use App\Core\App\KernelCli;
use App\Core\Repositories\PostCategory\TaxonomyItemRepositoryInterface;
use App\Core\Repositories\PostCategory\TaxonomyRepositoryInterface;
use Illuminate\Container\Container;

class CategoriesSeeder
{
    private TaxonomyRepositoryInterface $repository;
    private TaxonomyItemRepositoryInterface $taxonomyItemRepository;
    private KernelCli $kernel;
    private Container $container;

    // Taxonomy Items for Product Categories (taxonomy_id = 1)
    private $taxonomyItems = [
        // Seating Categories (Parent)
        ['taxonomy_id' => 1, 'taxonomy_item_id' => 1, 'sort_order' => 1, 'parent_id' => null],
        ['taxonomy_id' => 1, 'taxonomy_item_id' => 2, 'sort_order' => 2, 'parent_id' => null],
        ['taxonomy_id' => 1, 'taxonomy_item_id' => 3, 'sort_order' => 3, 'parent_id' => null],
        ['taxonomy_id' => 1, 'taxonomy_item_id' => 4, 'sort_order' => 4, 'parent_id' => null],
        ['taxonomy_id' => 1, 'taxonomy_item_id' => 5, 'sort_order' => 5, 'parent_id' => null],
        ['taxonomy_id' => 1, 'taxonomy_item_id' => 6, 'sort_order' => 6, 'parent_id' => null],

        // Workstations Categories (Parent)
        ['taxonomy_id' => 1, 'taxonomy_item_id' => 7, 'sort_order' => 7, 'parent_id' => null],
        ['taxonomy_id' => 1, 'taxonomy_item_id' => 8, 'sort_order' => 8, 'parent_id' => null],

        // Workstations Sub Categories
        ['taxonomy_id' => 1, 'taxonomy_item_id' => 9, 'sort_order' => 1, 'parent_id' => 7],
        ['taxonomy_id' => 1, 'taxonomy_item_id' => 10, 'sort_order' => 2, 'parent_id' => 7],
        ['taxonomy_id' => 1, 'taxonomy_item_id' => 11, 'sort_order' => 3, 'parent_id' => 7],
        ['taxonomy_id' => 1, 'taxonomy_item_id' => 12, 'sort_order' => 4, 'parent_id' => 7],
        ['taxonomy_id' => 1, 'taxonomy_item_id' => 13, 'sort_order' => 5, 'parent_id' => 7],

        // Screens Sub Categories
        ['taxonomy_id' => 1, 'taxonomy_item_id' => 14, 'sort_order' => 1, 'parent_id' => 8],
        ['taxonomy_id' => 1, 'taxonomy_item_id' => 15, 'sort_order' => 2, 'parent_id' => 8],
        ['taxonomy_id' => 1, 'taxonomy_item_id' => 16, 'sort_order' => 3, 'parent_id' => 8],
        ['taxonomy_id' => 1, 'taxonomy_item_id' => 17, 'sort_order' => 4, 'parent_id' => 8],
    ];

    private $taxonomyItemContent = [
        // Seating Categories Content
        ['taxonomy_item_id' => 1, 'language_id' => 1, 'name' => 'Task Seating', 'slug' => 'task-seating', 'content' => 'Ergonomic task seating designed for productivity and comfort during long work sessions', 'meta_title' => 'Task Seating', 'meta_description' => 'Professional task seating with ergonomic design for office environments', 'meta_keywords' => 'task seating, office chairs, ergonomic chairs, work seating'],
        
        ['taxonomy_item_id' => 2, 'language_id' => 1, 'name' => 'Executive Seating', 'slug' => 'executive-seating', 'content' => 'Premium executive seating with luxury materials and sophisticated design for leadership spaces', 'meta_title' => 'Executive Seating', 'meta_description' => 'Luxury executive chairs and seating solutions for executive offices', 'meta_keywords' => 'executive seating, luxury chairs, premium office seating, executive chairs'],
        
        ['taxonomy_item_id' => 3, 'language_id' => 1, 'name' => 'Training Seating', 'slug' => 'training-seating', 'content' => 'Versatile training seating solutions for conference rooms and educational environments', 'meta_title' => 'Training Seating', 'meta_description' => 'Flexible training room seating for conferences and educational spaces', 'meta_keywords' => 'training seating, conference chairs, educational seating, meeting room chairs'],
        
        ['taxonomy_item_id' => 4, 'language_id' => 1, 'name' => 'Occasional Seating', 'slug' => 'occasional-seating', 'content' => 'Stylish occasional seating for reception areas, lounges, and collaborative spaces', 'meta_title' => 'Occasional Seating', 'meta_description' => 'Comfortable occasional seating for reception and collaborative areas', 'meta_keywords' => 'occasional seating, reception chairs, lounge seating, guest chairs'],
        
        ['taxonomy_item_id' => 5, 'language_id' => 1, 'name' => 'Stools', 'slug' => 'stools', 'content' => 'Modern stools for standing desks, bars, and flexible workspace solutions', 'meta_title' => 'Stools', 'meta_description' => 'Contemporary stools for standing desks and flexible workspaces', 'meta_keywords' => 'stools, standing desk stools, bar stools, flexible seating'],
        
        ['taxonomy_item_id' => 6, 'language_id' => 1, 'name' => 'Lounges', 'slug' => 'lounges', 'content' => 'Comfortable lounge seating for relaxation and informal meeting areas', 'meta_title' => 'Lounge Seating', 'meta_description' => 'Relaxing lounge furniture for informal meeting and break areas', 'meta_keywords' => 'lounge seating, comfortable seating, break area furniture, informal seating'],

        // Workstations Parent Categories Content
        ['taxonomy_item_id' => 7, 'language_id' => 1, 'name' => 'Workstations', 'slug' => 'workstations', 'content' => 'Complete workstation solutions including desks, systems, and workspace furniture', 'meta_title' => 'Workstations', 'meta_description' => 'Professional workstation furniture and office desk systems', 'meta_keywords' => 'workstations, office desks, workspace furniture, desk systems'],
        
        ['taxonomy_item_id' => 8, 'language_id' => 1, 'name' => 'Screens', 'slug' => 'screens', 'content' => 'Privacy and acoustic screens for office workstations and open plan environments', 'meta_title' => 'Office Screens', 'meta_description' => 'Privacy screens and acoustic solutions for modern offices', 'meta_keywords' => 'office screens, privacy screens, acoustic screens, workspace dividers'],

        // Workstations Sub Categories Content
        ['taxonomy_item_id' => 9, 'language_id' => 1, 'name' => 'Keywork', 'slug' => 'keywork', 'content' => 'Keywork workstation systems offering flexible and modular workspace solutions', 'meta_title' => 'Keywork Workstations', 'meta_description' => 'Modular Keywork workstation systems for modern offices', 'meta_keywords' => 'keywork, workstation systems, modular desks, office furniture'],
        
        ['taxonomy_item_id' => 10, 'language_id' => 1, 'name' => 'Keywork Spine', 'slug' => 'keywork-spine', 'content' => 'Keywork Spine system providing structural support and cable management for workstations', 'meta_title' => 'Keywork Spine System', 'meta_description' => 'Keywork Spine structural system for organized workstation layouts', 'meta_keywords' => 'keywork spine, workstation structure, cable management, office systems'],
        
        ['taxonomy_item_id' => 11, 'language_id' => 1, 'name' => 'Benchwork', 'slug' => 'benchwork', 'content' => 'Benchwork systems for collaborative and open plan office environments', 'meta_title' => 'Benchwork Systems', 'meta_description' => 'Collaborative benchwork desk systems for open plan offices', 'meta_keywords' => 'benchwork, bench desks, collaborative workstations, open plan furniture'],
        
        ['taxonomy_item_id' => 12, 'language_id' => 1, 'name' => 'Benchwork Spine', 'slug' => 'benchwork-spine', 'content' => 'Benchwork Spine system providing infrastructure for bench desk configurations', 'meta_title' => 'Benchwork Spine System', 'meta_description' => 'Benchwork Spine infrastructure for bench desk arrangements', 'meta_keywords' => 'benchwork spine, bench desk system, workstation infrastructure, office planning'],
        
        ['taxonomy_item_id' => 13, 'language_id' => 1, 'name' => 'Screens', 'slug' => 'workstation-screens', 'content' => 'Integrated screens for workstation privacy and acoustic control', 'meta_title' => 'Workstation Screens', 'meta_description' => 'Privacy and acoustic screens integrated with workstation systems', 'meta_keywords' => 'workstation screens, desk screens, privacy panels, acoustic solutions'],

        // Screens Sub Categories Content
        ['taxonomy_item_id' => 14, 'language_id' => 1, 'name' => 'Arc Screen', 'slug' => 'arc-screen', 'content' => 'Arc Screen systems with curved design for enhanced privacy and aesthetics', 'meta_title' => 'Arc Screen Systems', 'meta_description' => 'Curved Arc Screen solutions for modern office privacy', 'meta_keywords' => 'arc screen, curved screens, office privacy, acoustic panels'],
        
        ['taxonomy_item_id' => 15, 'language_id' => 1, 'name' => 'Daily Screen', 'slug' => 'daily-screen', 'content' => 'Daily Screen solutions for everyday workspace privacy and organization', 'meta_title' => 'Daily Screen Solutions', 'meta_description' => 'Practical Daily Screen systems for workspace organization', 'meta_keywords' => 'daily screen, workspace screens, office dividers, privacy solutions'],
        
        ['taxonomy_item_id' => 16, 'language_id' => 1, 'name' => 'Vast Screen', 'slug' => 'vast-screen', 'content' => 'Vast Screen systems for large-scale privacy and acoustic control in open offices', 'meta_title' => 'Vast Screen Systems', 'meta_description' => 'Large-scale Vast Screen solutions for open plan offices', 'meta_keywords' => 'vast screen, large screens, open office solutions, acoustic control'],
        
        ['taxonomy_item_id' => 17, 'language_id' => 1, 'name' => 'Trak Screen', 'slug' => 'trak-screen', 'content' => 'Trak Screen systems with tracking and mounting solutions for flexible workspace design', 'meta_title' => 'Trak Screen Systems', 'meta_description' => 'Flexible Trak Screen mounting systems for adaptable workspaces', 'meta_keywords' => 'trak screen, tracking screens, flexible screens, mounting systems'],
    ];

    public function __construct()
    {
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(TaxonomyRepositoryInterface::class);
        $this->taxonomyItemRepository = $this->container->make(TaxonomyItemRepositoryInterface::class);
    }

    public function seed(): void
    {
        // Insert taxonomy items and their content
        $this->taxonomyItemRepository->insertMultiple($this->taxonomyItems);
        $this->taxonomyItemRepository->insertTaxonomyItemContents($this->taxonomyItemContent);
    }
}
