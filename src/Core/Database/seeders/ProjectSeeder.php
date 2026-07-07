<?php

declare(strict_types=1);

namespace App\Core\Database\seeders;

use App\Core\App\KernelCli;
use App\Core\Repositories\Project\ProjectRepositoryInterface;

use Illuminate\Container\Container;

class ProjectSeeder
{
    private ProjectRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private $projects = [
        [
            'project_id' => 1,
            'site_id' => 1,
            'status_id' => 1,
            'customer_id' => 1,
            'name' => 'Modern Office Complex',
            'slug' => 'modern-office-complex',
            'description' => 'A state-of-the-art office complex featuring sustainable design, smart building technology, and collaborative workspaces.',
            'location' => 'Melbourne CBD',
            'status' => 'completed',
            'image' => '[{"objectURL": "/img/bg/home/home_feature_project_1.jpg"}]',
            'meta_title' => 'Modern Office Complex - Melbourne CBD',
            'meta_description' => 'State-of-the-art office complex with sustainable design and smart building technology.',
            'meta_keywords' => 'office complex, melbourne, sustainable design, smart building',
            'title' => 'Modern Office Complex',
            'label' => 'Commercial',
            'link_text' => 'View Project',
            'is_featured' => 1
        ],
        [
            'project_id' => 2,
            'site_id' => 1,
            'status_id' => 1,
            'customer_id' => 2,
            'name' => 'Luxury Residential Tower',
            'slug' => 'luxury-residential-tower',
            'description' => 'Premium residential development featuring luxury apartments with panoramic city views and world-class amenities.',
            'location' => 'Southbank, Melbourne',
            'status' => 'completed',
            'image' => '[{"objectURL": "/img/bg/home/home_feature_project_2.jpg"}]',
            'meta_title' => 'Luxury Residential Tower - Southbank',
            'meta_description' => 'Premium residential development with panoramic city views and luxury amenities.',
            'meta_keywords' => 'luxury apartments, residential tower, southbank, melbourne',
            'title' => 'Luxury Residential Tower',
            'label' => 'Residential',
            'link_text' => 'View Project',
            'is_featured' => 1
        ],
        [
            'project_id' => 3,
            'site_id' => 1,
            'status_id' => 1,
            'customer_id' => 3,
            'name' => 'Shopping Center Renovation',
            'slug' => 'shopping-center-renovation',
            'description' => 'Complete renovation of an existing shopping center to create a modern retail experience with enhanced customer amenities.',
            'location' => 'Doncaster, Melbourne',
            'status' => 'in-progress',
            'image' => '[{"objectURL": "/img/bg/home/home_feature_project_3.jpg"}]',
            'meta_title' => 'Shopping Center Renovation - Doncaster',
            'meta_description' => 'Modern retail experience with enhanced customer amenities and contemporary design.',
            'meta_keywords' => 'shopping center, renovation, retail, doncaster',
            'title' => 'Shopping Center Renovation',
            'label' => 'Retail',
            'link_text' => 'View Project',
            'is_featured' => 0
        ],
        [
            'project_id' => 4,
            'site_id' => 1,
            'status_id' => 1,
            'customer_id' => 4,
            'name' => 'Healthcare Facility',
            'slug' => 'healthcare-facility',
            'description' => 'Modern healthcare facility designed to provide comprehensive medical services with patient-centered care approach.',
            'location' => 'Geelong, Victoria',
            'status' => 'completed',
            'image' => '[{"objectURL": "/img/bg/home/home_feature_project_4.jpg"}]',
            'meta_title' => 'Healthcare Facility - Geelong',
            'meta_description' => 'Modern healthcare facility with patient-centered care and comprehensive medical services.',
            'meta_keywords' => 'healthcare facility, medical center, geelong, patient care',
            'title' => 'Healthcare Facility',
            'label' => 'Healthcare',
            'link_text' => 'View Project',
            'is_featured' => 0
        ],
        [
            'project_id' => 5,
            'site_id' => 1,
            'status_id' => 1,
            'customer_id' => 5,
            'name' => 'Educational Campus',
            'slug' => 'educational-campus',
            'description' => 'Innovative educational campus featuring modern learning spaces, research facilities, and collaborative environments.',
            'location' => 'Bendigo, Victoria',
            'status' => 'completed',
            'image' => '[{"objectURL": "/img/bg/home/home_feature_project_1.jpg"}]',
            'meta_title' => 'Educational Campus - Bendigo',
            'meta_description' => 'Innovative educational campus with modern learning spaces and research facilities.',
            'meta_keywords' => 'educational campus, learning spaces, research facilities, bendigo',
            'title' => 'Educational Campus',
            'label' => 'Education',
            'link_text' => 'View Project',
            'is_featured' => 1
        ],
        [
            'project_id' => 6,
            'site_id' => 1,
            'status_id' => 1,
            'customer_id' => 6,
            'name' => 'Industrial Warehouse',
            'slug' => 'industrial-warehouse',
            'description' => 'Large-scale industrial warehouse facility designed for efficient logistics operations and modern manufacturing processes.',
            'location' => 'Dandenong, Melbourne',
            'status' => 'in-progress',
            'image' => '[{"objectURL": "/img/bg/home/home_feature_project_2.jpg"}]',
            'meta_title' => 'Industrial Warehouse - Dandenong',
            'meta_description' => 'Large-scale industrial warehouse facility for efficient logistics and manufacturing operations.',
            'meta_keywords' => 'industrial warehouse, logistics, manufacturing, dandenong',
            'title' => 'Industrial Warehouse',
            'label' => 'Industrial',
            'link_text' => 'View Project',
            'is_featured' => 0
        ],
        [
            'project_id' => 7,
            'site_id' => 1,
            'status_id' => 1,
            'customer_id' => 7,
            'name' => 'Hotel Development',
            'slug' => 'hotel-development',
            'description' => 'Boutique hotel development featuring luxury accommodations, fine dining, and world-class hospitality services.',
            'location' => 'St Kilda, Melbourne',
            'status' => 'completed',
            'image' => '[{"objectURL": "/img/bg/home/home_feature_project_3.jpg"}]',
            'meta_title' => 'Hotel Development - St Kilda',
            'meta_description' => 'Boutique hotel development with luxury accommodations and fine dining experiences.',
            'meta_keywords' => 'hotel development, boutique hotel, luxury accommodations, st kilda',
            'title' => 'Hotel Development',
            'label' => 'Hospitality',
            'link_text' => 'View Project',
            'is_featured' => 1
        ],
        [
            'project_id' => 8,
            'site_id' => 1,
            'status_id' => 1,
            'customer_id' => 8,
            'name' => 'Sports Complex',
            'slug' => 'sports-complex',
            'description' => 'Multi-purpose sports complex featuring indoor and outdoor facilities for various athletic activities and community events.',
            'location' => 'Ballarat, Victoria',
            'status' => 'completed',
            'image' => '[{"objectURL": "/img/bg/home/home_feature_project_4.jpg"}]',
            'meta_title' => 'Sports Complex - Ballarat',
            'meta_description' => 'Multi-purpose sports complex with indoor and outdoor facilities for athletic activities.',
            'meta_keywords' => 'sports complex, athletic facilities, community events, ballarat',
            'title' => 'Sports Complex',
            'label' => 'Sports',
            'link_text' => 'View Project',
            'is_featured' => 0
        ],
        [
            'project_id' => 9,
            'site_id' => 1,
            'status_id' => 1,
            'customer_id' => 9,
            'name' => 'Mixed-Use Development',
            'slug' => 'mixed-use-development',
            'description' => 'Innovative mixed-use development combining residential, commercial, and retail spaces in a sustainable urban environment.',
            'location' => 'Footscray, Melbourne',
            'status' => 'in-progress',
            'image' => '{"objectURL": "/img/bg/home/home_feature_project_1.jpg"}',
            'meta_title' => 'Mixed-Use Development - Footscray',
            'meta_description' => 'Innovative mixed-use development combining residential, commercial, and retail spaces.',
            'meta_keywords' => 'mixed-use development, residential, commercial, retail, footscray',
            'title' => 'Mixed-Use Development',
            'label' => 'Mixed-Use',
            'link_text' => 'View Project',
            'is_featured' => 1
        ],
        [
            'project_id' => 10,
            'site_id' => 1,
            'status_id' => 1,
            'customer_id' => 10,
            'name' => 'Cultural Center',
            'slug' => 'cultural-center',
            'description' => 'Modern cultural center designed to showcase local arts, host performances, and provide community gathering spaces.',
            'location' => 'Shepparton, Victoria',
            'status' => 'completed',
            'image' => '[{"objectURL": "/img/bg/home/home_feature_project_2.jpg"}]',
            'meta_title' => 'Cultural Center - Shepparton',
            'meta_description' => 'Modern cultural center showcasing local arts and providing community gathering spaces.',
            'meta_keywords' => 'cultural center, arts, performances, community spaces, shepparton',
            'title' => 'Cultural Center',
            'label' => 'Cultural',
            'link_text' => 'View Project',
            'is_featured' => 0
        ]
    ];

    private $projectImages = [
        [
            'project_id' => 1,
            'image_link' => 'modern-office-complex-exterior.jpg',
            'image' => '["modern-office-complex-exterior.jpg"]',
            'sort_order' => 1,
            'status' => '{"active": true, "featured": false}'
        ],
        [
            'project_id' => 1,
            'image_link' => 'modern-office-complex-interior.jpg',
            'image' => '["modern-office-complex-interior.jpg"]',
            'sort_order' => 2,
            'status' => '{"active": true, "featured": true}'
        ],
        [
            'project_id' => 2,
            'image_link' => 'luxury-residential-tower-exterior.jpg',
            'image' => '["luxury-residential-tower-exterior.jpg"]',
            'sort_order' => 1,
            'status' => '{"active": true, "featured": true}'
        ],
        [
            'project_id' => 2,
            'image_link' => 'luxury-residential-tower-amenities.jpg',
            'image' => '["luxury-residential-tower-amenities.jpg"]',
            'sort_order' => 2,
            'status' => '{"active": true, "featured": false}'
        ],
        [
            'project_id' => 3,
            'image_link' => 'shopping-center-renovation-before.jpg',
            'image' => '["shopping-center-renovation-before.jpg"]',
            'sort_order' => 1,
            'status' => '{"active": true, "featured": false}'
        ],
        [
            'project_id' => 3,
            'image_link' => 'shopping-center-renovation-after.jpg',
            'image' => '["shopping-center-renovation-after.jpg"]',
            'sort_order' => 2,
            'status' => '{"active": true, "featured": true}'
        ],
        [
            'project_id' => 4,
            'image_link' => 'healthcare-facility-exterior.jpg',
            'image' => '["healthcare-facility-exterior.jpg"]',
            'sort_order' => 1,
            'status' => '{"active": true, "featured": true}'
        ],
        [
            'project_id' => 4,
            'image_link' => 'healthcare-facility-interior.jpg',
            'image' => '["healthcare-facility-interior.jpg"]',
            'sort_order' => 2,
            'status' => '{"active": true, "featured": false}'
        ],
        [
            'project_id' => 5,
            'image_link' => 'educational-campus-library.jpg',
            'image' => '["educational-campus-library.jpg"]',
            'sort_order' => 1,
            'status' => '{"active": true, "featured": true}'
        ],
        [
            'project_id' => 5,
            'image_link' => 'educational-campus-classroom.jpg',
            'image' => '["educational-campus-classroom.jpg"]',
            'sort_order' => 2,
            'status' => '{"active": true, "featured": false}'
        ],
        [
            'project_id' => 6,
            'image_link' => 'industrial-warehouse-exterior.jpg',
            'image' => '["industrial-warehouse-exterior.jpg"]',
            'sort_order' => 1,
            'status' => '{"active": true, "featured": false}'
        ],
        [
            'project_id' => 6,
            'image_link' => 'industrial-warehouse-interior.jpg',
            'image' => '["industrial-warehouse-interior.jpg"]',
            'sort_order' => 2,
            'status' => '{"active": true, "featured": true}'
        ],
        [
            'project_id' => 7,
            'image_link' => 'hotel-development-lobby.jpg',
            'image' => '["hotel-development-lobby.jpg"]',
            'sort_order' => 1,
            'status' => '{"active": true, "featured": true}'
        ],
        [
            'project_id' => 7,
            'image_link' => 'hotel-development-room.jpg',
            'image' => '["hotel-development-room.jpg"]',
            'sort_order' => 2,
            'status' => '{"active": true, "featured": false}'
        ],
        [
            'project_id' => 8,
            'image_link' => 'sports-complex-indoor.jpg',
            'image' => '["sports-complex-indoor.jpg"]',
            'sort_order' => 1,
            'status' => '{"active": true, "featured": false}'
        ],
        [
            'project_id' => 8,
            'image_link' => 'sports-complex-outdoor.jpg',
            'image' => '["sports-complex-outdoor.jpg"]',
            'sort_order' => 2,
            'status' => '{"active": true, "featured": true}'
        ],
        [
            'project_id' => 9,
            'image_link' => 'mixed-use-development-exterior.jpg',
            'image' => '["mixed-use-development-exterior.jpg"]',
            'sort_order' => 1,
            'status' => '{"active": true, "featured": true}'
        ],
        [
            'project_id' => 9,
            'image_link' => 'mixed-use-development-common-area.jpg',
            'image' => '["mixed-use-development-common-area.jpg"]',
            'sort_order' => 2,
            'status' => '{"active": true, "featured": false}'
        ],
        [
            'project_id' => 10,
            'image_link' => 'cultural-center-auditorium.jpg',
            'image' => '["cultural-center-auditorium.jpg"]',
            'sort_order' => 1,
            'status' => '{"active": true, "featured": true}'
        ],
        [
            'project_id' => 10,
            'image_link' => 'cultural-center-gallery.jpg',
            'image' => '["cultural-center-gallery.jpg"]',
            'sort_order' => 2,
            'status' => '{"active": true, "featured": false}'
        ]
    ]; 

    public function __construct()
    {
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(ProjectRepositoryInterface::class);
    }

    public function seed(): void
    {
        $this->repository->insertProjects(['projects' => $this->projects, 'projectImages' => $this->projectImages]);
    }
    

} 