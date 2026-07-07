<?php

declare(strict_types=1);

namespace App\Core\Models\Post;

use App\Core\Models\Base\Model;

class PostMeta extends Model
{
    // Core properties
    public int $product_id;
    public string $namespace;
    public string $key;
    public string $value;

    /**
     * Get the post this meta belongs to
     * @return array{join: string, select: string}
     */
    public function post(): array
    {
        return $this->belongsTo(Post::class, 'product_id');
    }

    /**
     * Get meta value
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Set meta value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    /**
     * Get meta value as boolean
     */
    public function getBoolean(): bool
    {
        return filter_var($this->value, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Get meta value as integer
     */
    public function getInteger(): int
    {
        return (int)$this->value;
    }

    /**
     * Get meta value as array (assumes JSON)
     */
    public function getArray(): array
    {
        if (empty($this->value)) {
            return [];
        }
        $decoded = json_decode($this->value, true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Set meta value as array (converts to JSON)
     */
    public function setArray(array $value): void
    {
        $this->value = json_encode($value);
    }

    /**
     * Get meta value as object (assumes JSON)
     */
    public function getObject(): ?object
    {
        if (empty($this->value)) {
            return null;
        }
        $decoded = json_decode($this->value);
        return is_object($decoded) ? $decoded : null;
    }

    /**
     * Set meta value as object (converts to JSON)
     */
    public function setObject(object $value): void
    {
        $this->value = json_encode($value);
    }

    public function __construct() 
    {
        parent::__construct();
    }
} 