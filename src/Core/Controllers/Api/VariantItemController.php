<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Http\ApiController;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Exceptions\ValidationException;
use App\Core\Models\Item\RequestResponse\ItemVariantRequest;
use App\Core\Repositories\Item\VariantItemRepositoryInterface;
use App\Core\Repositories\Item\ItemRepositoryInterface;
use App\Core\Repositories\ProductOptionGroup\ProductOptionGroupRepositoryInterface;
use App\Core\Repositories\Product\ProductOptionRepositoryInterface;
use App\Core\Repositories\Variant\ProductVariantRepositoryInterface;

class VariantItemController extends ApiController
{
    private VariantItemRepositoryInterface $variantItemRepository;
    private ItemRepositoryInterface $itemRepository;
    private ProductOptionRepositoryInterface $productOptionRepository;
    private ProductOptionGroupRepositoryInterface $productOptionGroupRepository;
    private ProductVariantRepositoryInterface $productVariantRepository;
    public function __construct(
        VariantItemRepositoryInterface $variantItemRepository,
        ItemRepositoryInterface $itemRepository,
        ProductOptionRepositoryInterface $productOptionRepository,
        ProductOptionGroupRepositoryInterface $productOptionGroupRepository,
        ProductVariantRepositoryInterface $productVariantRepository
    )
    {
        parent::__construct();
        $this->variantItemRepository = $variantItemRepository;
        $this->itemRepository = $itemRepository;
        $this->productOptionRepository = $productOptionRepository;
        $this->productOptionGroupRepository = $productOptionGroupRepository;
        $this->productVariantRepository = $productVariantRepository;
    }

    /**
     * Get all variants items.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $variantsItems = $this->variantItemRepository->findAll();
        return $this->renderResponse($variantsItems);
    }

    /**
     * Get a variants item by ID.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function show(Request $request, $id): Response
    {
        $variantsItem = $this->variantItemRepository->find((int)$id);
        if(!$variantsItem){
            return $this->renderError(404, 'Variants item not found');
        }
        return $this->renderResponse($variantsItem->data);
    }

    /**
     * Create a new variants item.
     *
     * @param Request $request
     * @return Response
     */

     public function getVariantByVariantId(Request $request, int $variant_id): Response
     {
         $variant = $this->variantItemRepository->getVariantByVariantId($variant_id);
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

     public function searchItemOptions(Request $request): Response
     {
         $name = $request->query('option_name');
         $product_id = $request->query('product_id');
         $productOptions = $this->itemRepository->searchItemOptions($name, (int) $product_id);
         return $this->renderResponse($productOptions);
     }

    public function createVariantsItem(Request $request): Response
    {
        try {
            $data = $request->all();
            if ($data instanceof Response) {
                return $data;
            }
            $inputData = [
                'value' => $data['value'] ?? null,
                'name' => $data['name'] ?? null,
                'unit' => $data['unit'] ?? null,
                'lanugage_id' => $data['lanugage_id'] ?? 1,
            ];
            $request->validate([
                'value' => 'required',
                'name' => 'required',
                'unit' => 'required',
            ],$inputData);

            $variantsItem = $this->variantItemRepository->createLenthType($data);
            return $this->renderResponse($variantsItem);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    /**
     * Update a variants item.
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
        $inputData = [
            'name' => $data['name'] ?? null,
            'unit' => $data['unit'] ?? null,
        ];
        try {
            $validatedData = $request->validate([
                'value' => 'required',
                'name' => 'required',
                'unit' => 'required',
            ],$inputData);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        }

        $existingVariantsItem = $this->variantItemRepository->find((int)$id);
        if (!$existingVariantsItem) {
            return $this->renderError(404, 'Variants item not found');
        }

        $variantsItem = $this->variantItemRepository->updateVariantsItem((int)$id, $data);
        if (!$variantsItem) {
            return $this->renderError(500, 'Failed to update variants item');
        }

        return $this->renderResponse($variantsItem);
    }

    /**
     * Delete a variants item.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function delete(Request $request, $id): Response
    {
        try {
            $variantItem = $this->variantItemRepository->deleteVariantItem((int) $id);
            return $this->renderResponse($variantItem);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    public function importVariantItem(Request $request): Response
    {
        $csv_file = $request->file('csv_file');
        
        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';
        
        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->variantItemRepository->importVariantItem($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }
} 