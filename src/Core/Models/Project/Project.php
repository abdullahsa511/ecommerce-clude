<?php

declare(strict_types=1);

namespace App\Core\Models\Project;

use App\Core\Models\Base\Model;
use App\Core\Models\Customer\Customer;
use App\Core\Models\Geoip\Address;
use App\Core\Models\Media\Image;
use App\Core\Models\Project\ProjectImage;
use App\Core\Models\Site\Site;
use App\Core\Models\User;
use function App\Core\System\utils\htmlToPlainText;
use function App\Core\System\utils\makeSlug;
use DateTime;
use stdClass;

class Project extends Model
{
    protected string $table = 'project';
    protected string $primaryKey = 'project_id';

    public int|string|null $project_id;
    public ?int $site_id;
    public ?int $status_id;
    public ?int $customer_id;
    public ?string $name;
    public ?string $slug;
    public ?string $description;
    public ?string $location;
    public ?string $status;
    public ?string $image;
    public ?string $meta_title;
    public ?string $meta_description;
    public ?string $meta_keywords;
    public ?string $created_at;
    public ?string $updated_at;
    public ?string $deleted_at;
    public ?string $title;
    public ?string $label;
    public ?string $link_text;
    public ?string $designer;
    public ?string $credit_label;
    public ?string $photographer;
    public ?string $main_title;
    public ?string $main_description_one;
    public ?string $main_description_two;
    public ?string $main_description_three;
    public ?string $main_description_four;
    public ?string $main_image_one;
    public ?string $main_image_two;
    public ?string $image_thumb;
    public ?string $keyline_quote;
    public ?string $preview_text;
    public ?string $banner_way_points;
    public int|null|bool $is_featured;

    public function __construct()
    {
        parent::__construct();
    }

    public function images(): array
    {
        return $this->hasMany(ProjectImage::class, 'project_id', 'project_id');
    }

    public function customer(): array
    {
        return $this->belongsTo(User::class, 'customer_id', 'user_id');
    }
    public function site(): array
    {
        return $this->belongsTo(Site::class, 'site_id', 'site_id');
    }
    public static function projectData($data)
    {
        $statuses = [
            1 => 'Draft',
            2 => 'Future',
            3 => 'Pending',
            4 => 'Published',
            5 => 'Private',
            6 => 'Trash',
        ];

        $statusId = isset($data->status_id) ? $data->status_id : 1;

        $dataArray = [
            'site_id' => $data->site_id ?? 0,
            'status_id' => $statusId,
            'customer_id' => $data->customer ?? null,
            'name' => $data->name ?? null,
            'slug' => makeSlug($data->slug) ?? null,
            'description' => $data->description ?? null,
            'location' => $data->location?->description ?? null,
            // 'status' => $data->status ?? '',
            'status' => $statuses[$statusId] ?? 'Draft',
            // 'image' => isset($data->image) ? json_encode($data->image) : json_encode([]),
            'meta_title' => $data->meta_title ?? null,
            'meta_description' => $data->meta_description ?? null,
            'meta_keywords' => $data->meta_keywords ?? null,
            'title' => $data->title ?? null,
            'label' => $data->label ?? null,
            'link_text' => $data->link_text ?? null,
            'created_at' => $data->created_at ?? date('Y-m-d H:i:s'),
            'updated_at' => $data->updated_at ?? date('Y-m-d H:i:s'),
            'deleted_at' => null,
            'is_featured' => $data->is_featured ?? 0,
            'designer' => $data->designer ?? null,
            'credit_label' => $data->credit_label ?? null,
            'photographer' => $data->photographer ?? null,
            'main_title' => $data->main_title ?? null,
            'main_description_one' => $data->main_description_one ?? null,
            'main_description_two' => $data->main_description_two ?? null,
            'main_description_three' => $data->main_description_three ?? null,
            'main_description_four' => $data->main_description_four ?? null,
            // 'main_image_one' => isset($data->main_image_one) ? json_encode($data->main_image_one) : json_encode([]),
            // 'main_image_two' => isset($data->main_image_two) ? json_encode($data->main_image_two) : json_encode([]),
            'keyline_quote' => $data->keyline_quote ?? null,
            'image_thumb' => json_encode([]),
            'banner_way_points' => $data->banner_way_points ?? null,
            'preview_text' => $data->preview_text ?? null,
        ];
        return $dataArray;
    }

    /**
     * Safely extract customer_id from ProjectData's customer field.
     * Handles Customer objects with uninitialized customer_id, arrays, and scalar IDs.
     */
    public static function extractCustomerId(array|int|Customer|null $customer): ?int
    {
        if ($customer === null) {
            return null;
        }
        if (is_int($customer)) {
            return $customer;
        }
        if (is_array($customer)) {
            return isset($customer['customer_id']) ? (int) $customer['customer_id']
                : (isset($customer['user_id']) ? (int) $customer['user_id'] : null);
        }
        if ($customer instanceof Customer) {
            return isset($customer->customer_id) ? $customer->customer_id : null;
        }
        return null;
    }
}

class ProjectData
{
    public null|int|string $project_id;
    public ?int $site_id;
    public ?string $name;
    public ?string $description;
    public ?string $preview_text;
    public ?Address $location;
    public ?string $status;
    public ?int $status_id;
    public ?string $image;
    public ?array $images;
    public ?string $slug;
    public ?string $created_at;
    public ?string $updated_at;
    public array|int|Customer|null $customer;
    public ?string $meta_title;
    public ?string $meta_description;
    public ?string $meta_keywords;
    public ?string $title;
    public ?string $label;
    public ?string $link_text;
    public ?string $designer;
    public ?string $credit_label;
    public ?string $photographer;
    public ?string $main_title;
    public ?string $main_description_one;
    public ?string $main_description_two;
    public ?string $main_description_three;
    public ?string $main_description_four;
    public ?string $main_image_one;
    public ?string $main_image_two;
    public ?string $keyline_quote;
    public array|string|null $banner_way_points;
    public int|null|bool $is_featured;


    public function __construct(array $data)
    {
        if (isset($data['project_id']))
            $this->project_id = $data['project_id'] ?? null;
        if (isset($data['site_id']))
            $this->site_id = $data['site_id'] ?? null;
        if (isset($data['name']))
            $this->name = $data['name'] ?? null;
        if (isset($data['description']))
            $this->description = $data['description'] ?? null;
        if (isset($data['preview_text']))
            $this->preview_text = $data['preview_text'] ?? null;
        if (isset($data['location']))
            $this->location = new Address($data['location']);
        if (isset($data['status']))
            $this->status = $data['status'] ?? '';
        if (isset($data['status_id']))
            $this->status_id = $data['status_id'] ?? null;
        if (isset($data['image'])) {
            if (is_array($data['image'])) {
                $this->image = json_encode($data['image']);
            } else {
                $this->image = $data['image'];
            }
        }
        if (isset($data['images']))
            $this->images = $data['images'] ?? null;
        if (isset($data['slug']))
            $this->slug = makeSlug($data['slug']) ?? null;
        if (isset($data['created_at']))
            $this->created_at = !empty($data['created_at'])
                ? (new DateTime($data['created_at']))->format('Y-m-d H:i:s')
                : (new DateTime())->format('Y-m-d H:i:s');
        if (isset($data['updated_at']))
            $this->updated_at = !empty($data['updated_at'])
                ? (new DateTime($data['updated_at']))->format('Y-m-d H:i:s')
                : (new DateTime())->format('Y-m-d H:i:s');
        if (isset($data['customer'])) {
            $cust = $data['customer'];
            if (is_int($cust)) {
                $this->customer = $cust;
            } elseif (is_array($cust)) {
                $this->customer = isset($cust['customer_id']) ? (int) $cust['customer_id']
                    : (isset($cust['user_id']) ? (int) $cust['user_id'] : null);
            } elseif ($cust instanceof Customer && isset($cust->customer_id)) {
                $this->customer = $cust;
            } else {
                $this->customer = null;
            }
        } else {
            $this->customer = null;
        }
        if (isset($data['meta_title']))
            $this->meta_title = htmlToPlainText($data['meta_title'] ?? null);
        if (isset($data['meta_description']))
            $this->meta_description = htmlToPlainText($data['meta_description'] ?? null);
        if (isset($data['meta_keywords']))
            $this->meta_keywords = htmlToPlainText($data['meta_keywords'] ?? null);
        if (isset($data['title']))
            $this->title = htmlToPlainText($data['title'] ?? null);
        if (isset($data['label']))
            $this->label = htmlToPlainText($data['label'] ?? null);
        if (isset($data['link_text']))
            $this->link_text = $data['link_text'] ?? null;
        if (isset($data['designer']))
            $this->designer = $data['designer'] ?? null;
        if (isset($data['credit_label']))
            $this->credit_label = $data['credit_label'] ?? null;
        if (isset($data['photographer']))
            $this->photographer = $data['photographer'] ?? null;
        if (isset($data['main_title']))
            $this->main_title = $data['main_title'] ?? null;
        if (isset($data['main_description_one']))
            $this->main_description_one = $data['main_description_one'] ?? null;
        if (isset($data['main_description_two']))
            $this->main_description_two = $data['main_description_two'] ?? null;
        if (isset($data['main_description_three']))
            $this->main_description_three = $data['main_description_three'] ?? null;
        if (isset($data['main_description_four']))
            $this->main_description_four = $data['main_description_four'] ?? null;
        if (isset($data['main_image_one']))
            $this->main_image_one = json_encode($data['main_image_one']) ?? null;
        if (isset($data['main_image_two']))
            $this->main_image_two = json_encode($data['main_image_two']) ?? null;
        if (isset($data['keyline_quote']))
            $this->keyline_quote = $data['keyline_quote'] ?? null;
        if (isset($data['banner_way_points']))
            $this->banner_way_points = isset($data['banner_way_points']) ? json_encode($data['banner_way_points']) : '[]';
        if(isset($data['is_featured'])) 
            $this->is_featured = $data['is_featured'] ? 1 : 0;
    }

    public function toArray(): array
    {
        // echo 'test'; exit;
        $data = [];

        if (isset($this->project_id))
            $data['project_id'] = $this->project_id;
        if (isset($this->site_id))
            $data['site_id'] = $this->site_id;
        if (isset($this->customer))
            $data['customer_id'] = Project::extractCustomerId($this->customer);
        if (isset($this->name))
            $data['name'] = $this->name;
        if (isset($this->description))
            $data['description'] = $this->description;
        if (isset($this->preview_text))
            $data['preview_text'] = $this->preview_text;
        if (isset($this->location))
            $data['location'] = $this->location?->description;
        if (isset($this->status))
            $data['status'] = $this->status;
        if (isset($this->status_id))
            $data['status_id'] = $this->status_id;
        if (isset($this->image))
            $data['image'] = $this->image;
        if (isset($this->images))
            $data['images'] = $this->images;
        if (isset($this->slug))
            $data['slug'] = makeSlug($this->slug);
        if (isset($this->created_at))
            $data['created_at'] = $this->created_at;
        if (isset($this->updated_at))
            $data['updated_at'] = $this->updated_at;
        if (isset($this->meta_title))
            $data['meta_title'] = $this->meta_title;
        if (isset($this->meta_description))
            $data['meta_description'] = $this->meta_description;
        if (isset($this->meta_keywords))
            $data['meta_keywords'] = $this->meta_keywords;
        if (isset($this->title))
            $data['title'] = $this->title;
        if (isset($this->label))
            $data['label'] = $this->label;
        if (isset($this->link_text))
            $data['link_text'] = $this->link_text;
        if (isset($this->designer))
            $data['designer'] = $this->designer;
        if (isset($this->credit_label))
            $data['credit_label'] = $this->credit_label;
        if (isset($this->photographer))
            $data['photographer'] = $this->photographer;
        if (isset($this->main_title))
            $data['main_title'] = $this->main_title;
        if (isset($this->main_description_one))
            $data['main_description_one'] = $this->main_description_one;
        if (isset($this->main_description_two))
            $data['main_description_two'] = $this->main_description_two;
        if (isset($this->main_description_three))
            $data['main_description_three'] = $this->main_description_three;
        if (isset($this->main_description_four))
            $data['main_description_four'] = $this->main_description_four;
        if (isset($this->main_image_one))
            $data['main_image_one'] = $this->main_image_one;
        if (isset($this->main_image_two))
            $data['main_image_two'] = $this->main_image_two;
        if (isset($this->keyline_quote))
            $data['keyline_quote'] = $this->keyline_quote;
        if (isset($this->banner_way_points))
            $data['banner_way_points'] = $this->banner_way_points;
        if(isset($this->is_featured))
            $data['is_featured'] = $this->is_featured;
        // echo "<pre>";
        // print_r($data);
        // echo "</pre>";
        // exit;
        return $data;
    }
    public function getImages($projectId): array
    {
        $images = [];
        foreach ($this->images as $image) {
            $image['project_id'] = $projectId;
            $image['sort_order'] = $image['sort_order'] ?? 0;
            $img = new Image($image);
            $images[] = $img->toArray();
        }
        return $images;
    }
}

class ProjectResponse
{
    public string|int $project_id;
    public int $site_id;
    public ?string $name;
    public ?string $slug;
    public ?string $description;
    public ?string $preview_text;
    public ?Address $location;
    public string|int|null $status;
    public ?int $status_id;
    /** @var Image[] */
    public ?array $image;
    /** @var Image[] */
    public ?array $images;
    public ?string $created_at;
    public ?string $updated_at;
    public Customer|array|null $customer;
    public ?string $meta_title;
    public ?string $meta_description;
    public ?string $meta_keywords;
    public ?string $title;
    public ?string $label;
    public ?string $link_text;
    public ?string $designer;
    public ?string $credit_label;
    public ?string $photographer;
    public ?string $main_title;
    public ?string $main_description_one;
    public ?string $main_description_two;
    public ?string $main_description_three;
    public ?string $main_description_four;
    public array|string|null $banner_way_points;
    /** @var Image[] */
    public ?array $main_image_one;
    /** @var Image[] */
    public ?array $main_image_two;
     /** @var Image[] */
     public ?array $image_thumb;
    public ?string $keyline_quote;
    public int|null|bool $is_featured;
    
    public function __construct(stdClass $data)
    {
        if (isset($data->project_id))
            $this->project_id = $data->project_id;
        if (isset($data->name))
            $this->name = $data->name;
        if (isset($data->slug))
            $this->slug = makeSlug($data->slug);
        if (isset($data->description))
            $this->description = $data->description;
        if (isset($data->preview_text))
            $this->preview_text = $data->preview_text;
        if (isset($data->location))
            $this->location = new Address(['description' => $data->location]);
        if (isset($data->status_id))
            $this->status_id = $data->status_id;
        if (isset($data->status))
            $this->status = $data->status;
        if (isset($data->image))
            $this->image = $data->image ? array_map(function ($image) {
                if (is_array($image) && isset($image['objectURL'])) {
                    $url = ROOT_DIR . PUBLIC_PATH . $image['objectURL'];
                    if (file_exists($url)) {
                        $image['status'] = ['name' => 'Uploaded', 'severity' => 'success'];
                    }
                    return $image;
                }
                return null;
            }, json_decode($data->image, true)) : [];

        if (isset($data->images) && !empty($data->images) && $data->images !== null) {
            $this->images = array_filter(array_map(function ($image) {
                if (isset($image['image']) && !empty($image['image']) && $image['image'] !== null) {
                    $img = new stdClass();
                    if (isset($image['image'][0]['objectURL'])) {
                        $img = new Image($image['image'][0]);
                        $img->status = ['name' => 'Uploaded', 'severity' => 'success'];
                        $img->project_image_id = $image['project_image_id'];
                        $img->project_id = $image['project_id'];
                        $img->sort_order = $image['sort_order'];
                        $img->created_at = $image['created_at'];
                        return $img;
                    } else if (isset($image['image']['objectURL'])) {
                        $img = new Image($image['image']);
                        $img->status = ['name' => 'Uploaded', 'severity' => 'success'];
                        $img->project_id = $image['project_id'];
                        $img->project_image_id = $image['project_image_id'] ?? null;
                        $img->sort_order = $image['sort_order'];
                        $img->created_at = $image['created_at'];
                        return $img;
                    }
                }
                return null;
            }, json_decode($data->images, true)), function ($item) {
                return $item !== null;
            });
        }
        if (isset($data->created_at))
            $this->created_at = $data->created_at;
        if (isset($data->updated_at))
            $this->updated_at = $data->updated_at;
        if (isset($data->customer_id))
            $this->customer = json_decode($data->customer, true);
        if (isset($data->meta_title))
            $this->meta_title = htmlToPlainText($data->meta_title);
        if (isset($data->meta_description))
            $this->meta_description = htmlToPlainText($data->meta_description);
        if (isset($data->meta_keywords))
            $this->meta_keywords = htmlToPlainText($data->meta_keywords);
        if (isset($data->site_id))
            $this->site_id = $data->site_id;
        if (isset($data->title))
            $this->title = htmlToPlainText($data->title);
        if (isset($data->label))
            $this->label = htmlToPlainText($data->label);
        if (isset($data->link_text))
            $this->link_text = $data->link_text;
        if (isset($data->designer))
            $this->designer = $data->designer;
        if (isset($data->credit_label))
            $this->credit_label = $data->credit_label;
        if (isset($data->photographer))
            $this->photographer = $data->photographer;
        if (isset($data->main_title))
            $this->main_title = $data->main_title;
        if (isset($data->main_description_one))
            $this->main_description_one = $data->main_description_one;
        if (isset($data->main_description_two))
            $this->main_description_two = $data->main_description_two;
        if (isset($data->main_description_three))
            $this->main_description_three = $data->main_description_three;
        if (isset($data->main_description_four))
            $this->main_description_four = $data->main_description_four;
        if (isset($data->main_image_one))
            $this->main_image_one = json_decode($data->main_image_one, true);
        if (isset($data->main_image_two))
            $this->main_image_two = json_decode($data->main_image_two, true);
        if (isset($data->image_thumb))
            $this->image_thumb = json_decode($data->image_thumb, true);
        if (isset($data->keyline_quote))
            $this->keyline_quote = $data->keyline_quote;
        if (isset($data->banner_way_points))
            $this->banner_way_points = json_decode($data->banner_way_points, true);
        if (isset($data->is_featured))
            $this->is_featured = $data->is_featured;
    }
}
