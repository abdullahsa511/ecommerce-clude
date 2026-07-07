<?php

declare(strict_types=1);

namespace App\Core\Models\Search;

use App\Core\Models\Base\Model;


class PopularSearch extends Model
{
    protected string $table = 'popular_search';
    protected string $primaryKey = 'popular_search_id';

    public int $popular_search_id;
    public string $search_key;
    public int $search_count;
    public ?string $created_at;
    public ?string $updated_at;
    public ?string $deleted_at;
    
    public function __construct() 
    {
        parent::__construct();
    }

} 