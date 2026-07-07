<?php

declare(strict_types=1);

namespace App\Core\Repositories\Taxonomy;

use App\Core\Models\PostCategory\TaxonomyItemContent;
use App\Core\Models\PostCategory\TaxonomyItem;
use App\Core\Repositories\Base\BaseRepository;
use PDO;
use App\Core\Validation\TaxonomyItemDataValidation;
use League\Csv\Reader;
use Exception;

class TaxonomyItemRepository extends BaseRepository implements TaxonomyItemRepositoryInterface
{
    private TaxonomyItemContent $taxonomyItemContent;
    public function __construct(
        PDO $db, 
        TaxonomyItemContent $taxonomyItemContent
    )
    {
        parent::__construct($db, 'taxonomy_item', TaxonomyItem::class);
        $this->taxonomyItemContent = $taxonomyItemContent;
        $this->taxonomyItemContent->setDb($db);
    }

    /**
     * Get all items for a specific taxonomy
     */
    public function getByTaxonomyId(int $taxonomyId, ?int $parentId = null): array
    {
        $this->model->clearQuery();
        $query = $this->model;
        $query->where('taxonomy_item.taxonomy_item_id', '=', $taxonomyId)
        ->join('taxonomy_item_content', 'taxonomy_item_content.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
        ->select(
            ['taxonomy_item.*', 
            'taxonomy_item_content.name', 
            'taxonomy_item_content.content',
            'taxonomy_item_content.slug', 
            'taxonomy_item_content.link', 
            'taxonomy_item_content.products_link',
            'taxonomy_item_content.meta_title',
            'taxonomy_item_content.meta_description'
         ]);

        if ($parentId !== null) {
            $query->where('parent_id', '=', (string)$parentId);
        }
        $result = $query->first();

        return $result?(array) $result->data??[]:[];
    }

    public function getCategoryBySlug(string $slug): ?array
    {
        $category = $this->model
        ->join('taxonomy_item_content', 'taxonomy_item_content.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
        ->where('taxonomy_item_content.slug','=', $slug)
        ->select(['taxonomy_item.*', 
        'taxonomy_item_content.name', 
        'taxonomy_item_content.slug', 
        'taxonomy_item_content.link', 
        'taxonomy_item_content.products_link',
        'taxonomy_item_content.meta_title',
        'taxonomy_item_content.meta_description'])
        ->first();
        $this->model->clearQuery();
        if($category && $category->parent_id) {
            $parentCategory = $this->getByTaxonomyId($category->parent_id);
            if(count($parentCategory) > 0) {
                $category->data->parent = $parentCategory;
            }
        }
        return $category?(array) $category->data??null:null;
    }
    public function getCategoryByProductLink(string $productLink): ?array
    {
        $cat = $this->model
        ->join('taxonomy_item_content', 'taxonomy_item_content.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
        ->where('taxonomy_item_content.products_link','=', $productLink)
        ->select(['taxonomy_item.*', 
        'taxonomy_item_content.name', 
        'taxonomy_item_content.content',
        'taxonomy_item_content.slug', 
        'taxonomy_item_content.link', 
        'taxonomy_item_content.products_link',
        'taxonomy_item_content.meta_title',
        'taxonomy_item_content.meta_description',
        'taxonomy_item_content.meta_keywords'])
        ->first();
        $cat = $cat?(array) $cat->data??[]:[];
        $this->model->clearQuery();
        if($cat && $cat['parent_id']) {
            $parentCategory = $this->getByTaxonomyId($cat['parent_id']);
            if($parentCategory) {
                $cat['parent'] = $parentCategory;
            }
        }

        return $cat;
    }

    /**
     * Get all child items for a specific parent
     */
    public function getChildren(int $parentId): array
    {
        $query = $this->query();
        $query->where('parent_id', '=', (string)$parentId);
        $query->orderBy('sort_order', 'ASC')
             ->orderBy('taxonomy_item_id', 'ASC');

        return $query->findAll();
    }

    /**
     * Get all parent items for a specific item
     */
    public function getParents(int $taxonomyItemId): array
    {
        $item = $this->find($taxonomyItemId);
        if (!$item) {
            return [];
        }

        $parents = [];
        $parentId = $item->parent_id;

        while ($parentId > 0) {
            $parent = $this->find($parentId);
            if (!$parent) break;
            
            $parents[] = $parent;
            $parentId = $parent->parent_id;
        }

        return $parents;
    }

    /**
     * Get items by their status
     */
    public function getByStatus(int $status): array
    {
        $query = $this->query();
        $query->where('status', '=', (string)$status);
        $query->orderBy('sort_order', 'ASC')
             ->orderBy('taxonomy_item_id', 'ASC');

        return $query->findAll();
    }

    /**
     * Get items by post type
     */
    public function getByPostType(string $postType): array
    {
        $query = $this->query();
        $query->join('taxonomy t', 'taxonomy_item.taxonomy_id = t.taxonomy_id')
              ->where('t.post_type', '=', $postType)
              ->orderBy('sort_order', 'ASC')
              ->orderBy('taxonomy_item_id', 'ASC');

        return $query->findAll();
    }

    /**
     * Get items by site ID
     */
    public function getBySiteId(int $siteId): array
    {
        $query = $this->query();
        $query->join('taxonomy t', 'taxonomy_item.taxonomy_id = t.taxonomy_id')
              ->where('t.site_id', '=', (string)$siteId)
              ->orderBy('sort_order', 'ASC')
              ->orderBy('taxonomy_item_id', 'ASC');

        return $query->findAll();
    }

    /**
     * Update item status
     */
    public function updateStatus(int $taxonomyItemId, int $status): bool
    {
        return $this->update($taxonomyItemId, ['status' => $status]);
    }

    /**
     * Update item sort order
     */
    public function updateSortOrder(int $taxonomyItemId, int $sortOrder): bool
    {
        return $this->update($taxonomyItemId, ['sort_order' => $sortOrder]);
    }

    /**
     * Move item to new parent
     */
    public function moveToParent(int $taxonomyItemId, int $newParentId): bool
    {
        return $this->update($taxonomyItemId, ['parent_id' => $newParentId]);
    }

    public function insertTaxonomyItemContents(array $data): bool
    {
        return $this->taxonomyItemContent->insert($data);
    }


    public function importTaxonomyItems(string $csv_file, int $taxonomy_id): array
    { 
        //Mandetory 
        $reader = Reader::createFromPath($csv_file, 'r');
        $reader->setHeaderOffset(0);
        
        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }
            
        $records = $reader->getRecords();

        $validTaxonomyItems = [];
        $showValidData = [];
        $validTaxonomyItemContents = [];
        $validSubcategoryItems = [];
        $validSubcategoryContents = [];
        $invalid = [];
        $updated = [];
        $defaultFields = $this->getAllTaxonomyFields($headers);
        
        // Step 2: Extract unique categories first
        $parrentCategories = [];
        $subCategories = [];
        foreach ($records as $record) {
            $categoryName = trim($record['category_name'] ?? '');
            $categoryLink = trim($record['category_link'] ?? '');
            $categoryProductsLink = trim($record['category_products_link'] ?? '');
            $parentCategoryId = trim($record['parent_category_id'] ?? '');
            
            $category = [
                'taxonomy_id' => $record['category_group_id'] ?? 1,
                'language_id' => $record['language_id'] ?? 1,
                'taxonomy_item_id' => $record['id'] ?? null,
                'status' => $record['status'] ?? 1,
                'parent_id' => $parentCategoryId,
                'name' => $categoryName,
                'label' => trim($record['label'] ?? ''),
                'content' => $record['content'] ?? '',
                'sort_order' => $record['sort_order'] ?? 0,
                'slug' => $record['slug'] ?? '',
                'category_name' => $categoryName ?? '',
                'link' => $categoryLink,
                'products_link' => $categoryProductsLink,
                'category_banner' => $record['category_banner'] ?? null,
                'category_slider_image' => $record['category_slider_image'] ?? null,
                'meta_title' => $record['meta_title'] ?? '',
                'meta_description' => $record['meta_description'] ?? '',
                'meta_keywords' => $record['meta_keywords'] ?? ''
            ];
            $category = (isset($category['taxonomy_item_id']) && !!$category['taxonomy_item_id']) ? $category : array_merge($defaultFields, $category);
            $category = new TaxonomyItemDataValidation($category);
            $validatedCategory = $category->validate();
            if ($validatedCategory === false) {
                $invalid[] = [
                    'row' => 'category',
                    'data' => $category,
                    'errors' => $category->getErrors(),
                    'type' => 'category'
                ];
                continue;
            }
            if($parentCategoryId){
                $subCategories[$categoryName] = $validatedCategory;
                $taxonomyItem = (array) $validatedCategory->taxonomyItem;
                $content = (array) $validatedCategory->content;

                if (count($taxonomyItem) > 0) $validSubcategoryItems[] = $taxonomyItem;
                if (count($taxonomyItem) > 0) $showValidData[] = $record;
                if (count($content) > 0) $validSubcategoryContents[] = $content;
            }else{
                $parrentCategories[$categoryName] = $validatedCategory;
                $taxonomyItem = (array) $validatedCategory->taxonomyItem;
                $content = (array) $validatedCategory->content;

                if (count($taxonomyItem) > 0) $validTaxonomyItems[] = $taxonomyItem;
                if (count($taxonomyItem) > 0) $showValidData[] = $record;
                if (count($content) > 0) $validTaxonomyItemContents[] = $content;
            }
        }
        

        // Step 4: Insert categories first
        $categoryInsertResult = $this->insertTaxonomyItemsAndContents($validTaxonomyItems, $validTaxonomyItemContents);
        
        // // Step 5: Get category IDs for subcategories
        // $taxonomyItemIds = $categoryInsertResult['taxonomy_item_ids'];

        // // Step 6: Process ALL rows as subcategories
        // foreach ($validSubcategoryItems as $key => $subCategoryItem) {
        //     if(isset($taxonomyItemIds[$subCategoryItem['parrent_category_name']])){
        //         $validSubcategoryItems[$key]['parent_id'] = $taxonomyItemIds[$subCategoryItem['parrent_category_name']];
        //         unset($validSubcategoryItems[$key]['parrent_category_name']);
        //     }else{
        //         //Collect data into error variable to show later on in the frontend
        //         $invalid[] = [
        //             'row' => 'parrent_category_id',
        //             'data' => $subCategoryItem,
        //             'errors' => ['parrent_category_id' => "Parrent category ID does not exist"],
        //             'type' => 'subcategory'
        //         ];
        //         unset($validSubcategoryItems[$key]);
        //         continue;
        //     }
        // }
        

        // Step 7: Insert subcategories
        $subcategoryInsertResult = $this->insertTaxonomyItemsAndContents($validSubcategoryItems, $validSubcategoryContents);

        return [
            'success' => true,
            'categories' => [
                'valid_records' => count($validTaxonomyItems),
                'valid_data' => $showValidData,
                'inserted_count' => $categoryInsertResult['inserted_count'],
                'inserted_content_count' => $categoryInsertResult['inserted_content_count']
            ],
            'subcategories' => [
                'valid_records' => count($validSubcategoryItems),
                'valid_data' => $showValidData,
                'inserted_count' => $subcategoryInsertResult['inserted_count'],
                'inserted_content_count' => $subcategoryInsertResult['inserted_content_count']
            ],
            'total_valid_records' => count($validTaxonomyItems) + count($validSubcategoryItems),
            'valid_records' => count($showValidData),
            'valid_data' => $showValidData,
            'invalid_records' => count($invalid),
            'updated_records' => count($updated),
            'invalid_data' => $invalid,
            'updated_data' => $updated
        ];
    }

    /**
     * Insert taxonomy items and their content
     */
    private function insertTaxonomyItemsAndContents(array $taxonomyItems, array $taxonomyContents): array
    {
        $inserted = 0;
        $insertedContent = 0;
        
        if (empty($taxonomyItems)) {
            return ['inserted_count' => 0, 'inserted_content_count' => 0];
        }

        try {
            $this->db->beginTransaction();
            $taxonomyNames = [];
            $taxonomyItemIds = [];
            
            // Insert taxonomy items
            try {
                $this->model->clearQuery();
                $this->model->upsert($taxonomyItems, ['taxonomy_item_id']);
                $inserted = count($taxonomyItems);
                $taxonomyNames = array_column($taxonomyItems, 'name');
            } catch (Exception $e) {
                $this->db->rollBack();
                throw new Exception("Failed to insert taxonomy items: " . $e->getMessage());
            }

            // Insert taxonomy item contents
            if (count($taxonomyContents) > 0 && count($taxonomyNames) > 0) {
                // Get the taxonomy_item_ids for the just inserted items
                // We'll match by order since we just inserted them
                $this->model->clearQuery();
                $taxonomyItemIdData = $this->model->select(['taxonomy_item_id', 'name'])
                    ->whereIn('name', $taxonomyNames)
                    ->orderBy('taxonomy_item_id', 'DESC')
                    ->findAll();
                
                foreach($taxonomyItemIdData as $taxonomyItemId){
                    $taxonomyItemIds[$taxonomyItemId['name']] = $taxonomyItemId['taxonomy_item_id'];
                }
                
                $finalContentData = [];
                foreach ($taxonomyContents as $content) {
                    if (isset($taxonomyItemIds[$content['name']])) {
                        $content['taxonomy_item_id'] = $taxonomyItemIds[$content['name']];
                        $finalContentData[] = $content;
                    }
                }
                
                if (!empty($finalContentData)) {
                    $this->taxonomyItemContent->upsert($finalContentData, ['taxonomy_item_id', 'language_id']);
                    $insertedContent = count($finalContentData);
                }
            }

            $this->db->commit();
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Failed to insert taxonomy items: " . $e->getMessage());
        }

        return ['inserted_count' => $inserted, 'inserted_content_count' => $insertedContent, 'taxonomy_item_ids' => $taxonomyItemIds];
    }

    /**
     * Ensure all taxonomy fields exist with defaults
     */
    private function getAllTaxonomyFields(array $headers): array
    {
        $defaults = [];
        foreach ($headers as $h) { $defaults[$h] = null; }
        
        // taxonomy_item defaults
        $defaults['taxonomy_id'] = 1;
        $defaults['parent_id'] = null;
        $defaults['sort_order'] = 0;
        $defaults['status'] = 1;
        $defaults['template'] = '';
        $defaults['color'] = null;
        $defaults['image'] = null;
        
        // content defaults
        $defaults['language_id'] = 1;
        $defaults['name'] = '';
        $defaults['slug'] = '';
        $defaults['content'] = '';
        $defaults['meta_title'] = '';
        $defaults['meta_description'] = '';
        $defaults['meta_keywords'] = '';
        $defaults['link'] = '';
        
        return $defaults;
    }

    

} 