<?php

declare(strict_types=1);

namespace App\Core\Providers;

use App\Core\Repositories\Payment\PaymentIntentRepository;
use App\Core\Repositories\Payment\PaymentIntentRepositoryInterface;
use App\Core\Repositories\Payment\PaymentInvoiceRepository;
use App\Core\Repositories\Payment\PaymentInvoiceRepositoryInterface;
use App\Core\Repositories\Payment\PaymentOrganisationRepository;
use App\Core\Repositories\Payment\PaymentOrganisationRepositoryInterface;
use App\Core\Repositories\Payment\PaymentRateLimitRepository;
use App\Core\Repositories\Payment\PaymentRateLimitRepositoryInterface;
use App\Core\Services\NabPaymentService;
use Illuminate\Container\Container;

class PaymentServiceProvider
{
    public function __construct(private Container $container)
    {
    }

    public function register(): void
    {
        $this->container->singleton(PaymentOrganisationRepositoryInterface::class, PaymentOrganisationRepository::class);
        $this->container->singleton(PaymentIntentRepositoryInterface::class, PaymentIntentRepository::class);
        $this->container->singleton(PaymentInvoiceRepositoryInterface::class, PaymentInvoiceRepository::class);
        $this->container->singleton(PaymentRateLimitRepositoryInterface::class, PaymentRateLimitRepository::class);
        $this->container->singleton(NabPaymentService::class, NabPaymentService::class);
    }
}
