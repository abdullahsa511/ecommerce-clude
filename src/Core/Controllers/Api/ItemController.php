<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Components\Site;
use App\Core\Exceptions\ValidationException;
use App\Core\Http\ApiController;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Models\Item\RequestResponse\ItemVariantRequest;
use App\Core\Models\Item\ItemData;
use App\Core\Models\Item\ItemResponse;
use App\Core\ModelsFilters\RequestUri;
use App\Core\Repositories\Item\ItemRepositoryInterface;
use App\Core\Repositories\Item\VariantItemRepositoryInterface;
use App\Core\Repositories\Media\MediaRepositoryInterface;
use App\Core\Repositories\ProductOptionGroup\ProductOptionGroupRepositoryInterface;
use App\Core\Repositories\Product\ProductOptionRepositoryInterface;
use App\Core\Repositories\Variant\ProductVariantRepositoryInterface;
use Exception;

class ItemController extends ApiController
{
    private ItemRepositoryInterface $itemRepository;
    private VariantItemRepositoryInterface $variantItemRepository;
    private MediaRepositoryInterface $mediaRepository;
    private ProductOptionGroupRepositoryInterface $productOptionGroupRepository;
    private ProductOptionRepositoryInterface $productOptionRepository;
    private ProductVariantRepositoryInterface $productVariantRepository;
    public function __construct(
        ItemRepositoryInterface $itemRepository, 
        MediaRepositoryInterface $mediaRepository,
        VariantItemRepositoryInterface $variantItemRepository,
        ProductOptionGroupRepositoryInterface $productOptionGroupRepository,
        ProductOptionRepositoryInterface $productOptionRepository,
        ProductVariantRepositoryInterface $productVariantRepository
    ) {
        parent::__construct();
        $this->itemRepository = $itemRepository;
        $this->variantItemRepository = $variantItemRepository;
        $this->mediaRepository = $mediaRepository;
        $this->productOptionGroupRepository = $productOptionGroupRepository;
        $this->productOptionRepository = $productOptionRepository;
        $this->productVariantRepository = $productVariantRepository;
    }

    /**
     * Get all sites.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $query = $request->query()??[];
        $requestUri = new RequestUri($query);
        $data = $this->itemRepository->getItems($requestUri);
        return $this->renderResponse(['items' => $data, 'pagination' => $requestUri]);
    }

    /**
     * Show a site.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function show(Request $request, $id): Response
    {
        $item = $this->itemRepository->getItemById((int)$id);
        if (!$item) {
            return $this->renderError(404, 'Item not found');
        }
        $response = new ItemResponse($item);
        return $this->renderResponse($response);
    }

    /**
     * Create a new site.
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        $data = $request->all();
        try {
            $request->validate([
                'product_id' => 'required|integer',
                'product_variant_id' => 'required|integer',
                'item_code' => 'required',
                'km_item_id ' => 'int|nullable',
            ], $data);
        } catch (ValidationException $e) {

            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }
        $isExistingItem = $this->itemRepository->getItemByItemCode($data['item_code']);
        if ($isExistingItem) {
            throw new ValidationException([
                'item_code' => ['Item code already exists: ' . $data['item_code']],
            ]);
        }
        $itemData = new ItemData($data);
        $item = $this->itemRepository->createItem($itemData);
        if (!$item) {
            return $this->renderError(500, 'Failed to create item');
        }
        // $item = new ItemResponse($item);
        return $this->renderResponse($item);
    }

    /**
     * Update a site.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */

    public function update(Request $request, int $id): Response
    {
        // return $this->renderError(500, 'Failed to update product');
        try {
            $data = $request->all();
            $request->validate([
                'product_id' => 'required|int',
                'product_variant_id' => 'required|int',
                'item_code' => 'required',
                'km_item_id ' => 'int|nullable',
            ], $data);
            $itemData = new ItemData($data);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $item = $this->itemRepository->updateItem($itemData);

        if (!$item) {
            return $this->renderError(500, 'Failed to update item');
        }

        $item = new ItemResponse($item->data);
        return $this->renderResponse($item);
    }

    /**
     * Delete a site.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function delete(Request $request, $id): Response
    {
        $deleted = $this->itemRepository->delete((int) $id);
        return $this->renderResponse($deleted);
    }

    public function upload(Request $request, int $item_id): Response
    {
        $property = $request->input('property');

        // Set default size
        $size = [
            'width' => 945,
            'height' => 630,
        ];
        $thumbSize = null;
        $folderName = 'media/Products/items-images/';
       

        if($request->files() || isset($_FILES['files'])){
          $files = $request->files() ?? $_FILES['files'];

          if(!count($files)){
            return $this->renderError(422, 'No files uploaded');
          }

          //   $folderName = str_replace('_', '-', $property);
          $uploadDir = $folderName;
          $data = [
            'files' => $files,
            'upload_dir' => $uploadDir
          ];

          $result = $this->mediaRepository->upload($data, $size, 'media/Products');

          if(!$result){
            return $this->renderError(500, 'Failed to upload media');
          }
          if ($item_id > 0) {
            $this->itemRepository->insertItemTableImageFile($result['files'], $property, $item_id);
          }
          return $this->renderResponse($result);
        }

        return $this->renderError(422, 'No files uploaded');
    }

    public function deleteByPath(Request $request, int $item_id): Response
    {
        $path = $request->input('path');
        $property = $request->input('property');
        if (!$path) {
            return $this->renderError(422, 'Path is required');
        }
        $this->itemRepository->deleteMediaByPath($property, $item_id);
        return $this->renderResponse(['message' => 'Media deleted successfully']);
    }

    public function deleteItemImage(Request $request, int $item_image_id): Response
    {
        $deleted = $this->itemRepository->deleteItemImage($item_image_id);
        return $this->renderResponse(['message' => 'Media deleted successfully', 'deleted' => $deleted]);
    }

    public function importItems(Request $request): Response
    {
        $csv_file = $request->file('csv_file');

        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';

        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->itemRepository->importItems($csv_file_path);
            return $this->renderResponse($result);
        } catch (\PDOException $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
        catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }

    public function importDimensions(Request $request): Response
    {
        $csv_file = $request->file('csv_file');

        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';

        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->itemRepository->importDimensions($csv_file_path);
            return $this->renderResponse($result);
        } catch (\PDOException $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
        catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }

    protected function  getItemFields()
    {
        return [
            'id' => '',
            'item_type_id' => '',
            'company_id' => '',
            'vendor_id' => '',
            'import_vendor_id' => '',
            'factory_vendor_id' => '',
            'item_range_id' => '',
            'item_category_id' => '',
            'sort_order' => '',
            'item_code' => '',
            'factory_code' => '',
            'web_sku' => '',
            'description' => '',
            'specifications' => '',
            'warranty_period' => '',
            'active' => '',
            'width' => '',
            'height' => '',
            'depth' => '',
            'carton_qm' => '',
            'gross_weight' => '',
            'boradusages_sixteen' => '',
            'boardusages_eighteen' => '',
            'boardusages_twentyfive' => '',
            'boardusages_thirtythree' => '',
            'boardusages_fifty' => '',
            'lead_days' => '',
            'quote_rating' => '',
            'quote_image' => '',
            'web_link' => '',
            'print_sticker' => '',
            'track_stock' => '',
            'user_note' => '',
            'zone' => '',
            'archive' => '',
            'tlf_code' => '',
            'project_price_qty' => '',
            'project_price_discount' => ''
        ];
    }

    // variant item create 
    public function showVariantItem(Request $request, int $id): Response
    {
        $variant = $this->variantItemRepository->searchVariantItems($id);
        return $this->renderResponse($variant);
    }

    public function getVariantByItem(Request $request, int $item_id): Response
    {
        $variant = $this->variantItemRepository->getVariantByItem($item_id);
        return $this->renderResponse($variant);
    }

    //This will only call when a variant exist
    //Not allow to change variant here 
    //Only allow to update or delete option group and option
    public function updateItemVariant(Request $request, $id): Response
    {
        $data = $request->all();

        if (!array_key_exists('product_variant_id', $data)) {
            throw new ValidationException([
                'product_variant_id' => ['Product variant id is required'],
            ]);
        }
        if(!isset($data['product_id'])){
            throw new ValidationException([
                'product_id' => ['Product id is required'],
            ]);
        }
        if(!isset($data['item_id'])){
            throw new ValidationException([
                'item_id' => ['Item id is required'],
            ]);
        }
    
        if(isset($data['product_variant_id']) && $data['product_variant_id'] == 0){
            //Create a new Variant by duplicate checking
            $isExistingVarinat = $this->variantItemRepository->checkDuplicateVariant($data['product_id'], $data['variant_name']);
            if($isExistingVarinat){
                throw new ValidationException([
                    'variant_name' => ['A variant with the name ' . $data['variant_name'] . ' already exists.'],
                ]);
            }
            //Create a new variant 
            $variant = $this->variantItemRepository->createProductVariant($data);
            if(!isset($variant['product_variant_id'])){
                throw new ValidationException([
                    'variant_name' => ['Failed to create product variant'],
                ]);
            }
            $data['product_variant_id'] = $variant['product_variant_id'];
        }else{
            $productVariant = $this->productVariantRepository->getProductVariantById($data['product_variant_id']);
            if(!isset($productVariant['product_variant_id'])){
                throw new ValidationException([
                    'product_variant_id' => ['Product variant not found'],
                ]);
            }
            $this->variantItemRepository->editVariantItem($data);
        }

        $requestGroupNames = array_column($data['itemOptionGroups'], 'option_group_name');
        $groups = $this->productOptionGroupRepository->findProductOptionGroupsByNames($requestGroupNames, (int) $data['product_id'], (int) $data['product_variant_id']);
        $groups = array_column($groups, 'option_group_name', 'product_option_group_id');

        $groupIds = array_keys($groups);

        $options = $this->productOptionRepository->findProductOptionsByGroupIds($groupIds);
        $options = array_column($options, 'option_name', 'product_option_id');
        $validator = new ItemVariantRequest($data, $groups, $options);

        if (!$validator->isValid) {
            $errors = [];
            foreach ($validator->errors as $idx => $error) {
                foreach ($error as $key => $value) {
                    // If $errors already has the key, merge the messages
                    if (isset($errors[$key])) {
                        $errors[$idx.'_'.$key] = array_merge($errors[$key], (array)$value);
                    } else {
                        $errors[$idx.'_'. $key] = (array)$value;
                    }
                }
            }
            throw new ValidationException($errors);
        }

        $variant = $this->variantItemRepository->updateVariantItem($validator);
        return $this->renderResponse($variant);
    }

    public function createItemVariant(Request $request): Response
    {
        try {
            $data = $request->all();
            // Debug::dd($data, true);
            if(!isset($data['product_id'])){
                throw new ValidationException([
                    'product_id' => ['Product id is required'],
                ]);
            }
            
            if(!isset($data['item_id'])){
                throw new ValidationException([
                    'item_id' => ['Item id is required'],
                ]);
            }
        
            if(isset($data['product_variant_id']) && $data['product_variant_id'] == 0){
                //Create a new Variant by duplicate checking
                $isExistingVarinat = $this->variantItemRepository->checkDuplicateProudctVariant($data['product_id'], $data['variant_name']);
                if($isExistingVarinat){
                    throw new ValidationException([
                        'variant_name' => ['A variant with the name ' . $data['variant_name'] . ' already exists.'],
                    ]);
                }
                //Create a  new variant 
                $variant = $this->variantItemRepository->createProductVariant($data);
                $data['product_variant_id'] = $variant['product_variant_id'];
            }
            
            // create variant item.
            $isExistingVariantItem = $this->variantItemRepository->checkDuplicateVariantItem($data['product_id'], (int) $data['item_id']);
            if($isExistingVariantItem){
                throw new ValidationException([
                    'variant_name' => ['Product, variant combination already exists'],
                ]);
            }else{
                $variantItem = $this->variantItemRepository->addVariantItem($data);
                if(!isset($variantItem['variant_item_id'])){
                    throw new ValidationException([
                        'variant_name' => ['Failed to create variant item: ' . $data['variant_name']],
                    ]);
                }
            }

            $requestGroupNames = array_column($data['itemOptionGroups'], 'option_group_name');
            $groups = $this->productOptionGroupRepository->findProductOptionGroupsByNames($requestGroupNames, $data['product_id'], $data['product_variant_id']);
            $groups = array_column($groups, 'option_group_name', 'product_option_group_id');
    
            $groupIds = array_keys($groups);
    
            $options = $this->productOptionRepository->findProductOptionsByGroupIds($groupIds);
            $options = array_column($options, 'option_name', 'product_option_id');
            $validator = new ItemVariantRequest($data, $groups, $options);
    
            if (!$validator->isValid) {
                $errors = [];
                foreach ($validator->errors as $idx => $error) {
                    foreach ($error as $key => $value) {
                        if (isset($errors[$key])) {
                            $errors[$idx.'_'.$key] = array_merge($errors[$key], (array)$value);
                        } else {
                            $errors[$idx.'_'.$key] = (array)$value;
                        }
                    }
                }
                throw new ValidationException($errors);
            }
    
            $variant = $this->variantItemRepository->createVariantItem($validator);
            return $this->renderResponse($variant);

        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    private function duplicateNameCheck(array $groups): void
    {
        $duplicateGroups = [];
        $duplicateOptions = [];
        $multipleProductOptionsInGroup = [];

        $groupNames = [];

        foreach ($groups as $group) {
            $normalizedGroup = strtolower(trim($group['option_group_name']));

            if (isset($groupNames[$normalizedGroup])) {
                $duplicateGroups[] = $group['option_group_name'];
            } else {
                $groupNames[$normalizedGroup] = true;
            }
            if (count($group['productOptions']) > 1) {
                $multipleProductOptionsInGroup[] = $group['option_group_name'];
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

        if (!empty($multipleProductOptionsInGroup)) {
            $messages[] = 'Multiple product options found in group(s): ' . implode(', ', array_unique($multipleProductOptionsInGroup));
        }

        if (!empty($messages)) {
            throw new ValidationException([
                'global_message' => $messages,
            ]);
        }
    }

    public function searchItemOptions(Request $request): Response
    {
        $name = $request->query('option_name');
        $product_id = $request->query('product_id');
        $productOptions = $this->itemRepository->searchItemOptions($name, (int) $product_id);
        return $this->renderResponse($productOptions);
    }

    public function searchItemlists(Request $request): Response
    {
        $name = $request->query('item_code');
        $product_id = $request->query('product_id');
        $items = $this->itemRepository->searchItems((string) $name, (int) $product_id);
        return $this->renderResponse($items);
    }
}
