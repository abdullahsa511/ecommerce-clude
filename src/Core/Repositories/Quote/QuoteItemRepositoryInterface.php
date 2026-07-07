<?php

declare(strict_types=1);

namespace App\Core\Repositories\Quote;

use App\Core\Models\Quote\QuoteItem;
use App\Core\Models\Quote\QuoteItemData;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface QuoteItemRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Create a new quote
     *
     * @param QuoteData $quoteData
     * @return QuoteItem
     */
    public function createQuoteItem(array $quoteItems): array;

    /**
     * Update an existing quote
     *
     * @param QuoteData $quoteData
     * @return QuoteItem
     */
    public function updateQuoteItem(QuoteItemData $quoteItemData): QuoteItem;

    /**
     * Get a quote by ID
     *
     * @param int $quoteId
     * @return QuoteItem
     */
    public function showQuoteItem(int $quoteId): QuoteItem;

    public function productList(string $search): array;

} 