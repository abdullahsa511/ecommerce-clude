<?php

declare(strict_types=1);

namespace App\Core\Models\Media;

use App\Core\Models\Base\Model;

class MediaContent extends Model
{
    protected string $table = 'media_content';
    protected array $fillable = [
        'media_id',
        'language_id',
        'name',
        'caption',
        'description'
    ];

    public int $media_id;
    public int $language_id;
    public string $name;
    public string $caption;
    public string $description;

    public function __construct() 
    {
        parent::__construct();
    }

    /**
     * Define relationship with Media model
     */
    public function media()
    {
        return $this->belongsTo(Media::class, 'media_id');
    }
} 