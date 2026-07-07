<?php

declare(strict_types=1);

namespace App\Core\Repositories\Visit;

use App\Core\Exceptions\NotFoundHttpException;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Models\Visit\VisitShowroom;
use App\Core\Repositories\Showroom\ShowroomRepositoryInterface;
use App\Core\Repositories\Showroom\ShowroomDateTimeRepository;
use DateTimeImmutable;
use App\Core\Repositories\Pinboard\PinboardRepositoryInterface;
// customer repository
use App\Core\Repositories\Customer\CustomerRepositoryInterface;
use App\Core\Repositories\Email\EmailRepositoryInterface;
use DateTime;
use DateTimeZone;
use Exception;
use function App\Core\System\utils\uuidToBin;
use function App\Core\System\utils\generateUuidV4;
use function App\Core\System\utils\binToUuid;
use function App\Core\System\utils\config;
use PDO;

class VisitShowroomRepository extends BaseRepository implements VisitShowroomRepositoryInterface
{

    private CustomerRepositoryInterface $customerRepository;
    private EmailRepositoryInterface $emailRepository;
    private ShowroomRepositoryInterface $showroomRepository;
    private PinboardRepositoryInterface $pinboardRepository;
    private ShowroomDateTimeRepository $showroomDateTimeRepository;

    public function __construct(
        PDO $db,
        CustomerRepositoryInterface $customerRepository,
        EmailRepositoryInterface $emailRepository,
        ShowroomRepositoryInterface $showroomRepository,
        PinboardRepositoryInterface $pinboardRepository,
        ShowroomDateTimeRepository $showroomDateTimeRepository
    ) {
        parent::__construct($db, 'visit_showroom', VisitShowroom::class);
        $this->customerRepository = $customerRepository;
        $this->emailRepository = $emailRepository;
        $this->showroomRepository = $showroomRepository;
        $this->pinboardRepository = $pinboardRepository;
        $this->showroomDateTimeRepository = $showroomDateTimeRepository;
    }

    public function getShowroomDateTimes(DateTimeImmutable|string|null $at = null): array
    {
        return $this->showroomDateTimeRepository->getShowroomDateTimes($at);
    }


    public function bookNow(array $data): array
    {
        // ===================== Extract & Normalize Data =====================
        $tourType          = $data['tour_type'] ?? 'physicalTour';
        $date              = $data['date'] ?? null;
        $meetingTime       = !empty($data['meeting_time']) 
            ? date('H:i:s', strtotime($data['meeting_time'])) 
            : null;
    
        $duration          = $data['duration'] ??60;
        $timeZone          = $data['time_zone'] ?? 'AEST (GMT+10:00) Time in Sydney NSW';
        $label             = $data['label'] ?? $tourType;
        $meetingLink       = $data['meeting_link'] ?? null;
        $customerId        = $data['customer_id'] ?? 1;
        $customerName      = $data['customer_name'] ?? null;
        $showroomId        = $data['showroom_id'] ?? 1;
        $contactId         = 22; // sales team contact id
        $email             = $data['email'] ?? null;
        $pinboardId        = isset($data['pinboard_id']) ? $data['pinboard_id'] : null;
        $location          = $data['location'] ?? null;
        $source            = $data['source'] ?? 'Contact Sales';
        $enquiryType       = $data['enquiry_type'] ?? null;
        // ===================== Validation =====================
        if (empty($email)) {
            return ['error' => 'Email is required'];
        }
    
        if (empty($date) || empty($meetingTime)) {
            return ['error' => 'Date and meeting time are required'];
        }
    
        // ===================== Prepare Booking Data =====================
        $bookingData = [
            'uuid'                 => $this->generateUuid(),
            'customer_id'          => $customerId,
            'showroom_id'          => $showroomId,
            'showroom_contact_id'  => $contactId,
            'tour_type'            => $tourType,
            'date'                 => $date,
            'meeting_time'         => $meetingTime,
            'duration'             => $duration,
            'time_zone'            => $timeZone,
            'label'                => $label,
            'meeting_link'         => $meetingLink,
            'pinboard_id'          => $pinboardId,
            'source'               => $source,
            'enquiry_type'         => $enquiryType
        ];
    
        // ===================== Save Booking =====================
        $visitShowroom = $this->model->create($bookingData);
    
        if (!$visitShowroom) {
            return NotFoundHttpException::create('Something went wrong. Failed to create a booking.');
        }

        // update pinboard status to booked
        if ($pinboardId) {
            $this->pinboardRepository->updatePinboardStatus($pinboardId, 8); // 8 is the status id for In-discussion
        }

        $appUrl = config('APP_URL');
        $uuid = $visitShowroom->uuid;
        if ($pinboardId) {
            if ($tourType === "physicalTour") {
                $bookNowLink = $appUrl . '/pinboards/book-showroom-visit/' . $uuid . '#reschedule';
                $cancelLink = $appUrl . '/pinboards/cancelled-showroom-visit/' . $uuid;
            }else{
                $bookNowLink = $appUrl . '/pinboards/virtual-meeting/' . $uuid . '#reschedule';
                $cancelLink = $appUrl . '/pinboards/cancelled-virtual-meeting/' . $uuid;
            }
        }else{
            if ($tourType === "physicalTour") {
                $bookNowLink = $appUrl . '/contact-us/book-physical-showroom-visit/' . $uuid;
                $cancelLink = $appUrl . '/contact-us/cancelled-physical-showroom-visit/' . $uuid;
            }else{
                $bookNowLink = $appUrl . '/contact-us/virtual-meeting-booking/' . $uuid;
                $cancelLink = $appUrl . '/contact-us/cancelled-virtual-meeting-booking/' . $uuid;
            }
        }

        // ===================== Get Showroom Data =====================
        $showroomData = $this->showroomRepository->findById($showroomId);
        $googleMapLink = isset($showroomData->google_map_link) ? $showroomData->google_map_link : null;

        // ===================== Email Setup =====================
        $loaderPath = ROOT_DIR . '/src/themes/landing/src/emailtemplate';
    
        // ===================== Send Emails =====================
        $this->sendBookingEmails(
            tourType: $tourType,
            email: $email,
            customerName: $customerName,
            date: $date,
            meetingTime: $meetingTime,
            timeZone: $timeZone,
            location: $location,
            loaderPath: $loaderPath,
            meetingLink: $meetingLink,
            rescheduleLink: $bookNowLink,
            cancelLink: $cancelLink,
            googleMapLink: $googleMapLink,
            pinboardId: $pinboardId,
            data: $bookingData
        );
    
        // ===================== Response =====================
        return [
            'success'  => true,
            'message'  => 'Book now successful',
            'data'     => (array) ($visitShowroom->data ?? []),
            // 'customer' => $customerData,
        ];
    }

    public function rescheduleBooking(array $data): array
    {
        // ===================== Extract & Normalize Data =====================
        $tourType          = $data['tour_type'] ?? 'physicalTour';
        $date              = $data['date'] ?? null;
        $meetingTime       = !empty($data['meeting_time']) 
            ? date('H:i:s', strtotime($data['meeting_time'])) 
            : null;

        $customerName      = isset($data['customer_name']) ? $data['customer_name'] : null;
    
        $duration          = $data['duration'] ??60;
        $visitShowroomId   = isset($data['visit_showroom_id']) ? (int) $data['visit_showroom_id'] : null;
        $timeZone          = $data['time_zone'] ?? 'AEST (GMT+10:00) Time in Sydney NSW';
        $label             = $data['label'] ?? $tourType;
        $meetingLink       = $data['meeting_link'] ?? null;
        $googleMapLink     = $data['google_map_link'] ?? null;
        $customerId        = $data['customer_id'] ?? 1;
        $showroomId        = $data['showroom_id'] ?? 1;
        $contactId         = 22; // sales team contact id
        $email             = $data['email'] ?? null;
        $pinboardId        = isset($data['pinboard_id']) ? $data['pinboard_id'] : null;
        $location          = $data['location'] ?? null;
    
        // ===================== Validation =====================
        if (empty($email)) {
            return ['error' => 'Email is required'];
        }
    
        if (empty($date) || empty($meetingTime)) {
            return ['error' => 'Date and meeting time are required'];
        }
    
        // ===================== Prepare Booking Data =====================
        $bookingData = [
            'date'                 => $date,
            'meeting_time'         => $meetingTime,
            'time_zone'            => $timeZone,
        ];
    
        // ===================== Save Booking =====================
        $this->model->clearQuery();
        $visitShowroom = $this->model->where('visit_showroom_id', '=', $visitShowroomId)->first();
        if (!$visitShowroom) {
            return ['error' => 'Visit showroom not found'];
        }
        $visitShowroom->update($bookingData);
    
        if (!$visitShowroom) {
            return ['error' => 'Failed to book now'];
        }

        $appUrl = config('APP_URL');
        $encodedVisitShowroomId = base64_encode((string) $visitShowroomId);
        $uuid = $visitShowroom->uuid;
        $showroomId = $visitShowroom->showroom_id;
        $pinboardId = $visitShowroom->pinboard_id;
        if ($pinboardId) {
            if ($tourType === "physicalTour") {
                $rescheduleLink = $appUrl . '/pinboards/rescheduled-showroom-visit/' . $uuid;
                $cancelLink = $appUrl . '/pinboards/cancelled-showroom-visit/' . $uuid;
            }else{
                $rescheduleLink = $appUrl . '/pinboards/rescheduled-virtual-meeting/' . $uuid;
                $cancelLink = $appUrl . '/pinboards/cancelled-virtual-meeting/' . $uuid;
            }
        }else{
            if ($tourType === "physicalTour") {
                $rescheduleLink = $appUrl . '/contact-us/rescheduled-physical-showroom-visit/' . $uuid;
                $cancelLink = $appUrl . '/contact-us/cancelled-physical-showroom-visit/' . $uuid;
            }else{
                $rescheduleLink = $appUrl . '/contact-us/rescheduled-virtual-meeting-booking/' . $uuid;
                $cancelLink = $appUrl . '/contact-us/cancelled-virtual-meeting-booking/' . $uuid;
            }
        }
        // ===================== Email Setup =====================
        $loaderPath = ROOT_DIR . '/src/themes/landing/src/emailtemplate';
    
        // ===================== Send Emails =====================
        $this->sendRescheduleBookingEmails(
            tourType: $tourType,
            email: $email,
            customerName: $customerName,
            date: $date,
            meetingTime: $meetingTime,
            timeZone: $timeZone,
            location: $location,
            loaderPath: $loaderPath,
            meetingLink: $meetingLink,
            rescheduleLink: $rescheduleLink,
            cancelLink: $cancelLink,
            googleMapLink: $googleMapLink,
            showroom_id : $showroomId
        );
    
        // ===================== Response =====================
        return [
            'success'  => true,
            'message'  => 'Reschedule booking successful',
            'data'     => (array) ($visitShowroom->data ?? []),
            // 'customer' => $customerData,
        ];
    }

    public function cancelBooking(int $visit_showroom_id): array
    {
        $this->model->clearQuery();
        $visitShowroom = $this->model->join('customer', 'customer.customer_id', '=', 'visit_showroom.customer_id')
        ->select(['visit_showroom_id', 'tour_type', 'showroom_id', 'date', 'time_zone', 'meeting_time', 'customer.company_name', 'customer.name', 'customer.gmail_Id as email'])
        ->where('visit_showroom_id', '=', $visit_showroom_id)
        ->first();
        if (!$visitShowroom) {
            return ['error' => 'Visit showroom not found'];
        }
        // $visitShowroom->delete($visitShowroomId);
        $visitShowroom->update(['cancelled_at' => date('Y-m-d H:i:s')]);
        // ===================== Email Setup =====================
        $loaderPath = ROOT_DIR . '/src/themes/landing/src/emailtemplate';
        $tourType = isset($visitShowroom->tour_type) ? $visitShowroom->tour_type : null;
        $showroomId = isset($visitShowroom->showroom_id) ? $visitShowroom->showroom_id : null;
        $email = isset($visitShowroom->email) ? $visitShowroom->email : null;
        $customerName = isset($visitShowroom->name) ? $visitShowroom->name : null;
        $date = isset($visitShowroom->date) ? $visitShowroom->date : null;
        $meetingTime = isset($visitShowroom->meeting_time) ? $visitShowroom->meeting_time : null;
        $location = isset($visitShowroom->company_name) ? $visitShowroom->company_name : null;
        $timeZone = isset($visitShowroom->time_zone) ? $visitShowroom->time_zone : null;
        $companyName = isset($visitShowroom->company_name) ? $visitShowroom->company_name : null;
        // ===================== Send Emails =====================
        $this->sendCancelBookingEmails(
            tourType: $tourType,
            email: $email,
            customerName: $customerName,
            date: $date,
            meetingTime: $meetingTime,
            timeZone: $timeZone,
            location: $location,
            loaderPath: $loaderPath,
            companyName: $companyName,
            showroom_id: $showroomId
        );
    
        // ===================== Response =====================
        return [
            'success'  => true,
            'message'  => 'Cancel booking successful',
        ];
    }
    
    /**
     * Handle all booking-related emails
     */
    private function sendBookingEmails(
        string $tourType,
        string $email,
        string $customerName,
        string $date,
        string $meetingTime,
        string $timeZone,
        ?string $location,
        string $loaderPath,
        ?string $meetingLink = null,
        ?string $rescheduleLink = null,
        ?string $cancelLink = null,
        ?string $googleMapLink = null,
        ?int $pinboardId = null,
        array $data = []
    ): void {
        $isPhysical = $tourType === 'physicalTour';
        $bookingLink = config('APP_ADMIN_URL') . '/booking-management/list';
        $adminUrl = $pinboardId ? config('APP_ADMIN_URL') . '/pinboards/'. $pinboardId .'/overview' : null;
        // ===================== Sales Team Email =====================
        $start = date('g:i a', strtotime($meetingTime));
        $end   = date('g:i a', strtotime($meetingTime . ' +60 minutes'));
        $subject = $isPhysical
        ? 'Internal Notification - New Showroom Booking'
        : 'Internal Notification - New Online Meeting Booking';
        // $today = date('Y-m-d');
        // $bookingDate = date('Y-m-d', strtotime($date));
        // $cc = null;
        // if($bookingDate === $today){
        //     $subject = 'URGENT '.$subject;
        //     $cc = ['krost-sales@krost.com.au', 'marketing@krost.com.au'];
        // }

        // Combine the booking date and meeting time into a single timestamp
        $bookingTimestamp = strtotime("$date $meetingTime");
        $currentTimestamp = time();
        // (This checks if the meeting is in the future AND less than 24 hours away)
        $hoursDifference = ($bookingTimestamp - $currentTimestamp) / 3600;

       // Initialize standard CC array
        $cc = [];

        // Updated condition: Urgent if the meeting is less than 24 hours from now (Same Day)
        $adminEmail = 'sales@krost.com.au';
        // Updated condition: Urgent if the meeting is less than 24 hours from now (Same Day)
        if ($hoursDifference >= 0 && $hoursDifference < 24) {
            $subject = 'URGENT ' . $subject;
            $cc[] = 'sales@krost.com.au';
            // $cc[] = 'marketing@krost.com.au';

            // Client Logic: If urgent, also CC the specific state email based on showroom_id
            $showroomId = isset($data['showroom_id']) ? $data['showroom_id'] : '';
            if ($showroomId) {
                $stateEmail = $this->showroomStateEmail($showroomId);
                if ($stateEmail) {
                    // $cc[] = $stateEmail; // Add state email (e.g., sales-nsw@krost.com.au) to CC
                    $adminEmail = $stateEmail;
                }
            }
        }

        // Ensure $cc is null if empty, to prevent issues with email repository
        $cc = !empty($cc) ? $cc : null;

        $salesContext = [
            'subject'        => $subject,
            'recipient_name' => $isPhysical ? 'Sales Team' : 'Krost Sales',
            'client_name'    => $customerName,
            'meeting_date'   => date('d/m/Y', strtotime($date)), // format: d/m/Y
            // 'meeting_time'   => date('g:i a', strtotime($meetingTime)),
            'meeting_time'   => $start . ' - ' . $end, 
            'time_zone'      => $timeZone,
            'client_email'   => $email,
            'location'       => $location,
            'platform_url'   => $meetingLink,
            'platform_label' => 'Online Link',
            'booking_link'   => $meetingLink,
            'admin_url'      => $adminUrl,
            'google_map_link' => $googleMapLink,
            'pinboard_id'    => $pinboardId,
            'data'           => $data
        ];
    
        try {
            $this->emailRepository->sendEmail(
                $adminEmail,
                $subject,
                'Your booking is confirmed',
                $salesContext,
                $loaderPath,
                $isPhysical 
                    ? 'booking-admin-physical-tour.html.twig'
                    : 'booking-admin-virtual-meeting.html.twig',
                $cc
            );
        
        } catch (Exception $e) {
            $message = $e->getMessage();
        }
       
        // ===================== Guest Email =====================
        $subjectGuest = $isPhysical
                ? 'Confirmed: Your Showroom Tour with Krost'
                : 'Confirmed: Your Online Consultation with Krost';
        $guestContext = [
            'subject'           => $subjectGuest,
            'client_name'       => $customerName,
            'meeting_date'      => date('d/m/Y', strtotime($date)), // format: d/m/Y
            // 'meeting_time'      => date('g:i a', strtotime($meetingTime)),
            'meeting_time'      => $start . ' - ' . $end, // format: g:i a - g:i a
            'time_zone'         => $timeZone,
            'location'          => $location,
            'platform_url'      => $meetingLink,
            'platform_label'    => 'Online Link',
            'reschedule_url'    => $rescheduleLink,
            'cancel_url'        => $cancelLink,
            'salesperson_name'  => $isPhysical ? 'The Krost Team' : 'The Krost Team',
            'google_map_link' => $googleMapLink
        ];
    try{
       $emailSent = $this->emailRepository->sendEmail(
            $email,
            $subjectGuest,
            'Your booking is confirmed',
            $guestContext,
            $loaderPath,
            $isPhysical 
                ? 'booking-guest-physical-tour.html.twig'
                : 'booking-guest-virtual-meeting.html.twig'
        );
        if (!$emailSent) {
            throw new Exception('Failed to send email');
        }
    }catch(Exception $e){}
        
    }

    private function showroomStateEmail(int $showroom_id): ?string
    {
        $showrooms = [
            1 => ['city' => 'Sydney', 'state' => 'NSW', 'email' => 'sales-nsw@krost.com.au'],
            2 => ['city' => 'Melbourne', 'state' => 'VIC', 'email' => 'sales-vic@krost.com.au'],
            3 => ['city' => 'Brisbane', 'state' => 'QLD', 'email' => 'sales-qld@krost.com.au'],
        ];
    
        return isset($showrooms[$showroom_id]) ? $showrooms[$showroom_id]['email'] : null;
    }

    private function sendRescheduleBookingEmails(
        string $tourType,
        string $email,
        string $customerName,
        string $date,
        string $meetingTime,
        string $timeZone,
        ?string $location,
        string $loaderPath,
        ?string $meetingLink = null,
        ?string $rescheduleLink = null,
        ?string $cancelLink = null,
        ?string $googleMapLink = null,
        ?int $showroom_id = null
    ): void {
        $isPhysical = $tourType === 'physicalTour';

        $clientTemplateName = $isPhysical 
            ? 'reschedule-client-physical-tour.html.twig'
            : 'reschedule-client-virtual-meeting.html.twig';

        $salesTemplateName = $isPhysical 
            ? 'reschedule-sales-physical-tour.html.twig'
            : 'reschedule-sales-virtual-meeting.html.twig';
    
        $subjectSales = $isPhysical 
            ? 'RESCHEDULED: Showroom Appointment'
            : 'RESCHEDULED: Online Appointment';

        // ===================== Sales Team Email =====================
        $start = date('g:i a', strtotime($meetingTime));
        $end   = date('g:i a', strtotime($meetingTime . ' +60 minutes'));

        $bookingTimestamp = strtotime("$date $meetingTime");
        $currentTimestamp = time();
        // (This checks if the meeting is in the future AND less than 24 hours away)
        $hoursDifference = ($bookingTimestamp - $currentTimestamp) / 3600;

        // Initialize standard CC array
        $cc = [];
        $adminEmail = 'sales@krost.com.au';
        // Updated condition: Urgent if the meeting is less than 24 hours from now (Same Day)
        if ($hoursDifference >= 0 && $hoursDifference < 24) {
            $cc[] = ['sales@krost.com.au'];
            // Client Logic: If urgent, also CC the specific state email based on showroom_id
            $showroomId = isset($showroom_id) ? $showroom_id : '';
            if ($showroomId) {
                $stateEmail = $this->showroomStateEmail($showroomId);
                if ($stateEmail) {
                    // $cc[] = $stateEmail; // Add state email (e.g., sales-nsw@krost.com.au) to CC
                    $adminEmail = $stateEmail;
                }
            }
        }

        // Ensure $cc is null if empty, to prevent issues with email repository
        $cc = !empty($cc) ? $cc : null;

        $salesContext = [
            'subject'        => $subjectSales,
            'recipient_name' => $isPhysical ? 'Sales Team' : 'Krost Sales',
            'meeting_date'   => date('d/m/Y', strtotime($date)), // format: d/m/Y
            // 'meeting_time'   => $meetingTime,
            'meeting_time'   => $start . ' - ' . $end, 
            'client_email'   => $email,
            'client_name'    => $customerName,
            'location'       => $location,
            'platform_url'   => $meetingLink,
            'meeting_link'   => $meetingLink,
            'platform_label' => 'Online Link',
            'salesperson_name' => 'Sales Team',
            'original_date'    => date('d/m/Y @ h:i A', strtotime($meetingTime)),
            'sender_name'      => $email,
            'appointment_date' => date('d/m/Y', strtotime($date)), // format: d/m/Y
            'appointment_time' => $start . ' - ' . $end, 
            'appointment_location' => $location,
            'time_zone'      => $timeZone,
            'cancel_link' => $cancelLink,
            'reschedule_link'  => $rescheduleLink, 
            'google_map_link' => $googleMapLink,
        ];
    
            $this->emailRepository->sendEmail(
                $adminEmail,
                $subjectSales,
                'Your booking is rescheduled successfully',
                $salesContext,
                $loaderPath,
                $salesTemplateName,
                $cc
            );

        // ===================== Guest Email =====================
        $guestContext = [
            'subject'           => $isPhysical
                ? 'Updated Confirmation: Your New Appointment with Krost'
                : 'Updated Confirmation: Your New Appointment with Krost',
            'client_name'       => $customerName,
            'meeting_date'      => date('d/m/Y', strtotime($date)), // format: d/m/Y
            // 'meeting_time'      => $meetingTime,
            'meeting_time'      => $start . ' - ' . $end, 
            'time_zone'         => $timeZone,
            'location'          => $location,
            'platform_url'      => $meetingLink,
            'platform_label'    => 'Online Link',
            'meeting_link'      => $meetingLink,
            'salesperson_name' => 'Sales Team',        
            'original_date'    => date('d F Y @ h:i A', strtotime($meetingTime)),
            'sender_name'      => $email,
            'appointment_date' => date('d/m/Y', strtotime($date)), // format: d/m/Y
            'appointment_time' => $start . ' - ' . $end, 
            'appointment_location' => $location,
            'cancel_link' => $cancelLink,
            'reschedule_link'  => $rescheduleLink, 
            'google_map_link' => $googleMapLink,
        ];
    
            $this->emailRepository->sendEmail(
                $email,
                'Updated Confirmation: Your New Appointment with Krost',
                'Your booking is rescheduled successfully',
                $guestContext,
                $loaderPath,
                $clientTemplateName
            );
            
    }

    private function sendCancelBookingEmails(
        string $tourType,
        string $email,
        string $customerName,
        string $date,
        string $meetingTime,
        string $timeZone,
        ?string $location,
        string $loaderPath,
        ?string $companyName = null,
        ?int $showroom_id = null
    ): void {
        $isPhysical = $tourType === 'physicalTour';
        $start = date('g:i a', strtotime($meetingTime));
        $end   = date('g:i a', strtotime($meetingTime . ' +60 minutes'));
        $bookingLink = config('APP_URL') . '/contact-sales#book-now';
        // ===================== Sales Team Email =====================

        $subject = 'CANCELLATION: ' . $customerName . ' – ' . $location;

        $bookingTimestamp = strtotime("$date $meetingTime");
        $currentTimestamp = time();
        // (This checks if the meeting is in the future AND less than 24 hours away)
        $hoursDifference = ($bookingTimestamp - $currentTimestamp) / 3600;

        // Initialize standard CC array
        $cc = [];

        $adminEmail = 'sales@krost.com.au';
        // Updated condition: Urgent if the meeting is less than 24 hours from now (Same Day)
        if ($hoursDifference >= 0 && $hoursDifference < 24) {
            $cc[] = ['sales@krost.com.au'];
            // Client Logic: If urgent, also CC the specific state email based on showroom_id
            $showroomId = isset($showroom_id) ? $showroom_id : '';
            if ($showroomId) {
                $stateEmail = $this->showroomStateEmail($showroomId);
                if ($stateEmail) {
                    // $cc[] = $stateEmail; // Add state email (e.g., sales-nsw@krost.com.au) to CC
                    $adminEmail = $stateEmail;
                }
            }
        }

        // Ensure $cc is null if empty, to prevent issues with email repository
        $cc = !empty($cc) ? $cc : null;

        $salesContext = [
            'subject'        => $subject,
            'recipient_name' => $isPhysical ? 'Sales Team' : 'Krost Sales',
            'meeting_date'   => date('d/m/Y', strtotime($date)), // format: d/m/Y
            // 'meeting_time'   => $meetingTime,
            'meeting_time'   => $start . ' - ' . $end, 
            'time_zone'      => $timeZone,
            'client_email'   => $email,
            'location'       => $location,
            'platform_url'   => '#',
            'platform_label' => 'Online Link',
            'client_name'      => $customerName,
            'date'             => date('d/m/Y', strtotime($date)),
            'reschedule_link'  => $bookingLink,
            'company_name'       => $companyName,
            'showroom_location'  => $location,
            'original_time'      => date('d/m/Y @ h:i A', strtotime($meetingTime)),
            'salesperson_name'   => 'Sales Team',
        ];

        try{
            $this->emailRepository->sendEmail(
                $adminEmail,
                $subject,
                'Your booking is cancelled successfully',
                $salesContext,
                $loaderPath,
                'cancelled-booking-admin.html.twig',
                $cc
            );
        }catch(Exception $e){}
    
        // 1. 
    
        // ===================== Guest Email =====================
        $guestContext = [
            'subject'           => $isPhysical 
            ? 'Booking Cancelled: Showroom Tour with Krost'
            : 'Booking Cancelled: Online Consultation with Krost',
            'client_name'       => $customerName,
            'meeting_date'      => date('d/m/Y', strtotime($date)), // format: d/m/Y
            // 'meeting_time'      => date('g:i a', strtotime($meetingTime)),
            'meeting_time'      => $start . ' - ' . $end, 
            'location'          => $location,
            'platform_url'      => $bookingLink,
            'platform_label'    => 'Online Link',
            'reschedule_url'    => $bookingLink,
            'salesperson_name'  => $isPhysical ? 'Sales Team' : 'Krost Sales',
            'date'             => date('d/m/Y', strtotime($date)),
        ];

        $subject = $isPhysical 
            ? 'Booking Cancelled: Showroom Tour with Krost'
            : 'Booking Cancelled: Online Consultation with Krost';
        try{
            $this->emailRepository->sendEmail(
                $email,
                $subject,
                'Your booking is cancelled successfully',
                $guestContext,
                $loaderPath,
                $isPhysical 
                    ? 'cancel-booking-physical-tour.html.twig'
                    : 'cancel-booking-virtual-tour.html.twig'
            );
        
        }catch(Exception $e){}
    }

    public function addVisitShowroom(array $data): array
    {
        $startDateTime = strtotime($data['start_date'] . ' ' . $data['start_time']);
        $endDateTime   = strtotime($data['end_date'] . ' ' . $data['end_time']);

        // duration in minutes
        $duration = ($endDateTime - $startDateTime) /60; // 60 minutes
        $showroomContactId = isset($data['showroom_contact_id']) ? $data['showroom_contact_id'] : 22;
        $showroomId = isset($data['showroom_id']) ? $data['showroom_id'] : 1;
        $contactData = [
            'uuid'         => $this->generateUuid(),
            'customer_id'  => isset($data['customer_id']) ? $data['customer_id'] : 1,
            'showroom_id'  => $showroomId,
            'tour_type'    => isset($data['tour_type']) ? $data['tour_type'] : '',
            'enquiry_type'    => isset($data['enquiry_type']) ? $data['enquiry_type']: '',
            'source'    => 'admin booking',
            'showroom_contact_id' => $showroomContactId,
            'date'         => isset($data['start_date']) ? $data['start_date'] : null,     
            'meeting_time' => isset($data['start_time']) ? $data['start_time'] : null,     
            'duration'     => 60, // 60 minutes            
            'label'        => isset($data['label']) ? $data['label'] : 'physicalTour',
            'meeting_link' => isset($data['meeting_link']) ? $data['meeting_link'] : null,
            'time_zone'    => isset($data['time_zone']) ? $data['time_zone'] : null,
        ];

        $addVisitShowroom = $this->model->create($contactData);
        if (!$addVisitShowroom) {
            return ['error' => 'Failed to add visit showroom'];
        }
        return $this->bookingManagement($showroomContactId, $showroomId);
    }

    public function updateVisitShowroom(array $data, int $visit_showroom_id): array
    {
        $this->model->clearQuery();
        $visitShowroom = $this->model->where('visit_showroom_id', '=', $visit_showroom_id)->first();
        if (!$visitShowroom) {
            return ['error' => 'Visit showroom not found'];
        }

        $startDateTime = strtotime($data['start_date'] . ' ' . $data['start_time']);
        $endDateTime   = strtotime($data['end_date'] . ' ' . $data['end_time']);

        // duration in minutes
        $duration = ($endDateTime - $startDateTime) / 60;

        $showroomContactId = isset($data['showroom_contact_id']) ? $data['showroom_contact_id'] : 22;
        $showroomId = isset($data['showroom_id']) ? $data['showroom_id'] : 1;

        $contactData = [
            'customer_id'  => isset($data['customer_id']) ? $data['customer_id'] : 1,
            'date'         => isset($data['start_date']) ? $data['start_date'] : null,     
            'meeting_time' => isset($data['start_time']) ? $data['start_time'] : null,     
            'duration'     => isset($duration) ? $duration : 60,        
            'showroom_contact_id' => $showroomContactId,
            'label'        => isset($data['label']) ? $data['label'] : 'physicalTour',
            'meeting_link' => isset($data['meeting_link']) ? $data['meeting_link'] : null,
            'time_zone'    => isset($data['time_zone']) ? $data['time_zone'] : null,
            'tour_type'    => isset($data['tour_type']) ? $data['tour_type'] : '',
            'enquiry_type'    => isset($data['enquiry_type']) ? $data['enquiry_type']: '',
        ];

        $updatedVisitShowroom = $visitShowroom->update($contactData);
        if (!$updatedVisitShowroom) {
            return ['error' => 'Failed to update visit showroom'];
        }
        return $this->bookingManagement($showroomContactId, $showroomId);
    }

    public function fetchBookedDataByShowroomId(int $showroom_id, string $date, $visit_showroom_id = null, $tour_type = 'physicalTour'): array
    {
        // date 2026-04-16
        $date = trim($date);
        date_default_timezone_set('Australia/Sydney');
    
        $totalSlots = [
            '09:00:00', '09:30:00', '10:00:00', '10:30:00',
            '11:00:00', '11:30:00', '12:00:00', '12:30:00',
            '13:00:00', '13:30:00', '14:00:00', '14:30:00',
            '15:00:00', '15:30:00', '16:00:00', '16:30:00',
        ];
    
        $passedSlots = [];
    
        $today = date('Y-m-d');
        if ($date === $today) {
    
            $currentTime = new DateTime('now', new DateTimeZone('Australia/Sydney'));
            $currentTime->modify('+1 hour'); // add 1 hour to the current time
    
            foreach ($totalSlots as $slot) {
                $slotTime = DateTime::createFromFormat(
                    'H:i:s',
                    $slot,
                    new DateTimeZone('Australia/Sydney')
                );
    
                if ($slotTime <= $currentTime) {
                    $passedSlots[] = [
                        "meeting_time" => $slot,
                        "label" => $tour_type,
                        "title" => 'Sales Team',
                        "duration" => 60,
                        "start_date" => $date,
                        "end_date" => date('Y-m-d', strtotime($date . ' +60 minutes')),
                        "start_time" => $slot,
                        "end_time" => date('H:i:s', strtotime($slot . ' +60 minutes')),
                        "meeting_link" => '#',
                        "class" => ''
                    ];
                }
            }
        }
        // $solt = $passedSlots;
        $this->model->clearQuery();
        $query = $this->model
                    ->where('`date`', '=', $date)
                    ->where('tour_type', '=', $tour_type)
                    ->where('showroom_id', '=', $showroom_id)
                    ->whereNull('cancelled_at');
                    if($visit_showroom_id) {
                        $query = $query->where('visit_showroom_id', '!=', $visit_showroom_id);
                    }
        // var_dump($query->getQuery());
        // exit;
        
        $bookedData = $query->findAll(false);
        foreach ($bookedData as $key => $value) {
            $bookedData[$key]['class'] = 'th-booked-time-slot';
        }
        $bookedData = array_merge($passedSlots, $bookedData);
        return ['success' => true, 'message' => 'Fetch booked data successful', 'data' => (array) $bookedData ?? []]; 
    }

    public function bookingManagement( $userId, $showroom_id): array
    {
        $this->model->clearQuery();
        $getData = $this->model
        ->where('showroom_contact_id', '=', $userId)
        ->where('showroom_id', '=', $showroom_id)
        ->whereNull('cancelled_at')
        ->join('customer', 'customer.customer_id', '=', 'visit_showroom.customer_id')
        ->join('showrooms', 'showrooms.showrooms_id', '=', 'visit_showroom.showroom_id')
        ->join('pinboard', 'pinboard.pinboard_id', '=', 'visit_showroom.pinboard_id')
        ->select([
            'visit_showroom.visit_showroom_id',
            'customer.name as customer_name',
            'showrooms.title as showroom_title',
            'pinboard.job_title as pinboard_name',
            'visit_showroom.date',
            'visit_showroom.meeting_time',
            'visit_showroom.tour_type',
            'visit_showroom.duration',
            'visit_showroom.label',
            'visit_showroom.meeting_link',
            'visit_showroom.enquiry_type',
        ])
        ->orderBy('created_at', 'desc')
        ->findAll(false);

        $data = [];
        foreach ($getData as $item) {
            $startDateTime = strtotime($item['date'] . ' ' . $item['meeting_time']);
            $endDateTime   = $startDateTime + ($item['duration'] * 60); // 60 minutes
        
            $data[$item['date']][] = [
                "visit_showroom_id"  => $item['visit_showroom_id'],
                "label"      => $item['label'] ?? $item['showroom_title'],
                "title"      => $item['label'],
                "duration"   => $item['duration'],
                "start_date" => $item['date'],
                "end_date"   => date('Y-m-d', $endDateTime),
                "start_time" => date('H:i:s', $startDateTime),
                "end_time"   => date('H:i:s', $endDateTime),
                "meeting_link" => $item['meeting_link'],
                "enquiry_type" => $item['enquiry_type'],
                "tour_type" => $item['tour_type'],
            ];
        }
        return ['success' => true, 'message' => 'Booking management successful', 'data' => (array) $data ?? []];
    }

    public function getContactRequestList(): array
    {
        $contactRequestList = $this->model
        ->join('customer', 'customer.customer_id', '=', 'visit_showroom.customer_id')
        ->join('showroom_contact', 'showroom_contact.showroom_contact_id', '=', 'visit_showroom.showroom_contact_id')
        ->join('showrooms', 'showrooms.showrooms_id', '=', 'visit_showroom.showroom_id')
        ->join('pinboard', 'pinboard.pinboard_id', '=', 'visit_showroom.pinboard_id')
        ->select([
            'visit_showroom_id',
             'customer.name as customer_name', 
             'customer.gmail_Id as customer_email',
             'customer.phone as customer_phone',
             'showroom_contact.name as showroom_contact_name',
             'showrooms.title as showroom_title', 
             'pinboard.job_title as pinboard_name',
             'tour_type', 
             'date',
             'meeting_time', 
             'time_zone',
             'note',
             'source',
             'enquiry_type'
            ])
        ->orderBy('visit_showroom.visit_showroom_id', 'desc')
        ->findAll(false);
        return ['success' => true, 'message' => 'Get contact request list successful', 'data' => (array) $contactRequestList ?? []];
    }
    public function isExistsContactRequest(string $meeting_time, int $id): bool
    {
        $contactRequest = $this->model->where('meeting_time', '=', $meeting_time)->where('visit_showroom_id', '!=', $id)->first();
        if (!$contactRequest) {
            return false;
        }
        return true;
    }


    public function getContactRequestById(int $id): array
    {
        $contactRequest = $this->model
        ->join('customer', 'customer.customer_id', '=', 'visit_showroom.customer_id')
        ->join('showroom_contact', 'showroom_contact.showroom_contact_id', '=', 'visit_showroom.showroom_contact_id')
        ->join('showrooms', 'showrooms.showrooms_id', '=', 'visit_showroom.showroom_id')
        ->join('pinboard', 'pinboard.pinboard_id', '=', 'visit_showroom.pinboard_id')
        ->select(['visit_showroom_id', 'customer.name as customer_name', 'showroom_contact.name as showroom_contact_name','showrooms.title as showroom_title', 'pinboard.job_title as pinboard_name','tour_type', 'date', 'meeting_time', 'time_zone'])
        ->where('visit_showroom_id', '=', $id)
        ->first(false);
        
        if (!$contactRequest) {
            return ['success' => false, 'message' => 'Contact request not found'];
        }
        return ['success' => true, 'message' => 'Get contact request by id successful', 'data' => (array) $contactRequest ?? []];
    }

    public function updateContactRequest(array $data, int $id): ?object
    {
        $this->model->clearQuery();
        $this->model->where('visit_showroom_id', '=', $id);
        $contactRequest = $this->model->find($id);
        if (!$contactRequest) {
            return null;
        }
        $updatedContactRequest = $contactRequest->update($data);
        if (!$updatedContactRequest) {
            return null;
        }
        return $updatedContactRequest;
    }

    public function deleteContactRequest(int $visit_showroom_id): bool
    {
        $contactRequest = $this->model->where('visit_showroom_id', '=', $visit_showroom_id)->first();
        if (!$contactRequest) {
            return false;
        }
        $this->model->delete($visit_showroom_id);
        return true;
    }

    public function sendMessage(array $data): array
    {
        $pinboardId = $data['pinboard_id'] ?? null;
        $visitShowroomId = $data['visit_showroom_id'] ?? null;
        $note = $data['note'] ?? null;

        $visitShowroom = $this->model->where('visit_showroom_id', '=', $visitShowroomId)->first();
        if (!$visitShowroom) {
            return ['error' => 'Booking not found'];
        }
        $updatedVisitShowroom = $visitShowroom->update(['note' => $note]);
        if (!$updatedVisitShowroom) {
            return ['error' => 'Failed to send message'];
        }
        // send email to sales team
        $this->sendMessageToSalesTeam($data);

        return ['success' => true, 'message' => 'Message sent successfully'];
    }

    private function pinboardData(int $pinboard_id, string $note): array {
        $pinboard = $this->pinboardRepository->find($pinboard_id);
        if (!$pinboard) {
            return ['success' => false, 'message' => 'Pinboard data not found'];
        }

        $updatedPinboard = $pinboard->update(['note' => $note]);
        if (!$updatedPinboard) {
            return ['success' => false, 'message' => 'Failed to update pinboard data'];
        }
        return ['success' => true, 'message' => 'Pinboard data found', 'data' => (array) $pinboard ?? []];
    }

    private function sendMessageToSalesTeam(array $data): void
    {
        $loaderPath = ROOT_DIR . '/src/themes/landing/src/emailtemplate';
        $subject = 'New Message - Re: Booking on ' . date('d/m/Y');
        $salesTeamContext = [
            'subject' => $subject,
            'client_message' => $data['note'] ?? '',
            'email' => $data['email'] ?? '',
            'date' => date('d/m/Y'),
            'location' => $data['location'] ?? '',
        ]; 
        try{
            $emailSent = $this->emailRepository->sendEmail(
                 'sales@krost.com.au',
                 $subject,
                 'A new message has been sent to the sales team',
                 $salesTeamContext,
                 $loaderPath,
                'client-message-to-sales-team.html.twig'
             );
        }catch(Exception $e){}
        if (!$emailSent) {
            throw new \Exception('Failed to send email to sales team');
        }
    }
   
    public function checkExistingData(array $data, $visit_showroom_id = null): array
    {
        $showroomId = $data['showroom_id'] ?? null;
        $date = $data['date'] ?? null;
        $meetingTime = $data['meeting_time'] ?? null;
        $tourType = $data['tour_type'] ?? 'physicalTour';
    
        $this->model->clearQuery();
    
        $existingData = $this->model;

        if($visit_showroom_id) {
            $existingData = $existingData->where('visit_showroom_id', '!=', $visit_showroom_id);
        }
        $existingData = $existingData->where('`date`', '=', $date)
                        ->where('meeting_time', '=', $meetingTime)
                        ->where('showroom_id', '=', $showroomId)
                        ->where('tour_type', '=', $tourType)
                        ->whereNull('cancelled_at')
                        ->limit(0)
                        ->findAll(false);
    
        if (!empty($existingData)) {
            return [
                'success' => false,
                'message' => 'Someone already booked this time slot',
                'data' => (array) $existingData
            ];
        }
    
        return [];
    }
    

    public function checkExistingDataByCustomerId(array $data): array
    {
        $showroomId = $data['showroom_id'] ?? null;
        $date = $data['date'] ?? null;
        $customerId = isset($data['customer_id']) ? $data['customer_id'] : null;
        $tourType = $data['tour_type'] ?? 'physicalTour';
    
        $this->model->clearQuery();
        $query = $this->model;
        $query->where('customer_id', '=', $customerId);
        $query->where('`date`', '=', $date);
        $query->where('showroom_id', '=', $showroomId);
        $query->where('tour_type', '=', $tourType)
        ->whereNull('cancelled_at');
        $query->limit(0);
        $existingData = $query->findAll(false);
    
        if (!empty($existingData)) {
            return [
                'success' => false,
                'message' => 'You already booked slot for this day on this showroom',
                'data' => (array) $existingData
            ];
        }
    
        return [];
    }

    public function oneDayPriorVisitShowroomNotification(): array
    {
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $visitShowrooms = $this->model->with(['customer', 'showroom'])->where('`date`', '=', $tomorrow)->where('tour_type', '=', 'physicalTour')->findAll(false);
   
        if (!$visitShowrooms || !count($visitShowrooms)) {
            return ['success' => false, 'message' => 'No visit showroom found'];
        }
        foreach ($visitShowrooms as $visitShowroom) {
            $meetingDate = !empty($visitShowroom['date']) ? date('d M Y', strtotime((string) $visitShowroom['date'])) : '';
            $meetingTime = !empty($visitShowroom['meeting_time']) ? (string) $visitShowroom['meeting_time'] : '';
            $rescheduleUrl = '#';

            if(isset($visitShowroom['customer']) && !empty($visitShowroom['customer'])) {
                $customer = json_decode($visitShowroom['customer'], true);
                $customerName = $customer['name'];
            }
            if(isset($visitShowroom['showroom']) && !empty($visitShowroom['showroom'])) {
                $showroom = json_decode($visitShowroom['showroom'], true);
                $showroomName = $showroom['title'];
           
            }
            if (!empty($visitShowroom['visit_showroom_id'])) {
                $rescheduleUrl = config('APP_URL') . '/booking/showroom-visit/' . base64_encode((string) $visitShowroom['visit_showroom_id']);
            }

            $context = [
                'subject' => 'Your Showroom Tour is Tomorrow',
                'recipient_name' => (string) ($customerName ?? ''),
                'meeting_date' => $meetingDate,
                'meeting_time' => $meetingTime,
                'location_url' => (string) ($visitShowroom['meeting_link'] ?? '#'),
                'location_label' => (string) ($showroomName ?? 'Showroom Location'),
                'consultant_name' => "Sales Team",
                'reschedule_url' => $rescheduleUrl,
                'reschedule_label' => 'Reschedule or Cancel your Booking',
                'salesperson_name' => (string) ("Sales Team" ?? ''),
            ];
            try{  
                $this->emailRepository->sendEmail(
                    "shofi.tafe@gmail.com",
                    'Your Showroom Tour is Tomorrow',
                    'Your Showroom Tour is Tomorrow',
                    $context,
                    ROOT_DIR . '/src/themes/landing/src/emailtemplate',
                    'showroom-visit-tomorrow.html.twig'
                );
            }catch(Exception $e){}

        }


        return ['success' => true, 'message' => 'One day prior visit showroom notification successful'];
    }
    public function oneDayPriorOnlineMeetingNotification(): array
    {
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $onlineMeetings = $this->model->with(['customer', 'showroom'])->where('`date`', '=', $tomorrow)->where('tour_type', '=', 'virtualTour')->findAll(false);
   
        if (!$onlineMeetings || !count($onlineMeetings)) {
            return ['success' => false, 'message' => 'No online meeting found'];
        }

        foreach ($onlineMeetings as $onlineMeeting) {
            $meetingDate = !empty($onlineMeeting['date']) ? date('d M Y', strtotime((string) $onlineMeeting['date'])) : '';
            $meetingTime = !empty($onlineMeeting['meeting_time']) ? (string) $onlineMeeting['meeting_time'] : '';
            $rescheduleUrl = '#';
            if (!empty($onlineMeeting['visit_showroom_id'])) {
                $rescheduleUrl = config('APP_URL') . '/booking/showroom-visit/' . base64_encode((string) $onlineMeeting['visit_showroom_id']);
            }
            if(isset($visitShowroom['customer']) && !empty($visitShowroom['customer'])) {
                $customer = json_decode($visitShowroom['customer'], true);
                $customerName = $customer['name'];
            }
            if(isset($visitShowroom['showroom']) && !empty($visitShowroom['showroom'])) {
                $showroom = json_decode($visitShowroom['showroom'], true);
                $showroomName = $showroom['title'];
           
            }
            if (!empty($visitShowroom['visit_showroom_id'])) {
                $rescheduleUrl = config('APP_URL') . '/booking/showroom-visit/' . base64_encode((string) $visitShowroom['visit_showroom_id']);
            }


            $context = [
                'subject' => 'Our Online Consultation is Tomorrow',
                'recipient_name' => (string) ($customerName ?? ''),
                'meeting_date' => $meetingDate,
                'meeting_time' => $meetingTime,
                'meeting_url' => (string) ($onlineMeeting['meeting_link'] ?? '#'),
                'meeting_link_label' => (string) ($onlineMeeting['label'] ?? 'Join Online Meeting'),
                'consultant_name' => "Sales Team",
                'reschedule_url' => $rescheduleUrl,
                'reschedule_label' => 'Reschedule or Cancel your Booking',
                'salesperson_name' => (string) ("Sales Team" ?? ''),
            ];
        try{
            $this->emailRepository->sendEmail(
                'shofi.tafe@gmail.com',
                'Our Online Consultation is Tomorrow',
                'Our Online Consultation is Tomorrow',
                $context,
                ROOT_DIR . '/src/themes/landing/src/emailtemplate',
                'online-meeting-tomorrow.html.twig'
            );

        }catch(Exception $e){}
        }
        return ['success' => true, 'message' => 'One day prior visit showroom notification successful'];
        
    }

    public function absentCustomerNotification(int $visit_showroom_id): array
    {
        $absentCustomer = $this->model->with(['customer'])->where('visit_showroom_id', '=', $visit_showroom_id)->first();
        if (!$absentCustomer) {
            return ['success' => false, 'message' => 'No data found'];
        }
        $customer = json_decode($absentCustomer->customer, true);
        $name = isset($customer['name']) ? $customer['name'] : '';
        $email = isset($customer['gmail_Id']) ? $customer['gmail_Id'] : '';

        $context = [
            'client_name'  => $name,
            // 'booking_link' => config('APP_URL') . '/booking/showroom-visit/' . base64_encode((string) $visit_showroom_id),
            'booking_link' => config('APP_URL') . '/contact-sales#book-now',
            'project_link' => config('APP_URL') . '/projects',
            'sender_name'  => 'Krost Sales Team',
        ];

        try{
            $this->emailRepository->sendEmail(
                $email,
                'Missed Appointment with Krost',
                'Missed Appointment with Krost',
                $context,
                ROOT_DIR . '/src/themes/landing/src/emailtemplate',
                'missed-meeting-notification.html.twig'
            );

        }catch(Exception $e){}
        return ['success' => true, 'message' => 'Absent Email Notification successful'];
    }

    public function getVisitShowroomIdByUuid(string $uuid): int
    {
        $visitShowroom = $this->model->where('uuid', '=', $uuid)->first();
        if (!$visitShowroom) {
            return 0;
        }
        return $visitShowroom->visit_showroom_id;
    }

    private function generateUuid(): string
    {
        $uuid = \uniqid('', true);
        $uuid = str_replace('.', '', $uuid);
        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($uuid, 0, 8),
            substr($uuid, 8, 4),
            substr($uuid, 12, 4),
            substr($uuid, 16, 4),
            substr($uuid, 20)
        );
    }

} 