<?php

declare(strict_types=1);

namespace App\Core\Models\Post;

use App\Core\Models\Base\Model;
use App\Core\Models\Localisation\Language;

class PostContentMeta extends Model
{
    protected string $table = 'post_content_meta';
    protected string $primaryKey = 'post_id';

    // Core properties
    public int $post_id;
    public string $namespace;
    public string $key;
    public string $value;
    public int $language_id;

    /**
     * Get the post this meta belongs to
     * @return array{join: string, select: string}
     */
    public function post(): array
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    /**
     * Get the language this meta is in
     * @return array{join: string, select: string}
     */
    public function language(): array
    {
        return $this->belongsTo(Language::class, 'language_id');
    }

    /**
     * Get meta value
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Set meta value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function __construct() 
    {
        parent::__construct();
    }
} 