<?php

declare(strict_types=1);

namespace App\Core\Repositories\Post;

use App\Core\Models\Media\Media;
use PDO;
use App\Core\Models\Post\Post;
use App\Core\Models\PostCategory\PostToTaxonomyItem;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Models\Post\PostContent;
use App\Core\Models\Post\PostToSite;
use App\Core\Models\Post\PostContentRevision;
use App\Core\Models\Post\PostData;
use App\Core\Models\Post\PostMeta;
use App\Core\Models\PostCategory\TaxonomyItem;
use App\Core\Models\Post\PostImage;

use function App\Core\System\utils\app;
use function App\Core\System\utils\env;
use function App\Core\System\utils\htmlToPlainText;

use League\Csv\Reader;
use Exception;
use App\Core\Validation\PostDataValidation;
use App\Core\Validation\PostImageDataValidation;

class PostRepository extends BaseRepository implements PostRepositoryInterface
{
    private PostContent $postContent;
    private PostToTaxonomyItem $postToTaxonomyItem;
    private PostToSite $postToSite;
    private PostContentRevision $postContentRevision;
    // private AuthService $authService;
    private PostMeta $postMetadata;
    private TaxonomyItem $taxonomyItem;
    private PostImage $postImage;
    private Media $media;

    public function __construct(
        PDO $db,
        PostContent $postContent,
        PostToTaxonomyItem $postToTaxonomyItem,
        PostToSite $postToSite,
        PostContentRevision $postContentRevision,
        TaxonomyItem $taxonomyItem,
        // AuthService $authService,
        PostMeta $postMetadata,
        PostImage $postImage,
        Media $media
    ) {
        parent::__construct($db, 'post', Post::class);
        $this->postContent = $postContent;
        $this->postContent->setDb($db);
        $this->postToTaxonomyItem = $postToTaxonomyItem;
        $this->postToTaxonomyItem->setDb($db);
        $this->postToSite = $postToSite;
        $this->postToSite->setDb($db);
        $this->postContentRevision = $postContentRevision;
        $this->postContentRevision->setDb($db);
        // $this->authService = $authService;
        $this->postMetadata = $postMetadata;
        $this->postMetadata->setDb($db);
        $this->taxonomyItem = $taxonomyItem;
        $this->taxonomyItem->setDb($db);
        $this->postImage = $postImage;
        $this->postImage->setDb($db);
        $this->media = $media;
        $this->media->setDb($db);
    }

    public function importPosts(string $csv_file): array
    {
        $reader = Reader::createFromPath($csv_file, 'r');
        $reader->setHeaderOffset(0);

        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }

        $records = $reader->getRecords();

        $validPosts = [];
        $showPosts = [];
        $validPostContents = [];
        $invalid = [];
        $updated = [];
        $processed = [];
        $defaultFields = $this->getDefaultFields($headers);

        foreach ($records as $offset => $record) {
            try {
                // $record = $this->ensureAllPostFieldsExist($record, $headers);

                // $record = $this->sanitizePostTextFields($record);

                $record = isset($record['post_id']) && $record['post_id'] ? $record : array_merge($defaultFields, $record);
                $mediaPaths = [
                    'feature_image_path' => '/media/Blogs/Feature/',
                    'image_banner_path' => '/media/Blogs/Banner/',
                    'image_thumb_path' => '/media/Blogs/Thumbnails/',
                    'image_main_path' => '/media/Blogs/Main/',
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

                if (count($post) > 0) $validPosts[] = $post;
                if (count($post) > 0) $showPosts[] = $record;
                if (count($content) > 0) $validPostContents[] = $content;
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
                $this->model->upsert($validPosts, ['title']);

                if (count($validPostContents) > 0) {
                    $alltitles = array_column($validPosts, 'title');
                    $postIds = $this->model->select(['title', 'post_id'])->whereIn('title', $alltitles)->findAll();
                    $postIdsData = [];
                    $finalPostContentData = [];
                    foreach ($postIds as $postId) {
                        $postIdsData[$postId['title']] = $postId['post_id'];
                    }

                    foreach ($validPostContents as $postContent) {
                        if (isset($postIdsData[$postContent['name']])) {
                            $postContent['post_id'] = $postIdsData[$postContent['name']];
                            $finalPostContentData[] = $postContent;
                        }
                    }
                    $this->postContent->upsert($finalPostContentData, ['slug', 'language_id']);
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
            'valid_data' => $showPosts,
            'valid_content_data' => $validPostContents,
            'invalid_data' => $invalid,
            'updated_data' => $updated
        ];
    }

    public function importPostImages(string $csv_file): array
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
        // all post images list
        $postImageLinks = array_column(iterator_to_array($records), 'image_link');
        $allPostImages = $this->postImage->select(['post_image_id', 'post_id', 'image_link'])
        ->whereIn('image_link', $postImageLinks)->limit(0)->findAll();
        $allPostImagesMap = array_column($allPostImages, 'post_image_id', 'image_link');

        $validImages = [];
        $invalidImages = [];
        $updatedImages = [];
        $existingImages = [];
        $processed = [];
        $mediaData = [];

        foreach ($records as $offset => $record) {
            try {
                if (isset($record['status']) && !empty($record['status'])) {
                    $record['status'] = json_encode([
                        "name" => ($record['status'] == 'show' ? 'Uploaded' : 'Pending'),
                        "severity" => ($record['status'] == 'show' ? 'success' : 'info')
                    ]);
                }
                $way_points = [];
                for ($i = 1; $i <= 5; $i++) {
                    $way_point_name = 'way_point_' . $i . '_name';
                    $way_point_link = 'way_point_' . $i . '_link';
                    if (
                        isset($record[$way_point_name]) && !empty($record[$way_point_name])
                        && isset($record[$way_point_link]) && !empty($record[$way_point_link])
                    ) {
                        $way_points[] = [
                            'name' => $record[$way_point_name],
                            'link' => $record[$way_point_link]
                        ];
                    }
                    unset($record[$way_point_name]);
                    unset($record[$way_point_link]);
                }
                $record['way_points'] = json_encode($way_points);

                $record = isset($record['post_image_id']) && $record['post_image_id'] ? $record : array_merge($defaultFields, $record);

                // Create validation instance
                $imageValidation = new PostImageDataValidation($record, [], [], $allPostImagesMap);
                $validated = $imageValidation->validate();

                if ($validated === false) {
                    $invalidImages[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => $imageValidation->getErrors()
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

                $unique = $imageValidation->getUniqueIdentifier();

                if (in_array($unique, $processed, true)) {
                    $updatedImages[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'identifier' => $unique
                    ];
                    continue;
                }
                if ($validated->isExistingData) {
                    $existingImages[] = (array) $validated->postImage;
                } else {
                    $validImages[] = (array) $validated->postImage;
                }
                if(isset($validated->media) && !empty($validated->media)){
                    $mediaData[] = (array) $validated->media;
                }
                $processed[] = $unique;
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
        if(!empty($mediaData)){
            $this->media->upsert($mediaData, ['meta']);
            $imageLinks = array_column($mediaData, 'meta');
            $this->media->upsert($mediaData, ['path']);
            $mediaIds = $this->media->whereIn('meta', $imageLinks)->select(['meta', 'media_id'])->limit(0)->findAll();
            $mediaIdsMap = array_column($mediaIds, 'media_id', 'meta');
        }
       
        try {
            $this->db->beginTransaction();
            if (!empty($existingImages)) {
                foreach($existingImages as $key => $image){
                    if(isset($mediaIdsMap[$image['image_link']])){
                        $existingImages[$key]['media_id'] = $mediaIdsMap[$image['image_link']];
                    }else{
                        $existingImages[$key]['media_id'] = null;
                    }
                }
               // post_image_id is unique key but not working as expected
                $insertedCount = $this->postImage->upsert($existingImages, ['post_id', 'image_link']); // update existing images
            }

            if (!empty($validImages)) {
                foreach($validImages as $key => $image){
                    if(isset($mediaIdsMap[$image['image_link']])){
                        $validImages[$key]['media_id'] = $mediaIdsMap[$image['image_link']];
                    }else{
                        $validImages[$key]['media_id'] = null;
                    }
                }
                $insertedCount = $this->postImage->upsert($validImages, ['post_id', 'image_link']); // insert new images
            }

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Failed to insert post images: " . $e->getMessage());
        }

        return [
            'success' => true,
            'total_records' => iterator_count($records),
            'valid_records' => count($validImages),
            'invalid_records' => count($invalidImages),
            'updated_records' => count($existingImages),
            'inserted_count' => $insertedCount,
            'valid_data' => $validImages,
            'invalid_data' => $invalidImages,
            'updated_data' => $existingImages
        ];
    }

    private function getDefaultFields(array $headers): array
    {
        $defaults = [];
        foreach ($headers as $h) {
            $defaults[$h] = null;
        }
        // post defaults
        $defaults['site_id'] = 1;
        $defaults['admin_id'] = 1;
        $defaults['status'] = 'Published';
        $defaults['comment_status'] = 'open';
        $defaults['password'] = "1234";
        $defaults['parent'] = 0;
        $defaults['sort_order'] = 0;
        $defaults['type'] = 'post';
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

    // private function ensureAllImageFieldsExist(array $record, array $headers): array
    // {
    //     $defaultFields = [];

    //     foreach ($headers as $header) {
    //         $defaultFields[$header] = null;
    //     }

    //     $defaultFields['sort_order'] = 1;
    //     $defaultFields['status'] = json_encode([
    //         "active" => true,
    //         "featured" => false
    //     ]);
    //     $defaultFields['way_points'] = json_encode([]);
    //     $defaultFields['image'] = json_encode([]);
    //     $defaultFields['image_link'] = '';

    //     return array_merge($defaultFields, $record);
    // }

    // private function convertImageToJsonFormat(string $imageValue, string $subFolder = ""): string
    // {
    //     $path = null;
    //     if ($imageValue === '' || $imageValue === null) {
    //         return '[]';
    //     }
    //     $imageValue = is_string($imageValue) ? $imageValue : (string)$imageValue;
    //     if ($this->isValidJson($imageValue)) {
    //         return $imageValue;
    //     }
    //     if (!str_contains($imageValue, '/media/Blogs/')) {
    //         $path = "/media/Blogs/";
    //     }
    //     if (!!$subFolder && !empty($subFolder)) {
    //         $path .= $subFolder . '/';
    //     }
    //     if ($path && !empty($path)) {
    //         $imageValue = $path . $imageValue;
    //     }
    //     $data = [['id' => null, 'file' => ['name' => basename($imageValue), 'size' => 0, 'type' => 'image/jpeg', 'error' => 0, 'tmp_name' => $imageValue, 'full_path' => basename($imageValue)], 'name' => basename($imageValue), 'size' => 0, 'type' => 'image/jpeg', 'image' => $imageValue, 'status' => ['name' => 'Uploaded', 'severity' => 'success'], 'media_id' => null, 'objectURL' => $imageValue, 'created_at' => '', 'description' => '', 'post_image_id' => null, 'project_image_id' => null]];
    //     return json_encode($data) ?: '[]';
    // }
   

    // private function isPostImageDuplicate(array $record): bool
    // {
    //     // return false;
    //     if (!empty($record['post_id']) && !empty($record['image_link'])) {
    //         $existingImage = $this->postImage->where('post_id', '=', $record['post_id'])
    //             ->where('image_link', '=', $record['image_link'])
    //             ->first();
    //         if ($existingImage) {
    //             return true;
    //         }
    //     }

    //     if (!empty($record['post_image_id'])) {
    //         $existingImage = $this->postImage->where('post_image_id', '=', $record['post_image_id'])->first();
    //         if ($existingImage) {
    //             return true;
    //         }
    //     }

    //     return false;
    // }

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

    /**
     * Sanitize text fields to handle character encoding issues (smart quotes, dashes, etc.)
     */
    private function sanitizePostTextFields(array $record): array
    {
        $textFields = ['description', 'description_one', 'description_two', 'description_three', 'name', 'slug', 'excerpt', 'meta_title', 'meta_description', 'meta_keywords', 'link_text', 'status', 'comment_status', 'type', 'template'];
        foreach ($textFields as $field) {
            if (isset($record[$field]) && is_string($record[$field]) && $record[$field] !== '') {
                $record[$field] = $this->fixTextEncoding($record[$field]);
            }
        }
        return $record;
    }

    private function fixTextEncoding(string $text): string
    {
        if (mb_check_encoding($text, 'UTF-8')) {
            $replacements = [
                "\x92" => "'",
                "\x93" => '"',
                "\x94" => '"',
                "\x96" => "–",
                "\x97" => "—",
                "\x85" => "…",
                "\x91" => "'",
                "\x99" => "™",
                "\xa9" => "©",
                "\xae" => "®",
            ];
            $text = str_replace(array_keys($replacements), array_values($replacements), $text);
        } else {
            $converted = mb_convert_encoding($text, 'UTF-8', 'Windows-1252');
            if ($converted !== false) {
                $text = $converted;
            } else {
                $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
            }
        }
        if (!mb_check_encoding($text, 'UTF-8')) {
            $text = iconv('UTF-8', 'UTF-8//IGNORE', $text);
        }
        return trim($text);
    }

    // private function generateSlug(string $text): string
    // {
    //     if (empty($text)) { return 'post-'.uniqid(); }
    //     $slug = strtolower(trim($text));
    //     $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
    //     $slug = preg_replace('/[\s-]+/', '-', $slug);
    //     $slug = trim($slug, '-');
    //     return $slug ?: 'post-'.uniqid();
    // }

    /**
     * {@inheritDoc}
     */
    public function getAll(
        int $start = 0,
        int $limit = 200,
        array $filters = [],
        array $options = [],
        ?string $orderBy = null,
        string $direction = 'DESC'
    ): array {

        $query = $this->model->where('post.type', '=', 'post');

        // Load relationships
        $query->with(['postContent', 'author' => function ($query) {
            return $query->select(['admin_id', 'username', 'first_name', 'last_name', 'email']);
        }]);

        // Apply filters
        if (!empty($filters['search'])) {
            $query->with(['postContent'])
                ->where('post_content.name', 'LIKE', "%{$filters['search']}%")
                ->orWhere('post_content.content', 'LIKE', "%{$filters['search']}%");
        }

        // if (!empty($filters['username'])) {
        //     $query->with(['admin'])
        //           ->where('admin.username', '=', $filters['username']);
        // }

        if (!empty($filters['status'])) {
            $query->where('status', '=', $filters['status']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', '=', $filters['type']);
        }

        if (!empty($filters['site_id'])) {
            $query->with(['site'])
                ->where('site.site_id', '=', $filters['site_id']);
        }

        if (!empty($filters['taxonomy_item_id'])) {
            $query->with(['taxonomyItem'])
                ->where('taxonomy_item.taxonomy_item_id', '=', $filters['taxonomy_item_id']);
        }

        // Apply options
        if (!empty($options['language_id'])) {
            $query->with(['postContent'])
                ->where('post_content.language_id', '=', $options['language_id']);
        }

        // Add relationships if requested
        if (!empty($options['categories'])) {
            $query->with(['taxonomyItem']);
        }

        if (!empty($options['tags'])) {
            $query->with(['taxonomyItem']);
        }

        // Apply ordering
        if ($orderBy) {
            $query->orderBy($orderBy, $direction);
        } else {
            $query->orderBy('post_id', 'DESC');
        }

        // Apply pagination
        $query->limit($limit)->offset($start)->orderBy('post_id', 'DESC');

        // Get results
        $data = $query->findAll();
        $total = $query->countAll();

        return [
            'list' => $data,
            'total' => $total
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function get(?int $postId = null, ?string $slug = null, array $options = []): ?Post
    {
        $query = $this->model->where('post.status_id', '!=', 1)->where('post.status', '!=', 'Draft');

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


    public function getBySlug(string $slug, array $options = []): ?Post
    {

        return $this->get(null, $slug, $options);
    }

    /**
     * {@inheritDoc}
     */
    public function editContent(int $postId, array $postContent, int $languageId): bool
    {
        return (bool)$this->postContent->update(array_merge($postContent, ['post_id' => $postId, 'language_id' => $languageId]));
    }


    /**
     * {@inheritDoc}
     */
    public function setPostTaxonomy(int $postId, array $taxonomyItems): bool
    {
        try {
            // Delete existing taxonomy items
            $this->postToTaxonomyItem->where('post_id', '=', $postId)->deleteMultiple([$postId]);

            // Insert new taxonomy items
            if (!empty($taxonomyItems)) {
                $taxonomyRecords = array_map(fn($taxonomyItemId) => ['post_id' => $postId, 'taxonomy_item_id' => $taxonomyItemId], $taxonomyItems);
                $this->postToTaxonomyItem->insert($taxonomyRecords);
            }

            return true;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function recordRevision(PostContent $postContent): PostContentRevision
    {
        return $this->postContentRevision->create([
            'post_id' => $postContent->post_id ?? $postContent->getId(),
            'language_id' => $postContent->language_id,
            'revision' => $postContent->content,
            'created_at' => date('Y-m-d H:i:s'),
            // 'admin_id' => $this->authService->getAuthUser()?->getId()
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getArchives(int $start = 0, int $limit = 10, ?string $interval = null, ?string $type = null): array
    {

        $query = $this->model->select(["YEAR(post.created_at) as year"]);

        if ($interval && in_array($interval, ['month', 'day'])) {
            $query->select(["MONTH(post.created_at) as month"]);
            if ($interval === 'day') {
                $query->select(["DAYOFMONTH(post.created_at) as day"]);
            }
        }

        if ($type) {
            $query->where("post.type", '=', $type);
        }

        $query->groupBy("YEAR(post.created_at)");


        $query->orderBy("YEAR(post.created_at)", "DESC");

        if ($limit > 0) {
            $query->limit($limit);
            $query->offset($start);
        }

        $statement = $query->getQuery();

        echo $statement;

        $total = $query->countAll();

        $result = $query->findAll();

        return [
            'list' => collect($result),
            'total' => $total,
            'page' => $start,
            'limit' => $limit,
        ];
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


    public function createPost(PostData $postData): Post
    {
        try {
            $this->db->beginTransaction();
            $postDataArray = $postData->toArray();
            unset($postDataArray['images']);
            unset($postDataArray['banner_way_points']);

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
            $this->db->commit();
            return $post;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new Exception("Failed to create post: " . $e->getMessage());
        }
    }
    public function updatePost(PostData $postData): Post
    {
        $postDataArray = $postData->toArray();
        $post = $this->model->with(['images'])->find($postDataArray['post_id']);
        // $images = $postData->getImages($post->post_id);
        unset($postDataArray['images']);
        unset($postDataArray['banner_way_points']);
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

        // if(isset($images) && is_array($images)){
        //     if(count($images) > 0){
        //         $this->postImage->deleteWhere(['post_id' => $post->post_id]);
        //         $this->postImage->upsert($images, ['post_id', 'image_link']);
        //         $post->data->images = $images;
        //     }else{
        //         $postImages = $this->postImage->where('post_id', '=', $post->post_id)->select(['post_image_id'])->findAll();
        //         if(isset($postImages) && is_array($postImages)){
        //             $postImageIds = array_column($postImages, 'post_image_id');
        //             $this->postImage->deleteMultiple($postImageIds);
        //         }
        //         $post->data->images = [];
        //     }
        // }
        return $post;
    }

    public function showPost(int $postId): Post
    {
        $post = $this->model->where('post_id', '=', $postId)
            ->with([
                'postContent' => function ($query) {
                    return $query->select(['post_id', 'name', 'slug', 'content', 'excerpt', 'meta_keywords', 'meta_description']);
                },
                'meta' => function ($query) {
                    return $query->select(['post_id', 'namespace', 'key', 'value']);
                }
            ])->first();
        // $post->data->banner_way_points = json_decode($post->banner_way_points, true);
        $images = $this->postImage->where('post_id', '=', $postId)
        ->orderBy('sort_order', 'ASC')
        ->findAll();
        $imagesData = [];
        foreach ($images as $image) {
            $img = json_decode($image['image'], true);
            $imagesData[] = [
                'file' => [
                    'name' => $img['name'] ?? '',
                    'description' => $img['description'] ?? '',
                    'size' => $img['size'] ?? '',
                    'type' => $img['type'] ?? '',
                    'objectURL' => $img['objectURL'] ?? '',
                ],
                'size' => $img['size'] ?? '', // iv
                'name' => $img['name'] ?? '', // iv
                'objectURL' => $img['objectURL'] ?? '', // iv
                'post_image_id' => $image['post_image_id'] ?? '',
                'post_id' => $image['post_id'],
                'image_link' => $image['image_link'] ?? '',
                'sort_order' => $image['sort_order'] ?? '',
                'image' => $img ?? [],
                'status' => json_decode($image['status'], true),
                'created_at' => $image['created_at'] ?? '',
                'way_points' => json_decode($image['way_points'], true)
            ];
        }
        $post->data->images = $imagesData;

        return $post;
    }

    public function getTags(): array
    {
        $result = $this->taxonomyItem
            ->join('taxonomy_item_content', 'taxonomy_item_content.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
            ->join('taxonomy', 'taxonomy.taxonomy_id', '=', 'taxonomy_item.taxonomy_id')
            ->where('taxonomy.type', '=', 'tags')
            ->where('taxonomy.post_type', '=', 'post')
            ->where('taxonomy.site_id', '=', 1)
            ->where('taxonomy_item_content.language_id', '=', 1)
            ->select(['taxonomy_item.taxonomy_item_id', 'taxonomy_item_content.name'])
            ->findAll(false);
        return $result;
    }

    public function insertPosts(array $data): bool
    {
        $posts = $data['posts'];
        $postContents = $data['postContents'];
        $this->db->beginTransaction();
        $this->model->insert($posts);

        $this->postContent->insert($postContents);

        $this->db->commit();
        return true;
    }



    public function getBlogGalleryComponentData(array $param): array
    {
        $model = 'post';
        if (isset($param['model']) && $model == $param['model']) {
            $query = $this->model->join('post_content', 'post_content.post_id', '=', 'post.post_id');
            if (isset($param['joins']) && is_array($param['joins'])) {
                foreach ($param['joins'] as $join) {
                    $query->join($join[0], $join[1], $join[2], $join[3]);
                }
            }

            $query->where('post_content.slug', '=', $param['slug'])
                ->select($param['fields'])
                ->orderBy('post_image.sort_order', 'ASC');


            $results = $query->findAll(false);
            $galleryThumb = [];

            foreach ($results as $index => $result) {
                $imageData = json_decode($result['image'] ?? '{}', true);
                if (!isset($imageData['objectURL']) && !isset($imageData[0]['objectURL'])) continue;
                $imageUrl = $imageData['objectURL'] ?? $imageData[0]['objectURL'];

                $isActive = ($index === 0) ? ' active' : '';
                $showClass = ($index === 0) ? ' active show' : '';

                $galleryThumb[] = [
                    'thumb_image' => $imageUrl,
                    'image' => $imageUrl,
                    'thumb_class' => ' th-gallery-thumb' . $isActive,
                    'class' => 'tab-pane fade th-gallery-img th-gallery-img-preview' . $showClass,
                    'thumb_id' => 'img-' . ($index + 1) . '-tab',
                    'id' => 'img-' . ($index + 1),
                    'target' => '#img-' . ($index + 1)
                ];
            }


            // If no results found, return default gallery items
            // if (empty($galleryThumb)) {
            //     $galleryThumb = [
            //         [
            //             'thumb_image' => '/img/blog-detail/gallery-img-1.png',
            //             'image' => '/img/blog-detail/gallery-img-1.png',
            //             'thumb_class' => ' th-gallery-thumb active',
            //             'class' => 'tab-pane fade th-gallery-img active show',
            //             'thumb_id' => 'img-1-tab',
            //             'id' => 'img-1',
            //             'target' => '#img-1'
            //         ],
            //         [
            //             'thumb_image' => '/img/blog-detail/gallery-img-2.png',
            //             'image' => '/img/blog-detail/gallery-img-2.png',
            //             'thumb_class' => ' th-gallery-thumb',
            //             'class' => 'tab-pane fade th-gallery-img',
            //             'thumb_id' => 'img-2-tab',
            //             'id' => 'img-2',
            //             'target' => '#img-2'
            //         ],
            //         [
            //             'thumb_image' => '/img/blog-detail/gallery-img-3.png',
            //             'image' => '/img/blog-detail/gallery-img-3.png',
            //             'thumb_class' => ' th-gallery-thumb',
            //             'class' => 'tab-pane fade th-gallery-img',
            //             'thumb_id' => 'img-3-tab',
            //             'id' => 'img-3',
            //             'target' => '#img-3'
            //         ],
            //         [
            //             'thumb_image' => '/img/blog-detail/gallery-img-4.png',
            //             'image' => '/img/blog-detail/gallery-img-4.png',
            //             'thumb_class' => ' th-gallery-thumb',
            //             'class' => 'tab-pane fade th-gallery-img',
            //             'thumb_id' => 'img-4-tab',
            //             'id' => 'img-4',
            //             'target' => '#img-4'
            //         ],
            //         [
            //             'thumb_image' => '/img/blog-detail/gallery-img-5.png',
            //             'image' => '/img/blog-detail/gallery-img-5.png',
            //             'thumb_class' => ' th-gallery-thumb',
            //             'class' => 'tab-pane fade th-gallery-img',
            //             'thumb_id' => 'img-5-tab',
            //             'id' => 'img-5',
            //             'target' => '#img-5'
            //         ]
            //     ];
            // }

            return [
                'sectionTitle' => 'Project Gallery',
                'items' => $galleryThumb
            ];
        }

        return [
            'sectionTitle' => 'Project Gallery',
            'galleryThumb' => []
        ];
    }


    public function getBlogMainComponentData(array $param)
    {
        $model = 'post';
        if (isset($param['model']) && $model == $param['model']) {
            $query = $this->model;
            if (isset($param['joins']) && is_array($param['joins'])) {
                foreach ($param['joins'] as $join) {
                    $query->join($join[0], $join[1], $join[2], $join[3]);
                }
            }
            $query->where('post_content.slug', '=', $param['slug'])
                ->select($param['fields']);
            if (isset($param['item_count']) && $param['item_count'] > 0) {
                $query->limit($param['item_count']);
            }
            $result = $query->findAll(false);

            $item = $result[0];
            $results = [];

            $results['section_subtitle2'] = $item['description_one'];
            $results['section_subtitle3'] = $item['description_two'];
            $results['section_subtitle4'] = $item['description_three'];

            // Process image data to extract only the URL
            $imageData = json_decode($item['feature_image'] ?? '{}', true);
            if (is_array($imageData) && isset($imageData[0]['objectURL'])) {
                $results['img'] = $imageData[0]['objectURL'];
            } elseif (is_array($imageData) && isset($imageData['objectURL'])) {
                $results['img'] = $imageData['objectURL'];
            } else {
                $results['img'] = '';
            }

            return $results;
        }
    }


    public function getBlogDetailExcerptComponentData(array $param)
    {
        $model = 'post';
        if (isset($param['model']) && $model == $param['model']) {
            $query = $this->model;
            if (isset($param['joins']) && is_array($param['joins'])) {
                foreach ($param['joins'] as $join) {
                    $query->join($join[0], $join[1], $join[2], $join[3]);
                }
            }
            $query->where('post_content.slug', '=', $param['slug'])
                ->select($param['fields']);
            if (isset($param['item_count']) && $param['item_count'] > 0) {
                $query->limit($param['item_count']);
            }
            $result = $query->findAll(false);

            $post = $result[0] ?? [];

            return $post;
        }
    }

    // public function getPageHeroComponentData(array $params) : array
    // {
    //     $model = 'post';
    //     if (isset($params['model']) && $model == $params['model']) {
    //         $query = $this->model;
    //         if(isset($params['joins']) && is_array($params['joins'])) {
    //             foreach($params['joins'] as $join){
    //                 $query->join($join[0], $join[1], $join[2], $join[3]);
    //             }
    //         }
    //         $query->where('post.type', '=', 'page')
    //             ->select($params['fields']);

    //         $result = $query->findAll(false);

    //         $post = $result[0]??[];

    //         // var_dump($post); die;
    //         return $post;
    //     }

    //     return [];

    //     // return [
    //     //     "post_id" => 0,
    //     //     "site_id" => 6,
    //     //     "description" => "fsdfg",
    //     //     "description_one" => "sdfgdfgh",
    //     //     "description_two" => "gfhdfgh",
    //     //     "description_three" => "dfghdfg",
    //     // ];
    // }


    public function getBlogDetailHeroComponentData(array $params): array
    {
        $model = 'post';
        if (isset($params['model']) && $model == $params['model']) {
            $query = $this->model;
            if (isset($params['joins']) && is_array($params['joins'])) {
                foreach ($params['joins'] as $join) {
                    $query->join($join[0], $join[1], $join[2], $join[3]);
                }
            }
            $query->where('post_content.slug', '=', $params['slug'])
                ->select($params['fields']);

            $result = $query->findAll(false);

            $post = $result[0] ?? [];

            // var_dump($post); die;
            return $post;
        }
        return [];
    }


    public function getLatestNewsComponentData(array $param)
    {
        $model = 'post';
        $total = 0;

        $count_per_page = isset($param['item_count']) ? $param['item_count'] : 0;
        $per_page = isset($param['per_page']) ? $param['per_page'] : 0;
        $current_page = isset($param['current_page']) ? $param['current_page'] : 0;
        $limit = ($per_page * $current_page);

        if (isset($param['model']) && $model == $param['model']) {
            $total = $this->model->where('post.type', '=', 'post')->where('post.status', '!=', 1)->where('post.status', '!=', 'Draft')->countAll();
            $this->model->clearQuery();
            $query = $this->model;
            if (isset($param['joins']) && is_array($param['joins'])) {
                foreach ($param['joins'] as $join) {
                    $query->join($join[0], $join[1], $join[2], $join[3]);
                }
            }
            $fields = array_merge($param['fields'], ['feature_image_thumb', 'post_id']);

            $query->where('post.type', '=', 'post')
                ->where('post.status_id', '!=', 1)->where('post.status', '!=', 'Draft')
                ->select($fields)
                ->orderBy('post.post_id', 'DESC');

            if ($limit > 0) {
                $query->limit($limit);
            }

            $results = $query->findAll(false);
            $items = [];



            // If no results found, return default gallery items
            foreach ($results as $index => $result) {
                $imageData = json_decode($result['feature_image_thumb'] ?? '{}', true);
                // if(!isset($imageData[0]['objectURL']) && !isset($imageData['objectURL'])) continue;
                $imageUrl = $imageData[0]['objectURL'] ?? $imageData['objectURL'] ?? null;


                $items[] = [
                    'post_id' => $result['post_id'] ?? "",
                    'title' => $result['name'] ?? "",
                    'link' => '/blog/' . $result['slug'] ?? "",
                    'edit_link' => env('APP_ADMIN_URL') . "/posts/edit/{$result['post_id']}",
                    'excerpt' => $result['excerpt'] ?? "",
                    'image' => $imageUrl,
                ];
            }

            return [
                'section_title' => 'Latest News',
                'section_subtitle' => 'Lorem ipsum dolor sit amet consectetur. Scelerisque urna pellentesque.',
                'items' => $items,
                'total' => $total
            ];
        }

        return [
            'section_title' => 'Latest News',
            'items' => []
        ];
    }


    public function insertPostImages(array $data, int $post_id): array
    {
        $imageData = [];
        $image_link = [];
        $config = app('config');
        $imageServer = $config['APP_URL'];
        foreach ($data as $image) {
            $img = [];
            $img['post_id'] = $post_id;
            $img['image_link'] = $image['image'];
            $image_link[] = $image['image'];
            $img['media_id'] = $image['media_id'];
            $img['image'] = json_encode([
                'name' => $image['name'],
                'objectURL' => $imageServer . $image['objectURL'],
                // 'objectURL' => $image['objectURL'] ?? '',
                'size' => $image['size'],
                'type' => $image['type'],
                'path' => ROOT_DIR . PUBLIC_PATH . $image['image'],
                'status' => $image['status']
            ]);
            $img['sort_order'] = 0;
            $img['status'] = json_encode($image['status']);
            $img['way_points'] = json_encode([]);
            $imageData[] = $img;
        }
        if (count($imageData)) {
            $this->db->beginTransaction();
            $this->postImage->insert($imageData);
            $this->db->commit();
        }
        return $this->galleryResponseFormat($post_id, $image_link);
    }

    private function galleryResponseFormat(int $post_id, array $image_link): array
    {
        $this->postImage->clearQuery();

        $imageData = $this->postImage
            ->where('post_id', '=', $post_id)
            ->whereIn('image_link', $image_link)
            ->select([
                'post_image_id',
                'post_id',
                'image_link',
                'image',
                'sort_order',
                'status',
                'created_at'
            ])
            ->orderBy('sort_order', 'ASC')
            ->findAll(false);

        $files = [];

        foreach ($imageData as $item) {
            $image = is_string($item['image'])
                ? json_decode($item['image'], true)
                : (array) $item['image'];

            $files[] = [
                'post_image_id' => $item['post_image_id'],
                'path'             => $item['image_link'],
                'name'             => $image['name'] ?? basename($item['image_link']),
                'image'            => $item['image_link'],
                'description'      => '',
                'size'             => $image['size'] ?? 0,
                'type'             => $image['type'] ?? '',
                'objectURL'        => $item['image_link'],
                'created_at'       => date('Y-m-d', strtotime($item['created_at'])),
                'file'             => [],
                'dimensions'       => null,
                'target_size'      => [],
                'status'           => $image['status'] ?? [],
                'error'            => [],
            ];
        }

        return [
            'files' => $files
        ];
    }

    public function deletePostImage(int $post_image_id): bool
    {

        return $this->postImage->delete($post_image_id);
    }
    public function deletePostBannerFeatureImage(int $post_id, string $property): bool
    {
        $post = $this->model->where('post_id', '=', $post_id)->first();
        if (!$post) {
            return false;
        }
        // $post->$property = json_encode([]);
        // $this->model->update([$property => json_encode([]), $property.'_thumb' => json_encode([])]);
        $post->update([$property => json_encode([])]);
        return true;
    }

    /**
     * Get blog list pagination data for the public blog listing
     *
     * @param int $page
     * @param int $per_page
     * @return array{
     *   list: array,
     *   total: int,
     *   page: int,
     *   limit: int
     * }
     */
    public function getBloglistPaginationData(int $current_page, int $per_page, $is_admin = false)
    {
        // var_dump($page, $per_page);
        // $offset = ($current_page - 1) * $per_page;
        $limit = $per_page;
        $start = $per_page * $current_page; // old code
        // var_dump($start);
        // echo $start;
        $offset = ($current_page - 1) * $per_page;


        $query = $this->model;
        $query->join('post_content', 'post_content.post_id', '=', 'post.post_id')
            ->where('post.type', '=', 'post')
            ->where('post.status_id', '!=', 1)
            ->where('post.status', '!=', 'Draft')
            ->select([
                'post.post_id',
                'post.type',
                'post.feature_image_thumb',
                'post.feature_image',
                'post.image_banner',
                'post_content.name',
                'post_content.slug',
                'post_content.excerpt'
            ])
            ->orderBy('post.post_id', 'DESC')
            ->limit($limit)
            ->offset($offset);
            // ->offset($start); // old code

        $data = $query->findAll();

        $total = $query->countAll();
        foreach ($data as $key => $post) {
            $featureImage = json_decode($post['feature_image'], true);
            $featureImgeThumb = json_decode($post['feature_image_thumb'], true);
            $bannerImage = json_decode($post['image_banner'], true);
            $data[$key]['feature_image'] = $featureImage[0]['objectURL'] ?? '';
            $data[$key]['feature_image_thumb'] = $featureImgeThumb[0]['objectURL'] ?? '';
            $data[$key]['image_banner'] = $bannerImage[0]['objectURL'] ?? '';
            $data[$key]['link'] = env('APP_ADMIN_URL') . "/posts/edit/{$post['post_id']}";
            $data[$key]['excerpt'] = $post['excerpt'] ?? '';
            $data[$key]['is_admin'] = $is_admin;
        }
        return [
            'list' => $data,
            'total' => $total,
            'current_page' => $current_page,
            'per_page' => $per_page,
        ];
    }


    public function updateWayPoints(array $data): array
    {
        $post_id = $data['post_id'];
        $way_points = $data['way_points'];
        $post = $this->model->where('post_id', '=', $post_id)->first();
        if (!$post) {
            return [
                'success' => false,
                'message' => 'Post not found'
            ];
        }
        $data = $post->update(['banner_way_points' => json_encode($way_points)]);
        return [
            'success' => true,
            'message' => 'Way points updated successfully'
        ];
    }

    public function removeWayPoint(array $data): array
    {
        $model_id = $data['post_id'] ?? null;
        $point_id = $data['point_id'] ?? null;
    
        if (!$model_id || !$point_id) {
            return [
                'success' => false,
                'message' => 'Invalid post_id or point_id'
            ];
        }

        $query = $this->model->where('post_id', '=', $model_id)->first();
    
    
        if (!$query) {
            return [
                'success' => false,
                'message' => 'Post not found ddd'
            ];
        }
    
        $way_points = $query->banner_way_points;
    
        // Decode safely
        $way_points = $way_points ? json_decode($way_points, true) : [];
    
        if (!is_array($way_points)) {
            $way_points = [];
        }
    
        // Filter out the waypoint
        $way_points = array_values(array_filter($way_points, function ($point) use ($point_id) {
            return isset($point['id']) && $point['id'] != $point_id;
        }));
    
        $updated = $query->update([
            'banner_way_points' => json_encode($way_points)
        ]);
    
        return [
            'success' => true,
            'message' => 'Way point removed successfully',
            'way_points' => $updated ? $way_points : []
        ];
    }

    // reorder post images
    public function reorderPostImages(array $data, int $post_id): array
    {
        $dataMapped = [];
        $orderedItems = array_values($data);
        $postImageIds = array_values(array_unique(array_filter(array_map(
            static fn($item) => isset($item['post_image_id']) ? (int) $item['post_image_id'] : 0,
            $orderedItems
        ))));

        if (empty($postImageIds)) {
            return [
                'success' => true,
                'message' => 'No valid post images found to reorder',
                'data' => []
            ];
        }

        $existingImages = $this->postImage
            ->where('post_id', '=', $post_id)
            ->whereIn('post_image_id', $postImageIds)
            ->select(['post_image_id', 'post_id', 'image_link', 'image', 'status'])
            ->findAll();

        $existingMap = [];
        foreach ($existingImages as $existingImage) {
            $existingMap[(int) $existingImage['post_image_id']] = $existingImage;
        }

        $processedImageIds = [];
        foreach ($orderedItems as $index => $item) {
            if (!isset($item['post_image_id'])) {
                continue;
            }

            $postImageId = (int) $item['post_image_id'];
            if (isset($processedImageIds[$postImageId])) {
                continue;
            }

            if (!isset($existingMap[$postImageId])) {
                continue;
            }

            $existingImage = $existingMap[$postImageId];
            $imageLink = trim((string) ($existingImage['image_link'] ?? ''));
            if ($imageLink === '') {
                $imageLink = trim((string) ($item['image_link'] ?? $item['name'] ?? ''));
            }

            $dataMapped[] = [
                'post_image_id' => $postImageId,
                'sort_order' => $index + 1,
                'post_id' => $post_id,
                'image_link' => $imageLink,
                'status' => $existingImage['status'],
                'image' => $existingImage['image']
            ];

            $processedImageIds[$postImageId] = true;
        }

        if (empty($dataMapped)) {
            return [
                'success' => true,
                'message' => 'No matching post images found to reorder',
                'data' => []
            ];
        }

        $updated = $this->postImage->upsert($dataMapped, ['post_image_id']);
            return [
                'success' => true,
                'message' => 'Post images reordered successfully',
                'data' => $dataMapped
            ];
    }

    public function getPostIdBySlug(string $slug): int
    {
        $post = $this->postContent->where('slug', '=', $slug)->first();
        if ($post !== null && isset($post->post_id)) {
            return $post->post_id;
        }
        return 0;
    }

    public function deletePostGalleryImageById(array $ids, string $property = 'images'): array
    {
        $deletedIds = []; // not use
        $this->postImage->clearQuery();
        // delete multiple file from db.
        $deleted = $this->postImage->deleteMultiple($ids);

        if($deleted > 0){
            return [
                'success' => true,
                'deleted_ids' => $ids,
                'property' => $property,
            ];
        }
        return [
            'success' => false,
            'deleted_ids' => [],
            'property' => $property,
        ];
    }

    public function getRelatedArticlesSliderComponentData(array $params): array
    {
        $model = 'post';
        if (!isset($params['model']) || $model !== $params['model']) {
            return [];
        }

        $limit = 4;
        $languageId = isset($params['language_id']) ? (int) $params['language_id'] : 1;

        $this->model->clearQuery();
        $query = $this->model
            ->join('post_content', 'post_content.post_id', '=', 'post.post_id')
            ->where('post.status_id', '!=', 1)
            ->where('post.status', '!=', 'Draft')
            ->where('post_content.language_id', '=', $languageId)
            ->where('post.post_id', '!=', $params['post_id'])
            ->select([
                'post.post_id',
                'post.title',
                'post.description',
                'post.feature_image_thumb',
                'post.feature_image',
                'post.image_banner',
                'post_content.name',
                'post_content.slug',
                'post_content.excerpt'
            ])
            ->orderBy('post.post_id', 'DESC')
            ->limit($limit);
        $results = $query->findAll(false);
        $itemsByPostId = [];

        foreach ($results as $result) {
            $postId = (int) ($result['post_id'] ?? 0);
            if ($postId === 0) {
                continue;
            }


            if (!isset($itemsByPostId[$postId])) {
                $imageData = json_decode($result['feature_image_thumb'], true);
                $imageUrl = isset($imageData[0]) && isset($imageData[0]['objectURL']) ? $imageData[0]['objectURL'] : '';
                
                $description = $result['excerpt'] ?? $result['description'] ?? '';
                if ($description !== '') {
                    $description = htmlToPlainText($description) ?? '';
                }

                $itemsByPostId[$postId] = [
                    'id' => $postId,
                    'title' => $result['name'] ?? $result['title'] ?? '',
                    'slug' => $result['slug'] ?? '',
                    'image' => $imageUrl,
                    'description' => $description,
                    'model' => 'post',
                ];
            }
        }

        return $itemsByPostId;
    }


}

