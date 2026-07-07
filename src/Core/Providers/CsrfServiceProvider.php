<?php

declare(strict_types=1);

namespace App\Core\Providers;

use App\Core\Services\CsrfService;
use App\Core\System\Cache\Redis;
use Illuminate\Container\Container;

class CsrfServiceProvider
{
    public function __construct(private Container $container)
    {
    }

    public function register(): void
    {
        $this->container->singleton(CsrfService::class, function () {
            /** @var array<string, mixed> $config */
            $config = $this->container->make('config');
            $csrf = self::resolveAppCsrfConfig($config);

            return new CsrfService($this->container->make(Redis::class), $csrf);
        });
    }

    /**
     * ConfigurationsLoader may store `src/Core/config/app.php` under the `app` key.
     *
     * @param array<string, mixed> $config
     * @return array<string, mixed>
     */
    private static function resolveAppCsrfConfig(array $config): array
    {
        if (isset($config['csrf']) && is_array($config['csrf'])) {
            return $config['csrf'];
        }
        if (isset($config['app']['csrf']) && is_array($config['app']['csrf'])) {
            return $config['app']['csrf'];
        }

        return [];
    }
}
