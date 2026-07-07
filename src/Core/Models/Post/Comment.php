<?php

declare(strict_types=1);

namespace App\Core\Models\Post;

use App\Core\Models\Base\Model;
use App\Core\Models\User;
use DateTime;

class Comment extends Model
{
    // Core properties
    public int $comment_id;
    public int $post_id;
    public int $model_id;
    public string $model_type;
    public int $user_id;
    public string $author;
    public string $email;
    public string $url;
    public string $ip;
    public string $content;
    public int $status;
    public int $is_reply;
    public int $votes;
    public string $type;
    public ?int $parent_id;
    public string $created_at;
    public string $updated_at;
    public string $uuid;

    /**
     * Get the comment photo for this comment
     * @return array{hasMany: string, select: string}
     */
    public function commentPhoto(): array
    {
        return $this->hasMany(CommentPhoto::class, 'comment_id', 'comment_id');
    }

    /**
     * Get the post this comment belongs to
     * @return array{join: string, select: string}
     */
    public function post(): array
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    /**
     * Get the user who made this comment
     * @return array{join: string, select: string}
     */
    public function user(): array
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * Get the parent comment if this is a reply
     * @return array{join: string, select: string}
     */
    public function parent(): array
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Get all child comments
     * @return array{join: string, select: string}
     */
    public function children(): array
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Check if this comment is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 1;
    }

    /**
     * Check if this comment is pending
     */
    public function isPending(): bool
    {
        return $this->status === 0;
    }

    /**
     * Check if this comment is spam
     */
    public function isSpam(): bool
    {
        return $this->status === 2;
    }

    /**
     * Check if this comment is a reply
     */
    public function isReply(): bool
    {
        return $this->parent_id > 0;
    }

    /**
     * Get the comment's author URL
     */
    public function getAuthorUrl(): string
    {
        return !empty($this->url) ? $this->url : '#';
    }

    /**
     * Get the comment's author email hash for Gravatar
     */
    public function getAuthorEmailHash(): string
    {
        return md5(strtolower(trim($this->email)));
    }

    /**
     * Get formatted date
     */
    public function getFormattedDate(string $format = 'Y-m-d H:i:s'): string
    {
        $date = new DateTime($this->created_at);
        return $date->format($format);
    }

    /**
     * Get vote count with proper formatting
     */
    public function getVoteCount(): string
    {
        if ($this->votes === 0) {
            return 'No votes';
        }
        return $this->votes === 1 ? '1 vote' : "{$this->votes} votes";
    }

    /**
     * Increment vote count
     */
    public function incrementVotes(): void
    {
        $this->votes++;
    }

    /**
     * Decrement vote count
     */
    public function decrementVotes(): void
    {
        if ($this->votes > 0) {
            $this->votes--;
        }
    }

    public function __construct() 
    {
        parent::__construct();
    }

    public function toArray(): array
    {
        $data = [];
        if(isset($this->comment_id)) $data ['comment_id'] = $this->comment_id;
        if(isset($this->post_id)) $data ['post_id'] = $this->post_id;
        if(isset($this->model_id)) $data ['model_id'] = $this->model_id;
        if(isset($this->model_type)) $data ['model_type'] = $this->model_type;
        if(isset($this->user_id)) $data ['user_id'] = $this->user_id;
        if(isset($this->author)) $data ['author'] = $this->author;
        if(isset($this->email)) $data ['email'] = $this->email;
        if(isset($this->url)) $data ['url'] = $this->url;
        if(isset($this->ip)) $data ['ip'] = $this->ip;
        if(isset($this->content)) $data ['content'] = $this->content;
        if(isset($this->status)) $data ['status'] = $this->status;
        if(isset($this->is_reply)) $data ['is_reply'] = $this->is_reply;
        if(isset($this->votes)) $data ['votes'] = $this->votes;
        if(isset($this->type)) $data ['type'] = $this->type;
        if(isset($this->parent_id)) $data ['parent_id'] = $this->parent_id;
        if(isset($this->created_at)) $data ['created_at'] = $this->created_at;
        if(isset($this->updated_at)) $data ['updated_at'] = $this->updated_at;
        return $data;
    }
} 