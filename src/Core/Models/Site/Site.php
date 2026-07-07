<?php

declare(strict_types=1);

namespace App\Core\Models\Site;

use App\Core\Models\Base\Model;
use stdClass;

class Description {
    public string $title;
    public string $description;
    public string $meta_keywords;
    public string $meta_description;
    public string $phone_number;

    public function __construct(array $data = []) {
        $this->title = $data['title'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->meta_keywords = $data['meta_keywords'] ?? '';
        $this->meta_description = $data['meta_description'] ?? '';
        $this->phone_number = $data['phone_number'] ?? '';
    }
}

class LocalSettings {
    public string $address;
    public string $geocode;
    public string|int $country_id;
    public string|int $region_id;
    public string $company;
    public string|int $vat_id;
    public string|int $language_id;
    public string|int $currency_id;
    public string|int $length_type_id;
    public string|int $weight_type_id;
    public string|int $timezone;
    public string $date_format;
    public string $time_format;

    public function __construct(array $data = []) {
        $this->address = $data['address'] ?? '';
        $this->geocode = $data['geocode'] ?? '';
        $this->country_id = $data['country_id'] ?? '';
        $this->region_id = $data['region_id'] ?? '';
        $this->company = $data['company'] ?? '';
        $this->vat_id = $data['vat_id'] ?? '';
        $this->language_id = $data['language_id'] ?? '';
        $this->currency_id = $data['currency_id'] ?? '';
        $this->length_type_id = $data['length_type_id'] ?? '';
        $this->weight_type_id = $data['weight_type_id'] ?? '';
        $this->timezone = $data['timezone'] ?? '';
        $this->date_format = $data['date_format'] ?? '';
        $this->time_format = $data['time_format'] ?? '';
    }
}

class MediaSettings {
    public string|int $post_extra_large_width;
    public string|int $post_extra_large_height;
    public string $post_extra_large_method;
    public string|int $post_large_width;
    public string|int $post_large_height;
    public string $post_large_method;
    public string|int $post_medium_width;
    public string|int $post_medium_height;
    public string $post_medium_method;
    public string|int $post_thumb_width;
    public string|int $post_thumb_height;
    public string $post_thumb_method;
    public string|int $product_extra_large_width;
    public string|int $product_extra_large_height;
    public string $product_extra_large_method;
    public string|int $product_large_width;
    public string|int $product_large_height;
    public string $product_large_method;
    public string|int $product_medium_width;
    public string|int $product_medium_height;
    public string $product_medium_method;
    public string|int $product_thumb_width;
    public string|int $product_thumb_height;
    public string $product_thumb_method;
    public string $format;
    public string $quality;

    public function __construct(array $data = []) {
        $this->post_extra_large_width = $data['post_extra_large_width'] ?? '';
        $this->post_extra_large_height = $data['post_extra_large_height'] ?? '';
        $this->post_extra_large_method = $data['post_extra_large_method'] ?? '';
        $this->post_large_width = $data['post_large_width'] ?? '';
        $this->post_large_height = $data['post_large_height'] ?? '';
        $this->post_large_method = $data['post_large_method'] ?? '';
        $this->post_medium_width = $data['post_medium_width'] ?? '';
        $this->post_medium_height = $data['post_medium_height'] ?? '';
        $this->post_medium_method = $data['post_medium_method'] ?? '';
        $this->post_thumb_width = $data['post_thumb_width'] ?? '';
        $this->post_thumb_height = $data['post_thumb_height'] ?? '';
        $this->post_thumb_method = $data['post_thumb_method'] ?? '';
        $this->product_extra_large_width = $data['product_extra_large_width'] ?? '';
        $this->product_extra_large_height = $data['product_extra_large_height'] ?? '';
        $this->product_extra_large_method = $data['product_extra_large_method'] ?? '';
        $this->product_large_width = $data['product_large_width'] ?? '';
        $this->product_large_height = $data['product_large_height'] ?? '';
        $this->product_large_method = $data['product_large_method'] ?? '';
        $this->product_medium_width = $data['product_medium_width'] ?? '';
        $this->product_medium_height = $data['product_medium_height'] ?? '';
        $this->product_medium_method = $data['product_medium_method'] ?? '';
        $this->product_thumb_width = $data['product_thumb_width'] ?? '';
        $this->product_thumb_height = $data['product_thumb_height'] ?? '';
        $this->product_thumb_method = $data['product_thumb_method'] ?? '';
        $this->format = $data['format'] ?? '';
        $this->quality = $data['quality'] ?? '';
    }
}

class CommentSettings {
    public bool $allow_comments;
    public bool $logged_in_comments;
    public bool $break_comments;
    public string|int $close_comments_days_old;
    public string|int $thread_comments_depth;
    public string|int $comments_per_page;
    public string|int $default_comments_page;
    public string $comment_order;

    public function __construct(array $data = []) {
        $this->allow_comments = $data['allow_comments'] ?? false;
        $this->logged_in_comments = $data['logged_in_comments'] ?? false;
        $this->break_comments = $data['break_comments'] ?? false;
        $this->close_comments_days_old = $data['close_comments_days_old'] ?? '';
        $this->thread_comments_depth = $data['thread_comments_depth'] ?? '';
        $this->comments_per_page = $data['comments_per_page'] ?? '';
        $this->default_comments_page = $data['default_comments_page'] ?? '';
        $this->comment_order = $data['comment_order'] ?? '';
    }
}

class OrderSettings {
    public string $invoice_format;
    public string $customer_order_format;
    public bool $display_weight;
    public bool $allow_guest_checkout;
    public string $new_order_status;
    public string $subtract_stock_status;
    public string $enable_downloads_status;

    public function __construct(array $data = []) {
        $this->invoice_format = $data['invoice_format'] ?? '';
        $this->customer_order_format = $data['customer_order_format'] ?? '';
        $this->display_weight = $data['display_weight'] ?? false;
        $this->allow_guest_checkout = $data['allow_guest_checkout'] ?? false;
        $this->new_order_status = $data['new_order_status'] ?? '';
        $this->subtract_stock_status = $data['subtract_stock_status'] ?? '';
        $this->enable_downloads_status = $data['enable_downloads_status'] ?? '';
    }
}

class OtherSettings {
    public string $field;
    public string $value;
    public string $label;
    public bool $isSecret;
    public string $placeholder;
    public string $helper;

    public function __construct(array $data = []) {
        $this->field = $data['field'] ?? '';
        $this->value = $data['value'] ?? '';
        $this->label = $data['label'] ?? '';
        $this->isSecret = $data['isSecret'] ?? false;
        $this->placeholder = $data['placeholder'] ?? '';
        $this->helper = $data['helper'] ?? '';
    }
}

class SeoSettings {
    public string $open_graph_title;
    public string $open_graph_description;
    public string $twitter_title;
    public string $twitter_description;
    public string $twitter_label_1;
    public string $twitter_label_2;
    public string $twitter_data_1;
    public string $twitter_data_2;

    public function __construct(array $data = []) {
        $this->open_graph_title = $data['open_graph_title'] ?? '';
        $this->open_graph_description = $data['open_graph_description'] ?? '';
        $this->twitter_title = $data['twitter_title'] ?? '';
        $this->twitter_description = $data['twitter_description'] ?? '';
        $this->twitter_label_1 = $data['twitter_label_1'] ?? '';
        $this->twitter_label_2 = $data['twitter_label_2'] ?? '';
        $this->twitter_data_1 = $data['twitter_data_1'] ?? '';
        $this->twitter_data_2 = $data['twitter_data_2'] ?? '';
    }
}

class SocialSettings {
    private array $settings = [];

    public function __construct(array $data = []) {
        foreach ($data as $item) {
            if(isset($item['key']) && isset($item['value'])) {
                $this->settings[$item['key']] = $item['value'];
            }
        }
    }

    public function getSettings(): array {
        return $this->settings;
    }
}

class SiteLogoSettings {
    public array|string $site_logo_favicon;
    public array|string $site_logo;
    public array|string $site_logo_sticky;
    public array|string $site_logo_dark;
    public array|string $site_logo_dark_sticky;

    public function __construct(array $data = []) {
        unset($data['site_logo_favicon'][0]['file']);
        unset($data['site_logo'][0]['file']);
        unset($data['site_logo_sticky'][0]['file']);
        unset($data['site_logo_dark'][0]['file']);
        unset($data['site_logo_dark_sticky'][0]['file']);
        $this->site_logo_favicon = $data['site_logo_favicon'] ?? '';
        $this->site_logo = $data['site_logo'] ?? '';
        $this->site_logo_sticky = $data['site_logo_sticky'] ?? '';
        $this->site_logo_dark = $data['site_logo_dark'] ?? '';
        $this->site_logo_dark_sticky = $data['site_logo_dark_sticky'] ?? '';
    }
}
class SiteSettings {
    public SiteLogoSettings $siteLogoSettings;
    public array $siteBannerSettings;
    public $settings;

    public function __construct(array $data = []) {
        $this->siteLogoSettings = new SiteLogoSettings($data['site_logo_settings']??[]);
        $this->siteBannerSettings = $data['site_banner_settings'] ?? [];
        $this->settings = new stdClass;
        foreach($data as $key => $value) {
            if(!in_array($key, ['site_logo_settings', 'site_banner_settings'])) {
                $this->settings->$key = $value;
            }
        }
    }
}

class Site extends Model
{
    public int $site_id;
    public string $key;
    public string $name;
    public string $host;
    public string $theme;
    public string $template;
    public string $admin_email;
    public string $contact_email;
    public ?string $description;
    public ?string $local_settings;
    public ?string $media_settings;
    public ?string $comments_settings;
    public ?string $orders_settings;
    public ?string $others_settings;
    public ?string $seo_settings;
    public ?string $social_settings;
    public ?string $site_settings;
   
    
} 
class SiteResponse
{
    public int $site_id;
    public string $key;
    public string $name;
    public string $host;
    public string $theme;
    public string $template;
    public string $admin_email;
    public string $contact_email;
    public array $descriptionSettings;
    public array $localSettings;
    public array $mediaSettings;
    public array $commentSettings;
    public array $orderSettings;
    public array $otherSettings;
    public array $seoSettings;
    public array $socialSettings;
    public array $siteSettings;
    public function __construct(stdClass|null $data) 
    {
        $this->site_id = $data?->site_id ?? 0;
        $this->key = $data?->key ?? '';
        $this->name = $data?->name ?? '';
        $this->host = $data?->host ?? '';
        $this->theme = $data?->theme ?? '';
        $this->template = $data?->template ?? '';
        $this->admin_email = $data?->admin_email ?? '';
        $this->contact_email = $data?->contact_email ?? '';
        $this->descriptionSettings = json_decode($data?->description ?? '{}', true) ?? [];
        $this->localSettings = json_decode($data?->local_settings ?? '{}', true) ?? [];
        $this->mediaSettings = json_decode($data?->media_settings ?? '{}', true) ?? [];
        $this->commentSettings = json_decode($data?->comments_settings ?? '{}', true) ?? [];
        $this->orderSettings = json_decode($data?->orders_settings ?? '{}', true) ?? [];
        $this->otherSettings = json_decode($data?->others_settings ?? '{}', true) ?? [];
        $this->seoSettings = json_decode($data?->seo_settings ?? '{}', true) ?? [];
        $this->socialSettings = json_decode($data?->social_settings ?? '{}', true) ?? [];
        $this->siteSettings = json_decode($data?->site_settings ?? '{}', true) ?? [];
    }

    public function toArray(): array
    {
        return (array) $this;
    }
    
} 

class SiteData {
    public string $key;
    public string $name;
    public string $host;
    public string $theme;
    public string $template;
    public string $admin_email;
    public string $contact_email;
    public Description $description;
    public LocalSettings $local_settings;
    public MediaSettings $media_settings;
    public CommentSettings $comments_settings;
    public OrderSettings $orders_settings;
    public OtherSettings $others_settings;
    public SeoSettings $seo_settings;
    public SocialSettings $social_settings;
    public SiteSettings $site_settings;
    public function __construct($data) 
    {
        if(isset($data['key'])) $this->key = $data['key'];
        if(isset($data['name'])) $this->name = $data['name'];
        if(isset($data['host'])) $this->host = $data['host'];
        if(isset($data['theme'])) $this->theme = $data['theme'];
        if(isset($data['template'])) $this->template = $data['template'];
        if(isset($data['admin_email'])) $this->admin_email = $data['admin_email'];
        if(isset($data['contact_email'])) $this->contact_email = $data['contact_email'];
        if(isset($data['descriptionSettings'])) $this->description = new Description($data['descriptionSettings']);
        if(isset($data['localSettings'])) $this->local_settings = new LocalSettings($data['localSettings']);
        if(isset($data['mediaSettings'])) $this->media_settings = new MediaSettings($data['mediaSettings']);
        if(isset($data['commentSettings'])) $this->comments_settings = new CommentSettings($data['commentSettings']);
        if(isset($data['orderSettings'])) $this->orders_settings = new OrderSettings($data['orderSettings']);
        if(isset($data['otherSettings'])) $this->others_settings = new OtherSettings($data['otherSettings']);
        if(isset($data['seoSettings'])) $this->seo_settings = new SeoSettings($data['seoSettings']);
        if(isset($data['socialSettings'])) $this->social_settings = new SocialSettings($data['socialSettings']);
        if(isset($data['siteSettings'])) $this->site_settings = new SiteSettings($data['siteSettings']);
    }

    public function toArray(): array
    {
        $data = [];
        if(isset($this->key)) $data ['key'] = $this->key;
        if(isset($this->name)) $data ['name'] = $this->name;
        if(isset($this->host)) $data ['host'] = $this->host;
        if(isset($this->theme)) $data ['theme'] = $this->theme;
        if(isset($this->template)) $data ['template'] = $this->template;
        if(isset($this->admin_email)) $data ['admin_email'] = $this->admin_email;
        if(isset($this->contact_email)) $data ['contact_email'] = $this->contact_email;
        if(isset($this->description)) $data ['description'] = json_encode($this->description);
        if(isset($this->local_settings)) $data ['local_settings'] = json_encode($this->local_settings);
        if(isset($this->media_settings)) $data ['media_settings'] = json_encode($this->media_settings);
        if(isset($this->comments_settings)) $data ['comments_settings'] = json_encode($this->comments_settings);
        if(isset($this->orders_settings)) $data ['orders_settings'] = json_encode($this->orders_settings);
        if(isset($this->others_settings)) $data ['other_settings'] = json_encode($this->others_settings);
        if(isset($this->seo_settings)) $data ['seo_settings'] = json_encode($this->seo_settings);
        if(isset($this->social_settings)) $data ['social_settings'] = json_encode($this->social_settings);
        if(isset($this->site_settings)) $data ['site_settings'] = json_encode($this->site_settings);
        return $data;
    }
}