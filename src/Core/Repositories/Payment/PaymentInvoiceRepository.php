<?php

declare(strict_types=1);

namespace App\Core\Repositories\Payment;

use App\Core\Repositories\Order\OrderRepositoryInterface;
use App\Core\Repositories\Quote\QuoteRepositoryInterface;
use App\Core\Services\Payment\PaymentValidation;

/**
 * Validates payment references against quotes and orders in the database.
 */
class PaymentInvoiceRepository implements PaymentInvoiceRepositoryInterface
{
    /** @var list<int> */
    private const PAID_ORDER_STATUS_IDS = [3, 4];

    public function __construct(
        private QuoteRepositoryInterface $quoteRepository,
        private OrderRepositoryInterface $orderRepository
    ) {
    }

    public function findPayableByReference(string $reference, ?string $expectedAmount = null): ?array
    {
        $reference = PaymentValidation::sanitizeReference($reference);
        if ($reference === '') {
            return null;
        }

        $quote = $this->quoteRepository->findByReferenceNumber($reference);
        if ($quote !== null) {
            return $this->mapQuote($quote, $expectedAmount);
        }

        $order = $this->orderRepository->findByReferenceNumber($reference);
        if ($order !== null) {
            return $this->mapOrder($order, $expectedAmount);
        }

        return null;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function mapQuote(object $quote, ?string $expectedAmount): ?array
    {
        $amount = $this->normaliseAmount((float) ($quote->grand_total_sp_inc_gst ?? $quote->total_sp_inc_gst ?? 0));
        if ($amount === null || $amount === '0.00') {
            return null;
        }

        if ($expectedAmount !== null && !$this->amountsMatch($amount, $expectedAmount)) {
            return null;
        }

        $expiry = (string) ($quote->expiry_date ?? '');
        $expired = $expiry !== '' && strtotime($expiry) !== false && strtotime($expiry) < time();
        $email = trim((string) ($quote->bill_to ?? ''));
        if (!PaymentValidation::isValidEmail($email)) {
            $email = '';
        }

        return [
            'type' => 'quote',
            'id' => (int) ($quote->quote_id ?? 0),
            'reference' => (string) ($quote->reference_number ?? ''),
            'amount' => $amount,
            'currency' => 'AUD',
            'email' => $email,
            'paid' => false,
            'expired' => $expired,
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function mapOrder(object $order, ?string $expectedAmount): ?array
    {
        $amount = $this->normaliseAmount((float) ($order->total ?? 0));
        if ($amount === null || $amount === '0.00') {
            return null;
        }

        if ($expectedAmount !== null && !$this->amountsMatch($amount, $expectedAmount)) {
            return null;
        }

        $paymentStatusId = (int) ($order->payment_status_id ?? 1);

        return [
            'type' => 'order',
            'id' => (int) ($order->order_id ?? 0),
            'reference' => (string) ($order->reference_number ?? $order->invoice_no ?? ''),
            'amount' => $amount,
            'currency' => (string) ($order->currency ?? 'AUD'),
            'email' => PaymentValidation::isValidEmail($order->email ?? '') ? trim((string) $order->email) : '',
            'paid' => in_array($paymentStatusId, self::PAID_ORDER_STATUS_IDS, true),
            'expired' => false,
        ];
    }

    private function normaliseAmount(float $value): ?string
    {
        $parsed = PaymentValidation::parseAmount($value);

        return $parsed['ok'] ? $parsed['value'] : null;
    }

    private function amountsMatch(string $left, string $right): bool
    {
        return abs((float) $left - (float) $right) < 0.001;
    }
}
