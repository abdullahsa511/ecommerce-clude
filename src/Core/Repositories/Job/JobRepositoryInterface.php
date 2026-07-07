<?php

declare(strict_types=1);

namespace App\Core\Repositories\Job;

use App\Core\Models\Job\Job;
use App\Core\Models\Job\JobData;
use App\Core\Repositories\Base\BaseRepositoryInterface;

interface JobRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get all jobs
     *
     * @return array
     */
    public function all(): array;

    /**
     * Get job details
     *
     * @param int $id
     * @return Job
     */
    public function getJobDetails(int $id): Job;

    /**
     * Create a new job
     *
     * @param JobData $jobData
     * @return Job
     */
    public function createJob(JobData $jobData): Job;

    /**
     * Update an existing job
     *
     * @param JobData $jobData
     * @return Job
     */
    public function updateJob(JobData $jobData): Job;
    /**
     * Delete a job
     *
     * @param int $job_id
     * @return bool
     */
    public function deleteJob(int $job_id): bool;
    /**
     * Import jobs from CSV file
     *
     * @param string $csv_file
     * @return array
     */
    public function importJobs(string $csv_file): array;

} 