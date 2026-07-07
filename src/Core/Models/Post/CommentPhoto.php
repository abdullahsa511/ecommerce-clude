<?php

declare(strict_types=1);

namespace App\Core\Models\Post;

use App\Core\Models\Base\Model;
use App\Core\Models\Pinboard\PinboardItem;
use App\Core\Models\Media\Media;
use DateTime;

class CommentPhoto extends Model
{
    // Core properties
    public int $comment_photo_id;
    public int $comment_id;
    public int $media_id;
    // public array $image;
    public int $sort_order;
    public int $active_status;
    public string $created_at;
    public string $updated_at;
    protected $casts = [
        'image' => 'json',
    ];

    public function __construct() 
    {
        parent::__construct();
    }
    /**
     * Get the comment this photo belongs to
     * @return array{join: string, select: string}
     */
    public function comment(): array
    {
        return $this->belongsTo(Comment::class, 'comment_id');
    }

    /**
     * Get the comment item
     * @return array{join: string, select: string}
     */
    public function media(): array
    {
        return $this->belongsTo(Media::class, 'media_id');
    }
    
    // pinboard item
    public function pinboardItem(): array
    {
        return $this->belongsTo(PinboardItem::class, 'comment_item_id');
    }

} 