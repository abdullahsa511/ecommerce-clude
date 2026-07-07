<?php

declare(strict_types=1);

namespace App\Core\Repositories\Job;

use PDO;
use App\Core\Models\Job\Job;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Models\Job\JobData;
use App\Core\Models\Localisation\Language;
use App\Core\Validation\JobDataValidation;
use League\Csv\Reader;

class JobRepository extends BaseRepository implements JobRepositoryInterface
{
    private Language $language;
    public function __construct(PDO $db, Language $language)
    {
        parent::__construct($db, 'job', Job::class);
        $this->language = $language;
        $this->language->setDb($db);
    }

    /**
     * Get all jobs
     *
     * @return array
     */
    public function all(): array
    {
        return $this->model->orderBy('created_at', 'DESC')->whereNull('deleted_at')->findAll();
    }

    public function getJobDetails(int $id): Job
    {
        return $this->model->with(['orders' => function($query){
            return $query->select(['order_id', 'reference_number', 'order_description', 'created_at', 'updated_at', 'order_status_id', 'total']);
        }, 'quotes' => function($query){
            return $query->select(['quote_id', 'reference_number', 'quote_description', 'created_at', 'updated_at', 'quote_status_id', 'total']);
        }])->find($id);
    }


    /**
     * Create a new job
     *
     * @param JobData $jobData
     * @return Job
     */
    public function createJob(JobData $jobData): Job
    {
        $jobDataArray = $jobData->toArray();
        $job = $this->model->create($jobDataArray);
        
        return $job;
    }

    /**
     * Update an existing job
     *
     * @param JobData $jobData
     * @return Job
     */
    public function updateJob(JobData $jobData): Job
    {
        $jobDataArray = $jobData->toArray();
        $job = $this->model->find($jobDataArray['job_id']);
        $job = $job->update($jobDataArray);

        return $job;
    }

    public function deleteJob(int $job_id): bool
    {
        try{
            $this->db->beginTransaction();
            $this->model->clearQuery();
            $job = $this->model->where('job_id', '=', $job_id)->first();
            if (!$job) {
                return false;
            }
            $job->update(['deleted_at' => date('Y-m-d H:i:s')]);
            $this->db->commit();
            return true;
        }catch(\Exception $e){
            $this->db->rollBack();
            throw new \Exception("Failed to delete job: " . $e->getMessage());
        }
    }

    public function importJobs(string $csv_file): array
    {
        $reader = Reader::createFromPath($csv_file, 'r');
        $reader->setHeaderOffset(0);
        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }
        $defaultFields = $this->getDefaultFields($headers);
        $records = $reader->getRecords();
        $requiredFields = ['job_title', 'type', 'company', 'reference'];
        // default fields
        $valid = [];
        $invalid = [];
        $updated = [];
        $processed = [];
        // mapping existing data
        $existingJobMap = $this->model->select(['job_id', 'job_title'])->findAll(false);
        $existingJobMap = array_column($existingJobMap, 'job_id','job_title');
        $languageMap = $this->language->select(['language_id', 'code'])->findAll(false);
        $languageMap = array_column($languageMap, 'language_id', 'code');
        // existing data maps
        $existingDataMaps = [
            'jobIds' => $existingJobMap,
            'languageMap' => $languageMap,
        ];

        foreach ($records as $offset => $record) {
            try {
                // prepare record
                $record = $this->prepareRecord($record, $defaultFields);
                // validate record
                $validator = new JobDataValidation($record, $requiredFields, array_keys($defaultFields), $existingDataMaps);
                $validated = $validator->validate();

                // if validation fails, store record and error info in $invalid
                if ($validated === false) {
                    $invalid[] = [
                        'row' => $offset + 1, // +2 because CSV row count starts at 1 and includes header
                        'data' => $record,
                        'errors' => $validator->getErrors()
                    ];
                    continue;
                }

                $unique = $validator->getUniqueIdentifier();
                // if job has already been processed
                if (in_array($unique, $processed, true)) {
                    $updated[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'identifier' => $unique
                    ];
                    continue;
                }
                if($validated->isExistingData){
                    $updated[] = [
                        'row' => $offset + 1,
                        'data' => $record,
                        'identifier' => $unique
                    ];
                    continue;
                }else{
                    $valid[] = (array) $validated->job;
                }
                $processed[] = $unique;
            } catch (Exception $e) {
                // Capture any runtime exception per record
                $invalid[] = [
                    'row' => $offset + 2,
                    'data' => $record,
                    'errors' => ['processing_error' => $e->getMessage()]
                ];
                continue;
            }
        }

        // $result = $this->userGroupAndContentInsertorUpdate($valid, $languageMap);
        try{
            $this->db->beginTransaction();
            if(count($valid) > 0){
                $this->model->insert($valid);
            }
            $this->db->commit();
        }catch(\Exception $e){
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update jobs: " . $e->getMessage());
        }

        return [
            'success' => true,
            'total_records' => iterator_count($records),
            'valid_records' => count($valid),
            'valid_data' => $valid,
            'inserted_count' => count($valid),
            'inserted_data' => $valid,
            'invalid_records' => count($invalid),
            'updated_records' => count($updated),
            'updated_data' => $updated,
            'language_map' => array_flip($languageMap),
            'jobs' => [
                'inserted_count' => count($valid),
                'valid_data' => $valid
            ],
            'invalid_data' => $invalid,
            'summary' => [
                'success_rate' => iterator_count($records) > 0
                    ? round((count($valid) / iterator_count($records)) * 100, 2) . '%'
                    : '0%',
                'job_processed' => count($valid),
                'job_records_created' => $valid,
                'errors' => count($invalid),
            ],
            'language_map' => array_flip($languageMap)
        ];
    }

    private function getDefaultFields(array $headers): array
    {
        $defaultFields = [];
        // Initialize all CSV headers as null by default
        foreach ($headers as $header) {
            $defaultFields[$header] = null;
        }

        // Set default values for required fields
        $defaultFields['language_id'] = 1;
        $defaultFields['status'] = 'active';
        $defaultFields['value'] = 0;
        $defaultFields['company'] = 'SA Technology';
        $defaultFields['account_manager_id'] = 1;
        $defaultFields['account_manager_name'] = 'Moahammad Ali Abdullah';
        $defaultFields['created_at'] = date('Y-m-d H:i:s');
        $defaultFields['updated_at'] = date('Y-m-d H:i:s');

        return $defaultFields;
    }

    private function prepareRecord(array $record, array $defaultFields): array
    {
        return isset($record['job_id']) && $record['job_id'] ? $record : array_merge($defaultFields, $record);
    }
} 