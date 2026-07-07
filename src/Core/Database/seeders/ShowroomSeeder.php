<?php
/**
 * @Author: SA Technology
 * @Create by: Mohammad Ali Abdullah
 * @Date: 06-10-2025
 * @Description: Seeder for showroom sections, products, and images.
 * @Copyright: SA Technology all rights reserved.
 */

declare(strict_types=1);

namespace App\Core\Database\seeders;

use App\Core\App\KernelCli;
use Illuminate\Container\Container;
use App\Core\Repositories\Showroom\ShowroomRepositoryInterface;

class ShowroomSeeder
{
    private ShowroomRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;

    private $showrooms = [
        [
            'showrooms_id' => 1,
            'title' => 'Manhattan Showroom',
            'slug' => 'manhattan-showroom',
            'description' => 'Explore the Manhattan Showroom featuring modern collaborative workspace solutions designed for productivity and innovation.',
            'image' => '[{"alt":"Manhattan Showroom","objectURL":"/img/showroom/collaborative-hub-manhattan.png"}]',
            'status' => 'active',
            'sort_order' => 1
        ],
        [
            'showrooms_id' => 2,
            'title' => 'Brooklyn Showroom',
            'slug' => 'brooklyn-showroom',
            'description' => 'Visit the Brooklyn Showroom for creative and ergonomic workspaces tailored to modern teams.',
            'image' => '[{"alt":"Brooklyn Showroom","objectURL":"/img/showroom/collaborative-hub-brooklyn.png"}]',
            'status' => 'active',
            'sort_order' => 2
        ],
        [
            'showrooms_id' => 3,
            'title' => 'San Francisco Showroom',
            'slug' => 'san-francisco-showroom',
            'description' => 'Discover the San Francisco Showroom offering tech-focused collaborative workspaces for innovative projects.',
            'image' => '[{"alt":"San Francisco Showroom","objectURL":"/img/showroom/collaborative-hub-san-francisco.png"}]',
            'status' => 'active',
            'sort_order' => 3
        ],
        [
            'showrooms_id' => 4,
            'title' => 'London Showroom',
            'slug' => 'london-showroom',
            'description' => 'Experience the London Showroom with elegant, modern workspace solutions designed for creativity and productivity.',
            'image' => '[{"alt":"London Showroom","objectURL":"/img/showroom/collaborative-hub-london.png"}]',
            'status' => 'active',
            'sort_order' => 4
        ],
        [
            'showrooms_id' => 5,
            'title' => 'Tokyo Showroom',
            'slug' => 'tokyo-showroom',
            'description' => 'Step into the Tokyo Showroom, offering modern, innovative collaborative spaces for teams.',
            'image' => '[{"alt":"Tokyo Showroom","objectURL":"/img/showroom/collaborative-hub-tokyo.png"}]',
            'status' => 'active',
            'sort_order' => 5
        ],
        [
            'showrooms_id' => 6,
            'title' => 'Berlin Showroom',
            'slug' => 'berlin-showroom',
            'description' => 'Explore the Berlin Showroom with creative and flexible workspace solutions for startups and teams.',
            'image' => '[{"alt":"Berlin Showroom","objectURL":"/img/showroom/collaborative-hub-berlin.png"}]',
            'status' => 'active',
            'sort_order' => 6
        ],
        [
            'showrooms_id' => 7,
            'title' => 'Sydney Showroom',
            'slug' => 'sydney-showroom',
            'description' => 'Visit the Sydney Showroom, featuring bright and productive collaborative spaces designed for innovation.',
            'image' => '[{"alt":"Sydney Showroom","objectURL":"/img/showroom/collaborative-hub-sydney.png"}]',
            'status' => 'active',
            'sort_order' => 7
        ]
    ];

    private $sections = [
        [
            'project_sections_id' => 1,
            'project_id' => 1,
            'title' => 'Collaborative Hub T',
            'slug' => 'collaborative-hub-t',
            'description' => 'Dynamic collaborative workspace solutions designed for team productivity and innovation.',
            'status' => 'active',
            'image' => '[{"alt":"Collaborative Hub T","objectURL":"/img/showroom/collaborative-Hub.png"}]',
            'sort_order' => 1
        ],
        [
            'project_sections_id' => 2,
            'project_id' => 2,
            'title' => 'Work Hub',
            'slug' => 'work-hub',
            'description' => 'Efficient work environments with ergonomic furniture and modern office solutions.',
            'status' => 'active',
            'image' => '[{"alt":"Work Hub","objectURL":"/img/showroom/work-hub.png"}]',
            'sort_order' => 2
        ],
        [
            'project_sections_id' => 3,
            'project_id' => 3,
            'title' => 'Design Library',
            'slug' => 'design-library',
            'description' => 'Curated collection of design inspirations and contemporary furniture showcase.',
            'status' => 'active',
            'image' => '[{"alt":"Design Library","objectURL":"/img/showroom/design-library.png"}]',
            'sort_order' => 3
        ],
        [
            'project_sections_id' => 4,
            'project_id' => 4,
            'title' => 'Adaptive Solutions',
            'slug' => 'adaptive-solutions',
            'description' => 'Flexible and adaptable furniture solutions for evolving workspace needs.',
            'status' => 'active',
            'image' => '[{"alt":"Adaptive Solutions","objectURL":"/img/showroom/adaptive-solutions.png"}]',
            'sort_order' => 4
        ],
        [
            'project_sections_id' => 5,
            'project_id' => 5,
            'title' => 'Chair Display',
            'slug' => 'chair-display',
            'description' => 'Comprehensive collection of ergonomic and stylish seating options for every space.',
            'status' => 'active',
            'image' => '[{"alt":"Chair Display","objectURL":"/img/showroom/chair-display.png"}]',
            'sort_order' => 5
        ],
        [
            'project_sections_id' => 6,
            'project_id' => 6,
            'title' => 'Conference',
            'slug' => 'conference',
            'description' => 'Professional conference room setups with modern meeting furniture and technology integration.',
            'status' => 'active',
            'image' => '[{"alt":"Conference","objectURL":"/img/showroom/conference.png"}]',
            'sort_order' => 6
        ],
        [
            'project_sections_id' => 7,
            'project_id' => 7,
            'title' => 'Hospitality Hub',
            'slug' => 'hospitality-hub',
            'description' => 'Elegant hospitality furniture and design solutions for welcoming guest experiences.',
            'status' => 'active',
            'image' => '[{"alt":"Hospitality Hub","objectURL":"/img/showroom/hospitality-hub.png"}]',
            'sort_order' => 7
        ],
        [
            'project_sections_id' => 8,
            'project_id' => 8,
            'title' => 'Cafe',
            'slug' => 'cafe',
            'description' => 'Contemporary cafe furniture and casual dining space solutions.',
            'status' => 'active',
            'image' => '[{"alt":"Cafe","objectURL":"/img/showroom/cafe.png"}]',
            'sort_order' => 8
        ],
        [
            'project_sections_id' => 9,
            'project_id' => 9,
            'title' => 'Behavioral Health Display Wall',
            'slug' => 'behavioral-health-display-wall',
            'description' => 'Specialized furniture and design solutions for behavioral health facilities.',
            'status' => 'active',
            'image' => '[{"alt":"Behavioral Health Display Wall","objectURL":"/img/showroom/behavioral-health-display-wall.png"}]',
            'sort_order' => 9
        ],
        [
            'project_sections_id' => 10,
            'project_id' => 10,
            'title' => 'Exam Spaces Room 1',
            'slug' => 'exam-spaces-room-1',
            'description' => 'Medical examination room furniture and equipment for healthcare environments.',
            'status' => 'active',
            'image' => '[{"alt":"Exam Spaces Room 1","objectURL":"/img/showroom/exam-spaces-room-1.png"}]',
            'sort_order' => 10
        ],
        [
            'project_sections_id' => 11,
            'project_id' => 11,
            'title' => 'Exam Spaces Room 2',
            'slug' => 'exam-spaces-room-2',
            'description' => 'Advanced medical exam room configurations with modern healthcare furniture.',
            'status' => 'active',
            'image' => '[{"alt":"Exam Spaces Room 2","objectURL":"/img/showroom/exam-spaces-room-2.png"}]',
            'sort_order' => 11
        ],
        [
            'project_sections_id' => 12,
            'project_id' => 12,
            'title' => 'In-between Spaces',
            'slug' => 'in-between-spaces',
            'description' => 'Transitional area furniture for lobbies, corridors, and connecting spaces.',
            'status' => 'active',
            'image' => '[{"alt":"In-between Spaces","objectURL":"/img/showroom/In-between-spaces.png"}]',
            'sort_order' => 12
        ],
    ];


    private $sectionProducts = [
        // Living Room
        [
            'project_section_products_id' => 1,
            'section_id' => 1,
            'product_id' => 1,
            'status' => '{"featured": true, "visible": true}',
            'sort_order' => 1
        ],
        [
            'project_section_products_id' => 2,
            'section_id' => 2,
            'product_id' => 2,
            'status' => '{"featured": false, "visible": true}',
            'sort_order' => 2
        ],
        [
            'project_section_products_id' => 3,
            'section_id' => 3,
            'product_id' => 3,
            'status' => '{"featured": false, "visible": true}',
            'sort_order' => 3
        ],

        // Kitchen
        [
            'project_section_products_id' => 4,
            'section_id' => 4,
            'product_id' => 4,
            'status' => '{"featured": true, "visible": true}',
            'sort_order' => 1
        ],
        [
            'project_section_products_id' => 5,
            'section_id' => 5,
            'product_id' => 5,
            'status' => '{"featured": false, "visible": true}',
            'sort_order' => 2
        ]
    ];

    private $sectionImages = [
        [
            'project_section_images_id' => 1,
            'section_id' => 1,
            'image_link' => '/img/showroom/living-room-1.jpg',
            'image' => '[{"alt":"Modern Living Room","objectURL":"/img/showroom/living-room-1.jpg"}]',
            'status' => '{"active": true}',
            'sort_order' => 1
        ],
        [
            'project_section_images_id' => 2,
            'section_id' => 2,
            'image_link' => '/img/showroom/kitchen-1.jpg',
            'image' => '[{"alt":"Kitchen Design","objectURL":"/img/showroom/kitchen-1.jpg"}]',
            'status' => '{"active": true}',
            'sort_order' => 1
        ],
        [
            'project_section_images_id' => 3,
            'section_id' => 3,
            'image_link' => '/img/showroom/bathroom-1.jpg',
            'image' => '[{"alt":"Elegant Bathroom","objectURL":"/img/showroom/bathroom-1.jpg"}]',
            'status' => '{"active": true}',
            'sort_order' => 1
        ],
        [
            'project_section_images_id' => 4,
            'section_id' => 4,
            'image_link' => '/img/showroom/lighting-1.jpg',
            'image' => '[{"alt":"Lighting Collection","objectURL":"/img/showroom/lighting-1.jpg"}]',
            'status' => '{"active": true}',
            'sort_order' => 1
        ],
        [
            'project_section_images_id' => 5,
            'section_id' => 5,
            'image_link' => '/img/showroom/flooring-1.jpg',
            'image' => '[{"alt":"Flooring Materials","objectURL":"/img/showroom/flooring-1.jpg"}]',
            'status' => '{"active": true}',
            'sort_order' => 1
        ]
    ];

    public function __construct()
    {
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(ShowroomRepositoryInterface::class);
    }

    public function seed(): void
    {
        // echo "Seeding showroom data...\n";
        // exit;
        $this->repository->insertShowroomData([
            'showrooms' => $this->showrooms,
            'sections' => $this->sections,
            'sectionProducts' => $this->sectionProducts,
            'sectionImages' => $this->sectionImages,
        ]);
    }
}
