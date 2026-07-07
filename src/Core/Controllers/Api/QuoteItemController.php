<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\Quote\QuoteItemRepositoryInterface;
use App\Core\Models\Quote\QuoteResponse;
use App\Core\Models\Quote\QuoteItemData;

class QuoteItemController extends ApiController
{
    private QuoteItemRepositoryInterface $quoteItemRepository;

    public function __construct(QuoteItemRepositoryInterface $quoteItemRepository)
    {
        parent::__construct();
        $this->quoteItemRepository = $quoteItemRepository;
    }

    /**
     * Get all quotes
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $quotes = $this->quoteItemRepository->findAll();
        return $this->renderResponse($quotes);
    }

    /**
     * Get quote by ID
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function show(Request $request, $id): Response
    {
        $quote = $this->quoteItemRepository->showQuoteItem((int)$id);
        if(!$quote){
            return $this->renderError(404, 'Quote not found');
        }
        $response = new QuoteResponse($quote->data);
        return $this->renderResponse($response);
    }

    /**
     * Create a new quote
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        try {
            $quoteItems = $request->input('quoteItem');

            $quoteItemResult = $this->quoteItemRepository->createQuoteItem($quoteItems);
            if(!$quoteItemResult){
                return $this->renderError(500, 'Failed to create quote');
            }
            
            return $this->renderResponse([
                'message' => 'Quote items created successfully',
                'data' => $quoteItemResult
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        } catch (\Exception $e) {
            return $this->renderError(500, 'Failed to create quote items: ' . $e->getMessage());
        }
    }

    /**
     * Update a quote
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id): Response
    {
        try {
            $quote = $request->input('quote_item');
            $quoteItemData = new QuoteItemData($quote);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $quote = $this->quoteItemRepository->updateQuoteItem($quoteItemData);
        if(!$quote){
            return $this->renderError(500, 'Failed to update quote');
        }
        $quote = new QuoteResponse($quote->data);
        return $this->renderResponse($quote);
    }

    /**
     * Delete a quote
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function delete(Request $request, $id): Response
    {
        $quote = $this->quoteItemRepository->showQuoteItem((int) $id);
        if (!$quote) {
            return $this->renderError(404, 'Quote not found');
        }

        try {
            $this->quoteItemRepository->delete((int) $id);
            return $this->renderResponse(['message' => 'Quote deleted successfully']);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Failed to delete quote: ' . $e->getMessage());
        }
    }

    public function productList(Request $request): Response
    {
        $productList = $this->quoteItemRepository->productList($request->input('search'));
        return $this->renderResponse($productList);
    }
} 