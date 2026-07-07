<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\Variant\ProductVariantRepositoryInterface;
use App\Core\Repositories\Media\MediaRepositoryInterface;
use App\Core\Exceptions\ValidationException;

/**
 * All variant setup related data control from here.
 */
class ProductVariantController extends ApiController
{
    private ProductVariantRepositoryInterface $productVariantRepository;
    private MediaRepositoryInterface $mediaRepository;  
    public function __construct(
        ProductVariantRepositoryInterface $productVariantRepository,
        MediaRepositoryInterface $mediaRepository
    ) {
        parent::__construct();
        $this->productVariantRepository = $productVariantRepository;
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * Fetch all variants
     */
    public function getVariants(Request $request): Response
    {
        $data = $this->productVariantRepository->getVariants();
        return $this->renderResponse($data);
    }

    public function createVariant(Request $request): Response
    {
        try {
            $data = $request->all();
            // Debug::dd($data, true);
            if ($data instanceof Response) {
                return $data;
            }

            $request->validate([
                'product_id' => 'required',
                'variant_name' => 'required|string',
                'sort_order' => 'required|int',
            ], $data);

            // check duplicate groups
            $this->duplicateNameCheck($data['productOptionGroups']);

            $existingName = $this->productVariantRepository->findByName($data);
            if ($existingName) {
                throw new ValidationException([
                    'variant_name' => ['Variant name is already in use.'],
                ]);
            }

            $variant = $this->productVariantRepository->createVariant($data);
            return $this->renderResponse($variant);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    public function updateVariant(Request $request, $id): Response
    {
        $data = $request->all();

        if ($data instanceof Response) {
            return $data;
        }

        try {
            $request->validate([
                'variant_name' => 'required|string',
                'product_id' => 'required|int',
                'sort_order' => 'required|int',
            ], $data);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        }
        // check duplicate groups
        $this->duplicateNameCheck($data['productOptionGroups']);

        $this->productVariantRepository->clearQuery();
        $existingVariant = $this->productVariantRepository->getVariantById($id);
        if (!$existingVariant) {
            return $this->renderError(400, 'Variant with the id ' . $id . ' not found.');
        }
        $existingName = $this->productVariantRepository->findByName($data, (int) $id);
        if ($existingName) {
            throw new ValidationException([
                'variant_name' => ['Variant name is already in use.'],
            ]);
        }
        $variant = $this->productVariantRepository->updateProductVariant($data, $id);
        return $this->renderResponse($variant);
    }

    private function duplicateNameCheck(array $groups): void
    {
        $duplicateGroups = [];
        $duplicateOptions = [];

        $groupNames = [];

        foreach ($groups as $group) {
            $normalizedGroup = strtolower(trim($group['option_group_name']));

            if (isset($groupNames[$normalizedGroup])) {
                $duplicateGroups[] = $group['option_group_name'];
            } else {
                $groupNames[$normalizedGroup] = true;
            }

            $optionTracker = [];
            foreach ($group['productOptions'] as $option) {
                $normalizedOption = strtolower(trim($option['option_name']));

                if (isset($optionTracker[$normalizedOption])) {
                    $duplicateOptions[] = $option['option_name'];
                } else {
                    $optionTracker[$normalizedOption] = true;
                }
            }
        }

        $messages = [];

        if (!empty($duplicateGroups)) {
            $messages[] = 'Duplicate Option Group Name(s): ' . implode(', ', array_unique($duplicateGroups));
        }

        if (!empty($duplicateOptions)) {
            $messages[] = 'Duplicate Option Name(s) inside the same group: ' . implode(', ', array_unique($duplicateOptions));
        }

        if (!empty($messages)) {
            throw new ValidationException([
                'global_message' => $messages,
            ]);
        }
    }

    public function deleteVariant(Request $request, $id): Response
    {
        try {
            $variant = $this->productVariantRepository->deleteVariant((int) $id);
            return $this->renderResponse($variant);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    public function deleteVariantImage(Request $request, $product_variant_id): Response
    {
        $deleted = $this->productVariantRepository->deleteVariantImage((int) $product_variant_id);
        return $this->renderResponse(['deleted' => $deleted]);
    }
    public function deleteVariantOptionImage(Request $request, $product_variant_option_id): Response
    {
        $deleted = $this->productVariantRepository->deleteVariantOptionImage((int) $product_variant_option_id);
        return $this->renderResponse(['deleted' => $deleted]);
    }

    public function importVariants(Request $request): Response
    {
        $csv_file = $request->file('csv_file');

        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';

        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->productVariantRepository->importVariants($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }

    // search variants by name and product id
    public function searchVariants(Request $request): Response
    {
        $name = $request->query('variant_name');
        $product_id = $request->query('product_id');
        $variants = $this->productVariantRepository->searchVariants((int) $product_id, $name);
        return $this->renderResponse($variants);
    }
    public function searchItemOptionVariants(Request $request): Response
    {
        $name = $request->query('query');
        $product_id = $request->query('product_id');
        $variants = $this->productVariantRepository->searchItemOptionVariants((string) $name, (int) $product_id);
        return $this->renderResponse($variants);
    }
    
    public function productVariants(Request $request, string $product_id): Response
    {
        $variants = $this->productVariantRepository->searchVariants((int) $product_id);
        return $this->renderResponse($variants);
    }
    public function getVariantById(Request $request, string $id): Response
    {
        $variant = $this->productVariantRepository->getVariantById((int) $id);
        return $this->renderResponse($variant);
    }

    public function searchVariantItems(Request $request): Response
    {
        $name = $request->query('variant_name');
        $product_id = $request->query('product_id');
        $variants = $this->productVariantRepository->searchVariantItems((int) $product_id, $name);
        return $this->renderResponse($variants);
    }

    public function upload(Request $request, int $id): Response
    {
        $property = $request->input('property');
        
        // Set default size
        $size = [
            'width' => 748,
            'height' => 642,
        ];
        $thumbSize = [
            'width' => 150,
            'height' => 130,
        ];
        $folderName = 'items-images';
       
        
        if($request->files() || isset($_FILES['files'])){
            $files = $request->files() ?? $_FILES['files'];
            
            if(!count($files)){
            return $this->renderError(422, 'No files uploaded');
            }

            $uploadDir = "media/Products/{$folderName}";
            $data = [
                'files' => $files,
                'upload_dir' => $uploadDir
            ];

            $result = $this->mediaRepository->upload($data, $size, $uploadDir);

            if(!$result){
                return $this->renderError(500, 'Failed to upload media');
            }

            $this->productVariantRepository->uploadVariantImage($result['files'], (int) $id);

            return $this->renderResponse($result);

        }

        return $this->renderError(422, 'No files uploaded');
    }

    public function uploadProductOptionImage(Request $request, int $product_option_id): Response
    {
        // Set default size
        $size = [
            'width' => 748,
            'height' => 642,
        ];
        $thumbSize = [
            'width' => 150,
            'height' => 130,
        ];
        $folderName = 'items-images';
        
        if($request->files() || isset($_FILES['files'])){
          
            $files[0] = [
                'name' => $_FILES[0]['name']['file'],
                'full_path' => $_FILES[0]['full_path']['file'],
                'type' => $_FILES[0]['type']['file'],
                'tmp_name' => $_FILES[0]['tmp_name']['file'],
                'error' => $_FILES[0]['error']['file'],
                'size' => $_FILES[0]['size']['file'],
            ];
            if(!count($files)){
            return $this->renderError(422, 'No files uploaded');
            }

            $uploadDir = "media/Products/{$folderName}";
            $data = [
                'files' => $files,
                'upload_dir' => $uploadDir
            ];

            $result = $this->mediaRepository->upload($data, $size, $uploadDir);

            if(!$result){
                return $this->renderError(500, 'Failed to upload media');
            }

            $this->productVariantRepository->uploadProductOptionImage($result['files'], (int) $product_option_id);

            return $this->renderResponse($result);

        }

        return $this->renderError(422, 'No files uploaded');
    }
}
