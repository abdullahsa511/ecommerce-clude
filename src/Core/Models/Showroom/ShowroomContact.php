<?php

declare(strict_types=1);

namespace App\Core\Models\Showroom;

use App\Core\Models\Base\Model;

class ShowroomContact extends Model
{
    protected string $table = 'showroom_contact';
    protected string $primaryKey = 'showroom_contact_id';

    public int $showroom_contact_id;
    public int $showroom_id;
    public string $name;
    public array|string|null $image;
    public ?string $email;
    public ?string $phone;
    public ?string $designation;
    public ?string $message;
    public int $sort_order;
    public int $status;
    public ?string $created_at;
    public ?string $updated_at;
    public ?string $deleted_at;

    public function __construct()
    {
        parent::__construct();
    }

    public function showroom()
    {
        return $this->belongsTo(Showroom::class, 'showroom_id', 'showrooms_id');
    }
}
