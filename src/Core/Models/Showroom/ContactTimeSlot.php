<?php

declare(strict_types=1);

namespace App\Core\Models\Showroom;

use App\Core\Models\Base\Model;

class ContactTimeSlot extends Model
{
    protected string $table = 'contact_time_slot';
    protected string $primaryKey = 'contact_time_slot_id';

    public int $contact_time_slot_id;
    public int $showroom_contact_id;
    public string $slot_time;
    public ?string $note;
    public int $status;
    public ?string $created_at;
    public ?string $updated_at;

    public function __construct()
    {
        parent::__construct();
    }

    public function showroomContact()
    {
        return $this->belongsTo(ShowroomContact::class, 'showroom_contact_id', 'showroom_contact_id');
    }

}
