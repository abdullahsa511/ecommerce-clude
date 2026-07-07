<?php

declare(strict_types=1);

namespace App\Core\Models\Service;

use App\Core\Models\Base\Model;
use App\Core\Models\Order\Order;
use App\Core\Models\Pinboard\Pinboard;
use App\Core\Models\User;
use stdClass;

class ServiceRequest extends Model
{
    protected string $table = 'service_request';

    protected ?int $service_request_id;
    protected ?string $uuid;
    protected ?int $pinboard_id;
    protected ?int $customer_id;
    protected ?string $email;
    protected ?string $company;
    protected ?string $first_name;
    protected ?string $last_name;
    protected ?string $request_type;
    protected ?string $catalogue_format;
    protected ?string $form_type;
    protected ?string $content;
    protected ?string $phone_number;
    protected ?string $mailing_address;
    protected ?string $comment_attachment;
    protected ?string $source_of_enquiry;
    protected ?string $attachments;
    protected ?string $created_at;
    protected ?string $updated_at;
    protected ?string $project_details;
    protected ?string $state;

    public function __construct() 
    {
        parent::__construct();
    }

    public function pinboard(){
        return $this->belongsTo(Pinboard::class, 'pinboard_id', 'pinboard_id');
    }
}
