<?php

declare(strict_types=1);

namespace App\Core\Repositories\Pinboard;

use App\Core\Models\Pinboard\PinboardTemp;

interface PinboardTempRepositoryInterface
{
    public function savePinboard(array $data): array;
    public function showPinboard(int $pinboardId): PinboardTemp;
    public function allTemporaryPinboards(): array;
    public function showTemporaryPinboard(int $pinboardId): PinboardTemp;
} 