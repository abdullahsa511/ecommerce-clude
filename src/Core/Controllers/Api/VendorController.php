<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Http\ApiController;
use App\Core\Repositories\Product\VendorRepositoryInterface;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Exceptions\ValidationException;
use App\Core\Repositories\Media\MediaRepositoryInterface;

class VendorController extends ApiController
{
    private VendorRepositoryInterface $vendorRepository;
    private MediaRepositoryInterface $mediaRepository;
    public function __construct(
        VendorRepositoryInterface $vendorRepository,
        MediaRepositoryInterface $mediaRepository,
    )
    {
        parent::__construct();
        $this->vendorRepository = $vendorRepository;
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * Get all vendors.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $vendors = $this->vendorRepository->getAllVendors();
        return $this->renderResponse($vendors);
    }

    /**
     * Get a vendor by ID.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function show(Request $request, $id): Response
    {
        $vendor = $this->vendorRepository->getVendorById((int)$id);
        if(!$vendor){
            return $this->renderError(404, 'Vendor not found');
        }
        return $this->renderResponse($vendor);
    }

    public function searchVendors(Request $request): Response
    {
        $query = $request->query('name');
        $vendors = $this->vendorRepository->searchVendors($query);
        return $this->renderResponse($vendors);
    }

    /**
     * Create a new vendor.
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        try {
            $validatedData = $request->validate([
                'vendor_code' => 'required',
                'name' => 'required',
                'sort_order' => 'nullable|int',
            ]);

            $vendor = $this->vendorRepository->createVendor($validatedData);
            return $this->renderResponse($vendor);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    /**
     * Update a vendor.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id): Response
    {
        $data = $request->all();
        if($data instanceof Response){
            return $data;
        }
        try {
           $validatedData = $request->validate([
                    'vendor_code' => 'required',
                    'name' => 'required',
                    'sort_order' => 'nullable|int',
            ]);

        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        }

        $existingVendor = $this->vendorRepository->find((int)$id);
        if (!$existingVendor) {
            return $this->renderError(404, 'Vendor not found');
        }

        $vendor = $this->vendorRepository->updateVendor((int) $id, $validatedData);
        if (!$vendor) {
            return $this->renderError(500, 'Failed to update vendor');
        }

        return $this->renderResponse($vendor);
    }

    /**
     * Delete a vendor.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function delete(Request $request, $id): Response
    {
        try {
            $variant = $this->vendorRepository->deleteVendor((int) $id);
            return $this->renderResponse($variant);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    public function importVendors(Request $request): Response
    {
        $csv_file = $request->file('csv_file');
        
        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';
        
        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->vendorRepository->importVendors($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }
    
    public function upload(Request $request, int $vendor_id): Response
    {
        // Set default size
        $size = [
            'width' => 400,
            'height' => 420,
        ];

        $folder = 'media/vendors/';
        if ($request->files() || isset($_FILES['files'])) {
            $files = $request->files() ?? $_FILES['files'];

            if (!count($files)) {
                return $this->renderError(422, 'No files uploaded');
            }
            $data = [
                'files' => $files,
                'upload_dir' => $request->input('upload_dir', $folder)
            ];

            $result = $this->mediaRepository->upload($data, $size, $folder);
            if (!$result) {
                return $this->renderError(500, 'Failed to upload media');
            }

            $this->vendorRepository->updateVendorImage($result['files'], $vendor_id);
            return $this->renderResponse($result);
        }

        return $this->renderError(422, 'No files uploaded');
    }

    // delete vendor image
    public function deleteImage(Request $request, int $vendor_id): Response
    {
        $deleted = $this->vendorRepository->deleteVendorImage($vendor_id);
        return $this->renderResponse(['deleted' => $deleted]);
    }
} 