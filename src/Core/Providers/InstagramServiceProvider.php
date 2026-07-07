<?php

declare(strict_types=1);

namespace App\Core\Providers;

use App\Core\Repositories\Instagram\ProductInstagramRepository;
use App\Core\Repositories\Instagram\ProductInstagramRepositoryInterface;
use App\Core\Services\Instagram\InstagramGraphService;
use App\Core\Services\Instagram\InstagramGraphClient;
use App\Core\Services\Instagram\InstagramTokenService;
use Illuminate\Container\Container;

class InstagramServiceProvider
{
    public function __construct(private Container $container)
    {
    }

    public function register(): void
    {
        $this->container->singleton(InstagramGraphClient::class, InstagramGraphClient::class);
        $this->container->singleton(InstagramTokenService::class, InstagramTokenService::class);
        $this->container->singleton(InstagramGraphService::class, function (Container $container) {
            return new InstagramGraphService(
                $container->make(InstagramGraphClient::class),
                $container->make(InstagramTokenService::class)
            );
        });
        $this->container->singleton(
            ProductInstagramRepositoryInterface::class,
            ProductInstagramRepository::class
        );
    }
}
