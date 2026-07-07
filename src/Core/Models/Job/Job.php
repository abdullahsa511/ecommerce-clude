<?php

declare(strict_types=1);

namespace App\Core\Models\Job;

use App\Core\Models\Base\Model;
use App\Core\Models\Order\Order;
use App\Core\Models\Quote\Quote;
use App\Core\Models\User;
use stdClass;

class Job extends Model
{
    protected string $table = 'job';
    // protected string $tableAlias = 'j';

    protected ?int $job_id;
    protected ?int $language_id;
    protected ?string $type;
    protected ?string $reference;
    protected ?string $job_title;
    protected ?string $description;
    protected ?string $company;
    protected ?int $account_manager_id;
    protected ?string $account_manager_name;
    protected ?string $status;
    protected ?float $value;
    protected ?string $created_at;
    protected ?string $updated_at;

    public function __construct() 
    {
        parent::__construct();
    }

    /**
     * Get account manager relationship
     */
    public function accountManager()
    {
        return $this->belongsTo(User::class, 'account_manager_id', 'user_id');
    }

    public function orders(){
        return $this->hasMany(Order::class, 'job_id', 'job_id');
    }

    public function quotes(){
        return $this->hasMany(Quote::class, 'job_id', 'job_id');
    }
}

class JobResponse
{
    public ?int $job_id;
    public JobDetailsResponse $jobDetails;
    public CompanyDetailsResponse $companyDetails;

    public function __construct(stdClass $data) 
    {
        $this->job_id = $data->job_id ?? null;
        $this->jobDetails = new JobDetailsResponse($data);
        $this->companyDetails = new CompanyDetailsResponse($data);
    }
}

class JobDetailsResponse
{
    public string $type;
    public string $reference;
    public string $jobTitle;
    public string $description;
    public string $accountManagerName;
    public string $status;
    public string $value;
    public string $createdAt;
    public string $updatedAt;

    public function __construct(stdClass $data)
    {
        $this->type = $data->type ?? '';
        $this->reference = $data->reference ?? '';
        $this->jobTitle = $data->job_title ?? '';
        $this->description = $data->description ?? '';
        $this->accountManagerName = $data->account_manager_name ?? '';
        $this->status = $data->status ?? '';
        $this->value = number_format((float)($data->value ?? 0), 2, '.', '');
        $this->createdAt = $data->created_at ?? '';
        $this->updatedAt = $data->updated_at ?? '';
    }
}

class CompanyDetailsResponse
{
    public string $company;
    public string $accountManagerId;

    public function __construct(stdClass $data)
    {
        $this->company = $data->company ?? '';
        $this->accountManagerId = (string)($data->account_manager_id ?? '');
    }
}

class JobData
{
    public ?int $job_id;
    public ?int $language_id;
    public ?string $type;
    public ?string $reference;
    public ?string $job_title;
    public ?string $description;
    public ?string $company;
    public ?int $account_manager_id;
    public ?string $account_manager_name;
    public ?string $status;
    public ?string $value;

    public function __construct(array $data = [])
    {
        $this->job_id = $data['job_id'] ?? null;
        $this->language_id = $data['language_id'] ?? null;
        $this->type = $data['type'] ?? null;
        $this->reference = $data['reference'] ?? null;
        $this->job_title = $data['job_title'] ?? null;
        $this->description = $data['description'] ?? null;
        $this->company = $data['company'] ?? null;
        $this->account_manager_id = $data['account_manager_id'] ?? null;
        $this->account_manager_name = $data['account_manager_name'] ?? null;
        $this->status = $data['status'] ?? 'active';
        $this->value = $data['value'] ?? '0.00';
    }

    public function toArray(): array
    {
        return [
            'job_id' => $this->job_id,
            'language_id' => $this->language_id,
            'type' => $this->type,
            'reference' => $this->reference,
            'job_title' => $this->job_title,
            'description' => $this->description,
            'company' => $this->company,
            'account_manager_id' => $this->account_manager_id,
            'account_manager_name' => $this->account_manager_name,
            'status' => $this->status,
            'value' => $this->value,
        ];
    }
} 