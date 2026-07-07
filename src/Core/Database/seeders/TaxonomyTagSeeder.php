<?php

declare(strict_types=1);

namespace App\Core\Database\Seeders;

use App\Core\App\KernelCli;
use App\Core\Repositories\PostCategory\TaxonomyItemRepositoryInterface;
use App\Core\Repositories\PostCategory\TaxonomyRepositoryInterface;
use Illuminate\Container\Container;

class TaxonomyTagSeeder
{
    private TaxonomyRepositoryInterface $repository;
    private TaxonomyItemRepositoryInterface $taxonomyItemRepository;
    private KernelCli $kernel;
    private Container $container;
    private $taxonomy = [
        ['taxonomy_id' => 2, 'name' => 'Product Tags', 'post_type' => 'product', 'type' => 'tags', 'site_id' => 1],
    ];
    private $taxonomyContent = [
        ['taxonomy_id' => 2, 'language_id' => 1, 'name' => 'Product Tags', 'slug' => 'product-tags', 'content' => 'product']
    ];
    private $taxonomyItems = [
        ['taxonomy_id' => 2, 'taxonomy_item_id' => 21, 'sort_order' => 1],
        ['taxonomy_id' => 2, 'taxonomy_item_id' => 22, 'sort_order' => 2],
        ['taxonomy_id' => 2, 'taxonomy_item_id' => 23, 'sort_order' => 3],
        ['taxonomy_id' => 2, 'taxonomy_item_id' => 24, 'sort_order' => 4],
        ['taxonomy_id' => 2, 'taxonomy_item_id' => 25, 'sort_order' => 5],
        ['taxonomy_id' => 2, 'taxonomy_item_id' => 26, 'sort_order' => 6],
        ['taxonomy_id' => 2, 'taxonomy_item_id' => 27, 'sort_order' => 7],
        ['taxonomy_id' => 2, 'taxonomy_item_id' => 28, 'sort_order' => 8],
    ];

    private $taxonomyItemContent = [
        ['taxonomy_item_id' => 21, 'language_id' => 1, 'name' => 'Chair', 'slug' => 'chair', 'content' => 'Chair', 'meta_title' => 'Chair', 'meta_description' => 'Chair', 'meta_keywords' => 'Chair'],
        ['taxonomy_item_id' => 22, 'language_id' => 1, 'name' => 'Modern', 'slug' => 'modern', 'content' => 'Modern furniture designs', 'meta_title' => 'Modern Furniture', 'meta_description' => 'Contemporary modern furniture pieces', 'meta_keywords' => 'modern, contemporary, furniture'],
        ['taxonomy_item_id' => 23, 'language_id' => 1, 'name' => 'Vintage', 'slug' => 'vintage', 'content' => 'Vintage and retro furniture', 'meta_title' => 'Vintage Furniture', 'meta_description' => 'Classic vintage and retro furniture styles', 'meta_keywords' => 'vintage, retro, classic, furniture'],
        ['taxonomy_item_id' => 24, 'language_id' => 1, 'name' => 'Wooden', 'slug' => 'wooden', 'content' => 'Solid wood furniture pieces', 'meta_title' => 'Wooden Furniture', 'meta_description' => 'High-quality wooden furniture made from solid wood', 'meta_keywords' => 'wooden, wood, solid wood, furniture'],
        ['taxonomy_item_id' => 25, 'language_id' => 1, 'name' => 'Leather', 'slug' => 'leather', 'content' => 'Leather upholstered furniture', 'meta_title' => 'Leather Furniture', 'meta_description' => 'Premium leather furniture and upholstery', 'meta_keywords' => 'leather, upholstery, premium, furniture'],
        ['taxonomy_item_id' => 26, 'language_id' => 1, 'name' => 'Minimalist', 'slug' => 'minimalist', 'content' => 'Minimalist and simple furniture designs', 'meta_title' => 'Minimalist Furniture', 'meta_description' => 'Clean and simple minimalist furniture', 'meta_keywords' => 'minimalist, simple, clean, furniture'],
        ['taxonomy_item_id' => 27, 'language_id' => 1, 'name' => 'Industrial', 'slug' => 'industrial', 'content' => 'Industrial style furniture', 'meta_title' => 'Industrial Furniture', 'meta_description' => 'Rustic industrial furniture with metal accents', 'meta_keywords' => 'industrial, rustic, metal, furniture'],
        ['taxonomy_item_id' => 28, 'language_id' => 1, 'name' => 'Eco-Friendly', 'slug' => 'eco-friendly', 'content' => 'Environmentally friendly furniture', 'meta_title' => 'Eco-Friendly Furniture', 'meta_description' => 'Sustainable and eco-friendly furniture options', 'meta_keywords' => 'eco-friendly, sustainable, green, furniture'],
    ];

    //Post Tags 
    private $postTaxonomy = [
        ['taxonomy_id' => 3, 'name' => 'Post Categories', 'post_type' => 'post', 'type' => 'categories', 'site_id' => 1],
        ['taxonomy_id' => 4, 'name' => 'Post Tags', 'post_type' => 'post', 'type' => 'tags', 'site_id' => 1],
    ];
    private $postTaxonomyContent = [
        ['taxonomy_id' => 3, 'language_id' => 1, 'name' => 'Post Categories', 'slug' => 'post-categories', 'content' => 'post'],
        ['taxonomy_id' => 4, 'language_id' => 1, 'name' => 'Post Tags', 'slug' => 'post-tags', 'content' => 'post']
    ];
    private $postTaxonomyItems = [
        ['taxonomy_id' => 3, 'taxonomy_item_id' => 31, 'sort_order' => 1],
        ['taxonomy_id' => 3, 'taxonomy_item_id' => 32, 'sort_order' => 2],
        ['taxonomy_id' => 3, 'taxonomy_item_id' => 33, 'sort_order' => 3],
        ['taxonomy_id' => 3, 'taxonomy_item_id' => 34, 'sort_order' => 4],
        ['taxonomy_id' => 3, 'taxonomy_item_id' => 35, 'sort_order' => 5],
        ['taxonomy_id' => 3, 'taxonomy_item_id' => 36, 'sort_order' => 6],
        ['taxonomy_id' => 3, 'taxonomy_item_id' => 37, 'sort_order' => 7],
        ['taxonomy_id' => 3, 'taxonomy_item_id' => 38, 'sort_order' => 8],
        
        ['taxonomy_id' => 4, 'taxonomy_item_id' => 41, 'sort_order' => 1],
        ['taxonomy_id' => 4, 'taxonomy_item_id' => 42, 'sort_order' => 2],
        ['taxonomy_id' => 4, 'taxonomy_item_id' => 43, 'sort_order' => 3],
        ['taxonomy_id' => 4, 'taxonomy_item_id' => 44, 'sort_order' => 4],
        ['taxonomy_id' => 4, 'taxonomy_item_id' => 45, 'sort_order' => 5],
        ['taxonomy_id' => 4, 'taxonomy_item_id' => 46, 'sort_order' => 6],
        ['taxonomy_id' => 4, 'taxonomy_item_id' => 47, 'sort_order' => 7],
        ['taxonomy_id' => 4, 'taxonomy_item_id' => 48, 'sort_order' => 8],
    ];

    private $postTaxonomyItemContent = [
        ['taxonomy_item_id' => 31, 'language_id' => 1, 'name' => 'Furniture Reviews', 'slug' => 'furniture-reviews', 'content' => 'In-depth reviews of furniture pieces and collections', 'meta_title' => 'Furniture Reviews', 'meta_description' => 'Expert reviews and analysis of furniture pieces, brands, and collections', 'meta_keywords' => 'furniture reviews, furniture analysis, furniture ratings'],
        ['taxonomy_item_id' => 32, 'language_id' => 1, 'name' => 'Design Trends', 'slug' => 'design-trends', 'content' => 'Latest trends in furniture design and interior styling', 'meta_title' => 'Furniture Design Trends', 'meta_description' => 'Stay updated with the latest furniture design trends and interior styling ideas', 'meta_keywords' => 'design trends, furniture trends, interior design, styling'],
        ['taxonomy_item_id' => 33, 'language_id' => 1, 'name' => 'DIY Projects', 'slug' => 'diy-projects', 'content' => 'Do-it-yourself furniture projects and tutorials', 'meta_title' => 'DIY Furniture Projects', 'meta_description' => 'Step-by-step DIY furniture projects and woodworking tutorials', 'meta_keywords' => 'diy furniture, woodworking, furniture projects, tutorials'],
        ['taxonomy_item_id' => 34, 'language_id' => 1, 'name' => 'Care & Maintenance', 'slug' => 'care-maintenance', 'content' => 'Tips for furniture care, cleaning, and maintenance', 'meta_title' => 'Furniture Care & Maintenance', 'meta_description' => 'Essential tips for furniture care, cleaning, and long-term maintenance', 'meta_keywords' => 'furniture care, furniture maintenance, cleaning tips'],
        ['taxonomy_item_id' => 35, 'language_id' => 1, 'name' => 'Room Inspiration', 'slug' => 'room-inspiration', 'content' => 'Room design inspiration and furniture arrangement ideas', 'meta_title' => 'Room Design Inspiration', 'meta_description' => 'Beautiful room designs and furniture arrangement inspiration for every space', 'meta_keywords' => 'room inspiration, interior design, furniture arrangement'],
        ['taxonomy_item_id' => 36, 'language_id' => 1, 'name' => 'Shopping Guides', 'slug' => 'shopping-guides', 'content' => 'Comprehensive guides for buying furniture', 'meta_title' => 'Furniture Shopping Guides', 'meta_description' => 'Expert shopping guides to help you choose the perfect furniture', 'meta_keywords' => 'furniture shopping, buying guides, furniture selection'],
        ['taxonomy_item_id' => 37, 'language_id' => 1, 'name' => 'Materials & Finishes', 'slug' => 'materials-finishes', 'content' => 'Information about furniture materials and finishes', 'meta_title' => 'Furniture Materials & Finishes', 'meta_description' => 'Learn about different furniture materials, finishes, and their characteristics', 'meta_keywords' => 'furniture materials, wood types, finishes, upholstery'],
        ['taxonomy_item_id' => 38, 'language_id' => 1, 'name' => 'Budget Tips', 'slug' => 'budget-tips', 'content' => 'Affordable furniture options and money-saving tips', 'meta_title' => 'Budget Furniture Tips', 'meta_description' => 'Smart tips for finding quality furniture on a budget', 'meta_keywords' => 'budget furniture, affordable furniture, money saving tips'],

        ['taxonomy_item_id' => 41, 'language_id' => 1, 'name' => 'Living Room', 'slug' => 'living-room', 'content' => 'Living room furniture and decor ideas', 'meta_title' => 'Living Room Furniture', 'meta_description' => 'Beautiful living room furniture and decor inspiration', 'meta_keywords' => 'living room, sofa, coffee table, entertainment center'],
        ['taxonomy_item_id' => 42, 'language_id' => 1, 'name' => 'Bedroom', 'slug' => 'bedroom', 'content' => 'Bedroom furniture and design solutions', 'meta_title' => 'Bedroom Furniture', 'meta_description' => 'Complete bedroom furniture sets and design ideas', 'meta_keywords' => 'bedroom, bed frame, nightstand, dresser, wardrobe'],
        ['taxonomy_item_id' => 43, 'language_id' => 1, 'name' => 'Dining Room', 'slug' => 'dining-room', 'content' => 'Dining room furniture and table settings', 'meta_title' => 'Dining Room Furniture', 'meta_description' => 'Elegant dining room furniture and table arrangements', 'meta_keywords' => 'dining room, dining table, chairs, buffet, china cabinet'],
        ['taxonomy_item_id' => 44, 'language_id' => 1, 'name' => 'Office', 'slug' => 'office', 'content' => 'Home office and workspace furniture', 'meta_title' => 'Office Furniture', 'meta_description' => 'Productive home office furniture and workspace solutions', 'meta_keywords' => 'office, desk, chair, bookshelf, filing cabinet'],
        ['taxonomy_item_id' => 45, 'language_id' => 1, 'name' => 'Outdoor', 'slug' => 'outdoor', 'content' => 'Outdoor and patio furniture', 'meta_title' => 'Outdoor Furniture', 'meta_description' => 'Durable outdoor furniture for patios and gardens', 'meta_keywords' => 'outdoor, patio, garden, deck, weather-resistant'],
        ['taxonomy_item_id' => 46, 'language_id' => 1, 'name' => 'Storage', 'slug' => 'storage', 'content' => 'Storage solutions and organizational furniture', 'meta_title' => 'Storage Furniture', 'meta_description' => 'Smart storage furniture and organizational solutions', 'meta_keywords' => 'storage, organization, shelves, cabinets, closets'],
        ['taxonomy_item_id' => 47, 'language_id' => 1, 'name' => 'Kids', 'slug' => 'kids', 'content' => 'Children\'s furniture and nursery items', 'meta_title' => 'Kids Furniture', 'meta_description' => 'Safe and fun furniture for children and nurseries', 'meta_keywords' => 'kids, children, nursery, bunk beds, playroom'],
        ['taxonomy_item_id' => 48, 'language_id' => 1, 'name' => 'Accent', 'slug' => 'accent', 'content' => 'Accent furniture and decorative pieces', 'meta_title' => 'Accent Furniture', 'meta_description' => 'Statement accent furniture and decorative elements', 'meta_keywords' => 'accent, decorative, statement pieces, side tables, ottomans'],
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
        // $this->repository->insertMultiple($this->taxonomy);
        // $this->repository->insertTaxonomyContents($this->taxonomyContent);
        // $this->taxonomyItemRepository->insertMultiple($this->taxonomyItems);
        // $this->taxonomyItemRepository->insertTaxonomyItemContents($this->taxonomyItemContent);

        //Post Tags 

        $this->repository->insertMultiple($this->postTaxonomy);
        $this->repository->insertTaxonomyContents($this->postTaxonomyContent);
        $this->taxonomyItemRepository->insertMultiple($this->postTaxonomyItems);
        $this->taxonomyItemRepository->insertTaxonomyItemContents($this->postTaxonomyItemContent);
    }
} 