<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\Company\CompanyRepositoryInterface;
use App\Core\Utilities\Debug;
use Exception;

/**
 * All company setup related data control from here.
 */
class CompanyController extends ApiController
{
    private CompanyRepositoryInterface $companyRepository;
    
    public function __construct(
        CompanyRepositoryInterface $companyRepository
    ) {
        parent::__construct();
        $this->companyRepository = $companyRepository;
    }

    /**
     * Fetch all companies
     */
    public function getAllCompanies(Request $request): Response
    {
        $data = $this->companyRepository->getAllCompanies();
        return $this->renderResponse($data);
    }

    /**
     * Get company by ID
     */
    public function getCompanyById(Request $request, $id): Response
    {
        $data = $this->companyRepository->getCompanyById((int) $id);
        return $this->renderResponse($data);
    }

    /**
     * Search vendors
     */
    // public function searchVendors(Request $request): Response
    // {
    //     $query = $request->query('name');
    //     $data = $this->companyRepository->searchVendors($query);
    //     return $this->renderResponse($data);
    // }

    /**
     * Create a new company
     */
    public function create(Request $request): Response
    {
        $data = $request->all();
        try {
            if ($data instanceof Response) {
                return $data;
            }
            $company = $this->companyRepository->createCompany($data);
            return $this->renderResponse($company);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    /**
     * Update an existing company
     */
    public function update(Request $request, $id): Response
    {
        $data = $request->all();
        try {
            if ($data instanceof Response) {
                return $data;
            }
            $company = $this->companyRepository->updateCompany($data, (int) $id);
            return $this->renderResponse($company);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    /**
     * Delete a company
     */
    public function delete(Request $request, $id): Response
    {
        try {
            $company = $this->companyRepository->deleteCompany((int) $id);
            return $this->renderResponse($company);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    /**
     * Delete multiple companies
     */
    public function deleteMultiple(Request $request): Response
    {
        $data = $request->all();
        $company_ids = $data['company_ids'] ?? [];
        
        if (empty($company_ids) || !is_array($company_ids)) {
            return $this->renderError(400, 'company_ids array is required');
        }
        
        try {
            $result = $this->companyRepository->deleteMultipleCompanies($company_ids);
            return $this->renderResponse($result);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    /**
     * Import companies from CSV file
     */
    public function importCompanies(Request $request): Response
    {
        $csv_file = $request->file('csv_file');
        
        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';
        
        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->companyRepository->importCompanies($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Get all companies with pagination
     */
    public function getAll(Request $request): Response
    {
        $start = (int) ($request->get('start') ?? 0);
        $limit = (int) ($request->get('limit') ?? 10);
        $search = $request->get('search');
        
        try {
            $result = $this->companyRepository->getAll($start, $limit, $search);
            return $this->renderResponse($result);
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    /**
     * Get a single company by ID
     */
    public function get(Request $request, $id): Response
    {
        try {
            $company = $this->companyRepository->get((int) $id);
            if (!$company) {
                return $this->renderError(404, 'Company not found');
            }
            return $this->renderResponse($company);
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }
}

