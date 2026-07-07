<?php

declare(strict_types=1);

namespace App\Core\Repositories\Page;

use PDO;
use App\Core\Models\Post\Post;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Models\Post\PostContent;
use App\Core\Models\Post\PostData;
use App\Core\Models\Post\PostMeta;
use App\Core\Models\Post\PostContentMeta;
use App\Core\Models\Post\PostContentRevision;
use App\Core\Models\Post\PostToSite;
use App\Core\Models\PostCategory\PostToTaxonomyItem;
use App\Core\Models\PostCategory\PostToTaxonomy;
use App\Core\Models\PostCategory\TaxonomyItem;
use App\Core\Models\Post\Comment;
use App\Core\Utilities\Debug;

use function App\Core\System\utils\app;
use function App\Core\System\utils\env;

use League\Csv\Reader;
use Exception;
use App\Core\Validation\PostDataValidation;
use App\Core\Validation\PostImageDataValidation;

class PageRepository extends BaseRepository implements PageRepositoryInterface
{
    private PostContent $postContent;
    // private AuthService $authService;
    private PostMeta $postMetadata;
    private PostContentMeta $postContentMeta;
    private PostContentRevision $postContentRevision;
    private PostToSite $postToSite;
    private PostToTaxonomyItem $postToTaxonomyItem;
    private PostToTaxonomy $postToTaxonomy;
    private Comment $comment;

    public function __construct(
        PDO $db,
        PostContent $postContent,
        // AuthService $authService,
        PostMeta $postMetadata,
        PostContentMeta $postContentMeta,
        PostContentRevision $postContentRevision,
        PostToSite $postToSite,
        PostToTaxonomyItem $postToTaxonomyItem,
        PostToTaxonomy $postToTaxonomy,
        Comment $comment,
    ) {
        parent::__construct($db, 'post', Post::class);
        $this->postContent = $postContent;
        $this->postContent->setDb($db);
        $this->postMetadata = $postMetadata;
        $this->postMetadata->setDb($db);
        $this->postContentMeta = $postContentMeta;
        $this->postContentMeta->setDb($db);
        $this->postContentRevision = $postContentRevision;
        $this->postContentRevision->setDb($db);
        $this->postToSite = $postToSite;
        $this->postToSite->setDb($db);
        $this->postToTaxonomyItem = $postToTaxonomyItem;
        $this->postToTaxonomyItem->setDb($db);
        $this->postToTaxonomy = $postToTaxonomy;
        $this->postToTaxonomy->setDb($db);
        $this->comment = $comment;
        $this->comment->setDb($db);
    }

     /**
     * {@inheritDoc}
     */
    public function get(?int $postId = null, ?string $slug = null, array $options = []): ?Post
    {
        $query = $this->model->where('post.type', '=', 'page');
        // ->where('post.status_id', '!=', 1)
        // ->where('post.status', '!=', 'Draft');

        // Load relationships
        $query->with(['admin', 'postContent']);

        if ($postId) {
            $query->where('post_id', '=', $postId);
        } elseif ($slug) {
            $query->where('postContent.slug', '=', $slug);
        } else {
            return null;
        }

        // Apply language filter
        if (!empty($options['language_id'])) {
            $query->where('postContent.language_id', '=', $options['language_id']);
        }

        return $query->first();
    }

    /**
     * Get all pages with admin information.
     *
     * @return array
     */
    public function findAll(): array
    {
        $result = $this->model
            ->join('admin', 'post.admin_id', '=', 'admin.admin_id')
            ->join('post_content', 'post_content.post_id', '=', 'post.post_id')
            ->where('post.type', '=', 'page')
            ->select([
                'post.*',
                'admin.display_name',
                'admin.username',
                'admin.first_name',
                'admin.last_name',
                'post_content.name as post_content_name',
                'post_content.slug as post_content_slug',
                'post_content.meta_title as post_content_meta_title',
                'post_content.excerpt as post_content_excerpt',
                'post_content.meta_description as post_content_meta_description',
                'post_content.meta_keywords as post_content_meta_keywords',
                'post_content.language_id'
            ])
            ->orderBy('post.post_id', 'DESC')
            ->findAll();

            foreach ($result as &$row) { // &$row 
                $row['postContent'] = [
                    'post_id' => $row['post_id'],
                    'language_id' => $row['language_id'] ?? 1,
                    'name' => $row['post_content_name'] ?? '',
                    'slug' => $row['post_content_slug'] ?? '',
                    'meta_title' => $row['post_content_meta_title'] ?? '',
                    'excerpt' => $row['post_content_excerpt'] ?? '',
                    'meta_description' => $row['post_content_meta_description'] ?? '',
                    'meta_keywords' => $row['post_content_meta_keywords'] ?? ''
                ];
            }

        return $result ?? [];
    }

    /**
     * Delete a page and all its related data.
     *
     * @param int $postId
     * @return bool
     */
    public function deletePage(int $postId): bool
    {
        try {
            // Start transaction
            $this->db->beginTransaction();

            // Delete comments first (they reference post_id)
            $this->comment->deleteWhere(['post_id' => $postId]);

            // Delete post content revisions
            $this->postContentRevision->deleteWhere(['post_id' => $postId]);

            // Delete post content meta
            $this->postContentMeta->deleteWhere(['post_id' => $postId]);

            // Delete post meta
            $this->postMetadata->deleteWhere(['post_id' => $postId]);

            // Delete post content
            $this->postContent->deleteWhere(['post_id' => $postId]);

            // Delete post to site relationships
            $this->postToSite->deleteWhere(['post_id' => $postId]);

            // Delete post to taxonomy item relationships
            $this->postToTaxonomyItem->deleteWhere(['post_id' => $postId]);

            // Delete post to taxonomy relationships
            $this->postToTaxonomy->deleteWhere(['post_id' => $postId]);

            // Delete post to menu relationships (if table exists)
            $this->deletePostToMenuRelationships($postId);

            // Finally delete the main post record
            $deleted = $this->model->deleteWhere(['post_id' => $postId, 'type' => 'page']);

            // Commit transaction
            $this->db->commit();

            return true;
        } catch (\Exception $e) {
            // Rollback transaction on error
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Delete post to menu relationships.
     * This method handles the case where the table might not exist.
     *
     * @param int $postId
     * @return void
     */
    private function deletePostToMenuRelationships(int $postId): void
    {
        try {
            $sql = "DELETE FROM post_to_menu WHERE post_id = :post_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['post_id' => $postId]);
        } catch (\PDOException $e) {
            // Table might not exist, ignore the error
            // Log the error if needed: error_log("post_to_menu table not found: " . $e->getMessage());
        }
    }

    /**
     * Delete multiple pages and all their related data.
     *
     * @param array $postIds
     * @return int Number of deleted posts
     */
    public function deleteMultiplePages(array $postIds): int
    {
        if (empty($postIds)) {
            return 0;
        }

        try {
            // Start transaction
            $this->db->beginTransaction();

            $deletedCount = 0;

            foreach ($postIds as $postId) {
                if ($this->deletePage((int)$postId)) {
                    $deletedCount++;
                }
            }

            // Commit transaction
            $this->db->commit();

            return $deletedCount;
        } catch (\Exception $e) {
            // Rollback transaction on error
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getBlogSlider(int $postId, array $fields, int $limit = 4): array
    {
        $result = $this->model
        ->join('post_content', 'post_content.post_id', '=', 'post.post_id')
        ->join('post_to_taxonomy_item', 'post_to_taxonomy_item.post_id', '=', 'post.post_id')
        ->join('taxonomy_item_content as category', 'category.taxonomy_item_id', '=', 'post_to_taxonomy_item.taxonomy_item_id')
        ->orderBy('post.post_id', 'DESC')
  
        ->select($fields)
        ->limit($limit);
        
        $result = $result->findAll(false);
       
        return $result;
    }
    public function insertPageData(array $data)
    {
        // Convert the raw array into PostData object
        $postData = new PostData($data);

        // Convert PostData to array for inserting into `post` table
        $postDataArray = $postData->toArray();

        // Insert into `post` table
        $post = $this->model->create($postDataArray);

        // Handle content data
        if (!empty($data['postContent']) && is_array($data['postContent'])) {
            $contentData = $data['postContent'];
            $contentData['post_id'] = $post->post_id;

            // Insert content into `post_content` table
            $this->postContent->insert([$contentData]);

            // Attach content to returned post object
            $post->content = $contentData;
        } else {
            $post->content = null;
        }

        // Insert metadata into `post_meta` table
        $metadata = $postData->getPostMetadata($post->post_id);
        if (!empty($metadata)) {
            $this->postMetadata->upsert($metadata, ['post_id', 'namespace', 'key']);
        }

        return $post;
    }


    public function createPage(PostData $postData): Post
    {
        $postDataArray = $postData->toArray();
        $post = $this->model->create($postDataArray);

        $content = $postData->getPostContent();
        if ($content) {
            $content['post_id'] = $post->post_id;
            $this->postContent->insert([$content]);
            $post->content = $content;
        }

        $metadata = $postData->getPostMetadata($post->post_id);
        if (count($metadata) > 0) {
            $this->postMetadata->upsert($metadata, ['post_id', 'namespace', 'key']);
        }
        return $post;
    }

    public function updatePageData($data)
    {
        $postData = new PostData($data);

        $postDataArray = $postData->toArray();
        $post = $this->model->find($postDataArray['post_id']);
        $post = $post->update($postDataArray);

        // Handle content data
        if (!empty($data['postContent']) && is_array($data['postContent'])) {
            $contentData = $data['postContent'];
            $contentData['post_id'] = $post->post_id;

            $this->postContent->upsert([$contentData], ['post_id', 'language_id']);
            $post->content = $contentData;
        } else {
            $post->content = null;
        }

        // Insert metadata into `post_meta` table
        $metadata = $postData->getPostMetadata($post->post_id);
        if (count($metadata) > 0) {
            $this->postMetadata->upsert($metadata, ['post_id', 'namespace', 'key']);
        }
        return $post;
    }
    public function updatePage(PostData $postData): Post
    {
        $postDataArray = $postData->toArray();
        $post = $this->model->find($postDataArray['post_id']);
        $post = $post->update($postDataArray);

        $content = $postData->getPostContent();
        if ($content) {
            $content['post_id'] = $post->post_id;
            $this->postContent->upsert([$content], ['post_id', 'language_id']);
            $post->content = $content;
        }

        $metadata = $postData->getPostMetadata($post->post_id);
        if (count($metadata) > 0) {
            $this->postMetadata->upsert($metadata, ['post_id', 'namespace', 'key']);
        }
        return $post;
    }

    public function showPage(int $postId): Post
    {
        $post = $this->model->where('post_id', '=', $postId)
            ->with(['postContent' => function ($query) {
                return $query->where('language_id', '=', 1)->select(['post_id', 'name', 'slug', 'content', 'excerpt', 'meta_keywords', 'meta_description']);
            }, 'meta' => function ($query) {
                return $query->select(['post_id', 'namespace', 'key', 'value']);
            }])
            ->first();

        return $post;
    }
    // upload all images in the page
    public function updatePageImges(array $data, string $property, int $page_id): bool
    {
        $page = $this->model->where('post_id', '=', $page_id)->first();
        if (!$page) {
            return false; // project not found
        }

        $config = ''; // ' app('config');
        $imageServer = ''; // $config['APP_URL'];
        $dataobj = $data;
        $imageServer = ''; // $config['APP_URL'];

        $img = [];
        foreach ($dataobj as $item) {
            $img[] = [
                'page_id' => $page_id,
                'name' => $item['name'] ?? '',
                'size' => $item['size'] ?? '',
                'type' => $item['type'] ?? '',
                'image' => $item['image'] ?? '',
                'status' => isset($item['status']) && is_array($item['status'])
                    ? $item['status']
                    : ['name' => 'Uploaded', 'severity' => 'success'],
                'media_id' => null,
                'objectURL' => $imageServer . ($item['objectURL'] ?? ''),
                'created_at' => date('Y-m-d H:i:s'),
                'product_id' => null,
                'description' => $item['description'] ?? '',
                'post_image_id' => null,
                'product_image_id' => null,
                'page_image_id' => $page_id,
            ];
        }
        $imgJson = json_encode($img);
        $this->db->beginTransaction();
        try {
            $page->update([$property => $imgJson]);
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function deletePageImage(string $path, string $property, int $pageId): bool
    {
        // Fetch project
        $project = $this->model->where('post_id', '=', $pageId)->first();

        if (!$project) {
            return false; // Project not found
        }

        // Resolve physical file path (safe)
        $filePath = $_SERVER['DOCUMENT_ROOT'] . $path;

        // Delete file if exists
        if (is_file($filePath)) {
            @unlink($filePath);
        }

        // Update the project's image property — empty array to reset
        $project->update([
            $property => json_encode([]),
        ]);

        return true;
    }


    // ================================= start page header & body content ================================
    public function getPageBodyForComponent(array $params) : array
    {
        $model = 'post';
        if (isset($params['model']) && $model == $params['model']) {
            $query = $this->model;
            if(isset($params['joins']) && is_array($params['joins'])) {
                foreach($params['joins'] as $join){
                    $query->join($join[0], $join[1], $join[2], $join[3]);
                }
            }
            $query->with(['postContent' => function ($query) {
                return $query->where('language_id', '=', 1)->select(['post_id', 'name', 'slug', 'content', 'excerpt', 'meta_keywords', 'meta_description']);
            }, 'meta' => function ($query) {
                return $query->select(['post_id', 'namespace', 'key', 'value']);
            }]);

            $query->with(['images' => function ($query) {
                return $query->select(['post_image_id', 'post_id', 'image_link', 'image', 'sort_order', 'status', 'way_points']);
            }, 'meta' => function ($query) {
                return $query->select(['post_id', 'namespace', 'key', 'value']);
            }]);


            $query->where('post.type', '=', 'page')
            ->where('post_content.slug', '=', $params['slug']);
            $result = $query->findAll(false);

            $post = $result[0]??[];
            
            $post['postContent'] = json_decode($post['postContent'], true);
            $post['images'] = json_decode($post['images'], true);


            $galleryThumb = [];
            foreach ($post['images'] as $index => $result) {
                if(!isset($result['image']) && !isset($result[0]['image'])) continue;
                $imageUrl = $result['image'][0]['objectURL']??$result['image'][0]['objectURL'];
                
                $isActive = ($index === 0) ? ' active' : '';
                $showClass = ($index === 0) ? ' active show' : '';
                
                $galleryThumb[] = [
                    'thumb_image' => $imageUrl,
                    'image' => $imageUrl,
                    'thumb_class' => ' th-gallery-thumb' . $isActive,
                    'class' => 'tab-pane fade th-gallery-img' . $showClass,
                    'thumb_id' => 'img-' . ($index + 1) . '-tab',
                    'id' => 'img-' . ($index + 1),
                    'target' => '#img-' . ($index + 1)
                ];
            }


            $post['galleryItems'] = $galleryThumb;

            // feature_image_thumb
            $post['feature_image_thumb'] = json_decode($post['feature_image_thumb'], true) ?? [];
            if(is_string($post['feature_image_thumb'])) $post['feature_image_thumb'] = json_decode($post['feature_image_thumb'], true) ?? [];
            $post['feature_image_thumb'] = $post['feature_image_thumb'][0]['image'] ?? '/img/project-detail/hero.png';

            //imgage_banner
            $post['image_banner'] = json_decode($post['image_banner'], true) ?? [];
            if(is_string($post['image_banner'])) $post['image_banner'] = json_decode($post['image_banner'], true) ?? [];
            $post['image_banner'] = $post['image_banner'][0]['image'] ?? '/img/project-detail/hero.png';


            // feature_image
            $post['feature_image'] = json_decode($post['feature_image'], true) ?? [];
            if(is_string($post['feature_image'])) $post['feature_image'] = json_decode($post['feature_image'], true) ?? [];
            $post['feature_image'] = $post['feature_image'][0]['objectURL'] ?? '/img/project-detail/hero.png';

            // image_thumb
            $post['image_thumb'] = json_decode($post['image_thumb'], true) ?? [];
            if(is_string($post['image_thumb'])) $post['image_thumb'] = json_decode($post['image_thumb'], true) ?? [];
            $post['image_thumb'] = $post['image_thumb'][0]['objectURL'] ?? '/img/project-detail/hero.png';


            $post['created_at'] = date('F d, Y', strtotime($post['created_at']));
            $post['updated_at'] = date('F d, Y', strtotime($post['updated_at']));

            return $post;
        }
        return [];
       
    }

    public function getPageHeaderForComponent(array $params) : array
    {

        $results = [];
        $results['topbar_message'] = 'Free shipping on apparel and gear over 75 USD';
        $results['desktop_logo'] = '/img/logo_black.png';
        $results['mobile_logo'] = '/img/logo-white.png';
        $results['pinboard_icon'] = '/img/pinboard-icon.svg';

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

    private function getMenuItemsFromDatabase(int $taxonomyId): array
    {
        // $model = 'taxonomy_item_content';
        // $query = $this->model //taxonomy_item

        $taxonomyItem = new TaxonomyItem();
        $taxonomyItem->setDb($this->db);
        $query = $taxonomyItem
        ->join('taxonomy_item_content', 'taxonomy_item_content.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
        ->where('taxonomy_item.taxonomy_id', '=', $taxonomyId)
        ->where('taxonomy_item_content.language_id', '=', 1)
        ->where('taxonomy_item.status', '>', 0)
        ->orderBy('taxonomy_item.sort_order', 'ASC')
        ->orderBy('taxonomy_item.taxonomy_item_id', 'ASC');

        return $query->select([
            'taxonomy_item.taxonomy_item_id',
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
                $rows[$category['taxonomy_item_id']]['class'] = 'sub-title';
                $rows[$category['taxonomy_item_id']]['links'][0] = [
                    'title' => $category['name'],
                    'href' => $category['link']
                ];
                
            }
        }
        $destopMenuRows = array_chunk($rows, 5);
        foreach($rows as $row){
           $row['children'] = $row['links'];
           unset($row['links']);
           $row['has_children'] = true;
           $row['class'] = 'menu-item-has-children';
           $row['href'] = '#';
           $mobileMenu[] = $row;
        }
        return [$destopMenuRows, $mobileMenu];
    }
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
    private function buildDesktopMenu(array $rows): array
    {
        // Create the main Products menu item (like Header.php)
        // $productsMenuItem = [
        //     'title' => 'Products',
        //     'href' => '#',
        //     'class' => 'menu-item-has-children mega-menu-wrap desktop-menu-item',
        //     'has_children' => true,
        //     'mega_menu' => true,
        //     'rows' => $rows,
        //     'view_all_text' => 'View all product Categories',
        //     'sidebar_images' => [
        //         [
        //             'src' => '/img/navbar-img/book-metting.png',
        //             'alt' => 'boot-meetting'
        //         ],
        //         [
        //             'src' => '/img/navbar-img/contact-sells.png',
        //             'alt' => 'contact-sells'
        //         ],
        //         [
        //             'src' => '/img/navbar-img/request-catalog.png',
        //             'alt' => 'request-catalog'
        //         ]
        //     ]
        // ];

        // $menu[] = $productsMenuItem;

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
    // ================================= end page header & body content ================================


    // ================================= Start pageImport data csv  ================================
    public function importPages(string $csv_file): array
    {
        $reader = Reader::createFromPath($csv_file, 'r');
        $reader->setHeaderOffset(0);
        
        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }
            
        $records = $reader->getRecords();

        $validPosts = [];
        $validPostContents = [];
        $invalid = [];
        $updated = [];
        $processed = [];
        $defaultFields = $this->getDefaultFields($headers);

        foreach ($records as $offset => $record) {
            try {
                $record = isset($record['post_id']) && $record['post_id'] ? $record : array_merge($defaultFields, $record);
                // $record['type'] = 'page';
                $mediaPaths = [
                    'feature_image_path' => '/media/Pages/Feature/',
                    'image_banner_path' => '/media/Pages/Banner/',
                    'image_thumb_path' => '/media/Pages/Thumbnails/',
                    'image_main_path' => '/media/Pages/Main/',
                ];
                
                $validator = new PostDataValidation($record, $mediaPaths);
                $validated = $validator->validate();

                if ($validated === false) {
                    $invalid[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => $validator->getErrors()
                    ];
                    continue;
                }

                $post = (array) $validated->post;
                $content = (array) $validated->content;

                $unique = $post['post_id'] ?? ($content['slug'] ?? md5(json_encode($record)));
                if (in_array($unique, $processed, true)) {
                    $updated[] = ['row' => $offset + 2, 'data' => $record];
                    continue;
                }

                if(count($post) > 0) $validPosts[] = $post;
                if(count($content) > 0) $validPostContents[] = $content;
                $processed[] = $unique;
            } catch (Exception $e) {
                $invalid[] = [
                    'row' => $offset + 2,
                    'data' => $record,
                    'errors' => ['processing_error' => $e->getMessage()]
                ];
                continue;
            }
        }

        $inserted = 0;
        $insertedContent = 0;
        if (!empty($validPosts)) {
            try {
                $this->db->beginTransaction();
                try{
                    $this->model->upsert($validPosts, ['title']);
                }catch(Exception $e){
                    $this->db->rollBack();
                    throw new Exception("Failed to insert posts: " . $e->getMessage());
                }

                if(count($validPostContents) > 0){
                    $alltitles = array_column($validPosts, 'title');
                    $postIds = $this->model->select(['title', 'post_id'])->whereIn('title', $alltitles)->findAll();
                    $postIdsData = [];
                    $finalPostContentData = [];
                    foreach($postIds as $postId){
                        $postIdsData[$postId['title']] = $postId['post_id'];
                    }
                    
                    foreach($validPostContents as $postContent){
                        if(isset($postIdsData[$postContent['name']])){
                            $postContent['post_id'] = $postIdsData[$postContent['name']];
                            $finalPostContentData[] = $postContent;
                        }
                    }
                    $this->postContent->upsert($finalPostContentData, ['slug','language_id']);
                }

                $this->db->commit();
            } catch (Exception $e) {
                $this->db->rollBack();
                throw new Exception("Failed to insert posts: " . $e->getMessage());
            }
        }

        return [
            'success' => true,
            'valid_records' => count($validPosts),
            'invalid_records' => count($invalid),
            'updated_records' => count($updated),
            'inserted_count' => $inserted,
            'inserted_content_count' => $insertedContent,
            'valid_data' => $validPosts,
            'valid_content_data' => $validPostContents,
            'invalid_data' => $invalid,
            'updated_data' => $updated
        ];
    }

    private function getDefaultFields(array $headers): array
    {
        $defaults = [];
        foreach ($headers as $h) { $defaults[$h] = null; }
        // post defaults
        $defaults['site_id'] = 1;
        $defaults['admin_id'] = 1;
        $defaults['status'] = 'draft';
        $defaults['comment_status'] = 'open';
        $defaults['password'] = "1234";
        $defaults['parent'] = 0;
        $defaults['sort_order'] = 0;
        $defaults['type'] = 'page';
        $defaults['template'] = 'default';
        $defaults['comment_count'] = 0;
        $defaults['views'] = 0;
        $defaults['description'] = '';
        $defaults['description_one'] = '';
        $defaults['description_two'] = '';
        $defaults['description_three'] = null;
        $defaults['feature_image_thumb'] = null;
        $defaults['feature_image'] = null;
        $defaults['image_banner'] = null;
        $defaults['image_thumb'] = null;
        $defaults['main_image_one'] = null;
        $defaults['main_image_two'] = null;
        $defaults['is_featured'] = 0;
        // content defaults
        $defaults['language_id'] = 1;
        $defaults['name'] = '';
        $defaults['slug'] = '';
        $defaults['excerpt'] = '';
        $defaults['meta_title'] = 'Meta Title';
        $defaults['meta_description'] = 'Meta Description';
        $defaults['meta_keywords'] = 'Meta Keywords';
        $defaults['link_text'] = '';
        
        return $defaults;
    }

    // Image Import ====================================
    public function importPageImages(string $csv_file): array
    {
        $reader = Reader::createFromPath($csv_file, 'r');
        $reader->setHeaderOffset(0);
        
        // Get CSV headers first
        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }
        $defaultFields = $this->getDefaultImageFields($headers);
        $records = $reader->getRecords();

        $validImages = [];
        $invalidImages = [];
        $updatedImages = [];
        $processedIdentifiers = [];

        foreach ($records as $offset => $record) {
            try {
                // Ensure all required fields exist with default values based on CSV headers
                // Set default values for image-specific fields
               
                if(isset($record['status']) && !empty($record['status'])){
                    $record['status'] = json_encode([
                        "name" => ($record['status'] == 'show' ? 'Uploaded' : 'Pending'),
                        "severity" => ($record['status'] == 'show' ? 'success' : 'info')
                    ]);
                }
                $way_points = [];
                for($i = 1; $i <= 5; $i++){
                    $way_point_name = 'way_point_'.$i.'_name';
                    $way_point_link = 'way_point_'.$i.'_link';
                    if(isset($record[$way_point_name]) && !empty($record[$way_point_name])
                    && isset($record[$way_point_link]) && !empty($record[$way_point_link])){
                        $way_points[] = [
                            'name' => $record[$way_point_name],
                            'link' => $record[$way_point_link]
                        ];
                    }
                    unset($record[$way_point_name]);
                    unset($record[$way_point_link]);
                }
                $record['way_points'] = json_encode($way_points);
                
                
                // Convert image_link to image JSON format
                if (isset($record['image_link']) && !empty($record['image_link'])) {
                    $record['image_link'] = $record['image_link'];
                    $record['image'] = $this->convertImageToJsonFormat($record['image_link'], 'Gallery');
                } else {
                    // Ensure image field is always set with a default value
                    $record['image'] = json_encode([]);
                }

                $record = isset($record['post_image_id']) && $record['post_image_id'] ? $record : array_merge($defaultFields, $record);
                
                
                // Create validation instance
                $imageValidation = new PostImageDataValidation($record);
                
                // Validate the data
                $validationResult = $imageValidation->validate();
            
                if ($validationResult === false) {
                    // Data is invalid
                    $invalidImages[] = [
                        'row' => $offset + 2, // +2 because CSV is 1-indexed and we have header
                        'data' => $record,
                        'errors' => $imageValidation->getErrors()
                    ];
                    continue;
                }
                
                // Check for duplicates using unique identifier
                $uniqueIdentifier = $imageValidation->getUniqueIdentifier();
                if (in_array($uniqueIdentifier, $processedIdentifiers)) {
                    $updatedImages[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'identifier' => $uniqueIdentifier
                    ];
                    continue;
                }
                
                // Check if image already exists in database - if exists, mark as update
                if ($this->isPostImageDuplicate($record)) {
                    $updatedImages[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'reason' => 'Post image already exists in database - will be updated'
                    ];
                    // Continue processing to include in validImages for upsert
                }
                
                // Check if referenced project exists
                if (empty($record['post_id']) || !is_numeric($record['post_id'])) {
                    $invalidImages[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => ['post_id' => "Invalid or missing post_id"]
                    ];
                    continue;
                }
                
                if (!$this->doesPostExist((int)$record['post_id'])) {
                    $invalidImages[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => ['post_id' => "Referenced post with ID {$record['post_id']} does not exist"]
                    ];
                    continue;
                }
                
                // Data is valid - normalize to ensure consistent structure
                // $normalizedData = $this->normalizeImageData($validationResult, $headers);
                $validImages[] = $validationResult;
                $processedIdentifiers[] = $uniqueIdentifier;
            } catch (Exception $e) {
                // Log the error and add to invalid images
                error_log("Error processing image row " . ($offset + 2) . ": " . $e->getMessage());
                $invalidImages[] = [
                    'row' => $offset + 2,
                    'data' => $record,
                    'errors' => ['processing_error' => $e->getMessage()]
                ];
                continue;
            }
        }
        
        $insertedCount = 0;
        if (!empty($validImages)) {
            try {
                $this->db->beginTransaction();
                
                $uniqueKeys = ['post_id', 'image_link'];
                $insertedCount = $this->postImage->upsert($validImages, $uniqueKeys);
                
                $this->db->commit();
            } catch (Exception $e) {
                $this->db->rollBack();
                throw new Exception("Failed to insert post images: " . $e->getMessage());
            }
        }
        
        return [
            'success' => true,
            'total_records' => iterator_count($records),
            'valid_records' => count($validImages),
            'invalid_records' => count($invalidImages),
            'updated_records' => count($updatedImages),
            'inserted_count' => $insertedCount,
            'valid_data' => $validImages,
            'invalid_data' => $invalidImages,
            'updated_data' => $updatedImages
        ];
    }

    private function getDefaultImageFields(array $headers): array
    {
        $defaults = [];
        $defaults['sort_order'] = 1;
        $defaults['status'] = json_encode([
            "active" => true,
            "featured" => false
        ]);
        $defaults['way_points'] = json_encode([]);
        $defaults['image_link'] = '';
        $defaults['image'] = json_encode([]);
        return $defaults;
    }

    private function convertImageToJsonFormat(string $imageValue, string $subFolder = ""): string
    {
        $path = null;
        if ($imageValue === '' || $imageValue === null) { return '[]'; }
        $imageValue = is_string($imageValue) ? $imageValue : (string)$imageValue;
        if ($this->isValidJson($imageValue)) { return $imageValue; }
        if (!str_contains($imageValue, '/media/Blogs/')) { $path = "/media/Blogs/"; }
        if(!!$subFolder && !empty($subFolder)) { $path .= $subFolder.'/'; }
        if($path && !empty($path)) { $imageValue = $path.$imageValue; }
        $data = [[ 'id'=>null,'file'=>['name'=>basename($imageValue),'size'=>0,'type'=>'image/jpeg','error'=>0,'tmp_name'=>$imageValue,'full_path'=>basename($imageValue)],'name'=>basename($imageValue),'size'=>0,'type'=>'image/jpeg','image'=>$imageValue,'status'=>['name'=>'Uploaded','severity'=>'success'],'media_id'=>null,'objectURL'=>$imageValue,'created_at'=>'','description'=>'','post_image_id'=>null,'project_image_id'=>null ]];
        return json_encode($data) ?: '[]';
    }

    private function isPostImageDuplicate(array $record): bool
    {
        // return false;
        if (!empty($record['post_id']) && !empty($record['image_link'])) {
            $existingImage = $this->postImage->where('post_id', '=', $record['post_id'])
                ->where('image_link', '=', $record['image_link'])
                ->first();
            if ($existingImage) {
                return true;
            }
        }
        
        if (!empty($record['post_image_id'])) {
            $existingImage = $this->postImage->where('post_image_id', '=', $record['post_image_id'])->first();
            if ($existingImage) {
                return true;
            }
        }
        
        return false;
    }

    private function doesPostExist(int $postId): bool
    {
        $post = $this->model->where('post_id', '=', $postId)->first();
        return $post !== null;
    }

    private function isValidJson(string $string): bool
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
    // ================================= End pageImport data csv  ================================

}
