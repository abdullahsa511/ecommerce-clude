<?php

declare(strict_types=1);

namespace App\Core\Models\Company;

use App\Core\Models\Base\Model;

class Company extends Model
{
    protected string $table = 'company';
    protected string $primaryKey = 'company_id';

    public int $company_id;
    public string $company_name;
    public ?string $company_entity;
    public ?string $company_short;
    public int $sort_order;
    public string $company_code;
    public string $company_prefix;
    public ?string $company_trade_name;
    public ?string $company_entity_name;
    public ?string $phone_main;
    public ?string $krost_org_id;
    public ?string $krost_qld_org_id;
    public ?string $klein_org_id;
    public ?string $meloz_org_id;
    public ?string $gregbar_org_id;
    public string $vendor_id;
    public ?string $ship_building;
    public ?string $ship_street;
    public ?string $ship_suburb;
    public ?string $ship_state;
    public ?string $ship_postcode;
    public ?string $ship_country;
    public ?string $bill_building;
    public ?string $bill_street;
    public ?string $bill_suburb;
    public ?string $bill_state;
    public ?string $bill_postcode;
    public ?string $bill_country;
    public ?string $po_box;
    public ?string $po_box_suburb;
    public ?string $po_box_state;
    public ?string $abn;
    public ?string $bsb;
    public ?string $account_number;
    public ?int $bpay_biller_code;
    public ?string $deleted_at;
    public ?string $created_at;
    public ?string $updated_at;
    
    public function __construct() 
    {
        parent::__construct();
    }
} 