<?php

declare(strict_types=1);

namespace App\Core\Database\seeders;

use App\Core\App\KernelCli;
use App\Core\Repositories\Product\ProductQuestionRepositoryInterface;

use Illuminate\Container\Container;

class ProductQuestionSeeder
{
    private ProductQuestionRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private $productQuestions = [
        [
            'product_id' => 1,
            'user_id' => 1,
            'author' => 'John Smith',
            'content' => 'What are the dimensions of this product? I need to make sure it will fit in my space.',
            'status' => 1,
            'parent_id' => null
        ],
        [
            'product_id' => 1,
            'user_id' => 114,
            'author' => 'Sarah Johnson',
            'content' => 'Does this product come with a warranty? If so, how long is it valid for?',
            'status' => 1,
            'parent_id' => null
        ],
        [
            'product_id' => 1,
            'user_id' => 115,
            'author' => 'Mike Davis',
            'content' => 'What materials is this product made from? I\'m looking for something durable.',
            'status' => 1,
            'parent_id' => null
        ],
        [
            'product_id' => 1,
            'user_id' => 116,
            'author' => 'Emily Wilson',
            'content' => 'How long does shipping typically take? I need this for an upcoming event.',
            'status' => 1,
            'parent_id' => null
        ],
        [
            'product_id' => 1,
            'user_id' => 117,
            'author' => 'David Brown',
            'content' => 'Can this product be used outdoors? I need something weather-resistant.',
            'status' => 1,
            'parent_id' => null
        ],
        [
            'product_id' => 1,
            'user_id' => 118,
            'author' => 'Lisa Anderson',
            'content' => 'Is assembly required for this product? If so, are tools included?',
            'status' => 1,
            'parent_id' => null
        ],
        [
            'product_id' => 3,
            'user_id' => 1,
            'author' => 'Robert Taylor',
            'content' => 'What is the weight capacity of this product? I need to know if it can support my needs.',
            'status' => 1,
            'parent_id' => null
        ],
        [
            'product_id' => 3,
            'user_id' => 114,
            'author' => 'Jennifer Martinez',
            'content' => 'Does this product require any special maintenance or cleaning?',
            'status' => 1,
            'parent_id' => null
        ],
        [
            'product_id' => 3,
            'user_id' => 115,
            'author' => 'Christopher Lee',
            'content' => 'What colors are available for this product? I\'m looking for something specific.',
            'status' => 1,
            'parent_id' => null
        ],
        [
            'product_id' => 3,
            'user_id' => 116,
            'author' => 'Amanda Garcia',
            'content' => 'Is this product compatible with other brands? I have existing equipment.',
            'status' => 1,
            'parent_id' => null
        ],
        [
            'product_id' => 3,
            'user_id' => 117,
            'author' => 'Kevin Rodriguez',
            'content' => 'What is the return policy for this product? I want to make sure I can return it if needed.',
            'status' => 1,
            'parent_id' => null
        ],
        [
            'product_id' => 3,
            'user_id' => 118,
            'author' => 'Michelle White',
            'content' => 'Does this product come with instructions or a manual?',
            'status' => 1,
            'parent_id' => null
        ],
        [
            'product_id' => 3,
            'user_id' => 1,
            'author' => 'Daniel Thompson',
            'content' => 'What is the power consumption of this product? I need to know the energy efficiency.',
            'status' => 1,
            'parent_id' => null
        ],
        [
            'product_id' => 3,
            'user_id' => 114,
            'author' => 'Jessica Moore',
            'content' => 'Is this product suitable for commercial use? I run a small business.',
            'status' => 1,
            'parent_id' => null
        ],
        [
            'product_id' => 3,
            'user_id' => 115,
            'author' => 'Andrew Jackson',
            'content' => 'What is the noise level of this product? I need something quiet for my home office.',
            'status' => 1,
            'parent_id' => null
        ],
        [
            'product_id' => 3,
            'user_id' => 116,
            'author' => 'Nicole Martin',
            'content' => 'Does this product have any safety certifications? I\'m concerned about safety standards.',
            'status' => 1,
            'parent_id' => null
        ],
        [
            'product_id' => 3,
            'user_id' => 117,
            'author' => 'Steven Lee',
            'content' => 'What is the expected lifespan of this product? I want to know how long it will last.',
            'status' => 1,
            'parent_id' => null
        ],
        [
            'product_id' => 3,
            'user_id' => 118,
            'author' => 'Rachel Clark',
            'content' => 'Can this product be customized or modified? I have specific requirements.',
            'status' => 1,
            'parent_id' => null
        ],
        [
            'product_id' => 3,
            'user_id' => 1,
            'author' => 'Thomas Lewis',
            'content' => 'What is the temperature range this product can operate in?',
            'status' => 1,
            'parent_id' => null
        ],
        [
            'product_id' => 3,
            'user_id' => 114,
            'author' => 'Hannah Walker',
            'content' => 'Does this product come with any accessories or additional parts?',
            'status' => 1,
            'parent_id' => null
        ]
    ];


    public function __construct()
    {
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(ProductQuestionRepositoryInterface::class);
    }

    public function seed(): void
    {
        $this->repository->insertMultiple($this->productQuestions);
    }
    

} 