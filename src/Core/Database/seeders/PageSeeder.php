<?php

declare(strict_types=1);

namespace App\Core\Database\seeders;

use App\Core\App\KernelCli;
use App\Core\Repositories\Post\PostRepositoryInterface;

use Illuminate\Container\Container;

class PageSeeder
{
    private PostRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private $pages = [
        [
            'post_id' => 11,
            'admin_id' => 1,
            'site_id' => 1,
            'status' => 'publish',
            'image' => '{"objectURL": "/img/bg/furniture/furniture-page-1.jpg"}',
            'comment_status' => 'open',
            'password' => '',
            'parent' => 0,
            'sort_order' => 1,
            'type' => 'page',
            'template' => 'default',
            'comment_count' => 3,
            'views' => 850,
            'description' => 'Discover the latest trends in modern furniture design and how they transform living spaces.'
        ],
        [
            'post_id' => 12,
            'admin_id' => 1,
            'site_id' => 1,
            'status' => 'publish',
            'image' => '{"objectURL": "/img/bg/furniture/furniture-page-2.jpg"}',
            'comment_status' => 'open',
            'password' => '',
            'parent' => 0,
            'sort_order' => 2,
            'type' => 'page',
            'template' => 'default',
            'comment_count' => 6,
            'views' => 1200,
            'description' => 'A comprehensive guide to choosing the perfect furniture for your home office setup.'
        ],
        [
            'post_id' => 13,
            'admin_id' => 2,
            'site_id' => 1,
            'status' => 'publish',
            'image' => '{"objectURL": "/img/bg/furniture/furniture-page-3.jpg"}',
            'comment_status' => 'open',
            'password' => '',
            'parent' => 0,
            'sort_order' => 3,
            'type' => 'page',
            'template' => 'default',
            'comment_count' => 8,
            'views' => 1600,
            'description' => 'Essential tips for selecting durable and stylish furniture for your dining room.'
        ],
        [
            'post_id' => 14,
            'admin_id' => 2,
            'site_id' => 1,
            'status' => 'publish',
            'image' => '{"objectURL": "/img/bg/furniture/furniture-page-4.jpg"}',
            'comment_status' => 'open',
            'password' => '',
            'parent' => 0,
            'sort_order' => 4,
            'type' => 'page',
            'template' => 'default',
            'comment_count' => 4,
            'views' => 950,
            'description' => 'Explore sustainable furniture options that are both eco-friendly and beautifully designed.'
        ],
        [
            'post_id' => 15,
            'admin_id' => 3,
            'site_id' => 1,
            'status' => 'publish',
            'image' => '{"objectURL": "/img/bg/furniture/furniture-page-5.jpg"}',
            'comment_status' => 'open',
            'password' => '',
            'parent' => 0,
            'sort_order' => 5,
            'type' => 'page',
            'template' => 'default',
            'comment_count' => 7,
            'views' => 1350,
            'description' => 'A complete guide to bedroom furniture that creates a peaceful and functional sleep environment.'
        ],
        [
            'post_id' => 16,
            'admin_id' => 3,
            'site_id' => 1,
            'status' => 'publish',
            'image' => '{"objectURL": "/img/bg/furniture/furniture-page-6.jpg"}',
            'comment_status' => 'open',
            'password' => '',
            'parent' => 0,
            'sort_order' => 6,
            'type' => 'page',
            'template' => 'default',
            'comment_count' => 10,
            'views' => 2100,
            'description' => 'Comparing different furniture materials to help you choose the perfect pieces for your home.'
        ],
        [
            'post_id' => 17,
            'admin_id' => 1,
            'site_id' => 1,
            'status' => 'publish',
            'image' => '{"objectURL": "/img/bg/furniture/furniture-page-7.jpg"}',
            'comment_status' => 'open',
            'password' => '',
            'parent' => 0,
            'sort_order' => 7,
            'type' => 'page',
            'template' => 'default',
            'comment_count' => 5,
            'views' => 1100,
            'description' => 'Discover how to integrate smart furniture technology into your modern home design.'
        ],
        [
            'post_id' => 18,
            'admin_id' => 2,
            'site_id' => 1,
            'status' => 'publish',
            'image' => '{"objectURL": "/img/bg/furniture/furniture-page-8.jpg"}',
            'comment_status' => 'open',
            'password' => '',
            'parent' => 0,
            'sort_order' => 8,
            'type' => 'page',
            'template' => 'default',
            'comment_count' => 9,
            'views' => 1750,
            'description' => 'Designing beautiful and functional outdoor furniture for your patio and garden spaces.'
        ],
        [
            'post_id' => 19,
            'admin_id' => 3,
            'site_id' => 1,
            'status' => 'publish',
            'image' => '{"objectURL": "/img/bg/furniture/furniture-page-9.jpg"}',
            'comment_status' => 'open',
            'password' => '',
            'parent' => 0,
            'sort_order' => 9,
            'type' => 'page',
            'template' => 'default',
            'comment_count' => 12,
            'views' => 2400,
            'description' => 'Understanding furniture styles and how to mix different design aesthetics in your home.'
        ],
        [
            'post_id' => 20,
            'admin_id' => 1,
            'site_id' => 1,
            'status' => 'publish',
            'image' => '{"objectURL": "/img/bg/furniture/furniture-page-10.jpg"}',
            'comment_status' => 'open',
            'password' => '',
            'parent' => 0,
            'sort_order' => 10,
            'type' => 'page',
            'template' => 'default',
            'comment_count' => 6,
            'views' => 1300,
            'description' => 'Best practices for furniture maintenance and care to keep your pieces looking beautiful for years.'
        ]
    ];

    private $pageContents = [
        [
            'post_id' => 11,
            'language_id' => 1,
            'name' => 'Modern Furniture Trends: Transforming Living Spaces',
            'slug' => 'modern-furniture-trends-transforming-living-spaces',
            'content' => '<p>Modern furniture design has evolved significantly over the past decade, moving away from traditional heavy pieces toward lighter, more functional designs that prioritize both style and comfort.</p><p>Key elements of modern furniture design include:</p><ul><li>Clean lines and minimalist aesthetics</li><li>Multifunctional pieces that maximize space</li><li>Sustainable materials and eco-friendly options</li><li>Technology integration for smart homes</li><li>Neutral color palettes with bold accents</li></ul><p>Research shows that well-designed furniture can increase home satisfaction by up to 30% and improve daily living quality significantly.</p>',
            'excerpt' => 'Discover how modern furniture design principles can transform your living spaces into beautiful, functional environments that enhance daily life.',
            'label' => 'Design Trends',
            'link_text' => 'Read More',
            'meta_keywords' => 'modern furniture, furniture design, living room furniture, contemporary design, home decor',
            'meta_description' => 'Learn about modern furniture design trends and how they can transform your living spaces into beautiful, functional environments.'
        ],
        [
            'post_id' => 12,
            'language_id' => 1,
            'name' => 'Home Office Furniture: Creating Productive Workspaces',
            'slug' => 'home-office-furniture-creating-productive-workspaces',
            'content' => '<p>Home office furniture is no longer just a trend—it\'s a necessity for modern work life. This comprehensive guide explores the key pieces and principles that make home offices both functional and inspiring.</p><p>Essential home office furniture includes:</p><ul><li>Ergonomic office chairs for comfort and health</li><li>Adjustable standing desks for flexibility</li><li>Storage solutions for organization</li><li>Proper lighting fixtures</li><li>Acoustic panels for sound management</li></ul><p>Investing in quality home office furniture can increase productivity by up to 25% and reduce work-related stress.</p>',
            'excerpt' => 'Explore essential home office furniture pieces that create productive, comfortable, and inspiring work environments.',
            'label' => 'Home Office',
            'link_text' => 'Read More',
            'meta_keywords' => 'home office furniture, ergonomic chair, standing desk, office storage, productivity',
            'meta_description' => 'A comprehensive guide to choosing the perfect furniture for your home office setup.'
        ],
        [
            'post_id' => 13,
            'language_id' => 1,
            'name' => 'Dining Room Furniture: Setting the Perfect Table',
            'slug' => 'dining-room-furniture-setting-the-perfect-table',
            'content' => '<p>Dining room furniture plays a crucial role in creating memorable family gatherings and entertaining experiences. The right pieces can transform simple meals into special occasions.</p><p>Key dining room furniture considerations include:</p><ul><li>Table size and shape for your space</li><li>Comfortable seating for extended meals</li><li>Storage solutions for tableware</li><li>Lighting that enhances the dining experience</li><li>Materials that withstand daily use</li></ul><p>Remember that the dining room is the heart of family life, so choose pieces that balance beauty with durability and comfort.</p>',
            'excerpt' => 'Master the art of dining room furniture selection to create beautiful, functional spaces perfect for family meals and entertaining.',
            'label' => 'Dining Room',
            'link_text' => 'Read More',
            'meta_keywords' => 'dining room furniture, dining table, dining chairs, tableware storage, entertaining',
            'meta_description' => 'Essential tips for selecting durable and stylish furniture for your dining room.'
        ],
        [
            'post_id' => 14,
            'language_id' => 1,
            'name' => 'Sustainable Furniture: Eco-Friendly Design Choices',
            'slug' => 'sustainable-furniture-eco-friendly-design-choices',
            'content' => '<p>Sustainable furniture continues to evolve with new materials and manufacturing processes that prioritize environmental responsibility. 2024 brings exciting innovations in eco-friendly furniture design.</p><p>Top sustainable furniture features include:</p><ul><li>Reclaimed and recycled materials</li><li>FSC-certified wood products</li><li>Low-VOC finishes and adhesives</li><li>Modular designs for longevity</li><li>Local manufacturing to reduce carbon footprint</li></ul><p>Modern sustainable furniture offers both environmental benefits and beautiful design, proving that style and responsibility can coexist.</p>',
            'excerpt' => 'Discover sustainable furniture options that are both eco-friendly and beautifully designed for conscious consumers.',
            'label' => 'Sustainability',
            'link_text' => 'Read More',
            'meta_keywords' => 'sustainable furniture, eco-friendly, recycled materials, FSC certified, green design',
            'meta_description' => 'Explore sustainable furniture options that are both eco-friendly and beautifully designed.'
        ],
        [
            'post_id' => 15,
            'language_id' => 1,
            'name' => 'Bedroom Furniture: Creating Peaceful Sleep Spaces',
            'slug' => 'bedroom-furniture-creating-peaceful-sleep-spaces',
            'content' => '<p>Bedroom furniture design is a crucial element that can make or break your sleep quality and daily well-being. Understanding bedroom design principles helps create environments that support rest and relaxation.</p><p>Essential bedroom furniture principles include:</p><ul><li>Comfortable mattress and bed frame selection</li><li>Proper storage solutions for clothing</li><li>Soft lighting for evening routines</li><li>Sound-absorbing materials</li><li>Temperature-regulating fabrics</li></ul><p>Proper bedroom furniture design enhances sleep quality, improves mood, and can significantly impact overall health and well-being.</p>',
            'excerpt' => 'Master the art of bedroom furniture design to create peaceful, functional spaces that enhance sleep quality and daily living.',
            'label' => 'Bedroom',
            'link_text' => 'Read More',
            'meta_keywords' => 'bedroom furniture, bed frame, mattress, storage, sleep quality',
            'meta_description' => 'Complete guide to bedroom furniture design for creating peaceful and functional sleep environments.'
        ],
        [
            'post_id' => 16,
            'language_id' => 1,
            'name' => 'Furniture Materials: Making the Right Choice',
            'slug' => 'furniture-materials-making-the-right-choice',
            'content' => '<p>Choosing the right furniture materials is one of the most important decisions in any home furnishing project. Each material offers unique benefits, durability, and aesthetic qualities.</p><p>Popular furniture materials include:</p><ul><li>Solid wood: Classic beauty with durability</li><li>Engineered wood: Cost-effective and versatile</li><li>Metal: Modern and industrial appeal</li><li>Upholstered fabrics: Comfort and style</li><li>Glass and acrylic: Contemporary elegance</li></ul><p>Consider factors like lifestyle, budget, maintenance requirements, and room function when selecting furniture materials.</p>',
            'excerpt' => 'Compare different furniture materials to find the perfect options that balance style, durability, and budget for your home.',
            'label' => 'Materials',
            'link_text' => 'Read More',
            'meta_keywords' => 'furniture materials, solid wood, engineered wood, metal furniture, upholstery',
            'meta_description' => 'Comprehensive comparison of furniture materials to help you choose the perfect options for your space and lifestyle.'
        ],
        [
            'post_id' => 17,
            'language_id' => 1,
            'name' => 'Smart Furniture Technology in Modern Homes',
            'slug' => 'smart-furniture-technology-modern-homes',
            'content' => '<p>Smart furniture technology is revolutionizing how we interact with our living spaces. Modern furniture increasingly incorporates technology for enhanced comfort, convenience, and functionality.</p><p>Key smart furniture features include:</p><ul><li>Built-in wireless charging stations</li><li>Adjustable lighting and climate control</li><li>Hidden storage compartments</li><li>Modular designs for flexibility</li><li>Integrated entertainment systems</li></ul><p>Smart furniture technology not only improves convenience but can also increase home value and reduce energy costs over time.</p>',
            'excerpt' => 'Explore how smart furniture technology is transforming modern homes and creating more connected, efficient living spaces.',
            'label' => 'Smart Technology',
            'link_text' => 'Read More',
            'meta_keywords' => 'smart furniture, home automation, wireless charging, modular furniture, tech integration',
            'meta_description' => 'Discover how smart furniture technology integration is transforming modern home design and functionality.'
        ],
        [
            'post_id' => 18,
            'language_id' => 1,
            'name' => 'Outdoor Furniture: Extending Your Living Space',
            'slug' => 'outdoor-furniture-extending-your-living-space',
            'content' => '<p>Outdoor furniture has become an essential extension of modern homes, providing additional areas for relaxation, entertainment, and connection with nature throughout the year.</p><p>Popular outdoor furniture features include:</p><ul><li>Weather-resistant dining sets</li><li>Comfortable lounge seating</li><li>Fire pit tables and heaters</li><li>Storage solutions for cushions</li><li>Shade structures and umbrellas</li></ul><p>Well-designed outdoor furniture can significantly increase your home\'s usable space and create valuable areas for family gatherings and entertainment.</p>',
            'excerpt' => 'Transform your outdoor area into a beautiful and functional living space with the right outdoor furniture choices.',
            'label' => 'Outdoor Living',
            'link_text' => 'Read More',
            'meta_keywords' => 'outdoor furniture, patio furniture, weather-resistant, outdoor dining, garden furniture',
            'meta_description' => 'Design beautiful and functional outdoor furniture spaces that extend your home\'s living area and entertainment options.'
        ],
        [
            'post_id' => 19,
            'language_id' => 1,
            'name' => 'Furniture Styles: Mixing Design Aesthetics',
            'slug' => 'furniture-styles-mixing-design-aesthetics',
            'content' => '<p>Furniture styles play a crucial role in interior design, influencing the overall mood and character of your living spaces. Understanding different styles helps create cohesive and personalized environments.</p><p>Popular furniture styles include:</p><ul><li>Modern: Clean lines and minimal decoration</li><li>Traditional: Classic elegance and detailed craftsmanship</li><li>Industrial: Raw materials and urban appeal</li><li>Scandinavian: Light woods and functional design</li><li>Bohemian: Eclectic and artistic expression</li></ul><p>Strategic mixing of furniture styles can create unique, personalized spaces that reflect your individual taste and lifestyle.</p>',
            'excerpt' => 'Understand how different furniture styles influence interior design and learn to create cohesive, personalized living spaces.',
            'label' => 'Design Styles',
            'link_text' => 'Read More',
            'meta_keywords' => 'furniture styles, modern furniture, traditional furniture, industrial design, scandinavian',
            'meta_description' => 'Learn how different furniture styles influence interior design and how to mix styles to create personalized spaces.'
        ],
        [
            'post_id' => 20,
            'language_id' => 1,
            'name' => 'Furniture Care and Maintenance: Preserving Beauty',
            'slug' => 'furniture-care-maintenance-preserving-beauty',
            'content' => '<p>Effective furniture care and maintenance is essential for preserving the beauty and longevity of your investment pieces. Proper care ensures furniture remains beautiful and functional for years to come.</p><p>Key furniture care principles include:</p><ul><li>Regular cleaning and dusting routines</li><li>Proper protection from sunlight and moisture</li><li>Appropriate cleaning products for different materials</li><li>Seasonal maintenance and inspections</li><li>Professional restoration when needed</li></ul><p>Successful furniture care requires consistent attention, appropriate products, and understanding of your furniture\'s specific needs.</p>',
            'excerpt' => 'Master the essential principles of furniture care and maintenance to keep your pieces looking beautiful for years to come.',
            'label' => 'Care & Maintenance',
            'link_text' => 'Read More',
            'meta_keywords' => 'furniture care, furniture maintenance, cleaning, restoration, preservation',
            'meta_description' => 'Essential furniture care and maintenance principles for preserving the beauty and longevity of your furniture investment.'
        ]
    ];

    public function __construct()
    {
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(PostRepositoryInterface::class);
    }

    public function seed(): void
    {
        $this->repository->insertPosts(['posts' => $this->pages, 'postContents' => $this->pageContents]);
    }
    

} 