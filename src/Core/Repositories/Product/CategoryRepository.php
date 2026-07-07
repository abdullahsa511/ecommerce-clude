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

    public function getCategoryBySlug(int $languageId, int $taxonomyId, int $siteId, string $slug): ?array
    {
        $category = $this->model
            ->join('taxonomy', 'taxonomy.taxonomy_id', '=', 'taxonomy_item.taxonomy_id')
            ->join('taxonomy_item_content', 'taxonomy_item_content.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
            ->where('taxonomy_item_content.language_id', '=', $languageId)
            ->where('taxonomy.taxonomy_id', '=', $taxonomyId)
            ->where('taxonomy.site_id', '=', $siteId)
            ->where('taxonomy_item_content.slug', '=', $slug)
            ->select(['taxonomy_item.*', 'taxonomy_item_content.name', 'taxonomy_item_content.slug', 'taxonomy_item_content.link', 'taxonomy_item_content.products_link', 'taxonomy_item_content.meta_title', 'taxonomy_item_content.meta_description', 'taxonomy_item_content.meta_keywords'])
            ->first();

        return $category?(array) $category->data??null:null;

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
                // 'taxonomy_item.image', 
                'taxonomy_item.slider_image as image', 
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
                $subMenuItems = [];
                foreach($children as $child){
                    $slug = strtolower(str_replace(' ', '-', $child['name']??''));
                    $l = '/products/' . $categorySlug . '/' . $slug;
                    $link = isset($child['taxonomyItemContent']) ? ($child['taxonomyItemContent']['products_link'] ?? $child['taxonomyItemContent']['link'] ?? $l) : $l;
                    $subMenuItems[] = [
                        'title' => $child['name'],
                        'link' => $link,
                        'sort_order' => $child['sort_order'] ?? 0
                    ];
                }
                usort($subMenuItems, function($a, $b) {
                    return $a['sort_order'] <=> $b['sort_order'];
                });
                $results['collapseItems'][$index]['subMenuItems'] = $subMenuItems;

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

        if(!$mainCategory){
            return [];
        }
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
           
            $products = $this->productRepository->getProductsByParentCategoryId($seatingCategoryId);
            $categoryProducts = $this->productRepository->getProductsByCategoryId($seatingCategoryId);

            $products = array_merge($products, $categoryProducts);
            usort($products, function($a, $b) {
                return $a['product_sort_order'] <=> $b['product_sort_order'];
            });
            $results = $this->productRepository->prepareProducts($products, true);
        } 

        usort($results['sections'], function($a, $b) {
            return $a['sort_order'] <=> $b['sort_order'];
        });
     
        return $results;
    }

    public function getCategoryDetailsComponentData(array $param): array
    {
        $languageId = $param['language_id'] ?? 1;
        $siteId = $param['site_id'] ?? 1;
        
        $results = [];
        $results['sections'] = [];
        
        // Get main seating category
        $mainCategory = $this->model
            ->join('taxonomy_item_content', 'taxonomy_item_content.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
            ->where('taxonomy_item_content.slug', '=', $param['category'])
            ->where('taxonomy_item.status', '>', 0)
            ->select(['taxonomy_item.taxonomy_item_id'])
            ->first();
        $mainCategory = (array) $mainCategory->data;
            
        if ($mainCategory) {
            $mainCategoryId = $mainCategory['taxonomy_item_id'];
            // new add 
            $subCategoryProducts = $this->productRepository->getProductsByParentCategoryId($mainCategoryId);
            $mainCategoryProducts = $this->productRepository->getProductsByCategoryId($mainCategoryId);

            $products = array_merge($subCategoryProducts, $mainCategoryProducts);
            usort($products, function($a, $b) {
                return $a['product_sort_order'] <=> $b['product_sort_order'];
            });
            $results = $this->productRepository->prepareProducts($products, true);
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
            $products = $this->productRepository->getProductsByParentCategoryId($workstationCategoryId);
            $categoryProducts = $this->productRepository->getProductsByCategoryId($workstationCategoryId); 

            $products = array_merge($products, $categoryProducts);
            $results = $this->productRepository->prepareProducts($products, true);
             
        }
        
        return $results;
    }
    
    public function getHeaderMenu(bool $is_logged_in): array
    {
        $results = [];
        // $results['topbar_message'] = 'Free shipping on apparel and gear over 75 USD';
        $results['desktop_logo'] = '/img/logo_black.png';
        $results['mobile_logo'] = '/img/logo-white.png';
        $results['pinboard_icon'] = '/img/pinboard-icon.svg';

        // Get menu items for taxonomy_id = 1 (header menu)
        $menuItems = $this->getMenuItemsFromDatabase(1);
        $mobileMenu = [
            // [
            //     'title' => 'Home',
            //     'href' => '/',
            //     'class' => 'menu-item-has-children mega-menu-wrap',
            //     'has_children' => false,
            //     'mega_menu' => true,
            //     'children' => [
                  
            //     ]
            // ]
            [
                'title' => 'About',
                'href' => '/about',
                'class' => 'desktop-menu-item',
                'has_children' => false
            ],
            [
                'title' => 'Blog',
                'href' => '/blog',
                'class' => 'desktop-menu-item',
                'has_children' => false
            ],
            [
                'title' => 'Projects',
                'href' => '/projects',
                'class' => 'desktop-menu-item',
                'has_children' => false
            ],
            [
                'title' => 'Resources',
                'href' => '/resources',
                'class' => 'desktop-menu-item',
                'has_children' => false
            ]
            
        ];

        [$destopMenuRows, $mobileMenu] = $this->buildMenus($menuItems, $mobileMenu);

        $mobileMenu = $this->buildMobileMenu($mobileMenu, $is_logged_in );

        if($is_logged_in){
            $results['account_menu'] = [
                [
                    'title' => 'Account',
                    'href' => '/account/virtual-pinboards',
                    'id' => 'account-button',
                ],
                [
                    'title' => 'Logout',
                    'href' => '/logout',
                    'id' => 'logout-button',
                ]
            ];
        } else{
            $results['account_menu'] = [
                [
                    'title' => 'Login',
                    'href' => '/login',
                    'id' => 'login-button',
                ],
                [
                    'title' => 'Sign Up',
                    'href' => '/signup',
                    'id' => 'signup-button',
                ]
            ];
        }
        
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
            'taxonomy_item.label_name',
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
                    'label_name' => $category['label_name'],
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
                    $rows[$category['taxonomy_item_id']]['style'] = 'flex:.95';
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
        //    $row['href'] = '#';
           $mobileMenu[] = $row;
        }
        return [$destopMenuRows, $mobileMenu];
    }

    /**
     * Build mobile menu structure from database items
     */
    private function buildMobileMenu($menu, $is_logged_in = null): array
    {
        foreach ($menu as &$menuItem) {
            if (!empty($menuItem['has_children']) && !empty($menuItem['href'])) {
                $menuItem['children'] = $menuItem['children'] ?? [];
                $menuItem['children'][] = [
                    'title' => $menuItem['title'] ?? '',
                    'label_name' => 'View All',
                    'href' => $menuItem['href'],
                    'class' => "th-link-text"
                ];
            }
        }
        unset($menuItem);

        $menu[] = [
            'title' => 'Contact Us',
            'href' => '/contact-us',
            'class' => 'desktop-menu-item',
            'has_children' => false
        ];
        if($is_logged_in){
            $menu[] = [
                'title' => 'Account',
                'href' => '/account/virtual-pinboards',
                'class' => 'desktop-menu-item',
                'has_children' => false
            ];
            $menu[] = [
                'title' => 'Logout',
                'href' => '/logout',
                'class' => 'desktop-menu-item',
                'has_children' => false
            ];
        } else{
            $menu[] = [
                'title' => 'Login',
                'href' => '/login',
                'class' => 'desktop-menu-item',
                'has_children' => false
            ];
            $menu[] = [
                'title' => 'Sign Up',
                'href' => '/signup',
                'class' => 'desktop-menu-item',
                'has_children' => false
            ];
        }
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