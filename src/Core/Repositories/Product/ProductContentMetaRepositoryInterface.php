<?php

declare(strict_types=1);

namespace App\Core\Repositories\Product;

use App\Core\Repositories\Base\BaseRepositoryInterface;

interface ProductContentMetaRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get a single product content meta value
     *
     * @param int $productId Product ID
     * @param string $namespace Namespace
     * @param string $key Key
     * @param int|null $languageId Language ID
     * @return string|null
     */
    public function get(int $productId, string $namespace, string $key, ?int $languageId = null): ?string;

    /**
     * Set a single product content meta value
     *
     * @param int $productId Product ID
     * @param string $namespace Namespace
     * @param string $key Key
     * @param string $value Value
     * @param int|null $languageId Language ID
     * @return bool
     */
    public function set(int $productId, string $namespace, string $key, string $value, ?int $languageId = null): bool;

    /**
     * Get multiple product content meta values
     *
     * @param int $productId Product ID
     * @param string $namespace Namespace
     * @param array $keys Array of keys
     * @param int|null $languageId Language ID
     * @return array
     */
    public function getMulti(int $productId, string $namespace, array $keys, ?int $languageId = null): array;

    /**
     * Set multiple product content meta values
     *
     * @param int $productId Product ID
     * @param array $meta Array of meta data [['namespace' => string, 'key' => string, 'value' => string, 'language_id' => int], ...]
     * @return bool
     */
    public function setMulti(int $productId, array $meta): bool;

} 