<?php

declare(strict_types=1);

namespace App\Core\Database\seeders;

use App\Core\App\KernelCli;
use App\Core\Repositories\Product\ProductRepositoryInterface;

use Illuminate\Container\Container;

class ProductCategoryTaxonomySeeder
{
    private ProductRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;

    private $taxonomyItems = [
        [
            'taxonomy_item_id' => 6,
            'taxonomy_id' => 1,
            'image' => '{"objectURL": "/img/bg/home/home_cat_workstation.jpg"}',
            'template' => 'category',
            'parent_id' => null,
            'item_id' => 1,
            'sort_order' => 1,
            'status' => 1
        ],
        [
            'taxonomy_item_id' => 7,
            'taxonomy_id' => 1,
            'image' => null,
            'template' => 'category',
            'parent_id' => null,
            'item_id' => 2,
            'sort_order' => 2,
            'status' => 1
        ],
        [
            'taxonomy_item_id' => 8,
            'taxonomy_id' => 1,
            'image' => null,
            'template' => 'category',
            'parent_id' => null,
            'item_id' => 3,
            'sort_order' => 3,
            'status' => 1
        ],
        [
            'taxonomy_item_id' => 9,
            'taxonomy_id' => 1,
            'image' => '{"objectURL": "/img/bg/home/home_cat_seating.jpg"}',
            'template' => 'category',
            'parent_id' => null,
            'item_id' => 4,
            'sort_order' => 4,
            'status' => 1
        ],
        [
            'taxonomy_item_id' => 10,
            'taxonomy_id' => 1,
            'image' => null,
            'template' => 'category',
            'parent_id' => null,
            'item_id' => 5,
            'sort_order' => 5,
            'status' => 1
        ],
        [
            'taxonomy_item_id' => 11,
            'taxonomy_id' => 1,
            'image' => '{"objectURL": "/img/bg/home/home_cat_chair.jpg"}',
            'template' => 'category',
            'parent_id' => null,
            'item_id' => 6,
            'sort_order' => 6,
            'status' => 1
        ]
    ];

    private $taxonomyItemContents = [
        [
            'taxonomy_item_id' => 6,
            'language_id' => 1,
            'name' => 'Workstations',
            'slug' => 'workstations',
            'content' => 'Professional workstations designed for modern office environments. Features ergonomic design and modular configurations.',
            'meta_title' => 'Workstations - Professional Office Solutions',
            'meta_description' => 'Discover our range of professional workstations designed for modern office environments with ergonomic design and modular configurations.',
            'meta_keywords' => 'workstations, office furniture, ergonomic design, modular configurations, professional workspace',
            'link' => '#'
        ],
        [
            'taxonomy_item_id' => 7,
            'language_id' => 1,
            'name' => 'Desks',
            'slug' => 'desks',
            'content' => 'High-quality desks for various office settings. From executive desks to collaborative workstations.',
            'meta_title' => 'Desks - Office Desk Solutions',
            'meta_description' => 'Explore our collection of high-quality desks for various office settings, from executive desks to collaborative workstations.',
            'meta_keywords' => 'desks, office desks, executive desks, collaborative workstations, office furniture',
            'link' => '#'
        ],
        [
            'taxonomy_item_id' => 8,
            'language_id' => 1,
            'name' => 'Tables',
            'slug' => 'tables',
            'content' => 'Versatile table solutions for meetings, conferences, and collaborative spaces.',
            'meta_title' => 'Tables - Meeting and Conference Solutions',
            'meta_description' => 'Versatile table solutions for meetings, conferences, and collaborative spaces designed for productivity.',
            'meta_keywords' => 'tables, conference tables, meeting tables, collaborative spaces, office furniture',
            'link' => '#'
        ],
        [
            'taxonomy_item_id' => 9,
            'language_id' => 1,
            'name' => 'Seating',
            'slug' => 'seating',
            'content' => 'Comfortable and ergonomic seating solutions for all office environments.',
            'meta_title' => 'Seating - Ergonomic Office Chairs',
            'meta_description' => 'Comfortable and ergonomic seating solutions for all office environments designed for long-term comfort.',
            'meta_keywords' => 'seating, office chairs, ergonomic chairs, comfortable seating, office furniture',
            'link' => '#'
        ],
        [
            'taxonomy_item_id' => 10,
            'language_id' => 1,
            'name' => 'Storage',
            'slug' => 'storage',
            'content' => 'Efficient storage solutions to keep your workspace organized and clutter-free.',
            'meta_title' => 'Storage - Office Storage Solutions',
            'meta_description' => 'Efficient storage solutions to keep your workspace organized and clutter-free with modern design.',
            'meta_keywords' => 'storage, office storage, filing cabinets, organizers, workspace organization',
            'link' => '#'
        ],
        [
            'taxonomy_item_id' => 11,
            'language_id' => 1,
            'name' => 'Chair',
            'slug' => 'chair',
            'content' => 'Premium office chairs designed for comfort, support, and productivity.',
            'meta_title' => 'Chair - Premium Office Chairs',
            'meta_description' => 'Premium office chairs designed for comfort, support, and productivity in any work environment.',
            'meta_keywords' => 'chair, office chair, premium chairs, ergonomic chairs, comfortable seating',
            'link' => '#'
        ]
    ];

    private $productSubCategoriesTaxonomyItems = [
        // Workstations subcategories
        [
            'taxonomy_item_id' => 12,
            'taxonomy_id' => 1,
            'image' => null,
            'template' => 'subcategory',
            'parent_id' => 6,
            'item_id' => 101,
            'sort_order' => 1,
            'status' => 1
        ],
        [
            'taxonomy_item_id' => 13,
            'taxonomy_id' => 1,
            'image' => null,
            'template' => 'subcategory',
            'parent_id' => 6,
            'item_id' => 102,
            'sort_order' => 2,
            'status' => 1
        ],
        
        // Desks subcategories
        [
            'taxonomy_item_id' => 14,
            'taxonomy_id' => 1,
            'image' => null,
            'template' => 'subcategory',
            'parent_id' => 7,
            'item_id' => 201,
            'sort_order' => 1,
            'status' => 1
        ],
        [
            'taxonomy_item_id' => 15,
            'taxonomy_id' => 1,
            'image' => null,
            'template' => 'subcategory',
            'parent_id' => 7,
            'item_id' => 202,
            'sort_order' => 2,
            'status' => 1
        ],
        
        // Tables subcategories
        [
            'taxonomy_item_id' => 16,
            'taxonomy_id' => 1,
            'image' => null,
            'template' => 'subcategory',
            'parent_id' => 8,
            'item_id' => 301,
            'sort_order' => 1,
            'status' => 1
        ],
        [
            'taxonomy_item_id' => 17,
            'taxonomy_id' => 1,
            'image' => null,
            'template' => 'subcategory',
            'parent_id' => 8,
            'item_id' => 302,
            'sort_order' => 2,
            'status' => 1
        ],
        
        // Seating subcategories
        [
            'taxonomy_item_id' => 18,
            'taxonomy_id' => 1,
            'image' => null,
            'template' => 'subcategory',
            'parent_id' => 9,
            'item_id' => 401,
            'sort_order' => 1,
            'status' => 1
        ],
        [
            'taxonomy_item_id' => 19,
            'taxonomy_id' => 1,
            'image' => null,
            'template' => 'subcategory',
            'parent_id' => 9,
            'item_id' => 402,
            'sort_order' => 2,
            'status' => 1
        ],
        
        // Storage subcategories
        [
            'taxonomy_item_id' => 20,
            'taxonomy_id' => 1,
            'image' => null,
            'template' => 'subcategory',
            'parent_id' => 10,
            'item_id' => 501,
            'sort_order' => 1,
            'status' => 1
        ],
        [
            'taxonomy_item_id' => 21,
            'taxonomy_id' => 1,
            'image' => null,
            'template' => 'subcategory',
            'parent_id' => 10,
            'item_id' => 502,
            'sort_order' => 2,
            'status' => 1
        ],
        
        // Chair subcategories
        [
            'taxonomy_item_id' => 22,
            'taxonomy_id' => 1,
            'image' => null,
            'template' => 'subcategory',
            'parent_id' => 11,
            'item_id' => 501,
            'sort_order' => 1,
            'status' => 1
        ]
    ];

    private $productSubCategoriesTaxonomyItemContents = [
        // Workstations subcategories content
        [
            'taxonomy_item_id' => 12,
            'language_id' => 1,
            'name' => 'Workstations',
            'slug' => 'workstations',
            'content' => 'Professional workstations designed for collaborative and individual work environments.',
            'meta_title' => 'Workstations - Professional Office Workstations',
            'meta_description' => 'Professional workstations designed for collaborative and individual work environments with ergonomic features.',
            'meta_keywords' => 'workstations, office workstations, professional workstations, collaborative workspace',
            'link' => 'service.html'
        ],
        [
            'taxonomy_item_id' => 13,
            'language_id' => 1,
            'name' => 'Workstations Screens',
            'slug' => 'workstations-screens',
            'content' => 'Workstation screens and dividers for privacy and focused work environments.',
            'meta_title' => 'Workstations Screens - Privacy Solutions',
            'meta_description' => 'Workstation screens and dividers for privacy and focused work environments.',
            'meta_keywords' => 'workstation screens, privacy screens, office dividers, focused work',
            'link' => 'service-details.html'
        ],
        
        // Desks subcategories content
        [
            'taxonomy_item_id' => 14,
            'language_id' => 1,
            'name' => 'Desks',
            'slug' => 'desks',
            'content' => 'High-quality desks for various office settings and work requirements.',
            'meta_title' => 'Desks - Office Desk Solutions',
            'meta_description' => 'High-quality desks for various office settings and work requirements.',
            'meta_keywords' => 'desks, office desks, work desks, professional desks',
            'link' => 'service.html'
        ],
        [
            'taxonomy_item_id' => 15,
            'language_id' => 1,
            'name' => 'Desks System',
            'slug' => 'desks-system',
            'content' => 'Modular desk systems for flexible office configurations and collaborative spaces.',
            'meta_title' => 'Desks System - Modular Office Solutions',
            'meta_description' => 'Modular desk systems for flexible office configurations and collaborative spaces.',
            'meta_keywords' => 'desk systems, modular desks, flexible office, collaborative desks',
            'link' => 'service.html'
        ],
        
        // Tables subcategories content
        [
            'taxonomy_item_id' => 16,
            'language_id' => 1,
            'name' => 'Tables',
            'slug' => 'tables',
            'content' => 'Versatile table solutions for meetings, conferences, and collaborative spaces.',
            'meta_title' => 'Tables - Meeting and Conference Tables',
            'meta_description' => 'Versatile table solutions for meetings, conferences, and collaborative spaces.',
            'meta_keywords' => 'tables, meeting tables, conference tables, collaborative tables',
            'link' => 'service.html'
        ],
        [
            'taxonomy_item_id' => 17,
            'language_id' => 1,
            'name' => 'Boardroom Tables',
            'slug' => 'boardroom-tables',
            'content' => 'Professional boardroom tables for executive meetings and presentations.',
            'meta_title' => 'Boardroom Tables - Executive Meeting Solutions',
            'meta_description' => 'Professional boardroom tables for executive meetings and presentations.',
            'meta_keywords' => 'boardroom tables, executive tables, meeting tables, presentation tables',
            'link' => 'service.html'
        ],
        
        // Seating subcategories content
        [
            'taxonomy_item_id' => 18,
            'language_id' => 1,
            'name' => 'Seating',
            'slug' => 'seating',
            'content' => 'Comfortable and ergonomic seating solutions for all office environments.',
            'meta_title' => 'Seating - Ergonomic Office Seating',
            'meta_description' => 'Comfortable and ergonomic seating solutions for all office environments.',
            'meta_keywords' => 'seating, office seating, ergonomic seating, comfortable chairs',
            'link' => 'service.html'
        ],
        [
            'taxonomy_item_id' => 19,
            'language_id' => 1,
            'name' => 'Boardroom Tables',
            'slug' => 'boardroom-tables-seating',
            'content' => 'Professional boardroom seating for executive meetings and presentations.',
            'meta_title' => 'Boardroom Tables - Executive Seating',
            'meta_description' => 'Professional boardroom seating for executive meetings and presentations.',
            'meta_keywords' => 'boardroom seating, executive chairs, meeting chairs, presentation seating',
            'link' => 'service.html'
        ],
        
        // Storage subcategories content
        [
            'taxonomy_item_id' => 20,
            'language_id' => 1,
            'name' => 'Storage',
            'slug' => 'storage',
            'content' => 'Efficient storage solutions to keep your workspace organized and clutter-free.',
            'meta_title' => 'Storage - Office Storage Solutions',
            'meta_description' => 'Efficient storage solutions to keep your workspace organized and clutter-free.',
            'meta_keywords' => 'storage, office storage, filing cabinets, organizers',
            'link' => 'service.html'
        ],
        [
            'taxonomy_item_id' => 21,
            'language_id' => 1,
            'name' => 'Boardroom Tables',
            'slug' => 'boardroom-tables-storage',
            'content' => 'Storage solutions for boardroom and executive office environments.',
            'meta_title' => 'Boardroom Tables - Executive Storage',
            'meta_description' => 'Storage solutions for boardroom and executive office environments.',
            'meta_keywords' => 'boardroom storage, executive storage, office cabinets, filing solutions',
            'link' => 'service.html'
        ],
        
        // Chair subcategories content
        [
            'taxonomy_item_id' => 22,
            'language_id' => 1,
            'name' => 'Storage',
            'slug' => 'storage-chair',
            'content' => 'Storage solutions designed for chair and seating environments.',
            'meta_title' => 'Storage - Chair Storage Solutions',
            'meta_description' => 'Storage solutions designed for chair and seating environments.',
            'meta_keywords' => 'chair storage, seating storage, office storage, furniture storage',
            'link' => 'service.html'
        ]
    ];

    private $productTaxonomies = [
        // Miro - Product ID 1 (Workstations, Desks)
        ['product_id' => 1, 'taxonomy_item_id' => 6],
        ['product_id' => 1, 'taxonomy_item_id' => 7],
        
        // Miro S - Product ID 2 (Tables, Seating)
        ['product_id' => 2, 'taxonomy_item_id' => 8],
        ['product_id' => 2, 'taxonomy_item_id' => 9],
        
        // Kove - Product ID 3 (Storage, Chair)
        ['product_id' => 3, 'taxonomy_item_id' => 10],
        ['product_id' => 3, 'taxonomy_item_id' => 11],
        
        // Hana - Product ID 5 (Workstations, Tables)
        ['product_id' => 5, 'taxonomy_item_id' => 6],
        ['product_id' => 5, 'taxonomy_item_id' => 8],
        
        // Zak - Product ID 6 (Desks, Seating)
        ['product_id' => 6, 'taxonomy_item_id' => 7],
        ['product_id' => 6, 'taxonomy_item_id' => 9],
        
        // Sonic Task - Product ID 7 (Storage, Chair)
        ['product_id' => 7, 'taxonomy_item_id' => 10],
        ['product_id' => 7, 'taxonomy_item_id' => 11],
        
        // Sonic - Product ID 8 (Workstations, Desks)
        ['product_id' => 8, 'taxonomy_item_id' => 6],
        ['product_id' => 8, 'taxonomy_item_id' => 7],
        
        // Space - Product ID 9 (Tables, Seating)
        ['product_id' => 9, 'taxonomy_item_id' => 8],
        ['product_id' => 9, 'taxonomy_item_id' => 9],
        
        // Zed - Product ID 10 (Storage, Chair)
        ['product_id' => 10, 'taxonomy_item_id' => 10],
        ['product_id' => 10, 'taxonomy_item_id' => 11],
        
        // Alex - Product ID 11 (Workstations, Tables)
        ['product_id' => 11, 'taxonomy_item_id' => 6],
        ['product_id' => 11, 'taxonomy_item_id' => 8],
        
    ]; 

    public function __construct()
    {
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(ProductRepositoryInterface::class);
    }

    public function seed(): void
    {
        $this->repository->insertProductTaxonomies([
            'taxonomyItems' => $this->taxonomyItems, 
            'taxonomyItemContents' => $this->taxonomyItemContents,
            'productTaxonomies' => $this->productTaxonomies,
            'productSubCategoriesTaxonomyItems' => $this->productSubCategoriesTaxonomyItems,
            'productSubCategoriesTaxonomyItemContents' => $this->productSubCategoriesTaxonomyItemContents
        ]);
    }
    

} 