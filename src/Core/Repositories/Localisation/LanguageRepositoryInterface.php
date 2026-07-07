<?php

declare(strict_types=1);

namespace App\Core\Repositories\Localisation;

use App\Core\Repositories\Base\BaseRepositoryInterface;
use App\Core\Models\Localisation\Language;

interface LanguageRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all languages with optional filters
     * 
     * @param int|null $status Language status
     * @param int|null $start Pagination start
     * @param int|null $limit Pagination limit
     * @return array{items: array, total: int}
     */
    public function getAll(
        ?int $status = null,
        ?string $search = null,
        int $start = 0,
        int $limit = 10
    ): array;

    /**
     * Get a specific language by ID or code
     * 
     * @param int|null $languageId Language ID
     * @param string|null $code Language code
     * @return Language|null
     */
    public function get(?int $languageId = null, ?string $code = null): ?Language;

} 