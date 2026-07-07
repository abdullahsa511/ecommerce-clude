<?php

declare(strict_types=1);

namespace App\Core\Repositories\Product;

use App\Core\Repositories\Base\BaseRepositoryInterface;

interface ProductMetaRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get a single product meta value
     *
     * @param int $productId Product ID
     * @param string $namespace Namespace
     * @param string $key Key
     * @return string|null
     */
    public function get(int $productId, string $namespace, string $key): ?string;

    /**
     * Set a single product meta value
     *
     * @param int $productId Product ID
     * @param string $namespace Namespace
     * @param string $key Key
     * @param string $value Value
     * @return bool
     */
    public function set(int $productId, string $namespace, string $key, string $value): bool;

    /**
     * Get multiple product meta values
     *
     * @param int $productId Product ID
     * @param string $namespace Namespace
     * @param array $keys Array of keys
     * @return array
     */
    public function getMulti(int $productId, string $namespace, array $keys): array;

    /**
     * Set multiple product meta values
     *
     * @param int $productId Product ID
     * @param string $namespace Namespace
     * @param array $meta Array of meta data [key => value, ...]
     * @return bool
     */
    public function setMulti(int $productId, string $namespace, array $meta): bool;


    /**
     * Import product meta from CSV
     *
     * @param string $csv_file
     * @return array
     */
    public function importProductMeta(string $csv_file): array;

} 