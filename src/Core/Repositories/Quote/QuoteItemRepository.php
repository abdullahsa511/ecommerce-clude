<?php

declare(strict_types=1);

namespace App\Core\Repositories\Quote;

use App\Core\Models\Product\Product;
use PDO;
use App\Core\Models\Quote\QuoteItem;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Models\Quote\QuoteItemData;

class QuoteItemRepository extends BaseRepository implements QuoteItemRepositoryInterface
{
    private Product $product;
    public function __construct(PDO $db, Product $product)
    {
        parent::__construct($db, 'quote_item', QuoteItem::class);
        $this->product = $product;
        $this->product->setDb($db);
    }


    public function createQuoteItem(array $quoteItems): array
    {
        $mappedItems = array_map(function($item) {
            return [
                'quote_id' => $item['quote_id'],
                'product_id' => $item['product_id'],
                'description' => $item['item_description'],
                'quantity' => $item['item_quantity'],
                'unit_price' => $item['item_unit_price'],
                'total_price' => $item['item_total'],
                'uuid' => $this->generateUuid(),
                'language_id' => $item['language_id'] ?? 1 // Default language ID if not provided
            ];
        }, $quoteItems);

        $this->model->upsert($mappedItems, ['quote_item_id', 'language_id']);
        return $mappedItems;
    }
    public function updateQuoteItem(QuoteItemData $quoteItemData): QuoteItem
    {
        $quoteDataArray = $quoteItemData->toArray();
        $quote = $this->model->find($quoteDataArray['quote_id']);
        $quote = $quote->update($quoteDataArray);

        return $quote;
    }

    public function showQuoteItem(int $quoteId): QuoteItem
    {
        $quote = $this->model->where('quote_id', '=', $quoteId)
        ->first();

        return $quote;
    }

    public function productList(string $search): array
    {
        $result = $this->product
            // ->with([
            //     'prices' => function($query){
            //         return $query->select(['price']);
            //     }
            // ])
            ->join('product_content', 'product_content.product_id', '=', 'product.product_id')
            ->where('product_content.name', 'LIKE', '%' . $search . '%')
            ->select(['product.product_id', 'product.description', 'product.price', 'product_content.name'])
            ->orderBy('product.product_id', 'DESC')
            ->limit(50)
            ->findAll(false);
            
        return $result;
    }

    private function generateUuid(): string
    {
        $uuid = \uniqid('', true);
        $uuid = str_replace('.', '', $uuid);
        return sprintf('%s-%s-%s-%s-%s',
            substr($uuid, 0, 8),
            substr($uuid, 8, 4),
            substr($uuid, 12, 4),
            substr($uuid, 16, 4),
            substr($uuid, 20)
        );
    }
} 