<?php

declare(strict_types=1);

namespace App\Core\Repositories\Payment;

/**
 * @phpstan-type PayableInvoice array{
 *   type: 'quote'|'order',
 *   id: int,
 *   reference: string,
 *   amount: string,
 *   currency: string,
 *   email: string,
 *   paid: bool,
 *   expired: bool
 * }
 */
interface PaymentInvoiceRepositoryInterface
{
    /**
     * Resolve a reference to a payable invoice/quote and validate amount when supplied.
     *
     * @return PayableInvoice|null
     */
    public function findPayableByReference(string $reference, ?string $expectedAmount = null): ?array;
}
