<?php

declare(strict_types=1);

namespace App\Core\Models\Post;

use App\Core\Models\Base\Model;
use App\Core\Models\Post\PostContentMeta;
use App\Core\Models\Localisation\Language;

class PostContent extends Model
{
    // Core properties
    public int $post_id;
    public int $language_id;
    public string $name = '';
    public string $slug = '';
    public ?string $content = null;
    public ?string $excerpt = null;
    public string $meta_keywords = '';
    public string $meta_description = '';
    public ?string $label;
    public ?string $link_text;

    /**
     * Get the post this content belongs to
     * @return array{join: string, select: string}
     */
    public function post(): array
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    /**
     * Get the language this content is in
     * @return array{join: string, select: string}
     */
    public function language(): array
    {
        return $this->belongsTo(Language::class, 'language_id');
    }

    /**
     * Get the meta data for this content
     * @return array{join: string, select: string}
     */
    public function meta(): array
    {
        return $this->hasMany(PostContentMeta::class, 'post_id');
    }

    /**
     * Get the URL for this content
     */
    public function getUrl(): string
    {
        return "/post/{$this->slug}";
    }

    /**
     * Get a truncated excerpt
     */
    public function getTruncatedExcerpt(int $length = 200): string
    {
        if (empty($this->excerpt)) {
            $content = strip_tags($this->content ?? '');
            return mb_substr($content, 0, $length) . (mb_strlen($content) > $length ? '...' : '');
        }
        return $this->excerpt;
    }

    /**
     * Override primary key to match schema (composite exists, use post_id for grouping)
     */
    public function getPrimaryKey(): string
    {
        return 'post_id';
    }

    /**
     * Get meta keywords as array
     */
    public function getMetaKeywordsArray(): array
    {
        return array_filter(array_map('trim', explode(',', $this->meta_keywords)));
    }

    /**
     * Set meta keywords from array
     */
    public function setMetaKeywordsArray(array $keywords): void
    {
        $this->meta_keywords = implode(', ', array_filter($keywords));
    }

    public function __construct() 
    {
        parent::__construct();
    }
} 