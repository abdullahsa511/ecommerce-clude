<?php

declare(strict_types=1);

namespace App\Core\Models\Post;

use App\Core\Models\Base\Model;
use App\Core\Models\Admin\Admin;
use App\Core\Models\Localisation\Language;

class PostContentRevision extends Model
{
    // Core properties
    public int $post_id;
    public int $language_id;
    public string $created_at;
    public int $admin_id;
    public ?string $content = null;

    // Cached properties from joins
    public ?string $display_name = null;
    public ?string $username = null;

    /**
     * Get the post this revision belongs to
     * @return array{join: string, select: string}
     */
    public function post(): array
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    /**
     * Get the language this revision is in
     * @return array{join: string, select: string}
     */
    public function language(): array
    {
        return $this->belongsTo(Language::class, 'language_id');
    }

    /**
     * Get the admin who created this revision
     * @return array{join: string, select: string}
     */
    public function admin(): array
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    /**
     * Get formatted creation date
     */
    public function getFormattedDate(string $format = 'Y-m-d H:i:s'): string
    {
        return date($format, strtotime($this->created_at));
    }

    /**
     * Get admin display name
     */
    public function getAdminName(): string
    {
        return $this->display_name ?? $this->username ?? 'Unknown';
    }

    public function __construct() 
    {
        parent::__construct();
    }
} 