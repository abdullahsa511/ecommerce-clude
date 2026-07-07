<?php

declare(strict_types=1);

namespace App\Core\Models\Product;

use App\Core\Models\Base\Model;

class ProductInstagram extends Model
{
    protected string $table = 'product_instagram';

    protected array $fillable = [
        'product_id',
        'product_url',
        'instagram_url',
        'instagram_media_id',
        'thumbnail_url',
        'caption',
        'shortcode',
        'hashtag',
        'media_type',
        'sort_order',
    ];

    public int $product_instagram_id;
    public int $product_id;
    public string $product_url;
    public string $instagram_url;
    public ?string $instagram_media_id;
    public ?string $thumbnail_url;
    public ?string $caption;
    public ?string $shortcode;
    public ?string $hashtag;
    public ?string $media_type;
    public int $sort_order;
    public ?string $created_at;
    public ?string $updated_at;

    public function __construct()
    {
        parent::__construct();
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }
}

class ProductInstagramData
{
    public ?int $product_instagram_id;
    public int $product_id;
    public string $product_url;
    public string $instagram_url;
    public ?string $instagram_media_id;
    public ?string $thumbnail_url;
    public ?string $caption;
    public ?string $shortcode;
    public ?string $hashtag;
    public ?string $media_type;
    public int $sort_order;

    public function __construct(array $data = [])
    {
        $this->product_instagram_id = self::toNullableInt($data['product_instagram_id'] ?? null);
        $this->product_id = (int) ($data['product_id'] ?? 0);
        $this->product_url = (string) ($data['product_url'] ?? '');
        $this->instagram_url = (string) ($data['instagram_url'] ?? '');
        $this->instagram_media_id = self::toNullableString($data['instagram_media_id'] ?? null);
        $this->thumbnail_url = self::toNullableString($data['thumbnail_url'] ?? null);
        $this->caption = self::toNullableString($data['caption'] ?? null);
        $this->shortcode = self::toNullableString($data['shortcode'] ?? null);
        $this->hashtag = self::toNullableString($data['hashtag'] ?? null);
        $this->media_type = self::toNullableString($data['media_type'] ?? null);
        $this->sort_order = (int) ($data['sort_order'] ?? 0);
    }

    public function toArray(): array
    {
        $row = [
            'product_id' => $this->product_id,
            'product_url' => $this->product_url,
            'instagram_url' => $this->instagram_url,
            'instagram_media_id' => $this->instagram_media_id,
            'thumbnail_url' => $this->thumbnail_url,
            'caption' => $this->caption,
            'shortcode' => $this->shortcode,
            'hashtag' => $this->hashtag,
            'media_type' => $this->media_type,
            'sort_order' => $this->sort_order,
        ];

        if ($this->product_instagram_id !== null) {
            $row['product_instagram_id'] = $this->product_instagram_id;
        }

        return $row;
    }

    private static function toNullableInt(mixed $value): ?int
    {
        return is_numeric($value) ? (int) $value : null;
    }

    private static function toNullableString(mixed $value): ?string
    {
        if ($value === null || is_array($value) || is_object($value)) {
            return null;
        }

        $text = trim((string) $value);
        return $text === '' ? null : $text;
    }
}
