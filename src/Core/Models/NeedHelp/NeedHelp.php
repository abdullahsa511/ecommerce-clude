<?php

declare(strict_types=1);

namespace App\Core\Models\NeedHelp;


use App\Core\Models\Base\Model;

class NeedHelp extends Model
{
    protected string $table = 'need_help';
    protected string $primaryKey = 'need_help_id';

    public int $need_help_id;
    public ?string $icon;
    public ?string $title;
    public ?string $description;
    public ?string $link;
    public ?string $link_text;
    public ?string $link_icon;
    
}
