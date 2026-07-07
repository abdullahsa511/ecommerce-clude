<?php

declare(strict_types=1);

namespace App\Core\Repositories\Quote;

use App\Core\Models\Quote\Quote;
use App\Core\Models\Quote\QuoteData;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface QuoteRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all quotes
     *
     * @return array
     */
    public function all(): array;


    // public function findAllQuotes(): array;

    /**
     * Get quotes by company ID
     *
     * @param int $companyId
     * @return array
     */
    public function findByCompanyId(int $companyId): array;

    /**
     * Get quotes by user ID
     *
     * @param int $userId
     * @return array
     */
    public function findByUserId(int $userId): array;

    /**
     * Get quote by UUID
     *
     * @param string $uuid
     * @return Quote|null
     */
    public function getIdByUuid(string $uuid): ?Quote;

    /**
     * Get quote by reference number
     *
     * @param string $referenceNumber
     * @return Quote|null
     */
    public function findByReferenceNumber(string $referenceNumber): ?Quote;

    /**
     * Create a new quote
     *
     * @param QuoteData $quoteData
     * @return Quote
     */
    public function createQuote(QuoteData $quoteData): Quote;

    /**
     * Update an existing quote
     *
     * @param QuoteData $quoteData
     * @return Quote
     */
    public function updateQuote(QuoteData $quoteData): Quote;

    /**
     * Get a quote by ID
     *
     * @param string $uuid
     */
    public function showQuote(string $uuid): array;

    /**
     * Delete a quote
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Insert quotes
     *
     * @param array $data
     * @return bool
     */
    public function insertQuotes(array $data): bool;

    /**
     * Get quote payment data
     *
     * @param int $quoteId
     * @return array
     */
    public function getQuotePaymentData(int $quoteId): array;

    /**
     * Get active quotes
     *
     * @param int $customerId
     * @return array
     */
    public function getActiveQuotes(int $customerId): array;

    public function importCSVs(string $csv_file): array;
    public function getRecentQuotesWidget($limit = 20): array;
    public function getQuoteById(int $id): array;
    public function getCustomerQuotesForComponent(array $params): array;
    public function getQuoteByUuid(string $uuid): array;
    public function getQuoteAcceptance(string $quoteUuid): array;
} 