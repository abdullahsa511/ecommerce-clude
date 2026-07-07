<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Http\ApiController;
use App\Core\Repositories\Product\ManufacturerRepositoryInterface;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Exceptions\ValidationException;
use App\Core\Repositories\Media\MediaRepositoryInterface;

class ManufacturerController extends ApiController
{
    private ManufacturerRepositoryInterface $manufacturerRepository;
    private MediaRepositoryInterface $mediaRepository;
    public function __construct(
        ManufacturerRepositoryInterface $manufacturerRepository,
        MediaRepositoryInterface $mediaRepository,
    )
    {
        parent::__construct();
        $this->manufacturerRepository = $manufacturerRepository;
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * Get all manufacturers.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $manufacturers = $this->manufacturerRepository->getAllManufacturers();
        return $this->renderResponse($manufacturers);
    }

    /**
     * Get a manufacturer by ID.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function show(Request $request, $id): Response
    {
        $manufacturer = $this->manufacturerRepository->getManufacturerById((int)$id);
        if(!$manufacturer){
            return $this->renderError(404, 'Manufacturer not found');
        }
        return $this->renderResponse($manufacturer);
    }

    /**
     * Create a new manufacturer.
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        try {
            $validatedData = $request->validate([
                'manufacturer_code' => 'required',
                'name' => 'required',
                'sort_order' => 'nullable|int',
            ]);

            $manufacturer = $this->manufacturerRepository->createManufacturer($validatedData);
            return $this->renderResponse($manufacturer);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    /**
     * Update a manufacturer.
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
                    'manufacturer_code' => 'required',
                    'name' => 'required',
                    'sort_order' => 'nullable|int',
            ]);

        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        }

        $existingManufacturer = $this->manufacturerRepository->find((int)$id);
        if (!$existingManufacturer) {
            return $this->renderError(404, 'Manufacturer not found');
        }

        $manufacturer = $this->manufacturerRepository->updateManufacturer((int) $id, $validatedData);
        if (!$manufacturer) {
            return $this->renderError(500, 'Failed to update manufacturer');
        }

        return $this->renderResponse($manufacturer);
    }

    /**
     * Delete a manufacturer.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function delete(Request $request, $id): Response
    {
        try {
            $variant = $this->manufacturerRepository->deleteManufacturer((int) $id);
            return $this->renderResponse($variant);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    public function importManufacturers(Request $request): Response
    {
        $csv_file = $request->file('csv_file');
        
        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';
        
        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->manufacturerRepository->importManufacturers($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }
    
    public function upload(Request $request, int $manufacturer_id): Response
    {
        // Set default size
        $size = [
            'width' => 400,
            'height' => 420,
        ];

        $folder = 'media/manufacturers/';
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

            $this->manufacturerRepository->updateManufacturerImage($result['files'], $manufacturer_id);
            return $this->renderResponse($result);
        }

        return $this->renderError(422, 'No files uploaded');
    }

    // delete manufacturer image
    public function deleteImage(Request $request, int $manufacturer_id): Response
    {
        $deleted = $this->manufacturerRepository->deleteManufacturerImage($manufacturer_id);
        return $this->renderResponse(['deleted' => $deleted]);
    }
} 