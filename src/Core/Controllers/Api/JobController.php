<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\Job\JobRepositoryInterface;
use App\Core\Models\Job\JobData;
use App\Core\Models\Job\JobResponse;

class JobController extends ApiController
{
    private JobRepositoryInterface $jobRepository;

    public function __construct(JobRepositoryInterface $jobRepository)
    {
        parent::__construct();
        $this->jobRepository = $jobRepository;
    }

    /**
     * Get all jobs
     *
     * @param Request $request
     * @return Response
     */
    public function jobDetails(Request $request, int $id): Response
    {
        $jobs = $this->jobRepository->getJobDetails($id);
        if ($jobs->data) {
            // Decode orders and quotes JSON strings
            if (isset($jobs->data->orders) && is_string($jobs->data->orders)) {
                $jobs->data->orders = json_decode($jobs->data->orders, true);
            }
            
            if (isset($jobs->data->quotes) && is_string($jobs->data->quotes)) {
                $jobs->data->quotes = json_decode($jobs->data->quotes, true);
            }
        }
        return $this->renderResponse($jobs->data);
    }

    public function jobList(Request $request): Response
    {
        $jobs = $this->jobRepository->all();
        return $this->renderResponse($jobs);
    }

    /**
     * Create a new job
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        try {
            $job = $request->all();
            // validate job data
            $request->validate([
                'job_title' => 'required|string',
                'type' => 'required|string',
                'company' => 'required|string',
                'reference' => 'required|string',
            ], $job);
            
            $jobData = new JobData($job);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $job = $this->jobRepository->createJob($jobData);
        if(!$job){
            return $this->renderError(500, 'Failed to create job');
        }
        $job = new JobResponse($job->data);
        return $this->renderResponse($job);
    }

    /**
     * Update a job
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id): Response
    {
        try {
            $jobRequest = $request->all();
            // validate job data
            $request->validate([
                'job_title' => 'required|string',
                'type' => 'required|string',
                'company' => 'required|string',
                'reference' => 'required|string',
            ], $jobRequest);
            $jobData = new JobData($jobRequest);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $job = $this->jobRepository->updateJob($jobData);
        if(!$job){
            return $this->renderError(500, 'Failed to update job');
        }
        return $this->renderResponse($jobRequest);
    }

    public function delete(Request $request, $id): Response
    {
        try {
            $attribute = $this->jobRepository->deleteJob((int) $id);
            return $this->renderResponse($attribute);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }
    public function importJobs(Request $request): Response
    {
        $csv_file = $request->file('csv_file');
        
        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';
        
        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->jobRepository->importJobs($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }

} 