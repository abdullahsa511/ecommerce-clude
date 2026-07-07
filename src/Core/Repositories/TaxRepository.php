<?php

declare(strict_types=1);

namespace App\Core\Repositories;

use App\Core\Models\Setting;
use App\Core\Models\TaxType;
use App\Core\Models\TaxRate;
use App\Core\Models\TaxRule;
use App\Core\Repositories\Base\BaseRepository;
use PDO;

class TaxRepository extends BaseRepository implements TaxRepositoryInterface
{
    protected TaxType $taxType;
    protected TaxRate $taxRate;
    protected TaxRule $taxRule;

    public function __construct(PDO $pdo, TaxType $taxType, TaxRate $taxRate, TaxRule $taxRule)
    {
        parent::__construct($pdo, 'tax_type', TaxType::class);
        $this->taxType = $taxType;  
        $this->taxType->setDb($pdo);
        $this->taxRate = $taxRate;
        $this->taxRate->setDb($pdo);
        $this->taxRule = $taxRule;
        $this->taxRule->setDb($pdo);
    }
} 