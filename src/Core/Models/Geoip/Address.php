<?php

declare(strict_types=1);

namespace App\Core\Models\Geoip;

class Address
{
    public string $description; 

    public function __construct(array $data)
    {
        if(isset($data['description'])) $this->description = $data['description'];
    }
}
