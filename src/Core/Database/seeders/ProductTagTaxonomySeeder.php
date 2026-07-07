<?php

declare(strict_types=1);

namespace App\Core\Database\seeders;

use App\Core\App\KernelCli;
use App\Core\Repositories\Product\ProductRepositoryInterface;

use Illuminate\Container\Container;

class ProductTagTaxonomySeeder
{
    private ProductRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;

    private $taxonomyItems = [
        [
            'taxonomy_item_id' => 1,
            'taxonomy_id' => 2,
            'image' => null,
            'template' => 'tag',
            'parent_id' => null,
            'item_id' => 1,
            'sort_order' => 1,
            'status' => 1
        ],
        [
            'taxonomy_item_id' => 2,
            'taxonomy_id' => 2,
            'image' => null,
            'template' => 'tag',
            'parent_id' => null,
            'item_id' => 2,
            'sort_order' => 2,
            'status' => 1
        ],
        [
            'taxonomy_item_id' => 3,
            'taxonomy_id' => 2,
            'image' => null,
            'template' => 'tag',
            'parent_id' => null,
            'item_id' => 3,
            'sort_order' => 3,
            'status' => 1
        ],
        [
            'taxonomy_item_id' => 4,
            'taxonomy_id' => 2,
            'image' => null,
            'template' => 'tag',
            'parent_id' => null,
            'item_id' => 4,
            'sort_order' => 4,
            'status' => 1
        ],
        [
            'taxonomy_item_id' => 5,
            'taxonomy_id' => 2,
            'image' => null,
            'template' => 'tag',
            'parent_id' => null,
            'item_id' => 5,
            'sort_order' => 5,
            'status' => 1
        ]
    ];

    private $taxonomyItemContents = [
        [
            'taxonomy_item_id' => 1,
            'language_id' => 1,
            'name' => 'AFRDI Certified',
            'slug' => 'afrdi-certified',
            'content' => 'Products certified by AFRDI (Australian Furniture Research and Development Institute) for quality and safety standards.',
            'meta_title' => 'AFRDI Certified Products - Quality Assurance',
            'meta_description' => 'Browse our collection of AFRDI certified products that meet the highest quality and safety standards.',
            'meta_keywords' => 'afrdi certified, quality assurance, safety standards, furniture certification',
            'link' => '/tags/afrdi-certified'
        ],
        [
            'taxonomy_item_id' => 2,
            'language_id' => 1,
            'name' => 'OBP Certified',
            'slug' => 'obp-certified',
            'content' => 'Products certified by OBP (Office Business Products) for workplace safety and ergonomic standards.',
            'meta_title' => 'OBP Certified Products - Workplace Safety',
            'meta_description' => 'Discover our OBP certified products designed for workplace safety and ergonomic comfort.',
            'meta_keywords' => 'obp certified, workplace safety, ergonomic standards, office certification',
            'link' => '/tags/obp-certified'
        ],
        [
            'taxonomy_item_id' => 3,
            'language_id' => 1,
            'name' => 'Some Tag Name Here',
            'slug' => 'some-tag-name-here',
            'content' => 'Products featuring innovative design and modern functionality for contemporary workspaces.',
            'meta_title' => 'Some Tag Name Here - Innovative Design',
            'meta_description' => 'Explore products with innovative design and modern functionality for contemporary workspaces.',
            'meta_keywords' => 'innovative design, modern functionality, contemporary workspace, design innovation',
            'link' => '/tags/some-tag-name-here'
        ],
        [
            'taxonomy_item_id' => 4,
            'language_id' => 1,
            'name' => 'Tag Name Here',
            'slug' => 'tag-name-here',
            'content' => 'Premium quality products designed for professional environments and long-term durability.',
            'meta_title' => 'Tag Name Here - Premium Quality',
            'meta_description' => 'Premium quality products designed for professional environments and long-term durability.',
            'meta_keywords' => 'premium quality, professional environment, durability, quality products',
            'link' => '/tags/tag-name-here'
        ],
        [
            'taxonomy_item_id' => 5,
            'language_id' => 1,
            'name' => 'Tag Name Here As Well',
            'slug' => 'tag-name-here-as-well',
            'content' => 'Versatile products suitable for various applications and workplace configurations.',
            'meta_title' => 'Tag Name Here As Well - Versatile Solutions',
            'meta_description' => 'Versatile products suitable for various applications and workplace configurations.',
            'meta_keywords' => 'versatile products, various applications, workplace configurations, flexible solutions',
            'link' => '/tags/tag-name-here-as-well'
        ]
    ];

    private $productTaxonomies = [
        // Miro - Product ID 1 (AFRDI Certified, OBP Certified)
        ['product_id' => 1, 'taxonomy_item_id' => 1],
        ['product_id' => 1, 'taxonomy_item_id' => 2],
        
        // Miro S - Product ID 2 (AFRDI Certified, OBP Certified, Some Tag Name Here, Tag Name Here, Tag Name Here As Well)
        ['product_id' => 2, 'taxonomy_item_id' => 1],
        ['product_id' => 2, 'taxonomy_item_id' => 2],
        ['product_id' => 2, 'taxonomy_item_id' => 3],
        ['product_id' => 2, 'taxonomy_item_id' => 4],
        ['product_id' => 2, 'taxonomy_item_id' => 5],
        
        // Kove - Product ID 3 (AFRDI Certified, OBP Certified, Some Tag Name Here, Tag Name Here, Tag Name Here As Well)
        ['product_id' => 3, 'taxonomy_item_id' => 1],
        ['product_id' => 3, 'taxonomy_item_id' => 2],
        ['product_id' => 3, 'taxonomy_item_id' => 3],
        ['product_id' => 3, 'taxonomy_item_id' => 4],
        ['product_id' => 3, 'taxonomy_item_id' => 5],
        
        
        // Hana - Product ID 5 (AFRDI Certified, OBP Certified, Some Tag Name Here, Tag Name Here, Tag Name Here As Well)
        ['product_id' => 5, 'taxonomy_item_id' => 1],
        ['product_id' => 5, 'taxonomy_item_id' => 2],
        ['product_id' => 5, 'taxonomy_item_id' => 3],
        ['product_id' => 5, 'taxonomy_item_id' => 4],
        ['product_id' => 5, 'taxonomy_item_id' => 5],
        
        // Zak - Product ID 6 (AFRDI Certified, OBP Certified, Some Tag Name Here, Tag Name Here, Tag Name Here As Well)
        ['product_id' => 6, 'taxonomy_item_id' => 1],
        ['product_id' => 6, 'taxonomy_item_id' => 2],
        ['product_id' => 6, 'taxonomy_item_id' => 3],
        ['product_id' => 6, 'taxonomy_item_id' => 4],
        ['product_id' => 6, 'taxonomy_item_id' => 5],
        
        // Sonic Task - Product ID 7 (AFRDI Certified, OBP Certified, Some Tag Name Here, Tag Name Here, Tag Name Here As Well)
        ['product_id' => 7, 'taxonomy_item_id' => 1],
        ['product_id' => 7, 'taxonomy_item_id' => 2],
        ['product_id' => 7, 'taxonomy_item_id' => 3],
        ['product_id' => 7, 'taxonomy_item_id' => 4],
        ['product_id' => 7, 'taxonomy_item_id' => 5],
        
        // Sonic - Product ID 8 (AFRDI Certified, OBP Certified, Some Tag Name Here, Tag Name Here, Tag Name Here As Well)
        ['product_id' => 8, 'taxonomy_item_id' => 1],
        ['product_id' => 8, 'taxonomy_item_id' => 2],
        ['product_id' => 8, 'taxonomy_item_id' => 3],
        ['product_id' => 8, 'taxonomy_item_id' => 4],
        ['product_id' => 8, 'taxonomy_item_id' => 5],
        
        // Space - Product ID 9 (AFRDI Certified, OBP Certified, Some Tag Name Here, Tag Name Here, Tag Name Here As Well)
        ['product_id' => 9, 'taxonomy_item_id' => 1],
        ['product_id' => 9, 'taxonomy_item_id' => 2],
        ['product_id' => 9, 'taxonomy_item_id' => 3],
        ['product_id' => 9, 'taxonomy_item_id' => 4],
        ['product_id' => 9, 'taxonomy_item_id' => 5],
        
        // Zed - Product ID 10 (AFRDI Certified, OBP Certified, Some Tag Name Here, Tag Name Here, Tag Name Here As Well)
        ['product_id' => 10, 'taxonomy_item_id' => 1],
        ['product_id' => 10, 'taxonomy_item_id' => 2],
        ['product_id' => 10, 'taxonomy_item_id' => 3],
        ['product_id' => 10, 'taxonomy_item_id' => 4],
        ['product_id' => 10, 'taxonomy_item_id' => 5],
        
        // Alex - Product ID 11 (AFRDI Certified, OBP Certified, Some Tag Name Here, Tag Name Here, Tag Name Here As Well)
        ['product_id' => 11, 'taxonomy_item_id' => 1],
        ['product_id' => 11, 'taxonomy_item_id' => 2],
        ['product_id' => 11, 'taxonomy_item_id' => 3],
        ['product_id' => 11, 'taxonomy_item_id' => 4],
        ['product_id' => 11, 'taxonomy_item_id' => 5],
        
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
            'productSubCategoriesTaxonomyItems' => [],
            'productSubCategoriesTaxonomyItemContents' => []
        ]);
    }
    

} 