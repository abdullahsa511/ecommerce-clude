<?php

declare(strict_types=1);

namespace App\Core\Database\Seeders;

use App\Core\App\KernelCli;
use App\Core\Repositories\UserRepositoryInterface;
use Illuminate\Container\Container;

class CustomerSeeder
{
    private UserRepositoryInterface $repository;
    private KernelCli $kernel;
    private Container $container;
    private $data = [
        [
            'user_group_id' => 1,
            'site_id' => 1,
            'username' => 'johndoe',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'password' => '',
            'email' => 'john.doe@example.com',
            'phone_number' => '+1234567890',
            'url' => 'https://example.com/johndoe',
            'status' => 1,
            'display_name' => 'John Doe',
            'avatar' => 'default-avatar.jpg',
            'bio' => 'A passionate customer who loves shopping',
            'token' => '',
            'subscribe' => 1
        ],
        [
            'user_group_id' => 1,
            'site_id' => 1,
            'username' => 'janesmith',
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'password' => '',
            'email' => 'jane.smith@example.com',
            'phone_number' => '+1987654321',
            'url' => 'https://example.com/janesmith',
            'status' => 1,
            'display_name' => 'Jane Smith',
            'avatar' => 'default-avatar.jpg',
            'bio' => 'Regular online shopper with great taste',
            'token' => '',
            'subscribe' => 0
        ],
        [
            'user_group_id' => 1,
            'site_id' => 1,
            'username' => 'mikebrown',
            'first_name' => 'Mike',
            'last_name' => 'Brown',
            'password' => '',
            'email' => 'mike.brown@example.com',
            'phone_number' => '+1122334455',
            'url' => 'https://example.com/mikebrown',
            'status' => 1,
            'display_name' => 'Mike Brown',
            'avatar' => 'default-avatar.jpg',
            'bio' => 'Tech enthusiast and frequent buyer',
            'token' => '',
            'subscribe' => 1
        ]
    ];

    public function __construct()
    {
        $this->kernel = new KernelCli();
        $this->container = $this->kernel->getContainer();
        $this->repository = $this->container->make(UserRepositoryInterface::class);
        
        // Generate dynamic values for each user
        foreach ($this->data as &$user) {
            $user['password'] = password_hash('password123', PASSWORD_DEFAULT);
            $user['token'] = bin2hex(openssl_random_pseudo_bytes(16));
        }
    }

    public function seed(): void
    {
        $this->repository->insertMultiple($this->data);
    }
} 