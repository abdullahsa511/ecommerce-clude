<?php

declare(strict_types=1);

namespace App\Core\Validation;
use stdClass;

class JobDataValidation extends Validation
{
    public stdClass $job;

    public function __construct(array $data, array $requiredFields = [], array $textFields = [], array $existingData = ['jobIds' => [], 'languageMap' => []])
    {
        $this->rawData = $data;
        parent::__construct($requiredFields, $textFields);
        $this->job = new stdClass();

        if (isset($data['job_id'])) {
            $this->job->job_id = $this->validateInteger($data['job_id'], 'job_id', 0, true);
            if (isset($existingData['jobIds'][$data['job_title']])) {
                $this->isExistingData = true;
            }
        }
        // JOB TABLE
        // mandatory fields
        $this->job->job_title = $this->validateString($data['job_title'], 'job_title', 255, true);
        $this->job->type = $this->validateString($data['type'], 'type', 100, true);
        $this->job->company = $this->validateString($data['company'], 'company', 255, true);
        $this->job->reference = $this->validateString($data['reference'], 'reference', 100, true);
        // optional fields
        $this->job->description = $this->validateString($data['description'], 'description', 191);
        $this->job->account_manager_id = $this->validateInteger($data['account_manager_id'] ?? null, 'account_manager_id', 0);
        $this->job->account_manager_name = $this->validateString($data['account_manager_name'] ?? null, 'account_manager_name', 255);
        $this->job->status = $this->validateString($data['status'] ?? 'active', 'status', 50);
        $this->job->value = $this->validateInteger($data['value'] ?? 0, 'value', 0, true);
        $language_code = isset($data['language_code']) ? $this->validateString($data['language_code'], 'language_code', 50) : 'en_US';
        $this->job->language_id = isset($existingData['languageMap'][$language_code]) ? $existingData['languageMap'][$language_code] : 1;

        // NOT CHECK FOR DUPLICATE JOB TITLE // IF NEEDED, UNCOMMENT THE FOLLOWING CODE
        // if (exists($existingData['jobIds'][$this->job->job_title])) {
        //     $this->job->job_id = $existingData['jobIds'][$this->job->job_title];
        //     $this->isExistingData = true;
        // }
        // END OF NOT CHECK FOR DUPLICATE JOB TITLE
    }

    public function toArray(): array
    {
        return [
            'job' => (array)$this->job,
        ];
    }
}
