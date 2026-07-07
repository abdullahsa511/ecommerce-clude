<?php

declare(strict_types=1);

namespace App\Core\Database\seeders;

use App\Core\App\KernelCli;
use App\Core\Repositories\Product\ProductReviewRepositoryInterface;

use Illuminate\Container\Container;

class ProductReviewSeeder
{
    private ProductReviewRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private $productReviews = [
        [
            'product_id' => 1,
            'user_id' => 1,
            'author' => 'John Smith',
            'content' => 'Excellent product! The quality is outstanding and it exceeded my expectations. Highly recommend to anyone looking for this type of item.',
            'rating' => 5,
            'status' => 1,
            'parent_id' => null
        ],
        [
            'product_id' => 1,
            'user_id' => 2,
            'author' => 'Sarah Johnson',
            'content' => 'Good product overall, but shipping took longer than expected. The item itself is well-made and worth the wait.',
            'rating' => 4,
            'status' => 1,
            'parent_id' => null
        ],
        [
            'product_id' => 1,
            'user_id' => 3,
            'author' => 'Mike Davis',
            'content' => 'Average quality for the price. It works as described but nothing special. Would consider alternatives next time.',
            'rating' => 3,
            'status' => 1,
            'parent_id' => null
        ],
        [
            'product_id' => 1,
            'user_id' => 4,
            'author' => 'Emily Wilson',
            'content' => 'Disappointed with this purchase. The product arrived damaged and customer service was unhelpful. Would not recommend.',
            'rating' => 2,
            'status' => 1,
            'parent_id' => null
        ],
        [
            'product_id' => 1,
            'user_id' => 5,
            'author' => 'David Brown',
            'content' => 'Perfect for my needs! Fast delivery and excellent packaging. The product quality is top-notch.',
            'rating' => 5,
            'status' => 1,
            'parent_id' => null
        ],
        [
            'product_id' => 1,
            'user_id' => 5,
            'author' => 'Lisa Anderson',
            'content' => 'Great value for money. The product is durable and performs well. Very satisfied with this purchase.',
            'rating' => 4,
            'status' => 1,
            'parent_id' => null
        ],
        [
            'product_id' => 3,
            'user_id' => 1,
            'author' => 'Robert Taylor',
            'content' => 'Amazing product! The features are exactly what I was looking for. Easy to use and very reliable.',
            'rating' => 5,
            'status' => 1,
            'parent_id' => null
        ],
        [
            'product_id' => 3,
            'user_id' => 11,
            'author' => 'Jennifer Martinez',
            'content' => 'Good product but the instructions could be clearer. Once I figured it out, it works great.',
            'rating' => 4,
            'status' => 1,
            'parent_id' => null
        ],
        [
            'product_id' => 3,
            'user_id' => 1,
            'author' => 'Christopher Lee',
            'content' => 'Solid product with good build quality. The price is reasonable for what you get.',
            'rating' => 4,
            'status' => 1,
            'parent_id' => null
        ],
        [
            'product_id' => 3,
            'user_id' => 2,
            'author' => 'Amanda Garcia',
            'content' => 'Not impressed with this product. It feels cheap and doesn\'t work as advertised. Waste of money.',
            'rating' => 1,
            'status' => 1,
            'parent_id' => null
        ],
        [
            'product_id' => 3,
            'user_id' => 3,
            'author' => 'Kevin Rodriguez',
            'content' => 'Excellent customer service and the product is fantastic. Highly recommend this seller and product.',
            'rating' => 5,
            'status' => 1,
            'parent_id' => null
        ],
        [
            'product_id' => 3,
            'user_id' => 4,
            'author' => 'Michelle White',
            'content' => 'The product is okay but the packaging was damaged during shipping. Product itself works fine.',
            'rating' => 3,
            'status' => 1,
            'parent_id' => null
        ],
        [
            'product_id' => 3,
            'user_id' => 1,
            'author' => 'Daniel Thompson',
            'content' => 'Outstanding quality and performance! This product has exceeded all my expectations. Worth every penny.',
            'rating' => 5,
            'status' => 1,
            'parent_id' => null
        ],
        [
            'product_id' => 3,
            'user_id' => 5,
            'author' => 'Jessica Moore',
            'content' => 'Very good product with excellent features. The design is modern and it\'s easy to use.',
            'rating' => 4,
            'status' => 1,
            'parent_id' => null
        ],
        [
            'product_id' => 3,
            'user_id' => 11,
            'author' => 'Andrew Jackson',
            'content' => 'Decent product for the price. It does what it\'s supposed to do but nothing extraordinary.',
            'rating' => 3,
            'status' => 1,
            'parent_id' => null
        ],
        [
            'product_id' => 3,
            'user_id' => 1,
            'author' => 'Nicole Martin',
            'content' => 'I love this product! It\'s exactly what I needed and the quality is exceptional. Fast shipping too!',
            'rating' => 5,
            'status' => 1,
            'parent_id' => null
        ],
        [
            'product_id' => 3,
            'user_id' => 2,
            'author' => 'Steven Lee',
            'content' => 'Good product overall. The only downside is that it\'s a bit heavy, but the quality makes up for it.',
            'rating' => 4,
            'status' => 1,
            'parent_id' => null
        ],
        [
            'product_id' => 3,
            'user_id' => 3,
            'author' => 'Rachel Clark',
            'content' => 'Disappointed with the quality. For the price, I expected much better. Would not buy again.',
            'rating' => 2,
            'status' => 1,
            'parent_id' => null
        ],
        [
            'product_id' => 3,
            'user_id' => 1,
            'author' => 'Thomas Lewis',
            'content' => 'Excellent product with great features. The build quality is solid and it performs perfectly.',
            'rating' => 5,
            'status' => 1,
            'parent_id' => null
        ],
        [
            'product_id' => 3,
            'user_id' => 4,
            'author' => 'Hannah Walker',
            'content' => 'Very satisfied with this purchase. The product is well-made and the seller was professional.',
            'rating' => 4,
            'status' => 1,
            'parent_id' => null
        ]
    ];


    public function __construct()
    {
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(ProductReviewRepositoryInterface::class);
    }

    public function seed(): void
    {
        $this->repository->insertMultiple($this->productReviews);
    }
    

} 