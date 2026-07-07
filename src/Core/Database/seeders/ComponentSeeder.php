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
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => 'Load More',
                'template' => '',
                'active' => '1',
                'model' => 'project'
            ],
            [
                'name' => 'herohome',
                'section_title' => '',
                'section_subtitle' => '',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => 'Load More',
                'template' => '',
                'active' => '1',
                'model' => null
            ],
            [
                'name' => 'featuredprojectslider',
                'section_title' => 'Featured Projects Dynamic',
                'section_subtitle' => 'Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas et.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => 'project'
            ],
            [
                'name' => 'featureproductsmasonry',
                'section_title' => 'Featured Products',
                'section_subtitle' => 'Discover our premium collection of high-quality products.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => 'product'
            ],
            [
                'name' => 'blogslider',
                'section_title' => 'Blogs',
                'section_subtitle' => 'Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas et.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => 'post'
            ],
            [
                'name' => 'designresources',
                'section_title' => 'Design Resources',
                'section_subtitle' => 'Access our comprehensive design tools and resources.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => 'design_resource'
            ],
            [
                'name' => 'footer',
                'section_title' => '',
                'section_subtitle' => '',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => null
            ],
            [
                'name' => 'categoriesslidernav',
                'section_title' => 'Categories',
                'section_subtitle' => 'Browse our product categories.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => 'taxonomy_item'
            ],
            [
                'name' => 'virtualshowrooms',
                'section_title' => 'Virtual Showrooms',
                'section_subtitle' => 'Experience our products in immersive virtual environments.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => null
            ],
            [
                'name' => 'productsustainablity',
                'section_title' => 'Product Sustainability',
                'section_subtitle' => 'Learn about our commitment to sustainable practices.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => null
            ],
            [
                'name' => 'projectdetailmain',
                'section_title' => 'Project Details',
                'section_subtitle' => 'Detailed information about our projects.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => 'project'
            ],
            [
                'name' => 'projectgallery',
                'section_title' => 'Project Gallery',
                'section_subtitle' => 'Visual showcase of our completed projects.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => 'project'
            ],
            [
                'name' => 'requestcatalogue',
                'section_title' => 'Request Catalogue',
                'section_subtitle' => 'Get your copy of our latest product catalogue.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => null
            ],
            [
                'name' => 'salesteam',
                'section_title' => 'Sales Team',
                'section_subtitle' => 'Meet our dedicated sales professionals.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => null
            ],
            [
                'name' => 'salesteammelbourne',
                'section_title' => 'Melbourne Sales Team',
                'section_subtitle' => 'Our Melbourne-based sales representatives.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => null
            ],
            [
                'name' => 'salesteamsydneys',
                'section_title' => 'Sydney Sales Team',
                'section_subtitle' => 'Our Sydney-based sales representatives.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => null
            ],
            [
                'name' => 'showrooms',
                'section_title' => 'Showrooms',
                'section_subtitle' => 'Visit our showrooms to experience our products firsthand.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => null
            ],
            [
                'name' => 'site',
                'section_title' => '',
                'section_subtitle' => '',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => 'site'
            ],
            [
                'name' => 'user',
                'section_title' => '',
                'section_subtitle' => '',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => null
            ],
            [
                'name' => 'ourhistorymasonry',
                'section_title' => 'Our History',
                'section_subtitle' => 'Discover the journey that shaped our company.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => 'user'
            ],
            [
                'name' => 'ourprinciple',
                'section_title' => 'Our Principles',
                'section_subtitle' => 'The values that guide our business practices.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => null
            ],
            [
                'name' => 'ourprincipleicon',
                'section_title' => 'Our Principles with Icons',
                'section_subtitle' => 'Visual representation of our core principles.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => null
            ],
            [
                'name' => 'oursectorscurosel',
                'section_title' => 'Our Sectors',
                'section_subtitle' => 'Explore the diverse sectors we serve.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => null
            ],
            [
                'name' => 'productalsolike',
                'section_title' => 'You May Also Like',
                'section_subtitle' => 'Discover related products that might interest you.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => 'product'
            ],
            [
                'name' => 'productcalltoaction',
                'section_title' => 'Product Call to Action',
                'section_subtitle' => 'Take action on our featured products.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => null
            ],
            [
                'name' => 'productfeature',
                'section_title' => 'Product Features',
                'section_subtitle' => 'Highlighting the key features of our products.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => 'product'
            ],
            [
                'name' => 'productrelatedfamily',
                'section_title' => 'Related Product Family',
                'section_subtitle' => 'Explore products from the same family.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => 'product'
            ],
            [
                'name' => 'productslider',
                'section_title' => 'Product Slider',
                'section_subtitle' => 'Browse our products in an interactive slider.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => 'product'
            ],
            [
                'name' => 'productstorymasonry',
                'section_title' => 'Product Story Masonry',
                'section_subtitle' => 'The stories behind our products.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => 'product'
            ],
            [
                'name' => 'imagemasonrygallery',
                'section_title' => 'Image Masonry Gallery',
                'section_subtitle' => 'A beautiful gallery showcasing our work.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => 'media'
            ],
            [
                'name' => 'instagramslider',
                'section_title' => 'Instagram Slider',
                'section_subtitle' => 'Latest updates from our Instagram feed.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => null
            ],
            [
                'name' => 'manufacturingprocess',
                'section_title' => 'Manufacturing Process',
                'section_subtitle' => 'Discover how our products are made.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => null
            ],
            [
                'name' => 'ourdesignprocess',
                'section_title' => 'Our Design Process',
                'section_subtitle' => 'The creative journey behind our designs.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => null
            ],
            [
                'name' => 'ourhistory',
                'section_title' => 'Our History',
                'section_subtitle' => 'The story of our company\'s evolution.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => null
            ],
            [
                'name' => 'featuredmaterialslider',
                'section_title' => 'Featured Materials',
                'section_subtitle' => 'Premium materials used in our products.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => 'design_resource'
            ],
            [
                'name' => 'featureprojectsmasonry',
                'section_title' => 'Featured Projects Masonry',
                'section_subtitle' => 'Showcase of our featured projects.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => 'project'
            ],
            [
                'name' => 'governmentsupplier',
                'section_title' => 'Government Supplier',
                'section_subtitle' => 'Our services for government projects.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => null
            ],
            [
                'name' => 'blogmain',
                'section_title' => 'Blog Main',
                'section_subtitle' => 'Latest articles and insights from our team.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => 'post'
            ],
            [
                'name' => 'booknow',
                'section_title' => 'Book Now',
                "section_subtitle" => "Schedule your consultation or appointment.",
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => null
            ],
            [
                'name' => 'categoriesmasonry',
                'section_title' => 'Categories Masonry',
                'section_subtitle' => 'Browse our product categories in a masonry layout.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => null
            ],
            [
                'name' => 'contactus',
                'section_title' => 'Contact Us',
                'section_subtitle' => 'Get in touch with our team.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => null
            ],
            [
                'name' => 'header',
                'section_title' => '',
                'section_subtitle' => '',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => 'menu'
            ],
            [
                'name' => 'login',
                'section_title' => 'Sign up',
                'section_subtitle' => 'Create an account to access your account details, track the status of your orders and view saved specification lists.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '/img/login/login-img.png',
                'images' => [],
                'links' => [],
                'buttons' => ['Sign Up'],
                'template' => '',
                'active' => '1',
                'model' => null
            ],
            [
                'name' => 'getintouch',
                'section_title' => 'Get in touch',
                'section_subtitle' => 'Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas et. Pellentesque libero donec sit egestas orci.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '/img/contact/contact-location-one.jpeg',
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => null
            ],
            [
                'name' => 'explorevirtually',
                'section_title' => 'Explore Virtually',
                'section_subtitle' => '',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => null
            ],
            [
                'name' => 'herocatalogue',
                'section_title' => 'Catalogue',
                'section_subtitle' => 'Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '/img/catalogue/hero.png',
                'images' => [],
                'links' => [],
                'buttons' => ['Visit Our Showroom', 'Contact Sales'],
                'template' => '',
                'active' => '1',
                'model' => null
            ],
            [
                'name' => 'herosolution',
                'section_title' => 'CatSolutions, Segments, Spaces',
                'section_subtitle' => 'Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '/img/catalogue/hero.png',
                'images' => [],
                'links' => [],
                'buttons' => ['Visit Our Showroom', 'Contact Sales'],
                'template' => '',
                'active' => '1',
                'model' => null
            ],
            [
                'name' => 'projectdetails',
                'section_title' => 'Project Details',
                'section_subtitle' => '',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => 'project'
            ],
            [
                'name' => 'videogallerywhoweare',
                'section_title' => 'Video Gallery - Who We Are',
                'section_subtitle' => '',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => null
            ],
            [
                'name' => 'videogallerymanufacturingprocess',
                'section_title' => 'Video Gallery - Manufacturing Process',
                'section_subtitle' => '',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => null
            ],
            [
                'name' => 'designprocess',
                'section_title' => 'Design Process',
                'section_subtitle' => '',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => null
            ],
            [
                'name' => 'needhelp',
                'section_title' => 'Need Help',
                'section_subtitle' => '',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => null
            ],
            [
                'name' => 'latestnews',
                'section_title' => 'Latest News',
                'section_subtitle' => '',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => null
            ],
            [
                'name' => 'productlist',
                'section_title' => 'Product List',
                'section_subtitle' => '',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => 'product'
            ],
            [
                'name' => 'products',
                'section_title' => 'Products',
                'section_subtitle' => '',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => null
            ],
            [
                'name' => 'virtualpinboard',
                'section_title' => 'Virtual Pinboard',
                'section_subtitle' => '',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => 'product'
            ],
            [
                'name' => 'accountactivequotes',
                'section_title' => 'Account Active Quotes',
                'section_subtitle' => '',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => 'quote'
            ],
            [
                'name' => 'accountactivequotepayment',
                'section_title' => 'Account Active Quote Payment',
                'section_subtitle' => '',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => 'quote'
            ],
            [
                'name' => 'accountcreaterequest',
                'section_title' => 'Account Create Request',
                'section_subtitle' => '',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => null
            ],
            [
                'name' => 'accountpinboard',
                'section_title' => 'Account Pinboard',
                'section_subtitle' => '',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => 'pinboard'
            ],
            [
                'name' => 'accountnavigation',
                'section_title' => 'Account Navigation',
                'section_subtitle' => '',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => 'menu'
            ],
            [
                'name' => 'accountrecentorders',
                'section_title' => 'Account Recent Orders',
                'section_subtitle' => '',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => 'order'
            ],
            [
                'name' => 'whatishappening',
                'section_title' => "WHAT'S HAPPENING?",
                'section_subtitle' => '',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => '/img/blog-page/whats-happening-left.png',
                'images' => [],
                'links' => [],
                'buttons' => ['Read More'],
                'template' => '',
                'active' => '1',
                'model' => null
            ],
            [
                'name' => 'customizablesolution',
                'section_title' => 'Customizable Solution',
                'section_subtitle' => '',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => null
            ],
            [
                'name' => 'designresourcedocuments',
                'section_title' => 'Design Resource Documents',
                'section_subtitle' => '',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => 'design_resource'
            ],
            [
                'name' => 'designresourceimages',
                'section_title' => 'Design Resource Images',
                'section_subtitle' => '',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => 'design_resource'
            ],
            [
                'name' => 'designresourcetextiles',
                'section_title' => 'Design Resource Textiles',
                'section_subtitle' => '',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => 'design_resource'
            ],
            [
                'name' => 'designresourcefinishes',
                'section_title' => 'Design Resource Finishes',
                'section_subtitle' => '',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => 'design_resource'
            ],
            [
                'name' => 'designresourcemodels',
                'section_title' => 'Design Resource Models',
                'section_subtitle' => '',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => 'design_resource'
            ],
            [
                'name' => 'dashboarddeliveryinstall',
                'section_title' => 'Dashboard Delivery Install',
                'section_subtitle' => '',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => null
            ],
            [
                'name' => 'projectdetailunderhero',
                'section_title' => 'Project Detail Under Hero',
                'section_subtitle' => '',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => 'project'
            ],
            [
                'name' => 'projectdetailpenetrating',
                'section_title' => 'Project Detail Penetrating',
                'section_subtitle' => '',
                'section_link' => '',
                'title' => '',
                'subtitle' => '',
                'description' => '',
                'image' => [],
                'images' => [],
                'links' => [],
                'buttons' => [],
                'template' => '',
                'active' => '1',
                'model' => null
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
                        '`project`.`project_id`',
                        '`project`.`description`',
                        '`project`.`title`',
                        '`project`.`label`',
                        '`project`.`image`'
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
                        '`project`.`description`',
                        '`project`.`image`',
                        '`project`.`title`',
                        '`project`.`link_text`',
                        '`project`.`label`'
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
                        '`post`.`image`',
                        '`post`.`description`',
                        '`post_content`.`label`',
                        '`post_content`.`name`',
                        '`post_content`.`link_text`'
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
                        '`product`.`description`',
                        '`product`.`image`',
                        '`product_content`.`name`'
                    ],
                    'related_models' => [
                        [
                            'name' => 'ProductContent',
                            'type' => 'product_content',
                            'class' => 'App\\Core\\Models\\Product\\ProductContent',
                            'joinType' => 'LEFT',
                            'model_id' => 1,
                            'joinFields' => [
                                '`product_content`.`product_id`',
                                '`product_content`.`language_id`',
                                '`product_content`.`name`',
                                '`product_content`.`slug`',
                                '`product_content`.`content`',
                                '`product_content`.`tag`',
                                '`product_content`.`meta_title`',
                                '`product_content`.`meta_description`',
                                '`product_content`.`meta_keywords`',
                                '`product_content`.`icon`'
                            ],
                            'fieldsExist' => true
                        ]
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
                        '`design_resource`.`img`',
                        '`design_resource`.`title`',
                        '`design_resource`.`description`'
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
                        '`taxonomy_item`.`image`',
                        '`taxonomy_item_content`.`name`',
                        '`taxonomy_item_content`.`meta_description`',
                        '`taxonomy_item_content`.`link`'
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
                        [
                            'name' => 'showroom_image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => "{objectURL: '/img/contact-us/explore-1.png'}",
                            'imagesData' => []
                        ],
                        [
                            'name' => 'showroom_title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Sydney Showroom',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'showroom_book_btn',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Book a Virtual Tour',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'showroom_view_btn',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'View Tour',
                            'imagesData' => []
                        ]
                    ]
                ],
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'fields' => [
                        [
                            'name' => 'showroom_image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => "{objectURL: '/img/contact-us/explore-2.png'}",
                            'imagesData' => []
                        ],
                        [
                            'name' => 'showroom_title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Melbourne Showroom',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'showroom_book_btn',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Book a Virtual Tour',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'showroom_view_btn',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'View Tour',
                            'imagesData' => []
                        ]
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
                        [
                            'name' => 'image1',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => "{objectURL: '/img/product-detail/ocean image.png'}",
                            'imagesData' => []
                        ],
                        [
                            'name' => 'section_title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Made With Ocean Bound Plastic',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'section_subtitle',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque. Pellentesque libero donec sit egestas orci consequat est mauris duis.',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'section_link_text',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'View Catalogue',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'image2',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => "{objectURL: '/img/product-detail/ocean bound plastic chair.png'}",
                            'imagesData' => []
                        ]
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
                        '`project`.`image`',
                        '`project`.`name`',
                        '`project`.`description`'
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
                        '`project_image`.`image`'
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
                        [
                            'name' => 'title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Request Catalogue',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Get your copy of our latest product catalogue.',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'form_title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Request Your Catalogue',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'form_description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Fill out the form below to receive our latest catalogue.',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'button_text',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Request Catalogue',
                            'imagesData' => []
                        ]
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
                        [
                            'name' => 'name',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'John Smith',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'position',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Sales Manager',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'email',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'john.smith@krost.com.au',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'phone',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '+61 2 9557 3055',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '{objectURL: "/img/team/sales-team-1.jpg"}',
                            'imagesData' => []
                        ]
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
                        [
                            'name' => 'name',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Sarah Johnson',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'position',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Senior Sales Representative',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'email',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'sarah.johnson@krost.com.au',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'phone',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '+61 2 9557 3056',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '{objectURL: "/img/team/sales-team-2.jpg"}',
                            'imagesData' => []
                        ]
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
                        'name' => [
                            'name' => 'name',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Michael Brown',
                            'imagesData' => []
                        ],
                        'position' => [
                            'name' => 'position',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Sales Representative',
                            'imagesData' => []
                        ],
                        'email' => [
                            'name' => 'email',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'michael.brown@krost.com.au',
                            'imagesData' => []
                        ],
                        'phone' => [
                            'name' => 'phone',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '+61 2 9557 3057',
                            'imagesData' => []
                        ],
                        'image' => [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '{objectURL: "/img/team/sales-team-3.jpg"}',
                            'imagesData' => []
                        ]
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
                        [
                            'name' => 'name',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'David Wilson',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'position',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Melbourne Sales Manager',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'email',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'david.wilson@krost.com.au',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'phone',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '+61 3 9682 8280',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '{objectURL: "/img/team/melbourne-sales-1.jpg"}',
                            'imagesData' => []
                        ]
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
                        [
                            'name' => 'name',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Emma Davis',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'position',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Melbourne Sales Representative',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'email',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'emma.davis@krost.com.au',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'phone',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '+61 3 9682 8281',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '{objectURL: "/img/team/melbourne-sales-2.jpg"}',
                            'imagesData' => []
                        ]
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
                        [
                            'name' => 'name',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'James Taylor',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'position',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Sydney Sales Manager',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'email',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'james.taylor@krost.com.au',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'phone',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '+61 2 9557 3055',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '{objectURL: "/img/team/sydney-sales-1.jpg"}',
                            'imagesData' => []
                        ]
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
                        [
                            'name' => 'name',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Lisa Anderson',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'position',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Sydney Sales Representative',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'email',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'lisa.anderson@krost.com.au',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'phone',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '+61 2 9557 3056',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '{objectURL: "/img/team/sydney-sales-2.jpg"}',
                            'imagesData' => []
                        ]
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
                        [
                            'name' => 'showroom_image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '/img/contact/contact-location-one.jpeg',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'showroom_title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Sydney Showroom',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'showroom_opening_time',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Open Weekdays, 8am to 5pm',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'showroom_address',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '33 Ricketty Street Mascot NSW, 2020',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'showroom_book_btn',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Book a Tour',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'showroom_view_btn',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'View On Map',
                            'imagesData' => []
                        ]
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
                        'showroom_image' => [
                            'name' => 'showroom_image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '/img/contact/contact-location-two.png',
                            'imagesData' => []
                        ],
                        'showroom_title' => [
                            'name' => 'showroom_title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Melbourne Showroom',
                            'imagesData' => []
                        ],
                        'showroom_opening_time' => [
                            'name' => 'showroom_opening_time',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Open Weekdays, 8am to 5pm',
                            'imagesData' => []
                        ],
                        'showroom_address' => [
                            'name' => 'showroom_address',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '33 Ricketty Street Mascot NSW, 2020',
                            'imagesData' => []
                        ],
                        'showroom_book_btn' => [
                            'name' => 'showroom_book_btn',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Book a Tour',
                            'imagesData' => []
                        ],
                        'showroom_view_btn' => [
                            'name' => 'showroom_view_btn',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'View On Map',
                            'imagesData' => []
                        ]
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
                        '`site`.`name`',
                        '`site`.`description`',
                        '`site`.`site_settings`'
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
                        '`user`.`name`',
                        '`user`.`email`',
                        '`user`.`role`',
                        '`user`.`avatar`'
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
                        [
                            'name' => 'year',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '1985',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Company Founded',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Krost was established with a vision to create innovative furniture solutions.',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '{objectURL: "/img/history/1985-founded.jpg"}',
                            'imagesData' => []
                        ]
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
                        'year' => [
                            'name' => 'year',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '1995',
                            'imagesData' => []
                        ],
                        'title' => [
                            'name' => 'title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'First Showroom',
                            'imagesData' => []
                        ],
                        'description' => [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Opened our first showroom in Sydney to showcase our products.',
                            'imagesData' => []
                        ],
                        'image' => [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '{objectURL: "/img/history/1995-showroom.jpg"}',
                            'imagesData' => []
                        ]
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
                        'year' => [
                            'name' => 'year',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '2005',
                            'imagesData' => []
                        ],
                        'title' => [
                            'name' => 'title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Melbourne Expansion',
                            'imagesData' => []
                        ],
                        'description' => [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Expanded operations to Melbourne with a new showroom and warehouse.',
                            'imagesData' => []
                        ],
                        'image' => [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '{objectURL: "/img/history/2005-melbourne.jpg"}',
                            'imagesData' => []
                        ]
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
                        'year' => [
                            'name' => 'year',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '2015',
                            'imagesData' => []
                        ],
                        'title' => [
                            'name' => 'title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Sustainability Focus',
                            'imagesData' => []
                        ],
                        'description' => [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Launched our sustainability initiative with ocean-bound plastic products.',
                            'imagesData' => []
                        ],
                        'image' => [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '{objectURL: "/img/history/2015-sustainability.jpg"}',
                            'imagesData' => []
                        ]
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
                        [
                            'name' => 'title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Quality',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'We maintain the highest standards of quality in all our products.',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'icon',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'fa-star',
                            'imagesData' => []
                        ]
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
                        [
                            'name' => 'title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Innovation',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'We continuously innovate to meet evolving customer needs.',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'icon',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'fa-lightbulb',
                            'imagesData' => []
                        ]
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
                        [
                            'name' => 'title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Sustainability',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'We are committed to environmental responsibility in our operations.',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'icon',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'fa-leaf',
                            'imagesData' => []
                        ]
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
                        [
                            'name' => 'title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Quality',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'We maintain the highest standards of quality in all our products.',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'icon',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'fa-star',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'icon_color',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '#FFD700',
                            'imagesData' => []
                        ]
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
                        [
                            'name' => 'title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Innovation',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'We continuously innovate to meet evolving customer needs.',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'icon',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'fa-lightbulb',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'icon_color',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '#FF6B35',
                            'imagesData' => []
                        ]
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
                        [
                            'name' => 'title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Sustainability',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'We are committed to environmental responsibility in our operations.',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'icon',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'fa-leaf',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'icon_color',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '#4CAF50',
                            'imagesData' => []
                        ]
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
                        [
                            'name' => 'title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Healthcare',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Specialized furniture solutions for healthcare environments.',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '{objectURL: "/img/sectors/healthcare.jpg"}',
                            'imagesData' => []
                        ]
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
                        [
                            'name' => 'title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Education',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Innovative furniture for educational institutions.',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '{objectURL: "/img/sectors/education.jpg"}',
                            'imagesData' => []
                        ]
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
                        [
                            'name' => 'title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Corporate',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Professional furniture solutions for corporate offices.',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '{objectURL: "/img/sectors/corporate.jpg"}',
                            'imagesData' => []
                        ]
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
                        [
                            'name' => 'title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Government',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Reliable furniture for government facilities.',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '{objectURL: "/img/sectors/government.jpg"}',
                            'imagesData' => []
                        ]
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
                        '`product_content`.`name`',
                        '`product`.`image`',
                        '`product`.`price`',
                        '`product`.`category`'
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
                        'title' => [
                            'name' => 'title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Ready to Transform Your Space?',
                            'imagesData' => []
                        ],
                        'description' => [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Contact our sales team to discuss your project requirements.',
                            'imagesData' => []
                        ],
                        'button_text' => [
                            'name' => 'button_text',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Contact Sales',
                            'imagesData' => []
                        ],
                        'button_url' => [
                            'name' => 'button_url',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '/contact',
                            'imagesData' => []
                        ]
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
                        '`product_content`.`name`',
                        '`product`.`description`',
                        '`product_content`.`icon`'
                    ]
                ]
            ],

            // Have to analysis this component
            'productrelatedfamily' => [
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => 'product',
                    'model_id' => '1',
                    'join' => [
                        [
                            'table' => 'product_related',
                            'on' => 'product.product_id = product_related.product_id',
                            'type' => 'LEFT'
                        ],
                        [
                            'table' => 'product as product_family',
                            'on' => 'product_family.product_id = product_related.product_related_id',
                            'type' => 'LEFT'
                        ],
                        [
                            'table' => 'product_content',
                            'on' => 'product.product_id = product_content.product_id',
                            'type' => 'LEFT'
                        ],
                        [
                            'table' => 'product_content as product_content_family',
                            'on' => 'product_family.product_id = product_content_family.product_id',
                            'type' => 'LEFT'
                        ]
                    ],
                    'fields' => [
                        '`product_content_family`.`name` as family_name',
                        '`product_content_family`.`description` as description',
                        '`product_content_family`.`image` as image',
                        '`product_content_family`.`product_count` as product_count'
                    ]
                ]
            ],
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
                        '`product_content`.`name`',
                        '`product`.`image`',
                        '`product`.`price`',
                        '`product`.`category`'
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
                        '`product_content`.`name`',
                        '`product`.`description`',
                        '`product`.`image`'
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
                        [
                            'name' => 'name',
                            'type' => [
                                'type' => [
                                    'max' => 255,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Image Name',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'file',
                            'type' => [
                                'type' => [
                                    'max' => 255,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Image File',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 1000,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'TextArea',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Image Description',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '',
                            'imagesData' => []
                        ]
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
                        [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '{objectURL: "/img/instagram/instagram-1.jpg"}',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'caption',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Beautiful office space featuring our Chwyla chairs',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'likes',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '245',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'comments',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '12',
                            'imagesData' => []
                        ]
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
                        'image' => [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '{objectURL: "/img/instagram/instagram-2.jpg"}',
                            'imagesData' => []
                        ],
                        'caption' => [
                            'name' => 'caption',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Sustainability in action with our ocean-bound plastic products',
                            'imagesData' => []
                        ],
                        'likes' => [
                            'name' => 'likes',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '189',
                            'imagesData' => []
                        ],
                        'comments' => [
                            'name' => 'comments',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '8',
                            'imagesData' => []
                        ]
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
                        'image' => [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '{objectURL: "/img/instagram/instagram-3.jpg"}',
                            'imagesData' => []
                        ],
                        'caption' => [
                            'name' => 'caption',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Behind the scenes of our manufacturing process',
                            'imagesData' => []
                        ],
                        'likes' => [
                            'name' => 'likes',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '312',
                            'imagesData' => []
                        ],
                        'comments' => [
                            'name' => 'comments',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '15',
                            'imagesData' => []
                        ]
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
                        'image' => [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '{objectURL: "/img/instagram/instagram-4.jpg"}',
                            'imagesData' => []
                        ],
                        'caption' => [
                            'name' => 'caption',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Customer showcase: Healthcare facility transformation',
                            'imagesData' => []
                        ],
                        'likes' => [
                            'name' => 'likes',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '156',
                            'imagesData' => []
                        ],
                        'comments' => [
                            'name' => 'comments',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '6',
                            'imagesData' => []
                        ]
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
                        [
                            'name' => 'step_number',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '1',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Design & Planning',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Our design team creates detailed plans for each product.',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '{objectURL: "/img/manufacturing/step-1-design.jpg"}',
                            'imagesData' => []
                        ]
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
                        'step_number' => [
                            'name' => 'step_number',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '2',
                            'imagesData' => []
                        ],
                        'title' => [
                            'name' => 'title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Material Selection',
                            'imagesData' => []
                        ],
                        'description' => [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Carefully selecting sustainable and durable materials.',
                            'imagesData' => []
                        ],
                        'image' => [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '{objectURL: "/img/manufacturing/step-2-materials.jpg"}',
                            'imagesData' => []
                        ]
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
                        'step_number' => [
                            'name' => 'step_number',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '3',
                            'imagesData' => []
                        ],
                        'title' => [
                            'name' => 'title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Production',
                            'imagesData' => []
                        ],
                        'description' => [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Expert craftsmanship using advanced manufacturing techniques.',
                            'imagesData' => []
                        ],
                        'image' => [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '{objectURL: "/img/manufacturing/step-3-production.jpg"}',
                            'imagesData' => []
                        ]
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
                        'step_number' => [
                            'name' => 'step_number',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '4',
                            'imagesData' => []
                        ],
                        'title' => [
                            'name' => 'title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Quality Control',
                            'imagesData' => []
                        ],
                        'description' => [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Rigorous testing to ensure the highest standards.',
                            'imagesData' => []
                        ],
                        'image' => [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '{objectURL: "/img/manufacturing/step-4-quality.jpg"}',
                            'imagesData' => []
                        ]
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
                        [
                            'name' => 'step_number',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '1',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Research & Inspiration',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Understanding market needs and gathering inspiration.',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'icon',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'fa-search',
                            'imagesData' => []
                        ]
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
                        [
                            'name' => 'step_number',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '2',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Concept Development',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Creating initial concepts and prototypes.',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'icon',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'fa-lightbulb',
                            'imagesData' => []
                        ]
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
                        'step_number' => [
                            'name' => 'step_number',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '3',
                            'imagesData' => []
                        ],
                        'title' => [
                            'name' => 'title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Refinement',
                            'imagesData' => []
                        ],
                        'description' => [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Iterating and improving based on feedback.',
                            'imagesData' => []
                        ],
                        'icon' => [
                            'name' => 'icon',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'fa-cogs',
                            'imagesData' => []
                        ]
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
                        'step_number' => [
                            'name' => 'step_number',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '4',
                            'imagesData' => []
                        ],
                        'title' => [
                            'name' => 'title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Final Design',
                            'imagesData' => []
                        ],
                        'description' => [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Perfecting the design for production.',
                            'imagesData' => []
                        ],
                        'icon' => [
                            'name' => 'icon',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'fa-check-circle',
                            'imagesData' => []
                        ]
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
                        [
                            'name' => 'year',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '1985',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Company Founded',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Krost was established with a vision to create innovative furniture solutions.',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '{objectURL: "/img/history/1985-founded.jpg"}',
                            'imagesData' => []
                        ]
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
                        'year' => [
                            'name' => 'year',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '1995',
                            'imagesData' => []
                        ],
                        'title' => [
                            'name' => 'title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'First Showroom',
                            'imagesData' => []
                        ],
                        'description' => [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Opened our first showroom in Sydney to showcase our products.',
                            'imagesData' => []
                        ],
                        'image' => [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '{objectURL: "/img/history/1995-showroom.jpg"}',
                            'imagesData' => []
                        ]
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
                        'year' => [
                            'name' => 'year',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '2005',
                            'imagesData' => []
                        ],
                        'title' => [
                            'name' => 'title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Melbourne Expansion',
                            'imagesData' => []
                        ],
                        'description' => [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Expanded operations to Melbourne with a new showroom and warehouse.',
                            'imagesData' => []
                        ],
                        'image' => [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '{objectURL: "/img/history/2005-melbourne.jpg"}',
                            'imagesData' => []
                        ]
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
                        'year' => [
                            'name' => 'year',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '2015',
                            'imagesData' => []
                        ],
                        'title' => [
                            'name' => 'title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Sustainability Focus',
                            'imagesData' => []
                        ],
                        'description' => [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Launched our sustainability initiative with ocean-bound plastic products.',
                            'imagesData' => []
                        ],
                        'image' => [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '{objectURL: "/img/history/2015-sustainability.jpg"}',
                            'imagesData' => []
                        ]
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
                        'name' => [
                            'name' => 'name',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Ocean Bound Plastic',
                            'imagesData' => []
                        ],
                        'description' => [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Sustainable material made from recycled ocean plastics.',
                            'imagesData' => []
                        ],
                        'image' => [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '{objectURL: "/img/materials/ocean-plastic.jpg"}',
                            'imagesData' => []
                        ],
                        'sustainability_score' => [
                            'name' => 'sustainability_score',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '95%',
                            'imagesData' => []
                        ]
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
                        'name' => [
                            'name' => 'name',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'FSC Certified Wood',
                            'imagesData' => []
                        ],
                        'description' => [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Responsibly sourced wood from certified forests.',
                            'imagesData' => []
                        ],
                        'image' => [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '{objectURL: "/img/materials/fsc-wood.jpg"}',
                            'imagesData' => []
                        ],
                        'sustainability_score' => [
                            'name' => 'sustainability_score',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '90%',
                            'imagesData' => []
                        ]
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
                        'name' => [
                            'name' => 'name',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Recycled Steel',
                            'imagesData' => []
                        ],
                        'description' => [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'High-quality steel with recycled content.',
                            'imagesData' => []
                        ],
                        'image' => [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '{objectURL: "/img/materials/recycled-steel.jpg"}',
                            'imagesData' => []
                        ],
                        'sustainability_score' => [
                            'name' => 'sustainability_score',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '85%',
                            'imagesData' => []
                        ]
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
                        'name' => [
                            'name' => 'name',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Eco-Friendly Fabrics',
                            'imagesData' => []
                        ],
                        'description' => [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Sustainable fabrics with low environmental impact.',
                            'imagesData' => []
                        ],
                        'image' => [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '{objectURL: "/img/materials/eco-fabrics.jpg"}',
                            'imagesData' => []
                        ],
                        'sustainability_score' => [
                            'name' => 'sustainability_score',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '88%',
                            'imagesData' => []
                        ]
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
                        [
                            'name' => 'title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Government Supplier',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'We are proud to be an approved supplier for government projects across Australia.',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'certifications',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'ISO 9001, ISO 14001, FSC Certified',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'button_text',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Learn More',
                            'imagesData' => []
                        ],
                        'button_url' => [
                            'name' => 'button_url',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '/government-supplier',
                            'imagesData' => []
                        ]
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
                        '`post_content`.`name`',
                        '`post_content`.`excerpt`',
                        '`post`.`image` as image',
                        '`post`.`admin_id` as author',
                        '`post`.`created_at` as date'
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
                        [
                            'name' => 'title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Book Your Consultation',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Schedule a consultation with our sales team to discuss your project.',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'form_title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Booking Form',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'button_text',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Book Now',
                            'imagesData' => []
                        ]
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
                        'name' => [
                            'name' => 'name',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Workstations',
                            'imagesData' => []
                        ],
                        'description' => [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Complete workstation solutions for modern offices.',
                            'imagesData' => []
                        ],
                        'image' => [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '{objectURL: "/img/categories/workstations.jpg"}',
                            'imagesData' => []
                        ],
                        'product_count' => [
                            'name' => 'product_count',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '25',
                            'imagesData' => []
                        ]
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
                        'name' => [
                            'name' => 'name',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Desks',
                            'imagesData' => []
                        ],
                        'description' => [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Professional desk solutions for various environments.',
                            'imagesData' => []
                        ],
                        'image' => [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '{objectURL: "/img/categories/desks.jpg"}',
                            'imagesData' => []
                        ],
                        'product_count' => [
                            'name' => 'product_count',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '18',
                            'imagesData' => []
                        ]
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
                        'name' => [
                            'name' => 'name',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Seating',
                            'imagesData' => []
                        ],
                        'description' => [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Comfortable seating for all types of spaces.',
                            'imagesData' => []
                        ],
                        'image' => [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '{objectURL: "/img/categories/seating.jpg"}',
                            'imagesData' => []
                        ],
                        'product_count' => [
                            'name' => 'product_count',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '32',
                            'imagesData' => []
                        ]
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
                        'name' => [
                            'name' => 'name',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Storage',
                            'imagesData' => []
                        ],
                        'description' => [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Efficient storage solutions for organized spaces.',
                            'imagesData' => []
                        ],
                        'image' => [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '{objectURL: "/img/categories/storage.jpg"}',
                            'imagesData' => []
                        ],
                        'product_count' => [
                            'name' => 'product_count',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '15',
                            'imagesData' => []
                        ]
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
                        [
                            'name' => 'title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Contact Us',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Get in touch with our team for any inquiries.',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'email',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'sales@krost.com.au',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'phone',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '1800 1KROST',
                            'imagesData' => []
                        ],
                        'address' => [
                            'name' => 'address',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '33 Ricketty Street, Mascot NSW, 2020',
                            'imagesData' => []
                        ]
                    ]
                ]
            ],
            
            'herohome' => [
                [
                    'property_name' => 'content',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        [
                            'name' => 'load_btn',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Load More',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'hero_image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => [
                                [
                                    'id' => null,
                                    'file' => [
                                        'name' => 'hero_home.jpg',
                                        'size' => 237984,
                                        'type' => 'image/jpeg',
                                        'error' => 0,
                                        'tmp_name' => '/tmp/phpJ8BFqh',
                                        'full_path' => 'hero_home.jpg'
                                    ],
                                    'name' => 'hero_home.jpg',
                                    'size' => 237984,
                                    'type' => 'image/jpeg',
                                    'image' => '/media/uploads2025/08/hero_home.jpg',
                                    'status' => [
                                        'name' => 'Uploaded',
                                        'severity' => 'success'
                                    ],
                                    'media_id' => null,
                                    'objectURL' => '/media/uploads2025/08/hero_home.jpg',
                                    'created_at' => '',
                                    'description' => ''
                                ]
                            ],
                            'imagesData' => [
                                [
                                    'id' => null,
                                    'file' => [
                                        'name' => 'hero_home.jpg',
                                        'size' => 237984,
                                        'type' => 'image/jpeg',
                                        'error' => 0,
                                        'tmp_name' => '/tmp/phpJ8BFqh',
                                        'full_path' => 'hero_home.jpg'
                                    ],
                                    'name' => 'hero_home.jpg',
                                    'size' => 237984,
                                    'type' => 'image/jpeg',
                                    'image' => '/media/uploads2025/08/hero_home.jpg',
                                    'status' => [
                                        'name' => 'Uploaded',
                                        'severity' => 'success'
                                    ],
                                    'media_id' => null,
                                    'objectURL' => '/media/uploads2025/08/hero_home.jpg',
                                    'created_at' => '',
                                    'description' => ''
                                ]
                            ]
                        ],
                        [
                            'name' => 'hero_title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => "2026 <span class=''>Catalogue</span> <span class='line-break'>Sent Out</span>",
                            'imagesData' => []
                        ],
                        [
                            'name' => 'hero_description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Krost is a leading manufacturer of high-quality kitchen and bathroom products. Our products are designed to meet the needs of modern living, with a focus on style, durability, and functionality.',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'hero_button_label_white',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Visit Our Showroom',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'hero_button_label_outline',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Contact Sales',
                            'imagesData' => []
                        ]
                    ]
                ]
            ],
            'footer' => [
                [
                    'property_name' => 'content',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        [
                            'name' => 'contact_email',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'sales@krost.com.au',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'contact_phone',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '1800 1KROST',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'sydney_office_name',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Sydney Office',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'sydney_office_address',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '33 Ricketty Street, Mascot NSW, 2020',
                            'imagesData' => []
                        ],
                        'sydney_office_phone' => [
                            'name' => 'sydney_office_phone',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '02 9557 3055',
                            'imagesData' => []
                        ],
                        'sydney_office_hours' => [
                            'name' => 'sydney_office_hours',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Open weekdays, 8am to 5pm',
                            'imagesData' => []
                        ],
                        'melbourne_office_name' => [
                            'name' => 'melbourne_office_name',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Melbourne Office',
                            'imagesData' => []
                        ],
                        'melbourne_office_address' => [
                            'name' => 'melbourne_office_address',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '17-643 spencer st, West Melbourne VIC, 3003',
                            'imagesData' => []
                        ],
                        'melbourne_office_phone' => [
                            'name' => 'melbourne_office_phone',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '03 9682 8280',
                            'imagesData' => []
                        ],
                        'melbourne_office_hours' => [
                            'name' => 'melbourne_office_hours',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'open weekdays, 9am to 5pm',
                            'imagesData' => []
                        ],
                        'subscription_title' => [
                            'name' => 'subscription_title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Get krost product updates',
                            'imagesData' => []
                        ],
                        'subscription_description' => [
                            'name' => 'subscription_description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Receive the latest news and updates from Krost',
                            'imagesData' => []
                        ],
                        'subscription_placeholder' => [
                            'name' => 'subscription_placeholder',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Your Email Address Please',
                            'imagesData' => []
                        ],
                        'subscription_button_text' => [
                            'name' => 'subscription_button_text',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Subscribe Now',
                            'imagesData' => []
                        ],
                        'footer_navigation_our_store' => [
                            'name' => 'footer_navigation_our_store',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Our Store',
                            'imagesData' => []
                        ],
                        'footer_navigation_visit_us' => [
                            'name' => 'footer_navigation_visit_us',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Visit Us',
                            'imagesData' => []
                        ],
                        'footer_navigation_contact_us' => [
                            'name' => 'footer_navigation_contact_us',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Contact Us',
                            'imagesData' => []
                        ],
                        'footer_navigation_contact_us_url' => [
                            'name' => 'footer_navigation_contact_us_url',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '#',
                            'imagesData' => []
                        ],
                        'social_media' => [
                            'name' => 'social_media',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => [
                                [
                                    'platform' => 'LinkedIn',
                                    'url' => 'https://www.linkedin.com/company/krost-furniture/',
                                    'icon' => 'fa-brands fa-linkedin-in'
                                ],
                                [
                                    'platform' => 'Facebook',
                                    'url' => 'https://www.facebook.com/krostfurniture/',
                                    'icon' => 'fa-brands fa-facebook-f'
                                ],
                                [
                                    'platform' => 'Instagram',
                                    'url' => 'https://www.instagram.com/krostfurniture/',
                                    'icon' => 'fa-brands fa-instagram'
                                ],
                                [
                                    'platform' => 'Pinterest',
                                    'url' => 'https://www.pinterest.com/krostfurniture/',
                                    'icon' => 'fa-brands fa-pinterest-p'
                                ]
                            ],
                            'imagesData' => []
                        ],
                        'copyright_year' => [
                            'name' => 'copyright_year',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '2025',
                            'imagesData' => []
                        ],
                        'copyright_company_name' => [
                            'name' => 'copyright_company_name',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'KROST',
                            'imagesData' => []
                        ],
                        'copyright_terms_url' => [
                            'name' => 'copyright_terms_url',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'content/page.html',
                            'imagesData' => []
                        ],
                        'copyright_privacy_url' => [
                            'name' => 'copyright_privacy_url',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'content/page.html',
                            'imagesData' => []
                        ],
                        'copyright_powered_by_text' => [
                            'name' => 'copyright_powered_by_text',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Powered by Krost',
                            'imagesData' => []
                        ],
                        'copyright_powered_by_url' => [
                            'name' => 'copyright_powered_by_url',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'https://krost.com.au',
                            'imagesData' => []
                        ]
                    ]
                ]
            ],
            'login' => [
                [
                    'property_name' => 'content',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '/img/login/login-img.png',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'section_title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Sign up',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'section_description',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Create an account to access your account details, track the status of your orders and view saved specification lists.',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'name_input_label',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Full Name',
                            'imagesData' => []
                        ],
                        'name_input_placeholder' => [
                            'name' => 'name_input_placeholder',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Enter your name',
                            'imagesData' => []
                        ],
                        'email_input_label' => [
                            'name' => 'email_input_label',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Enter Your Email',
                            'imagesData' => []
                        ],
                        'email_input_placeholder' => [
                            'name' => 'email_input_placeholder',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Enter your email',
                            'imagesData' => []
                        ],
                        'password_input_label' => [
                            'name' => 'password_input_label',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Create A Password',
                            'imagesData' => []
                        ],
                        'password_input_placeholder' => [
                            'name' => 'password_input_placeholder',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Enter your password',
                            'imagesData' => []
                        ],
                        'submit_button_label' => [
                            'name' => 'submit_button_label',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Sign Up',
                            'imagesData' => []
                        ]
                    ]
                ]
            ],
            'getintouch' => [
                [
                    'property_name' => 'content',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        [
                            'name' => 'section_title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Get in touch',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'section_subtitle',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Lorem ipsum dolor sit amet consectetur. Egestas orci gravida amet egestas et. Pellentesque libero donec sit egestas orci.',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '/img/contact/contact-location-one.jpeg',
                            'imagesData' => []
                        ]
                    ]
                ]
            ],
            'explorevirtually' => [
                [
                    'property_name' => 'content',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        [
                            'name' => 'section_title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Explore Virtually',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'section_subtitle',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Experience our showrooms from anywhere in the world with our virtual tours.',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'virtual_tour_url',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '/virtual-tour',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'button_text',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Start Virtual Tour',
                            'imagesData' => []
                        ]
                    ]
                ]
            ],
            'herocatalogue' => [
                [
                    'property_name' => 'content',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        [
                            'name' => 'section_title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Catalogue',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'section_subtitle',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '/img/catalogue/hero.png',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'button_text_1',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Visit Our Showroom',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'button_text_2',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Contact Sales',
                            'imagesData' => []
                        ]
                    ]
                ]
            ],
            'herosolution' => [
                [
                    'property_name' => 'content',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        [
                            'name' => 'section_title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'CatSolutions, Segments, Spaces',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'section_subtitle',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '/img/catalogue/hero.png',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'button_text_1',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Visit Our Showroom',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'button_text_2',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Contact Sales',
                            'imagesData' => []
                        ]
                    ]
                ]
            ],
            'projectdetails' => [
                [
                    'property_name' => 'content',
                    'component_id' => '',
                    'model' => 'project',
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'join' => [
                        [
                            'table' => 'project_content',
                            'on' => 'project.project_id = project_content.project_id',
                            'type' => 'LEFT'
                        ]
                    ],
                    'fields' => [
                        '`project`.`name`',
                        '`project`.`description`',
                        '`project`.`image`',
                        '`project_content`.`meta_description`'
                    ]
                ]
            ],
            'videogallerywhoweare' => [
                [
                    'property_name' => 'content',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        [
                            'name' => 'section_title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Video Gallery - Who We Are',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'section_subtitle',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Discover our story and values through our video collection.',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'video_url',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '/videos/who-we-are.mp4',
                        ],
                        [
                            'name' => 'thumbnail',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '/img/videos/who-we-are-thumb.jpg',
                            'imagesData' => []
                        ]
                    ]
                ]
            ],
            'videogallerymanufacturingprocess' => [
                [
                    'property_name' => 'content',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        [
                            'name' => 'section_title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Video Gallery - Manufacturing Process',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'section_subtitle',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'See how our products are crafted with precision and care.',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'video_url',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '/videos/manufacturing-process.mp4',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'thumbnail',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '/img/videos/manufacturing-thumb.jpg',
                            'imagesData' => []
                        ]
                    ]
                ]
            ],
            'designprocess' => [
                [
                    'property_name' => 'content',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'section_title' => [
                            'name' => 'section_title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Design Process',
                            'imagesData' => []
                        ],
                        'section_subtitle' => [
                            'name' => 'section_subtitle',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Our comprehensive approach to creating innovative furniture solutions.',
                            'imagesData' => []
                        ],
                        'process_steps' => [
                            'name' => 'process_steps',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => [
                                [
                                    'step' => '1',
                                    'title' => 'Research & Inspiration',
                                    'description' => 'Understanding market needs and gathering inspiration.',
                                    'icon' => 'fa-search'
                                ],
                                [
                                    'step' => '2',
                                    'title' => 'Concept Development',
                                    'description' => 'Creating initial concepts and prototypes.',
                                    'icon' => 'fa-lightbulb'
                                ],
                                [
                                    'step' => '3',
                                    'title' => 'Refinement',
                                    'description' => 'Iterating and improving based on feedback.',
                                    'icon' => 'fa-cogs'
                                ],
                                [
                                    'step' => '4',
                                    'title' => 'Final Design',
                                    'description' => 'Perfecting the design for production.',
                                    'icon' => 'fa-check-circle'
                                ]
                            ],
                            'imagesData' => []
                        ]
                    ]
                ]
            ],
            'needhelp' => [
                [
                    'property_name' => 'content',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        [
                            'name' => 'section_title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Need Help',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'section_subtitle',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Our team is here to assist you with any questions or concerns.',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'contact_email',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'help@krost.com.au',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'contact_phone',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '1800 1KROST',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'support_hours',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Monday to Friday, 8am to 6pm',
                            'imagesData' => []
                        ]
                    ]
                ]
            ],
            'latestnews' => [
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        [
                            'name' => 'title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => "Krost's Sydney Office Update With 3d Tour",
                            'imagesData' => []
                        ],
                        [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '/img/blog-page/News 1.png',
                            'imagesData' => []
                        ]
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
                        [
                            'name' => 'title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'THE BOOTH: WHERE IDEAS AND CONNECTIONS GROW',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '/img/blog-page/News 2.png',
                            'imagesData' => []
                        ]
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
                        [
                            'name' => 'title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Unveiling Our Updated Sydney Showroom',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '/img/blog-page/News 3.png',
                            'imagesData' => []
                        ]
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
                        [
                            'name' => 'title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Beyond Aesthetics: How Organic Design',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '/img/blog-page/News 4.png',
                            'imagesData' => []
                        ]
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
                        [
                            'name' => 'title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Watch The Full Panel Discussion On Designing',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '/img/blog-page/News 5.png',
                            'imagesData' => []
                        ]
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
                        [
                            'name' => 'title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Extraordinary Sydney Showroom Event Hosted',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '/img/blog-page/News 6.png',
                            'imagesData' => []
                        ]
                    ]
                ]
            ],
            'productlist' => [
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => 'product',
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 18,
                    'join' => [
                        [
                            'table' => 'product_content',
                            'on' => 'product.product_id = product_content.product_id',
                            'type' => 'LEFT'
                        ]
                    ],
                    'fields' => [
                        '`product`.`product_id`',
                        '`product_content`.`name`',
                        '`product`.`image`',
                        '`product_content`.`description`',
                        '`product_content`.`tags`',
                        '`product_content`.`finishes`'
                    ]
                ]
            ],
            'products' => [
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => 'product',
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 10,
                    'join' => [
                        [
                            'table' => 'product_content',
                            'on' => 'product.product_id = product_content.product_id',
                            'type' => 'LEFT'
                        ]
                    ],
                    'fields' => [
                        '`product`.`product_id`',
                        '`product_content`.`name`',
                        '`product`.`image`',
                        '`product_content`.`description`',
                        '`product`.`price`',
                        '`product`.`category`'
                    ]
                ]
            ],
            'virtualpinboard' => [
                [
                    'property_name' => 'content',
                    'component_id' => '',
                    'model' => 'pinboard',
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'join' => [
                        [
                            'table' => 'pinboard_item',
                            'on' => 'pinboard.pinboard_id = pinboard_item.pinboard_id',
                            'type' => 'LEFT'
                        ],
                        [
                            'table' => 'product',
                            'on' => 'product.product_id = pinboard_item.product_id',
                            'type' => 'LEFT'
                        ],
                        [
                            'table' => 'product_content',
                            'on' => 'product.product_id = product_content.product_id',
                            'type' => 'LEFT'
                        ],
                        [
                            'table' => 'project',
                            'on' => 'project.project_id = pinboard_item.project_id',
                            'type' => 'LEFT'
                        ],
                        [
                            'table' => 'media',
                            'on' => 'media.media_id = pinboard_item.media_id',
                            'type' => 'LEFT'
                        ],
                        [
                            'table' => 'comment',
                            'on' => 'comment.comment_id = pinboard_item.comment_id',
                            'type' => 'LEFT'
                        ]
                    ],
                    'fields' => [
                        '`pinboard_item`.`photo` as image',
                        'type',
                        'name',
                        'description',
                        'options',
                        'comment_placeholder'
                    ]
                ]
            ],
            'accountactivequotes' => [
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => 'quote',
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 5,
                    'fields' => [
                        [
                            'name' => 'quote_id',
                            'type' => [
                                'type' => [
                                    'max' => 255,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Quote ID',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'status',
                            'type' => [
                                'type' => [
                                    'max' => 255,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Status',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'created_at',
                            'type' => [
                                'type' => [
                                    'max' => 255,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Created At',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'total_amount',
                            'type' => [
                                'type' => [
                                    'max' => 255,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Total Amount',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '',
                            'imagesData' => []
                        ]
                    ]
                ]
            ],
            'accountactivequotepayment' => [
                [
                    'property_name' => 'content',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        'Account Active Quote Payment',
                        'Manage your quote payments and invoices.',
                        ['Credit Card', 'Bank Transfer', 'PayPal'],
                        '#907526',
                        '59.89 $',
                        '9.89 $',
                        '69.89 $',
                
                        [
                            [
                                'type' => 'Credit Card',
                                'active' => true,
                                'choices' => [
                                    [
                                        'label' => '50% Deposit Payment Due',
                                        'amount' => '599.89 $',
                                        'checked' => true,
                                    ],
                                    [
                                        'label' => 'Full Payment',
                                        'amount' => '1299.89 $',
                                        'checked' => false,
                                    ],
                                ],
                                'to_pay_now' => '599.89 $',
                                'supported_cards' => ['Mastercard', 'Visa'],
                                'pay_now_url' => 'contact.html',
                            ],
                            [
                                'type' => 'Direct Deposit',
                                'active' => false,
                                'details' => 'Direct Deposit Payment Details...',
                            ],
                            [
                                'type' => 'B Pay',
                                'active' => false,
                                'details' => 'B Pay Payment Details...',
                            ],
                            [
                                'type' => 'Cheque',
                                'active' => false,
                                'details' => 'Cheque Payment Details...',
                            ]
                        ],
                        '/img/modal/paypal-img.png',
                
                        'Courtney Henry',
                        'courtney.henry@example.com',
                        '(207) 555-0119',
                        '3890 Poplar Dr.',
                        'Preston',
                        'NC',
                        'United States',
                        '(207) 555-0119',
                        'Preston',
                        'NC',
                        'United States',
                    ]
                ]
            ],
            'accountcreaterequest' => [
                [
                    'property_name' => 'content',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        [
                            'name' => 'section_title',
                            'type' => [
                                'type' => [
                                    'max' => 255,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Section Title',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Account Create Request',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'section_subtitle',
                            'type' => [
                                'type' => [
                                    'max' => 500,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'TextArea',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Section Subtitle',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Submit a request to create your account.',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'company_name_label',
                            'type' => [
                                'type' => [
                                    'max' => 255,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Company Name Label',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Company Name',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'contact_person_label',
                            'type' => [
                                'type' => [
                                    'max' => 255,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Contact Person Label',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Contact Person',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'email_label',
                            'type' => [
                                'type' => [
                                    'max' => 255,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Email Label',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Email Address',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'phone_label',
                            'type' => [
                                'type' => [
                                    'max' => 255,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Phone Label',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Phone Number',
                            'imagesData' => []
                        ]
                    ]
                ]
            ],
            'accountpinboard' => [
                [
                    'property_name' => 'content',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        [
                            'name' => 'section_title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Account Pinboard',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'section_subtitle',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Your personalized pinboard for saved items.',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'pinboard_items',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => [],
                            'imagesData' => []
                        ]
                    ]
                ]
            ],
            'accountnavigation' => [
                [
                    'property_name' => 'content',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        [
                            'name' => 'section_title',
                            'type' => [
                                'type' => [
                                    'max' => 255,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Section Title',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Account Navigation',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'section_subtitle',
                            'type' => [
                                'type' => [
                                    'max' => 500,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'TextArea',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Section Subtitle',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Navigate your account dashboard.',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'dashboard_label',
                            'type' => [
                                'type' => [
                                    'max' => 255,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Dashboard Label',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Dashboard',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'dashboard_url',
                            'type' => [
                                'type' => [
                                    'max' => 255,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Dashboard URL',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '/account/dashboard',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'orders_label',
                            'type' => [
                                'type' => [
                                    'max' => 255,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Orders Label',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Orders',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'orders_url',
                            'type' => [
                                'type' => [
                                    'max' => 255,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Orders URL',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '/account/orders',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'quotes_label',
                            'type' => [
                                'type' => [
                                    'max' => 255,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Quotes Label',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Quotes',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'quotes_url',
                            'type' => [
                                'type' => [
                                    'max' => 255,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Quotes URL',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '/account/quotes',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'profile_label',
                            'type' => [
                                'type' => [
                                    'max' => 255,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Profile Label',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Profile',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'profile_url',
                            'type' => [
                                'type' => [
                                    'max' => 255,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Profile URL',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '/account/profile',
                            'imagesData' => []
                        ]
                    ]
                ]
            ],
            'accountrecentorders' => [
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => 'order',
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 5,
                    'fields' => [
                        '`order`.`order_id`',
                        '`order`.`status`',
                        '`order`.`created_at`',
                        '`order`.`total_amount`'
                    ]
                ]
            ],
            'whatishappening' => [
                [
                    'property_name' => 'content',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        [
                            'name' => 'section_title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => "WHAT'S HAPPENING?",
                            'imagesData' => []
                        ],
                        [
                            'name' => 'section_subtitle',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Stay updated with the latest news and events.',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '/img/blog-page/whats-happening-left.png',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'button_text',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Read More',
                            'imagesData' => []
                        ]
                    ]
                ]
            ],
            'customizablesolution' => [
                [
                    'property_name' => 'content',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        [
                            'name' => 'section_title',
                            'type' => [
                                'type' => [
                                    'max' => 255,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Section Title',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Customizable Solution',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'section_subtitle',
                            'type' => [
                                'type' => [
                                    'max' => 500,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'TextArea',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Section Subtitle',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Tailored furniture solutions to meet your specific needs.',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'materials_label',
                            'type' => [
                                'type' => [
                                    'max' => 255,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Materials Label',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Materials',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'materials_options',
                            'type' => [
                                'type' => [
                                    'max' => 1000,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'TextArea',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Materials Options (comma separated)',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Wood, Metal, Fabric, Plastic',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'colors_label',
                            'type' => [
                                'type' => [
                                    'max' => 255,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Colors Label',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Colors',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'colors_options',
                            'type' => [
                                'type' => [
                                    'max' => 1000,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'TextArea',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Colors Options (comma separated)',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Black, White, Brown, Gray',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'sizes_label',
                            'type' => [
                                'type' => [
                                    'max' => 255,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Sizes Label',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Sizes',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'sizes_options',
                            'type' => [
                                'type' => [
                                    'max' => 1000,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'TextArea',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Sizes Options (comma separated)',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Small, Medium, Large, Custom',
                            'imagesData' => []
                        ]
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
                    'item_count' => 6,
                    'join' => [
                        [
                            'table' => 'project_content',
                            'on' => 'project.project_id = project_content.project_id',
                            'type' => 'LEFT'
                        ]
                    ],
                    'fields' => [
                        '`project`.`project_id`',
                        '`project_content`.`name`',
                        '`project`.`description`',
                        '`project`.`image`',
                        '`project`.`category`'
                    ]
                ]
            ],
            'designresourcedocuments' => [
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => 'design_resource',
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 10,
                    'fields' => [
                        '`design_resource`.`resource_id`',
                        '`design_resource`.`name`',
                        '`design_resource`.`file_url`',
                        '`design_resource`.`file_type`',
                        '`design_resource`.`file_size`'
                    ]
                ]
            ],
            'designresourceimages' => [
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => 'design_resource',
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 20,
                    'fields' => [
                        '`design_resource`.`resource_id`',
                        '`design_resource`.`name`',
                        '`design_resource`.`image_url`',
                        '`design_resource`.`thumbnail_url`',
                        '`design_resource`.`description`'
                    ]
                ]
            ],
            'designresourcetextiles' => [
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => 'design_resource',
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 15,
                    'fields' => [
                        '`design_resource`.`resource_id`',
                        '`design_resource`.`name`',
                        '`design_resource`.`image_url`',
                        '`design_resource`.`material_type`',
                        '`design_resource`.`color_options`'
                    ]
                ]
            ],
            'designresourcefinishes' => [
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => 'design_resource',
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 12,
                    'fields' => [
                        '`design_resource`.`resource_id`',
                        '`design_resource`.`name`',
                        '`design_resource`.`image_url`',
                        '`design_resource`.`finish_type`',
                        '`design_resource`.`durability_rating`'
                    ]
                ]
            ],
            'designresourcemodels' => [
                [
                    'property_name' => 'items',
                    'component_id' => '',
                    'model' => 'design_resource',
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 8,
                    'fields' => [
                        '`design_resource`.`resource_id`',
                        '`design_resource`.`name`',
                        '`design_resource`.`model_file_url`',
                        '`design_resource`.`file_format`',
                        '`design_resource`.`file_size`'
                    ]
                ]
            ],
            'dashboarddeliveryinstall' => [
                [
                    'property_name' => 'content',
                    'component_id' => '',
                    'model' => null,
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'fields' => [
                        [
                            'name' => 'section_title',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Dashboard Delivery Install',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'section_subtitle',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'Track your delivery and installation status.',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'delivery_status',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => 'In Transit',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'estimated_delivery',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '2025-01-25',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'installation_date',
                            'type' => [
                                'type' => [
                                    'max' => 100,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'InputText',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '2025-01-26',
                            'imagesData' => []
                        ]
                    ]
                ]
            ],
            'projectdetailunderhero' => [
                [
                    'property_name' => 'content',
                    'component_id' => '',
                    'model' => 'project',
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'join' => [
                        [
                            'table' => 'project_content',
                            'on' => 'project.project_id = project_content.project_id',
                            'type' => 'LEFT'
                        ]
                    ],
                    'fields' => [
                        [
                            'name' => 'project_id',
                            'type' => [
                                'type' => [
                                    'max' => 255,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Project ID',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'name',
                            'type' => [
                                'type' => [
                                    'max' => 255,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Project Name',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 1000,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'TextArea',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Project Description',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 255,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Project Image',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'location',
                            'type' => [
                                'type' => [
                                    'max' => 255,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Project Location',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'completion_date',
                            'type' => [
                                'type' => [
                                    'max' => 255,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Completion Date',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '',
                            'imagesData' => []
                        ]
                    ]
                ]
            ],
            'projectdetailpenetrating' => [
                [
                    'property_name' => 'content',
                    'component_id' => '',
                    'model' => 'project',
                    'model_id' => null,
                    'is_recent' => true,
                    'item_count' => 1,
                    'join' => [
                        [
                            'table' => 'project_content',
                            'on' => 'project.project_id = project_content.project_id',
                            'type' => 'LEFT'
                        ]
                    ],
                    'fields' => [
                        [
                            'name' => 'project_id',
                            'type' => [
                                'type' => [
                                    'max' => 255,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Project ID',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'name',
                            'type' => [
                                'type' => [
                                    'max' => 255,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Project Name',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'description',
                            'type' => [
                                'type' => [
                                    'max' => 1000,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'TextArea',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Project Description',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'image',
                            'type' => [
                                'type' => [
                                    'max' => 255,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'FileUpload',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Project Image',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'project_type',
                            'type' => [
                                'type' => [
                                    'max' => 255,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Project Type',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '',
                            'imagesData' => []
                        ],
                        [
                            'name' => 'client_name',
                            'type' => [
                                'type' => [
                                    'max' => 255,
                                    'min' => 0,
                                    'mask' => '',
                                    'name' => 'InputText',
                                    'step' => 1,
                                    'type' => 'InputText',
                                    'value' => '',
                                    'length' => 0,
                                    'options' => [],
                                    'required' => false,
                                    'keyfilter' => '',
                                    'placeholder' => 'Client Name',
                                    'suggestions' => [],
                                    'treeOptions' => [],
                                    'editorConfig' => [],
                                    'cascadeOptions' => []
                                ]
                            ],
                            'isNew' => true,
                            'value' => '',
                            'imagesData' => []
                        ]
                    ]
                ]
            ],
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
        // Convert string image paths to JSON format before seeding
        foreach ($this->data['components'] as &$component) {
            if (isset($component['image'])) {
                if ($component['image'] === '' || $component['image'] === null) {
                    $component['image'] = null;
                } else {
                    // Convert string image path to JSON object format
                    $component['image'] = json_encode([
                        'url' => $component['image'],
                        'alt' => '',
                        'title' => ''
                    ]);
                }
            } else {
                $component['image'] = null;
            }
        }
        
        $this->repository->seedData($this->data);
    }
}