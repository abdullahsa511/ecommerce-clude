<?php

declare(strict_types=1);

namespace App\Core\Models\Post;

use App\Core\Models\Base\Model;
use App\Core\Models\Admin\Admin;
use App\Core\Models\Post\Comment;
use App\Core\Models\Post\PostContent;
use App\Core\Models\Post\PostMeta;
use App\Core\Models\PostCategory\TaxonomyItem;
use App\Core\Models\Site\Site;
use App\Core\Models\Media\Image;
use App\Core\Models\Post\PostImage;
use function App\Core\System\utils\htmlToPlainText;
use function App\Core\System\utils\makeSlug;
use DateTime;
use Illuminate\Contracts\Support\Jsonable;
use \stdClass;

class Post extends Model
{
    // Core post properties
    public ?int $post_id;
    public ?int $admin_id;
    public string|int|null $site_id;
    public ?string $status;
    public ?string $image;
    public ?string $comment_status;
    public ?string $password;
    public ?int $parent;
    public ?int $sort_order;
    public ?string $type;
    public ?string $template;
    public ?int $comment_count;
    public ?int $views;
    public ?string $created_at;
    public ?string $updated_at;
    public ?string $description;
    public ?string $description_one;
    public ?string $description_two;
    public ?string $description_three;
    public ?string $title;
    public ?string $keyline_quote;
    public ?string $feature_image_thumb;
    public ?string $feature_image;
    public ?string $image_banner;
    public ?string $image_thumb;
    public ?string $main_image_one;
    public ?string $main_image_two;
    public null|bool|int $is_featured;
    public ?int $status_id;

    // Admin properties (from admin table join)
    public ?string $admin_display_name;
    public ?string $admin_username;
    public ?string $admin_first_name;
    public ?string $admin_last_name;
    public array $associations = [
        "admin",
        "site",
        "taxonomy_item",
        "post_content",
        "post_meta",
        "comment"  
    ];

    protected ?string $thumbnailUrl = null;
    protected ?string $authorName = null;
    // banner way points
    public array|string|null $banner_way_points;
    


    public function __construct() 
    {
        parent::__construct();
    }


    public function author(): array
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
 
    public function parent(): array
    {
        return $this->belongsTo(self::class, 'parent');
    }

    public function children(): array
    {
        return $this->hasMany(self::class, 'parent');
    }

    public function site(): array
    {
        return $this->belongsToMany(Site::class, 'post_to_site');
    }

    public function taxonomyItem(): array
    {
        return $this->belongsToMany(TaxonomyItem::class, 'post_to_taxonomy_item');
    }

    public function postContent(): array
    {
        return $this->hasOne(PostContent::class, 'post_id', 'post_id');
    }


    public function meta(): array
    {
        return $this->hasMany(PostMeta::class, 'post_id');
    }

    public function comment(): array
    {
        return $this->hasMany(Comment::class, 'post_id');
    }
    
    public function images(): array
    {
        return $this->hasMany(PostImage::class, 'post_id', 'post_id');
    }

    public function getThumbnail(?int $width = null, ?int $height = null): string
    {
        if ($this->thumbnailUrl === null) {
            $this->thumbnailUrl = !empty($this->image) ? $this->image : $this->getDefaultThumbnail();
        }

        // If dimensions are provided, you might want to implement image resizing here
        return $this->thumbnailUrl;
    }

    protected function getDefaultThumbnail(): string
    {
        return '/assets/images/default-post-thumbnail.jpg';
    }

 

    public function getAuthorName(): string
    {
        if ($this->authorName === null) {
            // Use the admin fields from the join if available
            if (!empty($this->admin_display_name)) {
                $this->authorName = $this->admin_display_name;
            } elseif (!empty($this->admin_first_name) || !empty($this->admin_last_name)) {
                $this->authorName = trim($this->admin_first_name . ' ' . $this->admin_last_name);
            } elseif (!empty($this->admin_username)) {
                $this->authorName = $this->admin_username;
            } else {
                $adminData = $this->admin_data ?? null;
                $this->authorName = $adminData ? $adminData['display_name'] : 'Unknown Author';
            }
        }
        return $this->authorName;
    }


    public function getAdminInfo(): array
    {
        return [
            'admin_id' => $this->admin_id,
            'display_name' => $this->admin_display_name,
            'username' => $this->admin_username,
            'first_name' => $this->admin_first_name,
            'last_name' => $this->admin_last_name,
            'full_name' => $this->getAuthorName()
        ];
    }


} 

class PostResponse
{
    public ?int $post_id;
    public ?int $status_id;
    public ?int $admin_id;
    public ?string $description;
    public ?string $description_one;
    public ?string $description_two;
    public ?string $description_three;
    public ?string $title;
    public ?string $keyline_quote;
    public string|int|null $site_id;
    public string|int|null $status;
    /** @var Image[] */
    public ?array $image_banner;
    /** @var Image[] */
    public ?array $image_thumb;
    /** @var Image[] */
    /** @var Image[] */
    public ?array $image;
    /** @var Image[] */
    public ?array $images;
    /** @var Image[] */
    public ?array $feature_image;
    /** @var Image[] */
    public ?array $feature_image_thumb;
    /** @var Image[] */
    public ?array $main_image_one;
    /** @var Image[] */
    public ?array $main_image_two;
    /** @var Image[] */
    public ?string $comment_status;
    public ?string $password;
    public ?int $parent;
    public ?int $sort_order;
    public ?string $type;
    public ?string $template;
    public ?int $comment_count;
    public ?int $views;
    public string $created_at;
    public string $updated_at;
    public int|null|bool $is_featured;

    // Admin information
    public ?string $admin_display_name;
    public ?string $admin_username;
    public ?string $admin_first_name;
    public ?string $admin_last_name;
    public ?string $admin_full_name;

    // banner way points
    public array|string|null $banner_way_points;

    public PostContentData $postContent;

    public array $metadata = [
        'openGraph' => [
            'openGraphTitle' => '',
            'openGraphDescription' => '',
        ],
        'twitter' => [
            'twitterTitle' => '',
            'twitterDescription' => '',
            'twitterLabel1' => '',
            'twitterLabel2' => '',
            'twitterData1' => '',
            'twitterData2' => '',
        ]
    ];

    public function __construct(stdClass $data) 
    {
        if(isset($data->post_id)) $this->post_id = $data->post_id;
        if(isset($data->status_id)) $this->status_id = $data->status_id;
        if(isset($data->admin_id)) $this->admin_id = $data->admin_id;
        if(isset($data->site_id)) $this->site_id = $data->site_id;
        if(isset($data->status)) $this->status = $data->status;
        if(isset($data->image)) $this->image = $data->image?array_map(function($image) {
            if(is_array($image) && isset($image['objectURL'])){
                $url = ROOT_DIR.PUBLIC_PATH.$image['objectURL'];
                if(file_exists($url)){
                    $image['status'] = ['name' => 'Uploaded', 'severity' => 'success'];
                }
                return $image;
            }
            return null;
        }, json_decode($data->image, true)):[];
        if(isset($data->images) && is_array($data->images)){
            foreach($data->images as $image){
                if(isset($image['image']) && is_string($image['image'])){
                    $img = json_decode($image['image'], true);
                }else{
                    $img = $image['image']??[];
                }
                if(isset($img[0])){
                    $image = array_merge($image, $img[0]);
                }
                $this->images[] = $image;
            }
        }
        if(isset($data->image_banner)) $this->image_banner = $this->processImages($data->image_banner);
        if(isset($data->image_thumb)) $this->image_thumb = $this->processImages($data->image_thumb);
        if(isset($data->feature_image)) $this->feature_image = $this->processImages($data->feature_image);
        if(isset($data->feature_image_thumb)) $this->feature_image_thumb = $this->processImages($data->feature_image_thumb);
        if(isset($data->main_image_one)) $this->main_image_one = $this->processImages($data->main_image_one);
        if(isset($data->main_image_two)) $this->main_image_two = $this->processImages($data->main_image_two);
        if(isset($data->is_featured)) $this->is_featured = $data->is_featured;

        if(isset($data->banner_way_points)) $this->banner_way_points = json_decode($data->banner_way_points, true);
        

        if(isset($data->comment_status)) $this->comment_status = $data->comment_status;
        if(isset($data->password)) $this->password = $data->password;
        if(isset($data->parent)) $this->parent = $data->parent;
        if(isset($data->sort_order)) $this->sort_order = $data->sort_order;
        if(isset($data->type)) $this->type = $data->type;
        if(isset($data->template)) $this->template = $data->template;
        if(isset($data->comment_count)) $this->comment_count = $data->comment_count;
        if(isset($data->views)) $this->views = $data->views;
        if(isset($data->created_at)) $this->created_at = $data->created_at;
        if(isset($data->updated_at)) $this->updated_at = $data->updated_at;
        if(isset($data->description)) $this->description = $data->description;
        if(isset($data->description_one)) $this->description_one = $data->description_one;
        if(isset($data->description_two)) $this->description_two = $data->description_two;
        if(isset($data->description_three)) $this->description_three = $data->description_three;
        if(isset($data->title)) $this->title = $data->title;
        
        // Admin information
        if(isset($data->admin_display_name)) $this->admin_display_name = $data->admin_display_name;
        if(isset($data->admin_username)) $this->admin_username = $data->admin_username;
        if(isset($data->admin_first_name)) $this->admin_first_name = $data->admin_first_name;
        if(isset($data->admin_last_name)) $this->admin_last_name = $data->admin_last_name;
        if (isset($data->banner_way_points)) {
            $this->banner_way_points = json_decode($data->banner_way_points, true);
        }
        // Set admin full name
        if (!empty($this->admin_display_name)) {
            $this->admin_full_name = $this->admin_display_name;
        } elseif (!empty($this->admin_first_name) || !empty($this->admin_last_name)) {
            $this->admin_full_name = trim($this->admin_first_name . ' ' . $this->admin_last_name);
        } elseif (!empty($this->admin_username)) {
            $this->admin_full_name = $this->admin_username;
        } else {
            $this->admin_full_name = 'Unknown Author';
        }
        
        if(isset($data->postContent)) $this->postContent = is_array($data->postContent) 
                ? new PostContentData($data->postContent) 
                : new PostContentData(json_decode($data->postContent, true));
        if(isset($data->meta)) {
            $metadata = json_decode($data->meta, true);
            foreach($metadata as $meta) {
                if(isset($meta['namespace']) && isset($meta['key']) && isset($meta['value'])){
                    $this->metadata[$meta['namespace']][$meta['key']] = $meta['value'];
                } 
            }
        }
    }

    public function processImages(array|string $images): array
    {
        if(isset($images) && !empty($images) && $images !== null ){ 
            $images = is_array($images) ? $images : json_decode($images, true);
            if(is_string($images)){
                $images = json_decode($images, true);
            }
            return array_filter(array_map(function($image){
                $img = new stdClass();
                if(isset($image) && !empty($image) && $image !== null && isset($image['objectURL'])){
                  $imageObject = $image;
                }else if(isset($image['image'])){
                    $imageObject = is_array($image['image']) ? $image['image'][0] : json_decode($image['image'], true);
                    if(isset($imageObject[0])) $imageObject = $imageObject[0];
                }
                if(isset($imageObject) && isset($imageObject['objectURL'])){
                    $img = new Image($imageObject);
                    $img->status = ['name' => 'Uploaded', 'severity' => 'success'];
                    $img->post_image_id = $imageObject['post_image_id']??null;
                    $img->post_id = $imageObject['post_id']??null;
                    $img->sort_order = $imageObject['sort_order']??0;
                    return $img;
                }
                return null;
            }, $images), function($item){return $item !== null;});
        }
        return [];
    }
} 

class PostData {
    public int $post_id;
    public ?int $status_id;
    public int $site_id;
    public ?int $admin_id;
    public ?string $status;
    public ?string $image;
    public ?string $image_banner;
    public ?string $image_thumb;
    public ?string $feature_image;
    public ?string $feature_image_thumb;
    public ?string $main_image_one;
    public ?string $main_image_two;
    public ?array $images;
    public ?string $comment_status;
    public ?string $password;
    public ?int $parent;
    public ?int $sort_order;
    public ?string $type;
    public ?string $template;
    public ?int $comment_count;
    public ?int $views;
    public string $created_at;
    public string $updated_at;
    public ?string $description;
    public ?string $description_one;
    public ?string $description_two;
    public ?string $description_three;
    public ?string $title;
    public null|int|bool $is_featured;

    public PostContentData $postContent;

    public array $metadata;

    // banner way points
    public array|string|null $banner_way_points;

    public function __construct(array $data) 
    {
        if(isset($data['post_id'])) $this->post_id = $data['post_id'];
        if(isset($data['site_id'])) $this->site_id = $data['site_id'];
        if(isset($data['status_id'])) $this->status_id = $data['status_id'];
        if(isset($data['admin_id'])) $this->admin_id = $data['admin_id'];
        if(isset($data['status'])) $this->status = (string)$data['status'];
        if(isset($data['description'])) $this->description = $data['description'];
        if(isset($data['description_one'])) $this->description_one = $data['description_one'];
        if(isset($data['description_two'])) $this->description_two = $data['description_two'];
        if(isset($data['description_three'])) $this->description_three = $data['description_three'];
        if(isset($data['title'])) $this->title = $data['title'];
        if (isset($data['banner_way_points'])) {
            if (is_string($data['banner_way_points'])) {
                $this->banner_way_points = json_decode($data['banner_way_points'], true);
            } else {
                $this->banner_way_points = $data['banner_way_points'];
            }
        }
        // if(isset($data['image'])) $this->image = $data['image'];
        if(isset($data['image'])) $this->image = json_encode($data['image']);
        if(isset($data['image_banner'])) $this->image_banner = json_encode($data['image_banner']);
        if(isset($data['image_thumb'])) $this->image_thumb = json_encode($data['image_thumb']);
        if(isset($data['feature_image'])) $this->feature_image = json_encode($data['feature_image']);
        if(isset($data['feature_image_thumb'])) $this->feature_image_thumb = json_encode($data['feature_image_thumb']);
        if(isset($data['main_image_one'])) $this->main_image_one = json_encode($data['main_image_one']);
        if(isset($data['main_image_two'])) $this->main_image_two = json_encode($data['main_image_two']);
        if(isset($data['images'])) $this->images = $data['images'] ?? null;
        if(isset($data['comment_status'])) $this->comment_status = $data['comment_status'];
        if(isset($data['password'])) $this->password = $data['password'];
        if(isset($data['parent'])) $this->parent = $data['parent'];
        if(isset($data['sort_order'])) $this->sort_order = $data['sort_order'];
        if(isset($data['type'])) $this->type = $data['type'];
        if(isset($data['template'])) $this->template = $data['template'];
        if(isset($data['comment_count'])) $this->comment_count = $data['comment_count'];
        if(isset($data['views'])) $this->views = $data['views'];
        if(isset($data['is_featured'])) $this->is_featured = $data['is_featured'] ? 1 : 0;
        $date = (new DateTime())->format('Y-m-d H:i:s');
        if(isset($data['created_at'])){
            $this->created_at = (new DateTime($data['created_at']))->format('Y-m-d H:i:s');
        }else{
            $this->created_at = $date;
        }
        if(isset($data['updated_at'])){
            $this->updated_at = (new DateTime($data['updated_at']))->format('Y-m-d H:i:s');
        }else{
            $this->updated_at = $date;
        }
        if(isset($data['postContent'])) $this->postContent = new PostContentData($data['postContent']);
        if(isset($data['metadata'])) $this->metadata = $data['metadata']??[];
        if(isset($data['banner_way_points'])) $this->banner_way_points = $data['banner_way_points'];
    }

    public function toArray(): array
    {
        $data = [];
        if(isset($this->post_id)) $data ['post_id'] = $this->post_id;
        if(isset($this->site_id)) $data ['site_id'] = $this->site_id;
        if(isset($this->status_id)) $data ['status_id'] = $this->status_id;
        if(isset($this->admin_id)) $data ['admin_id'] = $this->admin_id;
        if(isset($this->status)) $data ['status'] = $this->status;
        if(isset($this->image)) $data ['image'] = $this->image;
        if(isset($this->image_banner)) $data ['image_banner'] = $this->image_banner;
        if(isset($this->image_thumb)) $data ['image_thumb'] = $this->image_thumb;
        if(isset($this->feature_image)) $data ['feature_image'] = $this->feature_image;
        if(isset($this->feature_image_thumb)) $data ['feature_image_thumb'] = $this->feature_image_thumb;
        if(isset($this->images)) $data ['images'] = $this->images;
        if(isset($this->comment_status)) $data ['comment_status'] = $this->comment_status;
        if(isset($this->password)) $data ['password'] = $this->password;
        if(isset($this->parent)) $data ['parent'] = $this->parent;
        if(isset($this->sort_order)) $data ['sort_order'] = $this->sort_order;
        if(isset($this->type)) $data ['type'] = $this->type;
        if(isset($this->template)) $data ['template'] = $this->template;
        if(isset($this->comment_count)) $data ['comment_count'] = $this->comment_count;
        if(isset($this->views)) $data ['views'] = $this->views;
        if(isset($this->created_at)) $data ['created_at'] = $this->created_at;
        if(isset($this->updated_at)) $data ['updated_at'] = $this->updated_at;
        if(isset($this->description)) $data ['description'] = $this->description;
        if(isset($this->description_one)) $data ['description_one'] = $this->description_one;
        if(isset($this->description_two)) $data ['description_two'] = $this->description_two;
        if(isset($this->description_three)) $data ['description_three'] = $this->description_three;
        if(isset($this->postContent->name)) $data ['title'] = $this->postContent->name;
        if(isset($this->banner_way_points)) $data ['banner_way_points'] = $this->banner_way_points;
        if(isset($this->is_featured)) $data ['is_featured'] = $this->is_featured;
        return $data;
    }
    public function getPostContent(): array|null
    {
        return isset($this->postContent) ? $this->postContent->toArray() : null;
    }

    public function getPostMetadata($post_id): array
    {
        $data = [];
        if (!isset($post_id)) {
            return $data;
        }
        if(isset($this->metadata) && is_array($this->metadata)){
            foreach($this->metadata as $namespace => $meta){
                $metadata = [];
                $metadata['namespace'] = $namespace;
                foreach($meta as $key => $value){
                    $metadata['post_id'] = $post_id;
                    $metadata['key'] = $key;
                    $metadata['value'] = $value;
                    $data[] = $metadata;
                }
            }
        }
        
        return $data;
    }
    
    public function getImages($postId): array
    {
        $images = [];
        foreach ($this->images as $image) {   
            $image['post_id'] = $postId;
            $image['sort_order'] = $image['sort_order'] ?? 0;
            $img  = new Image($image);
            $images[] = $img->toArray();
        }
        return $images;
    }
}

class PostContentData {
    public int $language_id;
    public ?string $name;
    public ?string $slug;
    public ?string $content = null;
    public ?string $excerpt;
    public ?string $meta_description;
    public ?string $meta_keywords;

    public function __construct(array $data)
    {
        if(isset($data['language_id'])) $this->language_id = $data['language_id'];
        if(isset($data['name'])) $this->name = $data['name'];
        if(isset($data['slug'])) $this->slug = makeSlug($data['slug']);
        if(isset($data['content'])) $this->content = $data['content'];
        if(isset($data['excerpt'])) $this->excerpt = $data['excerpt'];
        if(isset($data['meta_description'])) $this->meta_description = $data['meta_description'];
        if(isset($data['meta_keywords'])) $this->meta_keywords = $data['meta_keywords'];
    }
    public function toArray(): array
    {
        return [
            'language_id' => $this->language_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'content' => $this->content,
            'excerpt' => $this->excerpt,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
        ];
    }
}

