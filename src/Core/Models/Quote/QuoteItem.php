<?php

declare(strict_types=1);

namespace App\Core\Models\Quote;

use App\Core\Models\Base\Model;

class QuoteItem extends Model
{
    protected string $table = 'quote_item';
    // protected string $tableAlias = 'qi';

    protected int $quote_item_id;
    protected int $language_id;
    protected string $uuid;
    protected int $quote_id;

    protected ?int $product_id;
    
    protected string $description;
    protected int $quantity;
    protected float $unit_price;
    protected float $total_price;
    
    protected ?string $photo;
    
    protected int $sort_order;

    protected string $created_at;
    protected string $updated_at;

    public function __construct() 
    {
        parent::__construct();
    }

    public function quote()
    {
        return $this->belongsTo(Quote::class, 'quote_id', 'quote_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

} 

class QuoteItemData
{
    public ?int $quote_item_id;
    public ?int $language_id;
    public ?string $uuid;
    public ?int $quote_id;
    public ?int $product_id;
    public ?string $description;
    public ?int $quantity;
    public ?float $unit_price;
    public ?float $total_price;
    public ?string $photo;
    public ?int $sort_order;

    public function __construct(array $data = [])
    {
        $this->quote_item_id = $data['quote_item_id'] ?? null;
        $this->language_id = $data['language_id'] ?? 1;
        $this->uuid = $data['uuid'] ?? null;
        $this->quote_id = $data['quote_id'] ?? null;
        $this->product_id = $data['product_id'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->quantity = $data['quantity'] ?? null;
        $this->unit_price = $data['unit_price'] ?? null;
        $this->total_price = $data['total_price'] ?? null;
        $this->photo = $data['photo'] ?? 'default-photo.jpg';
        $this->sort_order = $data['sort_order'] ?? 0;
    }

    public function toArray(): array
    {
        return [
            'quote_item_id' => $this->quote_item_id,
            'language_id' => $this->language_id,
            'uuid' => $this->uuid,
            'quote_id' => $this->quote_id,
            'product_id' => $this->product_id,
            'description' => $this->description,
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'total_price' => $this->total_price,
            'photo' => $this->photo,
            'sort_order' => $this->sort_order
        ];
    }
}