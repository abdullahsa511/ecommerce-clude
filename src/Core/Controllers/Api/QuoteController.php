<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\Quote\QuoteRepositoryInterface;
use App\Core\Models\Quote\QuoteData;
use App\Core\Models\Quote\QuoteResponse;

class QuoteController extends ApiController
{
    private QuoteRepositoryInterface $quoteRepository;

    public function __construct(QuoteRepositoryInterface $quoteRepository)
    {
        parent::__construct();
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Get all quotes
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $quotes = $this->quoteRepository->findAll();
        $quotes = array_map(function($quote){
            return new QuoteResponse((object) $quote);
        }, $quotes);
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
        $quote = $this->quoteRepository->showQuote((int)$id);
        if(!$quote){
            return $this->renderError(404, 'Quote not found');
        }
        // $response = new QuoteResponse((object) $quote);
        return $this->renderResponse($quote);
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
            $quote = $request->input('quote');
            $quoteData = new QuoteData($quote);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $quote = $this->quoteRepository->createQuote($quoteData);
        if(!$quote){
            return $this->renderError(500, 'Failed to create quote');
        }
        $quote = new QuoteResponse($quote->data);
        return $this->renderResponse($quote);
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
            $quote = $request->input('quote');
            $quoteData = new QuoteData($quote);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $quote = $this->quoteRepository->updateQuote($quoteData);
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
        $quote = $this->quoteRepository->showQuote((int) $id);
        if (!$quote) {
            return $this->renderError(404, 'Quote not found');
        }

        try {
            $this->quoteRepository->delete((int) $id);
            return $this->renderResponse(['message' => 'Quote deleted successfully']);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Failed to delete quote: ' . $e->getMessage());
        }
    }


    public function importQuotes(Request $request): Response
    {
        $csv_file = $request->file('csv_file');
        
        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';
        
        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->quoteRepository->importCSVs($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }

    public function getQuoteAcceptance(Request $request): Response
    {
        $quoteUuid = $request->query('quote_id');
        if(!$quoteUuid){
            return $this->renderError(400, 'Quote UUID is required');
        }
        $quoteAcceptance = $this->quoteRepository->getQuoteAcceptance($quoteUuid);
        if(!$quoteAcceptance){
            return $this->renderError(404, 'Quote acceptance not found');
        }
        return $this->renderResponse($quoteAcceptance);
    }
} 