<?php

declare(strict_types=1);

namespace App\Core\Models\Post;

use App\Core\Models\Base\Model;
use App\Core\Models\User;
use App\Core\Models\Comment;
use DateTime;

class CommentUpvote extends Model
{
    // Core properties
    public int $comment_upvote_id;
    public int $comment_id;
    public int $user_id;
    public string $created_at;
    public string $updated_at;
    public ?string $deleted_at;

    public function __construct() 
    {
        parent::__construct();
    }
    /**
     * Get the comment this upvote belongs to
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
    public function user(): array
    {
        return $this->belongsTo(User::class, 'user_id');
    }


} 