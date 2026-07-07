<?php

declare(strict_types=1);

namespace App\Core\Repositories\Product;

use App\Core\Models\PostCategory\TaxonomyItem;
use App\Core\Models\Product\ProductCategory;
use App\Core\Models\Product\ProductToTaxonomyItem;
use App\Core\Models\Product\ProductCertificate;
use App\Core\Repositories\Product\ProductRepositoryInterface;
use App\Core\Repositories\Base\BaseRepository;
use Illuminate\Support\Collection;
use PDO;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    protected PDO $db;
    protected ProductToTaxonomyItem $productToTaxonomyItem;
    protected ProductCertificate $productCertificate;
    protected ProductRepositoryInterface $productRepository;
    public function __construct(
        PDO $db, 
        ProductToTaxonomyItem $productToTaxonomyItem, 
        ProductCertificate $productCertificate, 
        ProductRepositoryInterface $productRepository
    ){
        parent::__construct($db, 'taxonomy_item', TaxonomyItem::class);
        $this->productToTaxonomyItem = $productToTaxonomyItem;
        $this->productToTaxonomyItem->setDb($db);
        $this->productCertificate = $productCertificate;
        $this->productCertificate->setDb($db);
        $this->productRepository = $productRepository;
    }

    public function getCategories(
        int $languageId, 
        int $taxonomyId, 
        int $siteId, 
        ?string $search = null, 
        ?string $type = null, 
        int $start = 0, 
        int $limit = 10): Collection
    {
        $query = $this->model
            ->where('language_id', '=', $languageId)
            ->where('taxonomy_id', '=', $taxonomyId)
            ->where('site_id', '=', $siteId);

        if ($search !== null) {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        if ($type !== null) {
            $query->where('type', '=', $type);
        }

        $query->limit($limit)
              ->offset($start)
              ->orderBy('name', 'ASC');

        return collect($query->findAll() ?? []);
    }

    public function getCategoriesPages(int $languageId, int $taxonomyId, int $siteId, ?string $search = null, ?string $type = null): int
    {
        $query = $this->model
            ->where('language_id', '=', $languageId)
            ->where('taxonomy_id', '=', $taxonomyId)
            ->where('site_id', '=', $siteId);

        if ($search !== null) {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        if ($type !== null) {
            $query->where('type', '=', $type);
        }

        return $query->countAll();
    }

    public function getCategory(int $id): ?ProductCategory
    {
        return $this->model->find($id);
    }

    public function getCategoryBySlug(int $languageId, int $taxonomyId, int $siteId, string $slug): ?ProductCategory
    {
        return $this->model
            ->where('language_id', '=', $languageId)
            ->where('taxonomy_id', '=', $taxonomyId)
            ->where('site_id', '=', $siteId)
            ->where('slug', '=', $slug)
            ->first();
    }

    public function editCategory(int $id, array $data): ProductCategory
    {
        $category = $this->model->find($id);
        $category->update($data);
        return $category;
    }

    public function addCategory(int $languageId, int $taxonomyId, int $siteId, array $data): int
    {
        $data['language_id'] = $languageId;
        $data['taxonomy_id'] = $taxonomyId;
        $data['site_id'] = $siteId;

        $result = $this->model->create($data);
        return $result ? $result->getId() : 0;
    }

    public function getCategoriesAllLanguages(int $taxonomyId, int $siteId, ?string $search = null, ?string $type = null, int $start = 0, int $limit = 10): array
    {
        $query = $this->model
            ->where('taxonomy_id', '=', $taxonomyId)
            ->where('site_id', '=', $siteId);

        if ($search !== null) {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        if ($type !== null) {
            $query->where('type', '=', $type);
        }

        $query->limit($limit)
              ->offset($start)
              ->orderBy('name', 'ASC');

        return $query->findAll() ?? [];
    }

    public function editTaxonomyItem(int $languageId, int $taxonomyId, int $siteId, int $postId, array $data): bool
    {
        $category = $this->editCategory($postId, $data);
        return $category instanceof ProductCategory;
    }

    public function addTaxonomyItem(int $languageId, int $taxonomyId, int $siteId, array $data): int
    {
        return $this->addCategory($languageId, $taxonomyId, $siteId, $data);
    }

    public function updateTaxonomyItems(int $languageId, int $taxonomyId, int $siteId, array $data): bool
    {
        $success = true;
        foreach ($data as $item) {
            if (!isset($item['post_id'])) {
                continue;
            }
            if (!$this->editTaxonomyItem($languageId, $taxonomyId, $siteId, $item['post_id'], $item)) {
                $success = false;
            }
        }
        return $success;
    }

    public function deleteTaxonomyItem(int $languageId, int $taxonomyId, int $siteId, int $postId): bool
    {
        $query = $this->model
            ->where('language_id', '=', $languageId)
            ->where('taxonomy_id', '=', $taxonomyId)
            ->where('site_id', '=', $siteId)
            ->where('post_id', '=', $postId);

        return $query->delete($postId);
    }

    public function getCategoriesForMasonryComponent(int $languageId = 1, int $taxonomyId = 1, int $siteId = 1): array
    {
        // Get main categories from database
        $query = $this->model
            ->join('taxonomy_item_content', 'taxonomy_item_content.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
            ->join('taxonomy', 'taxonomy.taxonomy_id', '=', 'taxonomy_item.taxonomy_id')
            ->where('taxonomy.type', '=', 'categories')
            ->where('taxonomy.post_type', '=', 'product')
            ->where('taxonomy.site_id', '=', $siteId)
            ->where('taxonomy_item_content.language_id', '=', $languageId)
            ->where('taxonomy_item.parent_id', '=', 0) // Only top-level categories
            ->where('taxonomy_item.status', '=', 1) // Only active categories
            ->select([
                'taxonomy_item.taxonomy_item_id', 
                'taxonomy_item.image', 
                'taxonomy_item_content.name', 
                'taxonomy_item_content.link',
                'taxonomy_item_content.content as description',
                'taxonomy_item_content.meta_title',
                'taxonomy_item_content.meta_description',
                'taxonomy_item.parent_id',
                'taxonomy_item.sort_order'
            ]);

        $query->limit(10)
              ->orderBy('taxonomy_item.sort_order', 'ASC')
              ->orderBy('taxonomy_item_content.name', 'ASC');

        $categories = $query->findAll(false);

        $results = [];
        $results['title'] = 'Product Categories';
        $results['subtitle'] = "Explore our comprehensive range of office furniture and solutions.";
        $results['items'] = [];

        if (!empty($categories)) {
            foreach ($categories as $index => $category) {
                // Get subcategories for this category
                $subcategoriesQuery = $this->model
                    ->join('taxonomy_item_content', 'taxonomy_item_content.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
                    ->where('taxonomy_item.parent_id', '=', $category['taxonomy_item_id'])
                    ->where('taxonomy_item_content.language_id', '=', $languageId)
                    ->where('taxonomy_item.status', '=', 1)
                    ->select(['taxonomy_item_content.name'])
                    ->limit(6)
                    ->orderBy('taxonomy_item.sort_order', 'ASC')
                    ->orderBy('taxonomy_item_content.name', 'ASC');

                $subcategories = $subcategoriesQuery->findAll(false);
                $subcategoriesList = array_map(function($sub) {
                    return ['text' => $sub['name']];
                }, $subcategories);

                // Process image data
                $imageData = json_decode($category['image'] ?? '{}', true);
                $imageUrl = $imageData['url'] ?? '/img/categories/default-category.png';

                // Determine grid class based on index (alternating between 8 and 5 columns)
                $gridClass = ($index % 2 == 0) ? 'th-masonry-grid-item grid-col-span-8' : 'th-masonry-grid-item grid-col-span-5';
                
                // Generate style with transform and padding for masonry effect
                $transformY = $index * 20 - 100; // Varying transform values
                $paddingTop = $index * 15; // Varying padding values
                $style = "transform: translateY({$transformY}px); padding-top: {$paddingTop}px";

                $results['items'][] = [
                    'heading' => $category['name'],
                    'img' => $imageUrl,
                    'des' => $category['description'] ?: 'Explore our comprehensive range of ' . strtolower($category['name']) . ' designed to meet your office needs.',
                    'class' => $gridClass,
                    'style' => $style,
                    'subcategories' => $subcategoriesList,
                    'link' => [
                        'text' => 'View '. $category['name'],
                        'url' => $category['link'] ?: '/categories/' . $category['name'],
                        'icon' => 'fa-regular fa-arrow-up degree-60'
                    ]
                ];
            }
        } 
        // else {
        //     // Fallback to static data if no categories found
        //     $results['items'] = [
        //         [
        //             'heading' => 'Workstations',
        //             'img' => '/img/categories/workstations.png',
        //             'des' => 'A full collection of workstations from leg-based systems to panel constructions and height-adjustable offerings. Find the perfect configuration and aesthetic for your space.',
        //             'class' => 'th-masonry-grid-item grid-col-span-8',
        //             'style' => 'transform: translateY(0px); padding-top: 0px',
        //             'subcategories' => [
        //                 ['text' => 'Workstations backend'],
        //                 ['text' => 'Workstation screens'],
        //                 ['text' => 'test-workstations']
        //             ],
        //             'link' => [
        //                 'text' => 'Read More',
        //                 'url' => '/categories/workstations',
        //                 'icon' => 'fa-regular fa-arrow-up degree-60'
        //             ]
        //         ],
        //         [
        //             'heading' => 'Screens',
        //             'img' => '/img/categories/screens.png',
        //             'des' => 'A wide variety of desk-mounted or floor-based screens for all your privacy and zoning needs. Choose from an extensive selection of fabrics and finishes.',
        //             'class' => 'th-masonry-grid-item grid-col-span-5',
        //             'style' => 'transform: translateY(0px); padding-top: 60px',
        //             'subcategories' => [
        //                 ['text' => 'Screens'],
        //                 ['text' => 'Acoustic booths'],
        //                 ['text' => 'Perspex screens']
        //             ],
        //             'link' => [
        //                 'text' => 'Read More',
        //                 'url' => '/categories/screens',
        //                 'icon' => 'fa-regular fa-arrow-up degree-60'
        //             ]
        //         ]
        //     ];
        // }

        return $results;
    }

    public function getCategoriesForSliderNavComponent(int $languageId = 1, int $taxonomyId = 1, int $siteId = 1): array
    {
        // Get main categories from database
        $query = $this->model
            ->join('taxonomy_item_content', 'taxonomy_item_content.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
            ->join('taxonomy', 'taxonomy.taxonomy_id', '=', 'taxonomy_item.taxonomy_id')
            ->where('taxonomy.type', '=', 'categories')
            ->where('taxonomy.post_type', '=', 'product')
            // ->where('taxonomy.site_id', '=', $siteId)
            // ->where('taxonomy_item_content.language_id', '=', $languageId)
            ->where(function($q){
                return $q->where('taxonomy_item.parent_id', '=', 0)->orWhereNull('taxonomy_item.parent_id');
            }) // Only top-level categories
            // ->where('taxonomy_item.status', '=', 1) // Only active categories
            ->with(['children' => function($q){
                return $q->with(['taxonomyItemContent'])->orderBy('taxonomy_item.sort_order', 'ASC');
            }])
            ->select([
                'taxonomy_item.taxonomy_item_id', 
                'taxonomy_item.image', 
                'taxonomy_item_content.name', 
                'taxonomy_item_content.link',
                'taxonomy_item_content.products_link',
                'taxonomy_item_content.content as description',
                'taxonomy_item_content.meta_title',
                'taxonomy_item_content.meta_description',
                'taxonomy_item.parent_id',
                'taxonomy_item.sort_order'
            ]);

        $query->orderBy('taxonomy_item.sort_order', 'ASC');
        try {
            $categories = $query->findAll();
        } catch (\Throwable $th) {
            //throw $th;
        }

        $results = [];
        $results['section_title'] = 'Product Categories';
        $results['section_subtitle'] = "Explore our comprehensive range of office furniture and solutions designed to enhance your workspace.";
        $results['section_link_text'] = "View All Categories";
        $results['section_link'] = "/categories";
        $results['collapseItems'] = [];
        $results['items'] = [];

        if (!empty($categories)) {
            // Process categories for both collapseItems and items
            foreach ($categories as $index => $category) {
                // Process image data
                $imageData = json_decode($category['image'] ?? '{}', true);
                $imageUrl = $imageData[0]['objectURL'] ?? '/img/bg/home/home_cat_default.jpg';
                
                // Generate category slug for links
                $categorySlug = strtolower(str_replace(' ', '-', $category['name']??''));
                $categoryLink = $category['link'] ?? '/categories/' . $categorySlug;

                // Add to collapseItems (first 5 categories)
                $results['collapseItems'][$index] = [
                    'menuTitle' => $category['name'],
                    'menuSubTitle' => 'Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.',
                    'link' => $categoryLink,
                    'subMenuItems' => []
                ];
                $children = json_decode($category['children'] ?? '[]', true);
                foreach($children as $child){
                    $slug = strtolower(str_replace(' ', '-', $child['name']??''));
                    $l = '/products/' . $categorySlug . '/' . $slug;
                    $link = isset($child['taxonomyItemContent']) ? ($child['taxonomyItemContent']['products_link'] ?? $child['taxonomyItemContent']['link'] ?? $l) : $l;
                    $results['collapseItems'][$index]['subMenuItems'][] = [
                        'title' => $child['name'],
                        'link' => $link
                    ];
                }

                // Add to items (first 4 categories for slider)
               
                $results['items'][] = [
                    'image' => $imageUrl,
                    'title' => $category['name'],
                    'subTitle' => $category['description'] ?? 'Energistically harness',
                    'link' => $categoryLink,
                ];
                
            }
        } 

        return $results;
    }

    public function getCategoryHeroComponentData(array $param): array
    {
        // Get parameters
        $categorySlug = $param['category_slug'] ?? $param['slug'] ?? $param['category'] ?? '';
        // Convert HTML entities to their corresponding characters for $categorySlug
        $categorySlug = rawurldecode(html_entity_decode($categorySlug, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $languageId = $param['language_id'] ?? 1;
        $siteId = $param['site_id'] ?? 1;
        
        
        $this->model->clearQuery();
        // Get main category by slug
        $mainCategory = $this->model
            ->join('taxonomy_item_content', 'taxonomy_item_content.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
            ->join('taxonomy', 'taxonomy.taxonomy_id', '=', 'taxonomy_item.taxonomy_id')
            ->where('taxonomy.type', '=', 'categories')
            ->where('taxonomy.post_type', '=', 'product')
            ->where('taxonomy.site_id', '=', $siteId)
            ->where('taxonomy_item_content.language_id', '=', $languageId)
            ->where('taxonomy_item_content.slug', '=', $categorySlug)
            ->whereRaw('(taxonomy_item.parent_id = 0 OR taxonomy_item.parent_id IS NULL)') // Only top-level categories
            ->where('taxonomy_item.status', '>', 0) // Only active categories
            ->select([
                'taxonomy_item.taxonomy_item_id',
                'taxonomy_item.image',
                'taxonomy_item_content.name',
                'taxonomy_item_content.content as description',
                'taxonomy_item_content.slug',
                'taxonomy_item_content.meta_title',
                'taxonomy_item_content.meta_description',
                'taxonomy_item_content.link',
                'taxonomy_item_content.products_link',
                'taxonomy_item.banner_way_points',
            ])
            ->first();
        $mainCategory = (array) $mainCategory->data??[];
        
        $results = [];
        
        if ($mainCategory) {
            $wayPoints = isset($mainCategory['banner_way_points']) ? json_decode($mainCategory['banner_way_points'], true) : [];
            $this->model->clearQuery();
            // Get subcategories for this main category
            $subcategories = $this->model
                ->join('taxonomy_item_content', 'taxonomy_item_content.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
                ->where('taxonomy_item.parent_id', '=', $mainCategory['taxonomy_item_id'])
                ->where('taxonomy_item_content.language_id', '=', $languageId)
                ->where('taxonomy_item.status', '>', 0)
                ->select([
                    'taxonomy_item_content.name',
                    'taxonomy_item_content.slug',
                    'taxonomy_item_content.link',
                    'taxonomy_item_content.products_link'
                ])
                ->orderBy('taxonomy_item.sort_order', 'ASC')
                ->findAll(false);
            
            // Process image data
            $imageData = json_decode($mainCategory['image'] ?? '[]', true);
            if(count($imageData) > 0){
                $imageData = $imageData[0];
            }
            $imageUrl = $imageData['objectURL'] ?? '/img/category-seating/default-hero.png';
            
            // Generate category URLs
            $categoryList = [];
            foreach ($subcategories as $subcategory) {
                $cat = [
                    'name' => $subcategory['name'],
                    'slug' => $subcategory['slug'],
                    'link' => $subcategory['products_link'],
                    'category_link' => $subcategory['link']
                ];
                if(isset($param['subcategory']) && $param['subcategory'] == $subcategory['slug']){
                    $cat['active'] = true;
                }
                $categoryList[] = $cat;
            }
            
            $results = [
                'title' => strtoupper($mainCategory['name']),
                'link' => $mainCategory['link'],
                'subtitle' => $mainCategory['description'] ?: 'Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.',
                'image' => $imageUrl,
                'categories' => $categoryList,
                'way_points' => $wayPoints
            ];
            if(isset($param['subcategory']) && $param['subcategory'] == $mainCategory['slug']){
                $results['active'] = true;
            }
        } 
        // else {
        //     // Fallback to static data if category not found
        //     $results = [
        //         'title' => 'SEATING',
        //         'subtitle' => 'Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.',
        //         'image' => '/img/category-seating/seating-hero.png',
        //         'categories' => [
        //             ['name' => 'Task Seating', 'url' => "/"],
        //             ['name' => 'Executive Seating', 'url' => "/"],
        //             ['name' => 'Training Seating', 'url' => "/"],
        //             ['name' => 'Occasional Seating', 'url' => "/"],
        //             ['name' => 'Stools', 'url' => "/"],
        //             ['name' => 'Lounges', 'url' => "/"],
        //         ]
        //     ];
        // }
        
        return $results;
    }

   

    public function getCategorySeatingDetailsComponentData(array $param): array
    {
        // Get language and site parameters
        $languageId = $param['language_id'] ?? 1;
        $siteId = $param['site_id'] ?? 1;
        
        $results = [];
        $results['sections'] = [];
        
        // Get main seating category
        $mainSeatingCategory = $this->model
            ->where('taxonomy_item.name', '=', 'Seating')
            ->where('taxonomy_item.status', '>', 0)
            ->select(['taxonomy_item.taxonomy_item_id'])
            ->first();
        $mainSeatingCategory = (array) $mainSeatingCategory->data;
            
        if ($mainSeatingCategory) {
            $seatingCategoryId = $mainSeatingCategory['taxonomy_item_id'];
           
           $productQuery = $this->productRepository->getProductQuery();
           $productQuery->where('taxonomy_item.parent_id', '=', $seatingCategoryId);
           $productQuery->orderBy('taxonomy_item.sort_order', 'ASC');
           $products = $productQuery->findAll(false);

           $categoryQuery = $this->productRepository->getProductQuery();
           $categoryQuery->where('taxonomy_item.taxonomy_item_id', '=', $seatingCategoryId);
           $categoryQuery->orderBy('taxonomy_item.sort_order', 'ASC');
           $categoryProducts = $categoryQuery->findAll(false);

           $products = array_merge($products, $categoryProducts);
           $results = $this->prepareProducts($products, $results);
        } 
        
        usort($results['sections'], function($a, $b) {
            return $a['sort_order'] <=> $b['sort_order'];
        });
     
        return $results;
    }
    public function getCategorySeatingDetailsComponentData_backup(array $param): array
    {
        // Get language and site parameters
        $languageId = $param['language_id'] ?? 1;
        $siteId = $param['site_id'] ?? 1;
        
        $results = [];
        $results['sections'] = [];
        
        // Get main seating category
        $mainSeatingCategory = $this->model
            ->where('taxonomy_item.name', '=', 'Seating')
            ->where('taxonomy_item.status', '>', 0)
            ->select(['taxonomy_item.taxonomy_item_id'])
            ->first();
        $mainSeatingCategory = (array) $mainSeatingCategory->data;
            
        if ($mainSeatingCategory) {
            $seatingCategoryId = $mainSeatingCategory['taxonomy_item_id'];
           
           $this->model = $this->model->clearQuery();
            $products = $this->model
            ->join('taxonomy_item_content', 'taxonomy_item_content.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
            ->join('product_to_taxonomy_item', 'product_to_taxonomy_item.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
            ->join('product', 'product.product_id', '=', 'product_to_taxonomy_item.product_id')
            ->join('product_content', 'product_content.product_id', '=', 'product.product_id')
            ->where('taxonomy_item.parent_id', '=', $seatingCategoryId)
            ->select([
                'taxonomy_item.sort_order as category_sort_order',
                'taxonomy_item_content.name as category',
                'taxonomy_item_content.slug as category_slug',
                'taxonomy_item_content.link as category_link',
                'taxonomy_item_content.products_link',
                'taxonomy_item_content.content as content',
                'product.product_id as id',
                'product_content.name',
                'product_content.title',
                'product_content.tag_line as description',
                'product.image',
                'product_content.slug as product_slug',
                'product_content.meta_keywords as tags',
                'product_content.meta_description as meta_description'
            ])
            ->orderBy('product.sort_order', 'ASC')
            ->orderBy('product.product_id', 'ASC')
            ->findAll(false);

            $productIds = array_column($products, 'id');

            $allTags = $this->productToTaxonomyItem
            ->join('taxonomy_item', 'product_to_taxonomy_item.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
            ->join('taxonomy_item_content', 'taxonomy_item.taxonomy_item_id', '=', 'taxonomy_item_content.taxonomy_item_id')
            ->join('taxonomy', 'taxonomy_item.taxonomy_id', '=', 'taxonomy.taxonomy_id')
            ->whereIn('product_to_taxonomy_item.product_id', $productIds)
            ->whereIn('taxonomy.type', ['tags', 'finishes'])
            ->where('taxonomy.post_type', '=', 'product')
            ->select([
                "`product_to_taxonomy_item`.`product_id`",
                "`taxonomy`.`type`",
                "`taxonomy_item_content`.`name`",
                "`taxonomy_item`.`image` as category_image",
                "`taxonomy_item`.`color` as category_color",
            ])
            ->findAll(false);

            $productTagsFinishes = [];
            foreach($allTags as $tag){
                if($tag['type'] == 'tags'){
                    $productTagsFinishes[$tag['product_id']]['tags'][] = ['name' => $tag['name']];
                }else {
                    $image = json_decode($tag['category_image'] ?? '[]', true);
                    if(count($image) > 0){
                        $imageUrl = $image[0]['objectURL'];
                    }else {
                        $imageUrl = '';
                    }
                    $productTagsFinishes[$tag['product_id']]['finishes'][] = ['name' => $tag['name'], 'image' => $imageUrl, 'color' => $tag['category_color']];
                }
            }


            $allProductCertificates = $this->productCertificate
            ->whereIn('product_id', $productIds)
            ->select([
                'product_certificate_id',
                'product_id',
                'certificate_type',
                'title as name',
                'description',
            ])
            ->findAll(false);
            $allProductCertificatesMap = [];
            foreach($allProductCertificates as $certificate){
                $allProductCertificatesMap[$certificate['product_id']][] = $certificate;
            }
          
             // Process products
             $processedProducts = [];
             foreach ($products as $product) {
       
                 // Process image data
                 $imageData = json_decode($product['image'] ?? '{}', true);
                 $imageUrl = $imageData[0]['objectURL'] ?? '/img/category-seating/default-product.png';
                 
                 // Process tags (assuming they're stored as JSON or comma-separated)
                 $tags = $productTagsFinishes[$product['id']]['tags'] ?? [];
                 $productCertificates = $allProductCertificatesMap[$product['id']] ?? [];
                 // Default tags if none found
                //  if (empty($tags)) {
                //      $tags = [
                //          ['name' => 'AFRDI Certified empty'],
                //          ['name' => 'OBP Certified empty']
                //      ];
                //  }
                 $finishes = $productTagsFinishes[$product['id']]['finishes'] ?? [];
                 
                 $processedProduct = [
                     'id' => $product['id'],
                     'name' => $product['title'] ?? ucwords(strtolower(str_replace(['_', '-'], ' ', $product['name'] ?? ''))),
                     'model' => 'product',
                     'title' => $product['title'] ?? ucwords(strtolower(str_replace(['_', '-'], ' ', $product['name'] ?? ''))),
                     'description' => $product['description'],
                     'meta_description' => $product['meta_description'],
                     'url' => '/products/'.$product['category_slug'].'/'.$product['product_slug'],
                     'image' => $imageUrl,
                     'tags' => $productCertificates,
                     'finishes' => $finishes,
                     'category' => $product['category'],    
                     'content' => $product['content']
                 ];
                 
                 // Add description for stools
                 if (!empty($product['description'])) {
                     $processedProduct['description'] = $product['description'];
                 }
                 if(!isset($processedProducts[$product['category_slug']])){
                    $processedProducts[$product['category_slug']] = [];
                 }
                 
                 $results['sections'][$product['category_slug']]['sort_order'] = $product['category_sort_order'];
                 $results['sections'][$product['category_slug']]['title'] = $product['category'];
                 $results['sections'][$product['category_slug']]['subtitle'] = $product['content'];
                 $results['sections'][$product['category_slug']]['link'] = $product['products_link'];
                 $results['sections'][$product['category_slug']]['category_link'] = $product['category_link'];
                 $results['sections'][$product['category_slug']]['items'][] = $processedProduct;
             }
        
        } 
        // $results['sections'] = [
        //     'popular' => [
        //         'title' => 'Popular Seating',
        //         'subtitle' => 'Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.',
        //         'link' => "/products/seatings/popular",
        //         'items' => $this->getFallbackSeatingProducts('popular', 'Popular Seating')
        //     ],
        // ];
        usort($results['sections'], function($a, $b) {
            return $a['sort_order'] <=> $b['sort_order'];
        });
     
        return $results;
    }

    private function prepareProducts(array $products, array $results = []): array
    {
        foreach ($products as $product) {
            if(!isset($product['id'])){
                continue;
            }
            if(!isset($results['sections'][$product['category_slug']])){
                $results['sections'][$product['category_slug']] = [];
                $results['sections'][$product['category_slug']]['items'] = [];
            }
            
            $results['sections'][$product['category_slug']]['title'] = $product['category'];
            $results['sections'][$product['category_slug']]['sort_order'] = $product['category_sort_order'];
            $results['sections'][$product['category_slug']]['subtitle'] = $product['content'];
            $results['sections'][$product['category_slug']]['link'] = $product['products_link'];
            $results['sections'][$product['category_slug']]['category_link'] = $product['category_link'];

            $imageData = json_decode($product['image'] ?? '{}', true);
            $imageUrl = $imageData[0]['objectURL'] ?? '/img/products/default-product.png';
            $finishImage = json_decode($product['finish_image'] ?? '[]', true);
            $finishImageUrl = '';
            if (count($finishImage) > 0) {
                $finishImageUrl = $finishImage[0]['objectURL'] ?? '';
            }
          
            if(!isset($results['sections'][$product['category_slug']]['items'][$product['id']])){

                $tags = $product['certificate_title']? [$product['product_certificate_id'] => $product['certificate_title']] : [];
                if($product['tag_id'] && $product['tag_name']){
                    $tags[$product['tag_id'].'_'.$product['tag_name']] = $product['tag_name'];
                }

                $results['sections'][$product['category_slug']]['items'][$product['id']] = [
                    'id' => $product['id'],
                    'name' => $product['title'] ?? ucwords(str_replace(['_', '-'], ' ', strtolower($product['name']))),
                    'title' => $product['title'] ?? ucwords(str_replace(['_', '-'], ' ', strtolower($product['name']))),
                    'model' => 'product',
                    'image' => $imageUrl,
                    'description' => $product['description'] ?? 'A comfortable and versatile seating option for various environments.',
                    'meta_description' => $product['meta_description'],
                    'url' => "/products/".$product['category_slug'].'/'.$product['product_slug'],
                    'category' => $product['category'],
                    'tags' => $product['certificate_title']? [$product['product_certificate_id'] => $product['certificate_title']] : [],
                    'finishes' => $product['finish_name'] ? [
                        $product['finish_name'] => [
                            'name' => $product['finish_name'],
                            'color' => 'th-circle ' .$product['finish_color'],
                            'img' => $finishImageUrl
                        ]] : []
                ];
            }else{
                if($product['certificate_title']){
                    $results['sections'][$product['category_slug']]['items'][$product['id']]['tags'][$product['product_certificate_id']] = $product['certificate_title'];
                }
                if($product['tag_id'] && $product['tag_name']){
                    $results['sections'][$product['category_slug']]['items'][$product['id']]['tags'][$product['tag_id'].'_'.$product['tag_name']] = $product['tag_name'];
                }
     
                $results['sections'][$product['category_slug']]['items'][$product['id']]['finishes'][$product['finish_name']] = [
                    'name' => $product['finish_name'],
                    'color' => 'th-circle ' .$product['finish_color'],
                    'img' => $finishImageUrl
                ];
            }            
        }
        return $results;
    }

    public function getCategoryDetailsComponentData(array $param): array
    {
        $languageId = $param['language_id'] ?? 1;
        $siteId = $param['site_id'] ?? 1;
        
        $results = [];
        $results['sections'] = [];
        
        // Get main seating category
        $mainSeatingCategory = $this->model
            ->join('taxonomy_item_content', 'taxonomy_item_content.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
            ->where('taxonomy_item_content.slug', '=', $param['category'])
            ->where('taxonomy_item.status', '>', 0)
            ->select(['taxonomy_item.taxonomy_item_id'])
            ->first();
        $mainSeatingCategory = (array) $mainSeatingCategory->data;
            
        if ($mainSeatingCategory) {
            $seatingCategoryId = $mainSeatingCategory['taxonomy_item_id'];
            // new add 
            $productQuery = $this->productRepository->getProductQuery();
            $productQuery->where('taxonomy_item.parent_id', '=', $seatingCategoryId);
            // $productQuery->where('product.product_id', '=', 243); // testing product id
            $productQuery->orderBy('taxonomy_item.sort_order', 'ASC');
            // var_dump($productQuery->getQuery());
            // exit;
            $products = $productQuery->findAll(false);

            $categoryQuery = $this->productRepository->getProductQuery();
            $categoryQuery->where('taxonomy_item.taxonomy_item_id', '=', $seatingCategoryId);
            // $categoryQuery->where('product.product_id', '=', 243); // testing product id
            $categoryQuery->orderBy('taxonomy_item.sort_order', 'ASC');
            $categoryProducts = $categoryQuery->findAll(false);

            $products = array_merge($products, $categoryProducts);
            $results = $this->prepareProducts($products, $results);
            // new end
            // old code
            // $this->model = $this->model->clearQuery();
            // $products = $this->model
            // ->join('taxonomy_item_content', 'taxonomy_item_content.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
            // ->join('product_to_taxonomy_item', 'product_to_taxonomy_item.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
            // ->join('product', 'product.product_id', '=', 'product_to_taxonomy_item.product_id')
            // ->join('product_content', 'product_content.product_id', '=', 'product.product_id')
            // ->join('product_certificate', 'product_certificate.product_id', '=', 'product.product_id')
            // //For finishes
            // ->join('`product_resource`', 'product_resource.product_id', '=', 'product.product_id and product_resource.resource_type = "finishes"')
            // ->join('`design_resource`', 'design_resource.design_resource_id', '=', 'product_resource.design_resource_id')

            // ->where('taxonomy_item.parent_id', '=', $seatingCategoryId)
            // ->select([
            //     'taxonomy_item_content.name as category',
            //     'taxonomy_item.sort_order as category_sort_order',
            //     'taxonomy_item_content.slug as category_slug',
            //     'taxonomy_item_content.link as category_link',
            //     'taxonomy_item_content.products_link',
            //     'taxonomy_item_content.content as content',
            //     'product.product_id as id',
            //     'product_content.name',
            //     'product_content.title',
            //     'product_content.tag_line as description',
            //     'product.image',
            //     'product_content.slug as product_slug',
            //     'product_content.meta_keywords as tags',
            //     'product_content.meta_description as meta_description',

            //     'product_certificate.product_certificate_id',
            //     'product_certificate.title as certificate_title',
            //     'product_certificate.description as certificate_description',

            //     "design_resource.title as finish_name",
            //     "design_resource.img as finish_image",
            //     "design_resource.hex_value as finish_color"
            // ])
            // ->orderBy('taxonomy_item.sort_order', 'ASC')
            // ->findAll(false);

            // $this->model->clearQuery();
            // $categoryProducts = $this->model
            // ->join('taxonomy_item_content', 'taxonomy_item_content.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
            // ->join('product_to_taxonomy_item', 'product_to_taxonomy_item.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
            // ->join('product', 'product.product_id', '=', 'product_to_taxonomy_item.product_id')
            // ->join('product_content', 'product_content.product_id', '=', 'product.product_id')
            // ->join('product_certificate', 'product_certificate.product_id', '=', 'product.product_id')
            // //For finishes
            // ->join('`product_resource`', 'product_resource.product_id', '=', 'product.product_id and product_resource.resource_type = "finishes"')
            // ->join('`design_resource`', 'design_resource.design_resource_id', '=', 'product_resource.design_resource_id')

            // ->where('taxonomy_item.taxonomy_item_id', '=', $seatingCategoryId)
            // ->select([
            //     'taxonomy_item_content.name as category',
            //     'taxonomy_item.sort_order as category_sort_order',
            //     'taxonomy_item_content.slug as category_slug',
            //     'taxonomy_item_content.link as category_link',
            //     'taxonomy_item_content.products_link',
            //     'taxonomy_item_content.content as content',
            //     'product.product_id as id',
            //     'product_content.name',
            //     'product_content.title',
            //     'product_content.tag_line as description',
            //     'product.image',
            //     'product_content.slug as product_slug',
            //     'product_content.meta_keywords as tags',
            //     'product_content.meta_description as meta_description',

            //     'product_certificate.product_certificate_id',
            //     'product_certificate.title as certificate_title',
            //     'product_certificate.description as certificate_description',

            //     "design_resource.title as finish_name",
            //     "design_resource.img as finish_image",
            //     "design_resource.hex_value as finish_color"
            // ])
            // ->orderBy('taxonomy_item.sort_order', 'ASC')
            // ->findAll(false);

            // foreach ($products as $product) {
            //     if(!isset($product['id'])){
            //         continue;
            //     }
            //     if(!isset($results['sections'][$product['category_slug']])){
            //         $results['sections'][$product['category_slug']] = [];
            //         $results['sections'][$product['category_slug']]['items'] = [];
            //     }
                
            //     $results['sections'][$product['category_slug']]['title'] = $product['category'];
            //     $results['sections'][$product['category_slug']]['sort_order'] = $product['category_sort_order'];
            //     $results['sections'][$product['category_slug']]['subtitle'] = $product['content'];
            //     $results['sections'][$product['category_slug']]['link'] = $product['products_link'];
            //     $results['sections'][$product['category_slug']]['category_link'] = $product['category_link'];
    
            //     $imageData = json_decode($product['image'] ?? '{}', true);
            //     $imageUrl = $imageData[0]['objectURL'] ?? '/img/products/default-product.png';
            //     $finishImage = json_decode($product['finish_image'] ?? '[]', true);
            //     $finishImageUrl = '';
            //     if (count($finishImage) > 0) {
            //         $finishImageUrl = $finishImage[0]['objectURL'] ?? '';
            //     }
              
            //     if(!isset($results['sections'][$product['category_slug']]['items'][$product['id']])){
    
            //         $tags = $product['certificate_title']? [$product['product_certificate_id'] => $product['certificate_title']] : [];
            //         if($product['tag_id'] && $product['tag_name']){
            //             $tags[$product['tag_id'].'_'.$product['tag_name']] = $product['tag_name'];
            //         }
    
            //         $results['sections'][$product['category_slug']]['items'][$product['id']] = [
            //             'id' => $product['id'],
            //             'name' => $product['title'] ?? ucwords(str_replace(['_', '-'], ' ', strtolower($product['name']))),
            //             'title' => $product['title'] ?? ucwords(str_replace(['_', '-'], ' ', strtolower($product['name']))),
            //             'model' => 'product',
            //             'image' => $imageUrl,
            //             'description' => $product['description'] ?? 'A comfortable and versatile seating option for various environments.',
            //             'meta_description' => $product['meta_description'],
            //             'url' => "/products/".$product['category_slug'].'/'.$product['product_slug'],
            //             'category' => $product['category'],
            //             'tags' => $product['certificate_title']? [$product['product_certificate_id'] => $product['certificate_title']] : [],
            //             'finishes' => $product['finish_name'] ? [
            //                 $product['finish_name'] => [
            //                     'name' => $product['finish_name'],
            //                     'color' => 'th-circle ' .$product['finish_color'],
            //                     'img' => $finishImageUrl
            //                 ]] : []
            //         ];
            //     }else{
            //         if($product['certificate_title']){
            //             $results['sections'][$product['category_slug']]['items'][$product['id']]['tags'][$product['product_certificate_id']] = $product['certificate_title'];
            //         }
            //         if($product['tag_id'] && $product['tag_name']){
            //             $results['sections'][$product['category_slug']]['items'][$product['id']]['tags'][$product['tag_id'].'_'.$product['tag_name']] = $product['tag_name'];
            //         }
         
            //         $results['sections'][$product['category_slug']]['items'][$product['id']]['finishes'][$product['finish_name']] = [
            //             'name' => $product['finish_name'],
            //             'color' => 'th-circle ' .$product['finish_color'],
            //             'img' => $finishImageUrl
            //         ];
            //     }            
            // }
            // old code end
        }
        return $results;     
    }

    public function getCategoryWorkstationDetailsComponentData_backup(array $param): array
    {
        // Get language and site parameters
        $languageId = $param['language_id'] ?? 1;
        $siteId = $param['site_id'] ?? 1;
        
        $results = [];
        $results['sections'] = [];
        
        // Get main workstation category
        $mainWorkstationCategory = $this->model
            ->join('taxonomy_item_content', 'taxonomy_item_content.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
            ->join('taxonomy', 'taxonomy.taxonomy_id', '=', 'taxonomy_item.taxonomy_id')
            ->where('taxonomy.type', '=', 'categories')
            ->where('taxonomy.post_type', '=', 'product')
            ->where('taxonomy.site_id', '=', $siteId)
            ->where('taxonomy_item_content.language_id', '=', $languageId)
            ->where('taxonomy_item_content.slug', 'LIKE', '%workstation%')
            ->whereNull('taxonomy_item.parent_id')
            ->where('taxonomy_item.status', '=', 1)
            ->select(['taxonomy_item.taxonomy_item_id'])
            ->first();
        $mainWorkstationCategory = (array) $mainWorkstationCategory->data;
            
        if ($mainWorkstationCategory) {
            $workstationCategoryId = $mainWorkstationCategory['taxonomy_item_id'];
           
           $this->model = $this->model->clearQuery();
            $products = $this->model
            ->join('taxonomy_item_content', 'taxonomy_item_content.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
            ->join('product_to_taxonomy_item', 'product_to_taxonomy_item.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
            ->join('product', 'product.product_id', '=', 'product_to_taxonomy_item.product_id')
            ->join('product_content', 'product_content.product_id', '=', 'product.product_id')
            ->where('taxonomy_item.parent_id', '=', $workstationCategoryId)
            ->select([
                'taxonomy_item_content.name as category',
                'taxonomy_item.sort_order as category_sort_order',
                'taxonomy_item_content.slug as category_slug',
                'taxonomy_item_content.content as content',
                'product.product_id as id',
                'product_content.tag_line as description',
                'product_content.name',
                'product_content.title',
                'product_content.slug as product_slug',
                'product.image',
                'product_content.meta_keywords as tags',
                'product_content.meta_description as meta_description'
            ])
            ->orderBy('product.sort_order', 'ASC')
            ->orderBy('product.product_id', 'ASC')
            ->findAll(false);

            $productIds = [];
            foreach($products as $product){
                if($product['id']){
                    $productIds[] = $product['id'];
                }
            }

            $allTags = $this->productToTaxonomyItem
            ->join('taxonomy_item', 'product_to_taxonomy_item.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
            ->join('taxonomy_item_content', 'taxonomy_item.taxonomy_item_id', '=', 'taxonomy_item_content.taxonomy_item_id')
            ->join('taxonomy', 'taxonomy_item.taxonomy_id', '=', 'taxonomy.taxonomy_id')
            ->whereIn('product_to_taxonomy_item.product_id', $productIds)
            ->whereIn('taxonomy.type', ['tags', 'finishes'])
            ->where('taxonomy.post_type', '=', 'product')
            ->select([
                "`product_to_taxonomy_item`.`product_id`",
                "`taxonomy`.`type`",
                "`taxonomy_item_content`.`name`",
                "`taxonomy_item`.`image` as category_image",
                "`taxonomy_item`.`color` as category_color",
            ])
            ->findAll(false);





            $productTagsFinishes = [];
            foreach($allTags as $tag){
                if($tag['type'] == 'tags'){
                    $productTagsFinishes[$tag['product_id']]['tags'][] = ['name' => $tag['name']];
                }else {
                    $image = json_decode($tag['category_image'] ?? '[]', true);
                    if(count($image) > 0){
                        $imageUrl = $image[0]['objectURL'];
                    }else {
                        $imageUrl = '';
                    }
                    $productTagsFinishes[$tag['product_id']]['finishes'][] = ['name' => $tag['name'], 'image' => $imageUrl, 'color' => $tag['category_color']];
                }
            }



            $allProductCertificates = $this->productCertificate
            ->whereIn('product_id', $productIds)
            ->select([
                'product_certificate_id',
                'product_id',
                'certificate_type',
                'title as name',
                'description',
            ])
            ->findAll(false);
            $allProductCertificatesMap = [];
            foreach($allProductCertificates as $certificate){
                $allProductCertificatesMap[$certificate['product_id']][] = $certificate;
            }
          
             // Process products
             $processedProducts = [];
             foreach ($products as $product) {
       
                 // Process image data
                 $imageData = json_decode($product['image'] ?? '{}', true);
                 $imageUrl = $imageData[0]['objectURL'] ?? '/img/category-seating/default-product.png';
                 
                 // Process tags (assuming they're stored as JSON or comma-separated)
                 $tags = $productTagsFinishes[$product['id']]['tags'] ?? [];
                 $productCertificates = $allProductCertificatesMap[$product['id']] ?? [];
                 // Default tags if none found
                //  if (empty($tags)) {
                //      $tags = [
                //          ['name' => 'AFRDI Certified empty'],
                //          ['name' => 'OBP Certified empty']
                //      ];
                //  }
                 $finishes = $productTagsFinishes[$product['id']]['finishes'] ?? [];
                 
                 $processedProduct = [
                     'id' => $product['id'],
                     'title' => $product['title'] ?? ucwords(strtolower(str_replace(['_', '-'], ' ', $product['name']??''))),
                     'name' => $product['title'] ?? ucwords(strtolower(str_replace(['_', '-'], ' ', $product['name']??''))),
                     'description' => $product['description'],
                     'model' => 'product',
                     'meta_description' => $product['meta_description'],
                     'url' => '/products/'.$product['category_slug'].'/'.$product['product_slug'],
                     'image' => $imageUrl,
                    //  'tags1' => json_encode($tags), // Convert tags array to JSON string
                     'tags' => json_encode($productCertificates), // Convert tags array to JSON string
                     'finishes' => $finishes,
                     'category' => $product['category'],
                     'content' => $product['content']

                 ];
                 
                 // Add description for stools
                 if (!empty($product['description'])) {
                     $processedProduct['description'] = $product['description'];
                 }
                 if(!isset($processedProducts[$product['category_slug']])){
                    $processedProducts[$product['category_slug']] = [];
                 }
                 
                 $processedProducts[$product['category_slug']][] = $processedProduct;
             }
             
             foreach($processedProducts as $category => $products){
                // If no products found, create fallback items
                if (empty($products)) {
                    $processedProducts = $this->getFallbackSeatingProducts($product['category_slug'], $product['category']);
                }

                $results['sections'][$category] = [
                    'title' => ucfirst($category),
                    // 'subtitle' => 'Explore our comprehensive range of ' . $category. ' designed for modern workspaces.',
                    'subtitle' => $products[0]['content'] ?? '',
                    // 'link' => "/categories/workstations/".$category,
                    'link' => "/products"."/".$category,
                    'items' => $products
                ];
             }
             
        } else {
            $results['sections'] = [];
        }
        
        return $results;
    }


    public function getCategoryWorkstationDetailsComponentData(array $param): array
    {
        // Get language and site parameters
        $languageId = $param['language_id'] ?? 1;
        $siteId = $param['site_id'] ?? 1;
        
        $results = [];
        $results['sections'] = [];
        
        // Get main workstation category
        $mainWorkstationCategory = $this->model
            ->join('taxonomy_item_content', 'taxonomy_item_content.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
            ->join('taxonomy', 'taxonomy.taxonomy_id', '=', 'taxonomy_item.taxonomy_id')
            ->where('taxonomy.type', '=', 'categories')
            ->where('taxonomy.post_type', '=', 'product')
            ->where('taxonomy.site_id', '=', $siteId)
            ->where('taxonomy_item_content.language_id', '=', $languageId)
            ->where('taxonomy_item_content.slug', 'LIKE', '%workstation%')
            ->whereNull('taxonomy_item.parent_id')
            ->where('taxonomy_item.status', '=', 1)
            ->select(['taxonomy_item.taxonomy_item_id'])
            ->first();
        $mainWorkstationCategory = (array) $mainWorkstationCategory->data;
            
        if ($mainWorkstationCategory) {
            $workstationCategoryId = $mainWorkstationCategory['taxonomy_item_id'];
        
            $productQuery = $this->productRepository->getProductQuery();
            $productQuery->where('taxonomy_item.parent_id', '=', $workstationCategoryId);
            $productQuery->orderBy('taxonomy_item.sort_order', 'ASC');
            $products = $productQuery->findAll(false);

            $categoryQuery = $this->productRepository->getProductQuery();
            $categoryQuery->where('taxonomy_item.taxonomy_item_id', '=', $workstationCategoryId);
            $categoryQuery->orderBy('taxonomy_item.sort_order', 'ASC');
            $categoryProducts = $categoryQuery->findAll(false);

            $products = array_merge($products, $categoryProducts);
            $results = $this->prepareProducts($products, $results);
             
        } else {
            $results['sections'] = [];
        }
        
        return $results;
    }
    
    /**
     * Get fallback seating products when no database data is available
     */
    private function getFallbackSeatingProducts(string $section, string $category): array
    {
        $fallbackProducts = [
            'popular' => [
                ['id' => 1, 'name' => 'Archi', 'image' => '/img/category-seating/Archi.png'],
                ['id' => 2, 'name' => 'Miro', 'image' => '/img/category-seating/Miro.png'],
                ['id' => 3, 'name' => 'Miro S', 'image' => '/img/category-seating/Miro S.png'],
                ['id' => 4, 'name' => 'Kove', 'image' => '/img/category-seating/Kove.png']
            ],
            'executive' => [
                ['id' => 1, 'name' => 'Otto', 'image' => '/img/category-seating/Otto.png'],
                ['id' => 2, 'name' => 'Miro S Leather', 'image' => '/img/category-seating/Miro S Leather.png'],
                ['id' => 3, 'name' => 'Sax', 'image' => '/img/category-seating/Sax.png'],
                ['id' => 4, 'name' => 'Rox', 'image' => '/img/category-seating/Rox.png']
            ],
            'training' => [
                ['id' => 1, 'name' => 'Spyder', 'image' => '/img/category-seating/Spyder.png'],
                ['id' => 2, 'name' => 'Vira', 'image' => '/img/category-seating/Vira.png'],
                ['id' => 3, 'name' => 'Wing', 'image' => '/img/category-seating/Wing.png'],
                ['id' => 4, 'name' => 'Miro Visitor', 'image' => '/img/category-seating/Miro Visitor.png']
            ],
            'occasional' => [
                ['id' => 1, 'name' => 'Ted', 'image' => '/img/category-seating/Ted.png'],
                ['id' => 2, 'name' => 'Indi', 'image' => '/img/category-seating/Indi.png'],
                ['id' => 3, 'name' => 'Taro', 'image' => '/img/category-seating/Taro.png'],
                ['id' => 4, 'name' => 'Calvin', 'image' => '/img/category-seating/Calvin.png']
            ],
            'stools' => [
                ['id' => 'ted-stool', 'name' => 'Ted Stool', 'image' => '/img/category-seating/Ted Stool.png', 'description' => 'A comfortable and versatile stool option for various environments.'],
                ['id' => 'indi-stool', 'name' => 'Indi Stool', 'image' => '/img/category-seating/Indi Stool.png', 'description' => 'A modern design stool that blends functionality with aesthetic appeal.'],
                ['id' => 'zorro-stool', 'name' => 'Zorro Stool', 'image' => '/img/category-seating/Zorro Stool.png', 'description' => 'A sleek and durable stool option with a distinctive design.'],
                ['id' => 'juna-stool', 'name' => 'Juna Stool', 'image' => '/img/category-seating/Juna Stool.png', 'description' => 'An elegant and comfortable stool solution for multiple settings.']
            ]
        ];
        
        $products = $fallbackProducts[$section] ?? [];
        
        // Add default tags and category to each product
        foreach ($products as &$product) {
            $product['tags'] = '[{"name":"AFRDI Certified"},{"name":"OBP Certified"},{"name":"Some Tag Name Here"},{"name":"Tag Name Here"},{"name":"Tag Name Here As Well"}]';
            $product['category'] = $category;
        }
        
        return $products;
    }

    public function getHeaderMenu(): array
    {
        $results = [];
        // $results['topbar_message'] = 'Free shipping on apparel and gear over 75 USD';
        $results['desktop_logo'] = '/img/logo_black.png';
        $results['mobile_logo'] = '/img/logo-white.png';
        $results['pinboard_icon'] = '/img/pinboard-icon.svg';

        // Get menu items for taxonomy_id = 1 (header menu)
        $menuItems = $this->getMenuItemsFromDatabase(1);
        $mobileMenu = [
            [
                'title' => 'Home',
                'href' => '#',
                'class' => 'menu-item-has-children mega-menu-wrap',
                'has_children' => false,
                'mega_menu' => true,
                'children' => [
                  
                ]
            ]
        ];

        [$destopMenuRows, $mobileMenu] = $this->buildMenus($menuItems, $mobileMenu);

        $mobileMenu = $this->buildMobileMenu($mobileMenu);
        
        // Build mobile and desktop menus
        $results['desktop_menu'] = $this->buildDesktopMenu($destopMenuRows);
        $results['mobile_menu'] = $mobileMenu;

        return $results;
    }

    /**
     * Get menu items from database for a specific taxonomy
     * Fetches only parent categories (parent_id = 0 or NULL) to get all 10 main categories
     */
    private function getMenuItemsFromDatabase(int $taxonomyId): array
    {
        $query = $this->model
            ->join('taxonomy_item_content', 'taxonomy_item_content.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
            ->where('taxonomy_item.taxonomy_id', '=', $taxonomyId)
            ->where('taxonomy_item_content.language_id', '=', 1)
            ->where('taxonomy_item.status', '>', 0)
            ->orderBy('taxonomy_item.sort_order', 'ASC');

        return $query->select([
            'taxonomy_item.taxonomy_item_id',
            'taxonomy_item.sort_order as category_sort_order',
            'taxonomy_item.parent_id',
            'taxonomy_item.image',
            'taxonomy_item.template',
            'taxonomy_item.sort_order',
            'taxonomy_item.color',
            'taxonomy_item_content.link',
            'taxonomy_item_content.products_link',
            'taxonomy_item_content.name',
            'taxonomy_item_content.slug'
        ])->findAll(false);
    }

    private function buildMenus($categories, $mobileMenu){
        $rows = [];
        $i = 0;
        foreach($categories as $category){
            if($category['parent_id'] > 0){
                if(!isset($rows[$category['parent_id']])){
                    $rows[$category['parent_id']] = [];
                    $rows[$category['parent_id']]['links'] = [];
                }
                $rows[$category['parent_id']]['links'][$category['taxonomy_item_id']] = [
                    'title' => $category['name'],
                    'href' => $category['products_link']
                ];

            }else{
                if(!isset($rows[$category['taxonomy_item_id']])){
                    $rows[$category['taxonomy_item_id']] = [];
                }
                $rows[$category['taxonomy_item_id']]['title'] = $category['name'];
                $rows[$category['taxonomy_item_id']]['sort_order'] = $category['category_sort_order'];
                $rows[$category['taxonomy_item_id']]['class'] = 'sub-title';
                $rows[$category['taxonomy_item_id']]['href'] = $category['link'];
                if(in_array($i, [0, 5])){
                    $rows[$category['taxonomy_item_id']]['style'] = 'flex:1.2';
                }else{
                    $rows[$category['taxonomy_item_id']]['style'] = 'flex:.95';
                }
                $i++;
                // $rows[$category['taxonomy_item_id']]['links'][0] = [
                //     'title' => $category['name'],
                //     'href' => $category['link']
                // ];
                
            }
        }
        usort($rows, function($a, $b) {
            return $a['sort_order'] <=> $b['sort_order'];
        });
        $destopMenuRows = array_chunk($rows, 5);
        foreach($rows as $row){
           $row['children'] = $row['links']??[];
           unset($row['links']);
           $row['has_children'] = true;
           $row['class'] = 'menu-item-has-children';
           $row['href'] = '#';
           $mobileMenu[] = $row;
        }
        return [$destopMenuRows, $mobileMenu];
    }

    /**
     * Build mobile menu structure from database items
     */
    private function buildMobileMenu($menu): array
    {
        $menu[] = [
            'title' => 'Projects',
            'href' => '/projects',
            'class' => 'desktop-menu-item',
            'has_children' => false
        ];

        $menu[] = [
            'title' => 'Blog',
            'href' => '/blog',
            'class' => 'desktop-menu-item',
            'has_children' => false
        ];

        $menu[] = [
            'title' => 'About',
            'href' => '/about',
            'class' => 'desktop-menu-item',
            'has_children' => false
        ];
      

        return $menu;
    }

    /**
     * Build desktop menu structure from database items
     * Creates a single "Products" menu with all categories organized in 2 rows
     */
    private function buildDesktopMenu(array $rows): array
    {
        // Create the main Products menu item (like Header.php)
        $productsMenuItem = [
            'title' => 'Products',
            'href' => '#',
            'class' => 'menu-item-has-children mega-menu-wrap desktop-menu-item',
            'has_children' => true,
            'mega_menu' => true,
            'rows' => $rows,
            'view_all_text' => 'View all product Categories',
            'sidebar_images' => [
                [
                    'src' => '/img/navbar-img/book-metting.png',
                    'alt' => 'boot-meetting'
                ],
                [
                    'src' => '/img/navbar-img/contact-sells.png',
                    'alt' => 'contact-sells'
                ],
                [
                    'src' => '/img/navbar-img/request-catalog.png',
                    'alt' => 'request-catalog'
                ]
            ]
        ];

        $menu[] = $productsMenuItem;

        // Add other menu items (Projects, Blog, About) - these should come from different taxonomy_id
        // For now, add them as static items to match Header.php structure
        $menu[] = [
            'title' => 'Projects',
            'href' => '/projects',
            'class' => 'desktop-menu-item',
            'has_children' => false
        ];

        $menu[] = [
            'title' => 'Blog',
            'href' => '/blog',
            'class' => 'desktop-menu-item',
            'has_children' => false
        ];

        $menu[] = [
            'title' => 'About',
            'href' => '/about',
            'class' => 'desktop-menu-item',
            'has_children' => false
        ];

        return $menu;
    }

} 