<?php

declare(strict_types=1);

namespace App\Core\Repositories\Service;

use App\Core\Repositories\Base\BaseRepositoryInterface;
use App\Core\Http\Request;

interface ServiceRequestRepositoryInterface extends BaseRepositoryInterface
{
    public function createRequest(array $data, array $files): array;
    public function accountCreateRequest(array $data, array $files): array;
    public function requestCatalogue(array $data, array $files, string $folder): array;

    public function getServiceRequestByUuid(string $uuid):array;
    public function downloadRequestImages(string $uuid, string $link): string;
    public function getServiceRequests($filters = []): array;
    public function getStateFromAddress(string $address_string): ?array;
} 