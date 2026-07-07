<?php

declare(strict_types=1);

namespace App\Core\Database\Seeders;

use App\Core\App\KernelCli;
use App\Core\Repositories\UserRepositoryInterface;
use App\Core\Repositories\Component\ComponentRepositoryInterface;
use Illuminate\Container\Container;

class ComponentSeeder
{
    private ComponentRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private $data = [
        'components' => [
            [
                'name' => 'allprojects',
                'section_title' => 'All Projects',
                'section_subtitle' => 'We are committed to delivering exceptional projects, utilizing high-quality products, meticulous finishes, and expertly chosen colors.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => 'Load More',
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'herohome',
                'section_title' => '',
                'section_subtitle' => '',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => 'Load More',
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'featuredprojectslider',
                'section_title' => 'Featured Projects Dynamic',
                'section_subtitle' => 'Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas et.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'featureproductsmasonry',
                'section_title' => 'Featured Products',
                'section_subtitle' => 'Discover our premium collection of high-quality products.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'blogslider',
                'section_title' => 'Blogs',
                'section_subtitle' => 'Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas et.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'designresources',
                'section_title' => 'Design Resources',
                'section_subtitle' => 'Access our comprehensive design tools and resources.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'footer',
                'section_title' => '',
                'section_subtitle' => '',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'categoriesslidernav',
                'section_title' => 'Categories',
                'section_subtitle' => 'Browse our product categories.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'virtualshowrooms',
                'section_title' => 'Virtual Showrooms',
                'section_subtitle' => 'Experience our products in immersive virtual environments.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'productsustainablity',
                'section_title' => 'Product Sustainability',
                'section_subtitle' => 'Learn about our commitment to sustainable practices.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'projectdetailmain',
                'section_title' => 'Project Details',
                'section_subtitle' => 'Detailed information about our projects.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'projectgallery',
                'section_title' => 'Project Gallery',
                'section_subtitle' => 'Visual showcase of our completed projects.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'requestcatalogue',
                'section_title' => 'Request Catalogue',
                'section_subtitle' => 'Get your copy of our latest product catalogue.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'salesteam',
                'section_title' => 'Sales Team',
                'section_subtitle' => 'Meet our dedicated sales professionals.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'salesteammelbourne',
                'section_title' => 'Melbourne Sales Team',
                'section_subtitle' => 'Our Melbourne-based sales representatives.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'salesteamsydneys',
                'section_title' => 'Sydney Sales Team',
                'section_subtitle' => 'Our Sydney-based sales representatives.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'showrooms',
                'section_title' => 'Showrooms',
                'section_subtitle' => 'Visit our showrooms to experience our products firsthand.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'site',
                'section_title' => '',
                'section_subtitle' => '',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'user',
                'section_title' => '',
                'section_subtitle' => '',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'ourhistorymasonry',
                'section_title' => 'Our History',
                'section_subtitle' => 'Discover the journey that shaped our company.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'ourprinciple',
                'section_title' => 'Our Principles',
                'section_subtitle' => 'The values that guide our business practices.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'ourprincipleicon',
                'section_title' => 'Our Principles with Icons',
                'section_subtitle' => 'Visual representation of our core principles.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'oursectorscurosel',
                'section_title' => 'Our Sectors',
                'section_subtitle' => 'Explore the diverse sectors we serve.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'productalsolike',
                'section_title' => 'You May Also Like',
                'section_subtitle' => 'Discover related products that might interest you.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'productcalltoaction',
                'section_title' => 'Product Call to Action',
                'section_subtitle' => 'Take action on our featured products.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'productfeature',
                'section_title' => 'Product Features',
                'section_subtitle' => 'Highlighting the key features of our products.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'productrelatedfamily',
                'section_title' => 'Related Product Family',
                'section_subtitle' => 'Explore products from the same family.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'productslider',
                'section_title' => 'Product Slider',
                'section_subtitle' => 'Browse our products in an interactive slider.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'productstorymasonry',
                'section_title' => 'Product Story Masonry',
                'section_subtitle' => 'The stories behind our products.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'imagemasonrygallery',
                'section_title' => 'Image Masonry Gallery',
                'section_subtitle' => 'A beautiful gallery showcasing our work.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'instagramslider',
                'section_title' => 'Instagram Slider',
                'section_subtitle' => 'Latest updates from our Instagram feed.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'manufacturingprocess',
                'section_title' => 'Manufacturing Process',
                'section_subtitle' => 'Discover how our products are made.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'ourdesignprocess',
                'section_title' => 'Our Design Process',
                'section_subtitle' => 'The creative journey behind our designs.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'ourhistory',
                'section_title' => 'Our History',
                'section_subtitle' => 'The story of our company\'s evolution.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'featuredmaterialslider',
                'section_title' => 'Featured Materials',
                'section_subtitle' => 'Premium materials used in our products.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'featureprojectsmasonry',
                'section_title' => 'Featured Projects Masonry',
                'section_subtitle' => 'Showcase of our featured projects.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'governmentsupplier',
                'section_title' => 'Government Supplier',
                'section_subtitle' => 'Our services for government projects.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'blogmain',
                'section_title' => 'Blog Main',
                'section_subtitle' => 'Latest articles and insights from our team.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'booknow',
                'section_title' => 'Book Now',
                'section_subtitle' => 'Schedule your consultation or appointment.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'categoriesmasonry',
                'section_title' => 'Categories Masonry',
                'section_subtitle' => 'Browse our product categories in a masonry layout.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
            ],
            [
                'name' => 'contactus',
                'section_title' => 'Contact Us',
                'section_subtitle' => 'Get in touch with our team.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
            ]
        ],
        'component_items' => [
            // If the associated items are refering to antoher database table, then there will be always one component_item
            // The the query will look into the is_recenet, is_featured property and item_count values to get the items
            // For example in this case all projects component will fetch all projects no condition will be applied
            // And Column will be selected from fields property as a simple columns array
            'allprojects' => [
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => 'project',
                    'model_id' => null,
                    'fields' => [
                        'id',
                        'title',
                        'image',
                        'description',
                        'label'
                    ]
                ]
            ],
            //IN this example is_featured will be true and is_recent can be true as well or false
            // So the query will look into the is_featured and is_recent property and item_count values to get the items
            // And Column will be selected from fields property as a simple columns array
            'featuredprojectslider' => [
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => 'project',
                    'model_id' => null,
                    'is_featured' => true,
                    'is_recent' => true,
                    'item_count' => 6,
                    'fields' => [
                        'image',
                        'label',
                        'title',
                        'description',
                        'link_text'
                    ]
                ]
            ],
            'blogslider' => [
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => 'post',
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 5,
                    'join' => [
                        [
                            'table' => 'post_content',
                            'on' => 'post.post_id = post_content.post_id',
                            'type' => 'LEFT'
                        ]
                    ],
                    'fields' => [
                        'post.image',
                        'post.description',
                        'post_content.label',
                        'post_content.name as title',
                        'post_content.link_text'
                    ]
                ]
            ],
            'featureproductsmasonry' => [
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => 'product',
                    'model_id' => null,
                    'is_featured' => true,
                    'is_recent' => true,
                    'item_count' => 4,
                    'join' => [
                        [
                            'table' => 'product_content',
                            'on' => 'product.product_id = product_content.product_id',
                            'type' => 'LEFT'
                        ]
                    ],
                    'fields' => [
                        'product_content.name as heading',
                        'product.image as img',
                        'product.description as des',
                        'product.class',
                        'product.style'
                    ]
                ]
            ],
            'designresources' => [
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => 'design_resource',
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 4,
                    'fields' => [
                        'img',
                        'title',
                        'description'
                    ]
                ]
            ],
            'categoriesslidernav' => [
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => 'taxonomy_item',
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 4,
                    'join' => [
                        [
                            'table' => 'taxonomy_item_content',
                            'on' => 'taxonomy_item.taxonomy_item_id = taxonomy_item_content.taxonomy_item_id',
                            'type' => 'LEFT'
                        ]
                    ],
                    'fields' => [
                        'taxonomy_item.image',
                        'taxonomy_item_content.name as title',
                        'taxonomy_item_content.meta_description as subTitle',
                        'taxonomy_item_content.link as link'
                    ]
                ]
            ],
            // This is example where model will be null 
            // There fore component_item table will record the associated item
            // If there are 4 items then in the component_item table there will be 4 records
        
            'virtualshowrooms' => [
                [
                    'property_name' => 'items',
                    'component_id' => null,
                    'model' => null,
                    'model_id' => null,
                    'fields' => [
                        'showroom_image' => "{objectURL: '/img/contact-us/explore-1.png'}",
                        'showroom_title' => 'Sydney Showroom',
                        'showroom_book_btn' => 'Book a Virtual Tour',
                        'showroom_view_btn' => 'View Tour'
                    ]
                ],
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'fields' => [
                        'showroom_image' => "{objectURL: '/img/contact-us/explore-2.png'}",
                        'showroom_title' => 'Melbourne Showroom',
                        'showroom_book_btn' => 'Book a Virtual Tour',
                        'showroom_view_btn' => 'View Tour'
                    ]
                ]
            ],
            'productsustainablity' => [
                [
                    'property_name' => 'content',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'fields' => [
                        'image1' => "{objectURL: '/img/product-detail/ocean image.png'}",
                        'section_title' => 'Made With Ocean Bound Plastic',
                        'section_subtitle' => 'Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque. Pellentesque libero donec sit egestas orci consequat est mauris duis.',
                        'section_link_text' => 'View Catalogue',
                        'image2' => "{objectURL: '/img/product-detail/ocean bound plastic chair.png'}"
                    ]
                ]
            ],

            // Need to ensure about image2 and subtitle2
            'projectdetailmain' => [
                [
                    'property_name' => 'content',
                    'component_id' => '',
                    'model' => 'project',
                    'model_id' => '1',
                    'is_recent' => true,
                    'item_count' => 1,
                    'with' => [
                        'images'
                    ],
                    'fields' => [
                        'project.image', // If use . then use . for every field
                        'project.name as title',
                        'project.description as subtitle',
                        'project.description as subtitle2'
                    ]
                ]
            ],
            'projectgallery' => [
                [
                    'property_name' => 'galleryThumb',
                    'component_id' => '',
                    'model' => 'project_image',
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 5,
                    'fields' => [
                        'project_image.image as thumb_image',
                        'project_image.image as image'
                    ]
                ]
            ],
            'requestcatalogue' => [
                [
                    'property_name' => 'content',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'title' => 'Request Catalogue',
                        'description' => 'Get your copy of our latest product catalogue.', // What about description???
                        'form_title' => 'Request Your Catalogue',
                        'form_description' => 'Fill out the form below to receive our latest catalogue.',
                        'button_text' => 'Request Catalogue'
                    ]
                ]
            ],
            'salesteam' => [
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'name' => 'John Smith',
                        'position' => 'Sales Manager', // What about position???
                        'email' => 'john.smith@krost.com.au',
                        'phone' => '+61 2 9557 3055',
                        'image' => '{objectURL: "/img/team/sales-team-1.jpg"}'
                    ]
                ],
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'name' => 'Sarah Johnson',
                        'position' => 'Senior Sales Representative',
                        'email' => 'sarah.johnson@krost.com.au',
                        'phone' => '+61 2 9557 3056',
                        'image' => '{objectURL: "/img/team/sales-team-2.jpg"}'
                    ]
                ],
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'name' => 'Michael Brown',
                        'position' => 'Sales Representative',
                        'email' => 'michael.brown@krost.com.au',
                        'phone' => '+61 2 9557 3057',
                        'image' => '{objectURL: "/img/team/sales-team-3.jpg"}'
                    ]
                ]
            ],
            'salesteammelbourne' => [
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'name' => 'David Wilson',
                        'position' => 'Melbourne Sales Manager',
                        'email' => 'david.wilson@krost.com.au',
                        'phone' => '+61 3 9682 8280',
                        'image' => '{objectURL: "/img/team/melbourne-sales-1.jpg"}'
                    ]
                ],
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'name' => 'Emma Davis',
                        'position' => 'Melbourne Sales Representative',
                        'email' => 'emma.davis@krost.com.au',
                        'phone' => '+61 3 9682 8281',
                        'image' => '{objectURL: "/img/team/melbourne-sales-2.jpg"}'
                    ]
                ]
            ],
            'salesteamsydneys' => [
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'name' => 'James Taylor',
                        'position' => 'Sydney Sales Manager',
                        'email' => 'james.taylor@krost.com.au',
                        'phone' => '+61 2 9557 3055',
                        'image' => '{objectURL: "/img/team/sydney-sales-1.jpg"}'
                    ]
                ],
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'name' => 'Lisa Anderson',
                        'position' => 'Sydney Sales Representative',
                        'email' => 'lisa.anderson@krost.com.au',
                        'phone' => '+61 2 9557 3056',
                        'image' => '{objectURL: "/img/team/sydney-sales-2.jpg"}'
                    ]
                ]
            ],
            'showrooms' => [
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'name' => 'Sydney Showroom',
                        'address' => '33 Ricketty Street, Mascot NSW, 2020',
                        'phone' => '02 9557 3055',
                        'hours' => 'Open weekdays, 8am to 5pm',
                        'image' => '{objectURL: "/img/showrooms/sydney-showroom.jpg"}'
                    ]
                ],
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'name' => 'Melbourne Showroom',
                        'address' => '17-643 spencer st, West Melbourne VIC, 3003',
                        'phone' => '03 9682 8280',
                        'hours' => 'Open weekdays, 9am to 5pm',
                        'image' => '{objectURL: "/img/showrooms/melbourne-showroom.jpg"}'
                    ]
                ]
            ],
            'site' => [
                [
                    'property_name' => 'content',
                    'component_id' => '',
                    'model' => 'site',
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'site.name as site_name',
                        'site.description as site_description',
                        'site.site_settings'
                    ]
                ]
            ],
            'user' => [
                [
                    'property_name' => 'content',
                    'component_id' => '',
                    'model' => 'user',
                    'model_id' => '1',
                    'fields' => [
                        'name',
                        'email',
                        'role',
                        'avatar'
                    ]
                ]
            ],
            'ourhistorymasonry' => [
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'year' => '1985',
                        'title' => 'Company Founded',
                        'description' => 'Krost was established with a vision to create innovative furniture solutions.',
                        'image' => '{objectURL: "/img/history/1985-founded.jpg"}'
                    ]
                ],
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'year' => '1995',
                        'title' => 'First Showroom',
                        'description' => 'Opened our first showroom in Sydney to showcase our products.',
                        'image' => '{objectURL: "/img/history/1995-showroom.jpg"}'
                    ]
                ],
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'year' => '2005',
                        'title' => 'Melbourne Expansion',
                        'description' => 'Expanded operations to Melbourne with a new showroom and warehouse.',
                        'image' => '{objectURL: "/img/history/2005-melbourne.jpg"}'
                    ]
                ],
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'year' => '2015',
                        'title' => 'Sustainability Focus',
                        'description' => 'Launched our sustainability initiative with ocean-bound plastic products.',
                        'image' => '{objectURL: "/img/history/2015-sustainability.jpg"}'
                    ]
                ]
            ],
            'ourprinciple' => [
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'title' => 'Quality',
                        'description' => 'We maintain the highest standards of quality in all our products.',
                        'icon' => 'fa-star'
                    ]
                ],
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'title' => 'Innovation',
                        'description' => 'We continuously innovate to meet evolving customer needs.',
                        'icon' => 'fa-lightbulb'
                    ]
                ],
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'title' => 'Sustainability',
                        'description' => 'We are committed to environmental responsibility in our operations.',
                        'icon' => 'fa-leaf'
                    ]
                ]
            ],
            'ourprincipleicon' => [
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'title' => 'Quality',
                        'description' => 'We maintain the highest standards of quality in all our products.',
                        'icon' => 'fa-star',
                        'icon_color' => '#FFD700'
                    ]
                ],
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'title' => 'Innovation',
                        'description' => 'We continuously innovate to meet evolving customer needs.',
                        'icon' => 'fa-lightbulb',
                        'icon_color' => '#FF6B35'
                    ]
                ],
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'title' => 'Sustainability',
                        'description' => 'We are committed to environmental responsibility in our operations.',
                        'icon' => 'fa-leaf',
                        'icon_color' => '#4CAF50'
                    ]
                ]
            ],
            'oursectorscurosel' => [
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'title' => 'Healthcare',
                        'description' => 'Specialized furniture solutions for healthcare environments.',
                        'image' => '{objectURL: "/img/sectors/healthcare.jpg"}'
                    ]
                ],
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'title' => 'Education',
                        'description' => 'Innovative furniture for educational institutions.',
                        'image' => '{objectURL: "/img/sectors/education.jpg"}'
                    ]
                ],
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'title' => 'Corporate',
                        'description' => 'Professional furniture solutions for corporate offices.',
                        'image' => '{objectURL: "/img/sectors/corporate.jpg"}'
                    ]
                ],
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'title' => 'Government',
                        'description' => 'Reliable furniture for government facilities.',
                        'image' => '{objectURL: "/img/sectors/government.jpg"}'
                    ]
                ]
            ],
            'productalsolike' => [
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => 'product',
                    'model_id' => null,
                    'is_featured' => true,
                    'is_recent' => true,
                    'item_count' => 5,
                    'join' => [
                        [
                            'table' => 'product_content',
                            'on' => 'product.product_id = product_content.product_id',
                            'type' => 'LEFT'
                        ]
                    ],
                    'fields' => [
                        'product_content.name as title',
                        'product.image as image',
                        'product.price as price',
                        'product.category as category' // What about category???
                    ]
                ]
            ],
            'productcalltoaction' => [
                [
                    'property_name' => 'content',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'title' => 'Ready to Transform Your Space?',
                        'description' => 'Contact our sales team to discuss your project requirements.',
                        'button_text' => 'Contact Sales',
                        'button_url' => '/contact'
                    ]
                ]
            ],
            'productfeature' => [
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => 'product_feature',
                    'model_id' => '1',
                    'join' => [
                        [
                            'table' => 'product_content',
                            'on' => 'product.product_id = product_content.product_id',
                            'type' => 'LEFT'
                        ]
                    ],
                    'fields' => [
                        'product_content.name as title',
                        'product.description as description',
                        'product_content.icon as icon'
                    ]
                ]
            ],

            // Have to analysis this component
            // 'productrelatedfamily' => [
            //     [
            //         'property_name' => 'items',
            //         'component_id' => '',
            //         'model' => 'product_family',
            //         'model_id' => '1',
            //         'fields' => [
            //             'family_name' => 'Chwyla Collection',
            //             'description' => 'Modern seating solutions for contemporary spaces.',
            //             'image' => '/img/families/chwyla-family.jpg',
            //             'product_count' => '8'
            //         ]
            //     ],
            //     [
            //         'property_name' => 'items',
            //         'component_id' => '',
            //         'model' => 'product_family',
            //         'model_id' => '2',
            //         'fields' => [
            //             'family_name' => 'Kobe Series',
            //             'description' => 'Professional desk solutions for modern offices.',
            //             'image' => '/img/families/kobe-family.jpg',
            //             'product_count' => '12'
            //         ]
            //     ],
            //     [
            //         'property_name' => 'items',
            //         'component_id' => '',
            //         'model' => 'product_family',
            //         'model_id' => '3',
            //         'fields' => [
            //             'family_name' => 'Sofa Collection',
            //             'description' => 'Comfortable seating for waiting areas and lounges.',
            //             'image' => '/img/families/sofa-family.jpg',
            //             'product_count' => '6'
            //         ]
            //     ]
            // ],
            'productslider' => [
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => 'product',
                    'model_id' => null,
                    'is_featured' => true,
                    'is_recent' => true,
                    'item_count' => 5,
                    'join' => [
                        [
                            'table' => 'product_content',
                            'on' => 'product.product_id = product_content.product_id',
                            'type' => 'LEFT'
                        ]
                    ],
                    'fields' => [
                        'product_content.name as title',
                        'product.image as image',
                        'product.price as price',
                        'product.category as category' // What about category???
                    ]
                ]
            ],
            'productstorymasonry' => [
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => 'product',
                    'model_id' => null,
                    'is_featured' => true,
                    'is_recent' => true,
                    'item_count' => 1,
                    'join' => [
                        [
                            'table' => 'product_content',
                            'on' => 'product.product_id = product_content.product_id',
                            'type' => 'LEFT'
                        ]
                    ],
                    'fields' => [
                        'product_content.name as title',
                        'product.description as description',
                        'product.image as image'
                    ]
                ]
            ],
            'imagemasonrygallery' => [
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => 'media',
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 4,
                    'join' => [
                        [
                            'table' => 'media_content',
                            'on' => 'media.media_id = media_content.media_id',
                            'type' => 'LEFT'
                        ]
                    ],
                    'fields' => [
                        'media_content.name as title',
                        'media.file as image',
                        'media_content.description as description'
                    ]
                ]
            ],
            'instagramslider' => [
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'image' => '{objectURL: "/img/instagram/instagram-1.jpg"}',
                        'caption' => 'Beautiful office space featuring our Chwyla chairs',
                        'likes' => '245',
                        'comments' => '12'
                    ]
                ],
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'image' => '{objectURL: "/img/instagram/instagram-2.jpg"}',
                        'caption' => 'Sustainability in action with our ocean-bound plastic products',
                        'likes' => '189',
                        'comments' => '8'
                    ]
                ],
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'image' => '{objectURL: "/img/instagram/instagram-3.jpg"}',
                        'caption' => 'Behind the scenes of our manufacturing process',
                        'likes' => '312',
                        'comments' => '15'
                    ]
                ],
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'image' => '{objectURL: "/img/instagram/instagram-4.jpg"}',
                        'caption' => 'Customer showcase: Healthcare facility transformation',
                        'likes' => '156',
                        'comments' => '6'
                    ]
                ]
            ],
            'manufacturingprocess' => [
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'step_number' => '1',
                        'title' => 'Design & Planning',
                        'description' => 'Our design team creates detailed plans for each product.',
                        'image' => '{objectURL: "/img/manufacturing/step-1-design.jpg"}'
                    ]
                ],
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'step_number' => '2',
                        'title' => 'Material Selection',
                        'description' => 'Carefully selecting sustainable and durable materials.',
                        'image' => '{objectURL: "/img/manufacturing/step-2-materials.jpg"}'
                    ]
                ],
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'step_number' => '3',
                        'title' => 'Production',
                        'description' => 'Expert craftsmanship using advanced manufacturing techniques.',
                        'image' => '{objectURL: "/img/manufacturing/step-3-production.jpg"}'
                    ]
                ],
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'step_number' => '4',
                        'title' => 'Quality Control',
                        'description' => 'Rigorous testing to ensure the highest standards.',
                        'image' => '{objectURL: "/img/manufacturing/step-4-quality.jpg"}'
                    ]
                ]
            ],
            'ourdesignprocess' => [
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'step_number' => '1',
                        'title' => 'Research & Inspiration',
                        'description' => 'Understanding market needs and gathering inspiration.',
                        'icon' => 'fa-search'
                    ]
                ],
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'step_number' => '2',
                        'title' => 'Concept Development',
                        'description' => 'Creating initial concepts and prototypes.',
                        'icon' => 'fa-lightbulb'
                    ]
                ],
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'step_number' => '3',
                        'title' => 'Refinement',
                        'description' => 'Iterating and improving based on feedback.',
                        'icon' => 'fa-cogs'
                    ]
                ],
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'step_number' => '4',
                        'title' => 'Final Design',
                        'description' => 'Perfecting the design for production.',
                        'icon' => 'fa-check-circle'
                    ]
                ]
            ],
            'ourhistory' => [
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'year' => '1985',
                        'title' => 'Company Founded',
                        'description' => 'Krost was established with a vision to create innovative furniture solutions.',
                        'image' => '{objectURL: "/img/history/1985-founded.jpg"}'
                    ]
                ],
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'year' => '1995',
                        'title' => 'First Showroom',
                        'description' => 'Opened our first showroom in Sydney to showcase our products.',
                        'image' => '{objectURL: "/img/history/1995-showroom.jpg"}'
                    ]
                ],
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'year' => '2005',
                        'title' => 'Melbourne Expansion',
                        'description' => 'Expanded operations to Melbourne with a new showroom and warehouse.',
                        'image' => '{objectURL: "/img/history/2005-melbourne.jpg"}'
                    ]
                ]
            ],
            'featuredmaterialslider' => [
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'name' => 'Ocean Bound Plastic',
                        'description' => 'Sustainable material made from recycled ocean plastics.',
                        'image' => '{objectURL: "/img/materials/ocean-plastic.jpg"}',
                        'sustainability_score' => '95%'
                    ]
                ],
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'name' => 'FSC Certified Wood',
                        'description' => 'Responsibly sourced wood from certified forests.',
                        'image' => '{objectURL: "/img/materials/fsc-wood.jpg"}',
                        'sustainability_score' => '90%'
                    ]
                ],
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'name' => 'Recycled Steel',
                        'description' => 'High-quality steel with recycled content.',
                        'image' => '{objectURL: "/img/materials/recycled-steel.jpg"}',
                        'sustainability_score' => '85%'
                    ]
                ],
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'name' => 'Eco-Friendly Fabrics',
                        'description' => 'Sustainable fabrics with low environmental impact.',
                        'image' => '{objectURL: "/img/materials/eco-fabrics.jpg"}',
                        'sustainability_score' => '88%'
                    ]
                ]
            ],
            'featureprojectsmasonry' => [
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => 'project',
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'title',
                        'description',
                        'image',
                        'category'
                    ]
                ]
            ],
            'governmentsupplier' => [
                [
                    'property_name' => 'content',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'title' => 'Government Supplier',
                        'description' => 'We are proud to be an approved supplier for government projects across Australia.',
                        'certifications' => 'ISO 9001, ISO 14001, FSC Certified',
                        'button_text' => 'Learn More',
                        'button_url' => '/government-supplier'
                    ]
                ]
            ],
            'blogmain' => [
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => 'post',
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'join' => [
                        [
                            'table' => 'post_content',
                            'on' => 'post.post_id = post_content.post_id',
                            'type' => 'LEFT'
                        ]
                    ],
                    'fields' => [
                        'post_content.name as title',
                        'post_content.excerpt as excerpt',
                        'post.image as image',
                        'post.admin_id as author',
                        'post.created_at as date'
                    ]
                ]
            ],
            'booknow' => [
                [
                    'property_name' => 'content',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'title' => 'Book Your Consultation',
                        'description' => 'Schedule a consultation with our sales team to discuss your project.',
                        'form_title' => 'Booking Form',
                        'button_text' => 'Book Now'
                    ]
                ]
            ],
            'categoriesmasonry' => [
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'name' => 'Workstations',
                        'description' => 'Complete workstation solutions for modern offices.',
                        'image' => '{objectURL: "/img/categories/workstations.jpg"}',
                        'product_count' => '25'
                    ]
                ],
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'name' => 'Desks',
                        'description' => 'Professional desk solutions for various environments.',
                        'image' => '{objectURL: "/img/categories/desks.jpg"}',
                        'product_count' => '18'
                    ]
                ],
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'name' => 'Seating',
                        'description' => 'Comfortable seating for all types of spaces.',
                        'image' => '{objectURL: "/img/categories/seating.jpg"}',
                        'product_count' => '32'
                    ]
                ],
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'name' => 'Storage',
                        'description' => 'Efficient storage solutions for organized spaces.',
                        'image' => '{objectURL: "/img/categories/storage.jpg"}',
                        'product_count' => '15'
                    ]
                ]
            ],
            'contactus' => [
                [
                    'property_name' => 'content',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'title' => 'Contact Us',
                        'description' => 'Get in touch with our team for any inquiries.',
                        'email' => 'sales@krost.com.au',
                        'phone' => '1800 1KROST',
                        'address' => '33 Ricketty Street, Mascot NSW, 2020'
                    ]
                ]
            ]
        ],
    ];

    public function __construct()
    {
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(ComponentRepositoryInterface::class);
    }

    public function seed(): void
    {
        $this->repository->seedData($this->data);
    }
} 