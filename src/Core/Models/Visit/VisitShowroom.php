<?php

declare(strict_types=1);

namespace App\Core\Models\Visit;

use App\Core\Models\Base\Model;
use App\Core\Models\Customer\Customer;
use App\Core\Models\Showroom\Showroom;

class VisitShowroom extends Model
{
    protected string $table = 'visit_showroom';
    protected string $primaryKey = 'visit_showroom_id';

    public int $visit_showroom_id;
    public int $customer_id;
    public ?int $pinboard_id;
    public int $showroom_id;
    public string $tour_type;
    public ?string $date;
    public ?string $meeting_time;
    public ?string $duration;
    public ?string $time_zone;
    public string $created_at;
    public string $updated_at;
    public ?string $cancelled_at;
    public ?string $note;
    public ?string $meeting_link;
    public ?string $label;
    public ?string $showroom_contact_id;
    public ?string $uuid;
    // custom fields
    public ?string $title;
    public ?string $address;
    public ?string $phone;
    public ?string $email;
    public ?string $mobile;
    public ?string $showroom_phone;
    public ?string $showroom_email;
    public ?string $google_map_link;
    public ?string $name;
    public ?string $designation;
    public ?string $image;
    public ?string $customer_email;
    public ?string $customer_name;
    public ?string $customer_phone;
    public ?string $company_name;
    public ?string $source;
    public ?int $user_id;
    
    public function __construct() 
    {
        parent::__construct();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'customer_id');
    }

    public function showroom()
    {
        return $this->belongsTo(Showroom::class, 'showroom_id', 'showrooms_id');
    }
} 