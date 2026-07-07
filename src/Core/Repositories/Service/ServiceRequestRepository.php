<?php

declare(strict_types=1);

namespace App\Core\Repositories\Service;

use App\Core\Models\Pinboard\Pinboard;
use App\Core\Models\Pinboard\PinboardItem;
use App\Core\Models\Service\ServiceRequest;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Repositories\Media\MediaRepositoryInterface;
use App\Core\Repositories\UserRepositoryInterface;
use App\Core\Repositories\Email\EmailRepositoryInterface;
use function App\Core\System\utils\htmlToPlainText;
use function App\Core\System\utils\config;
use App\Core\Http\Request;
use App\Core\Http\Response;
use function App\Core\System\utils\env;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Container\Attributes\Log;

use PDO;
class ServiceRequestRepository extends BaseRepository implements ServiceRequestRepositoryInterface
{
    protected ServiceRequest $serviceRequest;
    protected MediaRepositoryInterface $mediaRepository;
    protected Pinboard $pinboard;
    protected PinboardItem $pinboardItem;
    protected UserRepositoryInterface $userRepository;
    protected EmailRepositoryInterface $emailRepository;
    public function __construct(PDO $db, ServiceRequest $serviceRequest, MediaRepositoryInterface $mediaRepository, Pinboard $pinboard, PinboardItem $pinboardItem, UserRepositoryInterface $userRepository, EmailRepositoryInterface $emailRepository)
    {
        parent::__construct($db, 'service_request', ServiceRequest::class);
        $this->serviceRequest = $serviceRequest;
        $this->serviceRequest->setDb($db);  
        $this->mediaRepository = $mediaRepository;
        $this->pinboard = $pinboard;
        $this->pinboard->setDb($db);
        $this->pinboardItem = $pinboardItem;
        $this->pinboardItem->setDb($db);
        $this->userRepository = $userRepository;
        $this->emailRepository = $emailRepository;
    }

    // booking request by email to consultant
    public function createRequest(array $data, array $files):array
    {
        try {
            $pinboard = $this->pinboard
            ->with(['pinboard_items'])
            ->where('pinboard_id', '=', $data['pinboard_id'])->first();

            if (!$pinboard) {
                return ['success' => false, 'message' => 'Pinboard not found', 'data' => []];
            }
            // pinboard item count
            $pinboardItems = $this->pinboardItem->where('pinboard_id', '=', $pinboard->pinboard_id)->findAll(false);
            $pinboardItemsCount = count($pinboardItems);

            $attachment = [];
            $fileLinks = [];
            $attachmentLabels = [];
            if (count($files) > 0) {
                $attachment = $files;
            }
            $uuid = bin2hex(random_bytes(16));
            $pinboardId = isset($data['pinboard_id']) ? $data['pinboard_id'] : null;
            $createData = [
                'uuid' => $uuid,
                'pinboard_id' => $pinboardId,
                'customer_id' => isset($data['customer_id']) ? $data['customer_id'] : null,
                'email' => isset($data['email']) ? $data['email'] : null,
                'content' => isset($data['note']) ? $data['note'] : '',
                'comment_attachment' => isset($data['attachments']) ? $data['attachments'] : '',
                'attachments' => json_encode($attachment),
            ];
            $serviceRequest = $this->model->create($createData);

            if($serviceRequest){
                $pinboard->update(['pinboard_status_id' => 8]);
            }

            // send email to customer
            $user = $this->userRepository->findByUserId($pinboard->user_id);
            if($user){
                $baseurl = config('APP_URL');
                $adminUrl = config('APP_ADMIN_URL');
                foreach ($attachment as $file) {
                    $fileLinks[] = $baseurl . '/service-request/file?uuid=' . $serviceRequest->uuid . '&file=' . $file['objectURL'];
                    $originalName = '';
                    if (isset($file['file']['name']) && is_string($file['file']['name'])) {
                        $originalName = $file['file']['name'];
                    } elseif (isset($file['name']) && is_string($file['name'])) {
                        $originalName = $file['name'];
                    }
                    $attachmentLabels[] = $originalName !== '' ? $originalName : ('File ' . (string) (count($attachmentLabels) + 1));
                }
                $email = $user->email;
                $name = $user->first_name;
                $pinboardName = $pinboard->pinboard_name;
                $companyName = $pinboard->company_name;
                $notes = htmlToPlainText($data['note']) ?? $data['note'] ?? '';
                $items = json_decode($pinboard->data->pinboard_items, true);

                $phoneDisplay = trim((string) ($user->phone_number ?? ''));
                // $clientMailSubject = 'Krost Website | Email a Consultant' . ($phoneDisplay !== '' ? ' – ' . $phoneDisplay : '');
                $clientMailSubject = 'Krost Website | Email a Consultant';

                $dashboardProjectUrl = rtrim((string) $adminUrl, '/') . '/pinboards/' . $pinboardId. '/overview'; // sales team url
                $boardUrl = rtrim((string) $baseurl, '/') . '/account/virtual-pinboards'; // client url

                $baseContext = [
                    'team_name' => 'Sales Team',
                    'client_name' => $name,
                    'client_email' => $email,
                    'company' => $companyName,
                    'phone' => $phoneDisplay !== '' ? $phoneDisplay : 'Not provided',
                    'project_name' => $pinboardName,
                    'board_url' => $boardUrl,
                    'board_link_label' => 'URL to see the board',
                    'pinboard_phone' => $phoneDisplay !== '' ? $phoneDisplay : 'Not provided',
                    'client_notes' => $notes,
                    'items_count' => $pinboardItemsCount,
                    'submission_date' => date('d F Y'),
                    'submission_date_short' => date('d/m/Y'),
                    'dashboard_project_url' => $dashboardProjectUrl,
                    'attachment_links' => $fileLinks,
                    'attachment_labels' => $attachmentLabels,
                    'estimated_value' => '$' . number_format(array_sum(array_column($items, 'total_price')) ?? 0, 2),
                ];

                // Admin uses same subject line as client: Krost Website | Email a Consultant – [phone]
                $adminSubject = $clientMailSubject;

                // -------------- sales team email --------------
                $this->emailRepository->sendEmail(
                    'sales@krost.com.au',
                    $adminSubject,
                    $adminSubject,
                    array_merge($baseContext, ['subject' => $adminSubject]),
                    ROOT_DIR . '/src/themes/landing/src/emailtemplate',
                    'booking-email-admin.html.twig'
                );
                // -------------- guest user email --------------
                $this->emailRepository->sendEmail(
                    $email,
                    $clientMailSubject,
                    $clientMailSubject,
                    array_merge($baseContext, ['subject' => $clientMailSubject]),
                    ROOT_DIR . '/src/themes/landing/src/emailtemplate',
                    'booking-email-client.html.twig',
                    ['sales@krost.com.au']
                );
            }

            return $serviceRequest ? ['success' => true, 'message' => 'Service request created successfully'] : ['success' => false, 'message' => 'Service request creation failed'];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Service request creation failed', 'error' => $e->getMessage()];
        }
    }

    // account-side service request (no pinboard)
    public function accountCreateRequest(array $data, array $files):array
    {
        try {
            $attachment = count($files) > 0 ? $files : [];
            $fileLinks = [];
            $attachmentLabels = [];

            $uuid = bin2hex(random_bytes(16));
            $createData = [
                'uuid' => $uuid,
                'pinboard_id' => null,
                'customer_id' => isset($data['customer_id']) ? $data['customer_id'] : null,
                'email' => isset($data['email']) ? $data['email'] : null,
                'content' => isset($data['note']) ? $data['note'] : '',
                'comment_attachment' => isset($data['attachments']) ? $data['attachments'] : '',
                'attachments' => json_encode($attachment),
            ];
            $serviceRequest = $this->model->create($createData);

            // resolve user for email recipient + display name
            $user = null;
            $emailFromForm = isset($data['email']) ? trim((string) $data['email']) : '';
            if ($emailFromForm !== '') {
                $user = $this->userRepository->findByEmail($emailFromForm);
            }

            if ($serviceRequest && $user) {
                $baseurl = config('APP_URL');
                $adminUrl = config('APP_ADMIN_URL');
                foreach ($attachment as $file) {
                    $fileLinks[] = $baseurl . '/service-request/file?uuid=' . $serviceRequest->uuid . '&file=' . $file['objectURL'];
                    $originalName = '';
                    if (isset($file['file']['name']) && is_string($file['file']['name'])) {
                        $originalName = $file['file']['name'];
                    } elseif (isset($file['name']) && is_string($file['name'])) {
                        $originalName = $file['name'];
                    }
                    $attachmentLabels[] = $originalName !== '' ? $originalName : ('File ' . (string) (count($attachmentLabels) + 1));
                }

                $email = $user->email;
                $name = $user->first_name;
                $phoneDisplay = trim((string) ($user->phone_number ?? ''));
                $orderNumber = isset($data['name']) ? trim((string) $data['name']) : '';
                $notes = htmlToPlainText($data['note'] ?? '') ?: ($data['note'] ?? '');

                $subject = 'Krost Account | Service Request for #' . $orderNumber;
                $subjectAdmin = 'New Service Request for #' . $orderNumber;

                $dashboardUrl = rtrim((string) $baseurl, '/') . '/account/recent-orders';

                $baseContext = [
                    'team_name' => 'Customer Service',
                    'client_name' => $name,
                    'client_email' => $email,
                    'phone' => $phoneDisplay !== '' ? $phoneDisplay : 'Not provided',
                    'pinboard_phone' => $phoneDisplay !== '' ? $phoneDisplay : 'Not provided',
                    'order_number' => $orderNumber,
                    'project_name' => $orderNumber !== '' ? ('Order ' . $orderNumber) : 'Service Request',
                    'client_notes' => $notes,
                    'issue_description' => $notes,
                    'submission_date' => date('d F Y'),
                    'submission_date_short' => date('d/m/Y'),
                    'dashboard_project_url' => $dashboardUrl,
                    'board_url' => $dashboardUrl,
                    'board_link_label' => 'View your account',
                    'attachment_links' => $fileLinks,
                    'attachment_labels' => $attachmentLabels,
                ];

                // -------------- sales team email --------------
                $this->emailRepository->sendEmail(
                    'customerservice@krost.com.au',
                    $subjectAdmin,
                    $subjectAdmin,
                    array_merge($baseContext, ['subject' => $subject]),
                    ROOT_DIR . '/src/themes/landing/src/emailtemplate',
                    'account-service-request-admin.html.twig'
                );
                // -------------- client email --------------
                $this->emailRepository->sendEmail(
                    $email,
                    $subject,
                    $subject,
                    array_merge($baseContext, ['subject' => $subject]),
                    ROOT_DIR . '/src/themes/landing/src/emailtemplate',
                    'account-service-request.html.twig'
                );
            }

            return $serviceRequest
                ? ['success' => true, 'message' => 'Service request created successfully', 'data' => ['uuid' => $uuid]]
                : ['success' => false, 'message' => 'Service request creation failed'];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => 'Service request creation failed', 'error' => $e->getMessage()];
        }
    }


    public function downloadRequestImages(string $uuid, string $link): string
    {
        $serviceRequest = $this->model->where('uuid', '=', $uuid)->first();
        if ($serviceRequest) {
            $attachments = json_decode((string) ($serviceRequest->data->attachments ?? '[]'), true);
            if (!is_array($attachments)) {
                return '';
            }

            foreach ($attachments as $attachment) {
                if (!is_array($attachment)) {
                    continue;
                }

                $attachmentPath = (string) ($attachment['objectURL'] ?? $attachment['path'] ?? '');
                if ($attachmentPath === '') {
                    continue;
                }

                // Compare both raw and decoded values to handle '+' and URL-encoded links.
                if (
                    $attachmentPath === $link
                    || urldecode($attachmentPath) === urldecode($link)
                ) {
                    return $attachmentPath;
                }
            }
        }

        return '';
    }

    public function requestCatalogue(array $data, array $files, string $folder):array
    {
        $attachment = [];
        if (count($files) > 0) {
            $attachment = $files;
        }

        $email = isset($data['email']) ? $data['email'] : null;
        $company = isset($data['company']) ? $data['company'] : null;
        $first_name = isset($data['first_name']) ? $data['first_name'] : null;
        $last_name = isset($data['last_name']) ? $data['last_name'] : null;
        $request_type = isset($data['type']) ? $data['type'] : null;
        $form_type = isset($data['form_type']) ? $data['form_type'] : null;
        $catalogue_format = isset($data['catalogue_format']) ? $data['catalogue_format'] : null;
        $phone_number = isset($data['phone_number']) ? $data['phone_number'] : null;
        $mailing_address = isset($data['mailing_address']) ? $data['mailing_address'] : null;
        $content = isset($data['add_text']) ? $data['add_text'] : null;
        $source_of_enquiry = isset($data['source_of_enquiry']) ? $data['source_of_enquiry'] : null;
        $state = isset($data['state']) ? $data['state'] : null;
        $project_details = isset($data['project_details']) ? $data['project_details'] : null;
        $notes = htmlToPlainText((string) ($content ?? '')) ?: (string) ($content ?? '');

        if ($catalogue_format === 'physical_catalogue') {
            // $apiKey = env("GOOGLE_API_KEY");
            // $address = "1600 Amphitheatre Parkway, Mountain View";

            $stateData = $this->getStateFromAddress($mailing_address);
            $state = isset($stateData['short_name']) ? $stateData['short_name'] : '';

        }

        $createData = [
            'uuid' => bin2hex(random_bytes(16)),
            'email' => $email,
            'company' => $company,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'request_type' => $request_type,
            'form_type' => $form_type,
            'catalogue_format' => $catalogue_format,
            'phone_number' => $phone_number,
            'state' => $state,
            'project_details' => $project_details,
            'mailing_address' => $mailing_address,
            'content' => $content,
            'source_of_enquiry' => $source_of_enquiry,
            'attachments' => json_encode($attachment),
        ];

        $serviceRequest = $this->model->create($createData);

        if($serviceRequest){
            $attachment_data = isset($serviceRequest->data->attachments) ? json_decode($serviceRequest->data->attachments, true) : [];
            $attachment_url = isset($attachment_data[0]['objectURL']) ? $attachment_data[0]['objectURL'] : '';
            $serviceRequest->data->image = $attachment_url;

            if($catalogue_format === 'physical_catalogue' || $catalogue_format === 'online_catalogue'){

                $twigFile = $data['catalogue_format'] === 'physical_catalogue' ? 'catalogue-request-physical.html.twig' : 'catalogue-request-online.html.twig';
                $twigFileAdmin = $data['catalogue_format'] === 'physical_catalogue' ? 'catalogue-request-admin-physical.html.twig' : 'catalogue-request-admin-online.html.twig';
                $subject = $data['catalogue_format'] === 'physical_catalogue' ? 'Thank you for browsing our catalogue online' : 'Thank you for browsing our catalogue online';
                $subjectAdmin = 'CATALOGUE REQUEST - ' . $data['first_name'];
                
                $loaderPath = ROOT_DIR . '/src/themes/landing/src/emailtemplate';
                $showroomTourUrl = env('APP_URL') . '/contact-us#th-showrooms';
                $projectsUrl = env('APP_URL') . '/projects';
                $year = date('Y');

                $context = [
                    'subject' => $subject,
                    'client_name' => $data['first_name'],
                    'showroom_tour_url' => $showroomTourUrl,
                    'projects_url' => $projectsUrl,
                    'phone' => $data['phone_number'],
                    'sales_phone' => '02 9557 3055',
                    'sales_email' => 'sales@krost.com.au',
                    'catalogue_view' => 'Mailed to me',
                    'company' => $data['company'],
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'notes_text' => $notes,
                    'state' => $state,
                    // 'phone' => '',
                    'email' => $data['email'],
                    'mailing_address' => $data['mailing_address'],
                    'hear_about_us' => $data['source_of_enquiry'],
                    // physical
                    'catalogue_year' => $year,
                    'view_type'   => $data['catalogue_format'] === 'physical_catalogue' ? 'Physical' : 'Online'
                ];
                // send email to the client
                $this->emailRepository->sendEmail(
                    $data['email'],
                    $subject,
                    'Thank you for browsing our catalogue online',
                    $context,
                    $loaderPath,
                    $twigFile
                );
        
                // admin email
                $context['subject'] = "CATALOGUE REQUEST - ".$data['first_name'];

                // $state
                // For VIC, SA, TAS States: please send to  sales-vic-internal@krost.com.au , sales@krost.com.au 
                // For NSW, ACT, NT, WA States, “Other": please send to  sales-nsw-internal@krost.com.au, sales@krost.com.au,  jane@krost.com.au  
                // For QLD State: please send to valerie@krost.com.au, sales@krost.com.au 

                $to = ['sales@krost.com.au'];
                $cc = [];

                switch ($state) {
                    case 'VIC':
                    case 'SA':
                    case 'TAS':
                        $to[] = 'sales-vic-internal@krost.com.au';
                        break;

                    case 'NSW':
                    case 'ACT':
                    case 'NT':
                    case 'WA':
                    case 'Other':
                        $to[] = 'sales-nsw-internal@krost.com.au';
                        $to[] = 'jane@krost.com.au';
                        break;

                    case 'QLD':
                        $to[] = 'valerie@krost.com.au';
                        $to[] = 'sales-qld@krost.com.au';
                        break;
                }

                $this->emailRepository->sendEmail(
                    array_values(array_unique($to)),
                    $subjectAdmin,
                    'Your catalogue request has been received',
                    $context,
                    $loaderPath,
                    $twigFileAdmin,
                    ['marketing@krost.com.au']
                );
            } else {
                $loaderPath = ROOT_DIR . '/src/themes/landing/src/emailtemplate';
                $baseurl = rtrim((string) env('APP_URL', (string) config('APP_URL', '')), '/');
                $requestUuid = (string) ($serviceRequest->uuid ?? $serviceRequest->data->uuid ?? '');
                $fileLinks = [];
                $attachmentLabels = [];
                if (count($attachment) > 0) {
                    foreach ($attachment as $file) {
                        if (!is_array($file)) {
                            continue;
                        }

                        $objectUrl = (string) ($file['objectURL'] ?? $file['path'] ?? '');
                        if ($objectUrl === '' || $requestUuid === '') {
                            continue;
                        }

                        $fileLinks[] = $baseurl . '/service-request/file?uuid=' . rawurlencode($requestUuid) . '&file=' . rawurlencode($objectUrl);
                        $originalName = '';
                        if (isset($file['file']['name']) && is_string($file['file']['name'])) {
                            $originalName = $file['file']['name'];
                        } elseif (isset($file['name']) && is_string($file['name'])) {
                            $originalName = $file['name'];
                        }
                        $attachmentLabels[] = $originalName !== '' ? $originalName : ('File ' . (string) (count($attachmentLabels) + 1));
                    }
                }

                $clientName = trim((string) $first_name . ' ' . (string) $last_name);
                $subject = 'New Get In Touch Enquiry Submitted' . ($clientName !== '' ? ' - ' . $clientName : '');

                $context = [
                    'subject' => $subject,
                    'team_name' => 'Sales Team',
                    'first_name_last_name' => $clientName,
                    'email_address' => (string) $email,
                    'company' => (string) ($company ?? ''),
                    'phone' => (string) ($phone_number ?? ''),
                    'submission_date' => date('d F Y'),
                    'notes_text' => $notes,
                    'state' => $state,
                    'project_details' => $project_details,
                    'attachment_links' => $fileLinks,
                    'attachment_labels' => $attachmentLabels,
                    'projects_url' => $baseurl . '/projects',
                    'hero_image_url' => $baseurl . '/media/catalogue/catalogue-confirmation/confirm1.png',
                ];

                $this->emailRepository->sendEmail(
                    'sales@krost.com.au',
                    $subject,
                    $subject,
                    $context,
                    $loaderPath,
                    'contact-us-touch.html.twig'
                );
            }
        }

        return $serviceRequest ? [
                'success' => true, 
                'message' => 'Request created successfully',
                'data' => $serviceRequest->data->uuid ?? ''
            ] : ['success' => false, 'message' => 'Request creation failed'];
    }

    public function getStateFromAddress(string $address_string): ?array 
    {
        if (empty(trim($address_string))) {
            return null;
        }
    
        $client = new Client([
            'base_uri' => 'https://geocode.maps.co',
            'timeout'  => 6.0,
        ]);
    
        try {
            // This free API allows open global lookups
            $response = $client->request('GET', '/search', [
                'query' => [
                    'q' => $address_string
                ]
            ]);
    
            $body = $response->getBody()->getContents();
            $data = json_decode($body, true);
    
            if (!empty($data) && isset($data[0]['display_name'])) {
                // The API returns a comma-separated string: "Mascot, Bayside Council, New South Wales, 2020, Australia"
                $addressParts = explode(',', $data[0]['display_name']);
                
                // Trim whitespace from all parts
                $addressParts = array_map('trim', $addressParts);
                
                // State is usually the 3rd or 4th item from the right side (before zip/country)
                // Let's look for common state markers or pick the element relative to Country
                $totalParts = count($addressParts);
                
                if ($totalParts >= 3) {
                    // In "Mascot, NSW, 2020, Australia", Country is last, Zip is second to last, State is third to last
                    $stateName = $addressParts[$totalParts - 3]; 
                    
                    // If the user typed "NSW" in the address, let's keep it clean
                    return [
                        'long_name'  => $stateName, // e.g., "New South Wales" or "NSW"
                        'short_name' => strtoupper(substr($stateName, 0, 3)) // e.g., "NSW"
                    ];
                }
            }
        } catch (\Exception $e) {
            // Fallback string scanner if the external service times out
            if (preg_match('/\b(NSW|VIC|QLD|WA|SA|TAS|ACT|NT)\b/i', $address_string, $matches)) {
                return [
                    'long_name'  => strtoupper($matches[1]),
                    'short_name' => strtoupper($matches[1])
                ];
            }
        }
    
        // Ultimate fallback text match in case API returned data but array parsing skipped it
        if (preg_match('/\b(NSW|VIC|QLD|WA|SA|TAS|ACT|NT)\b/i', $address_string, $matches)) {
            return [
                'long_name'  => strtoupper($matches[1]),
                'short_name' => strtoupper($matches[1])
            ];
        }
    
        return null;
    }
    public function getServiceRequests($filters = []): array
    {
        $query = $this->model->select(['*']);

        $search = isset($filters['search']) ? trim((string) $filters['search']) : '';
        if ($search !== '') {
            $query
                ->where('email', 'LIKE', "%{$search}%")
                ->orWhere('first_name', 'LIKE', "%{$search}%")
                ->orWhere('last_name', 'LIKE', "%{$search}%")
                ->orWhere('company', 'LIKE', "%{$search}%")
                ->orWhere('request_type', 'LIKE', "%{$search}%")
                ->orWhere('form_type', 'LIKE', "%{$search}%");
        }

        if (isset($filters['email']) && trim((string) $filters['email']) !== '') {
            $query->where('email', '=', trim((string) $filters['email']));
        }

        if (isset($filters['uuid']) && trim((string) $filters['uuid']) !== '') {
            $query->where('uuid', '=', trim((string) $filters['uuid']));
        }

        if (isset($filters['pinboard_id']) && $filters['pinboard_id'] !== '') {
            $query->where('pinboard_id', '=', (int) $filters['pinboard_id']);
        }

        if (isset($filters['customer_id']) && $filters['customer_id'] !== '') {
            $query->where('customer_id', '=', (int) $filters['customer_id']);
        }

        if (isset($filters['request_type']) && trim((string) $filters['request_type']) !== '') {
            $query->where('request_type', '=', trim((string) $filters['request_type']));
        }

        if (isset($filters['form_type']) && trim((string) $filters['form_type']) !== '') {
            $query->where('form_type', '=', trim((string) $filters['form_type']));
        }

        if (isset($filters['created_from']) && trim((string) $filters['created_from']) !== '') {
            $query->where('created_at', '>=', trim((string) $filters['created_from']));
        }

        if (isset($filters['created_to']) && trim((string) $filters['created_to']) !== '') {
            $query->where('created_at', '<=', trim((string) $filters['created_to']));
        }

        $sortBy = isset($filters['sort_by']) && trim((string) $filters['sort_by']) !== ''
            ? trim((string) $filters['sort_by'])
            : 'created_at';
        $sortDirection = strtoupper((string) ($filters['sort_direction'] ?? 'DESC'));
        $sortDirection = in_array($sortDirection, ['ASC', 'DESC'], true) ? $sortDirection : 'DESC';
        $query->orderBy($sortBy, $sortDirection);

        $limit = isset($filters['limit']) ? (int) $filters['limit'] : 0;
        $offset = isset($filters['offset']) ? (int) $filters['offset'] : 0;
        if ($limit > 0) {
            $query->limit($limit)->offset(max(0, $offset));
        }

        $data = $query->findAll();
        $total = $query->countAll();

        return [
            'data' => $data,
            'total' => $total,
        ];
    }

    public function getServiceRequestByUuid(string $uuid):array
    {
        $serviceRequest = $this->model->where('uuid', '=', $uuid)
        ->orderBy('created_at', 'desc')
        ->limit(1)
        ->first();
        $data = (array) ($serviceRequest?->data ?? []);

        if(isset($data['catalogue_format']) && in_array($data['form_type'], ['physical_catalogue', 'online_catalogue', 'contact-us'])){
            $data['show_contact_sales_section'] = true;
            if(in_array($data['catalogue_format'], ['physical_catalogue', 'online_catalogue'])){
                $data['show_catalogue_request_section'] = true;
            }
        }
        if($serviceRequest){
            $image = isset($serviceRequest->data->attachments) ? json_decode($serviceRequest->data->attachments, true) : [];
            $image = isset($image[0]['objectURL']) ? $image[0]['objectURL'] : '';
            $data['image'] = $image;
            return $data;
        }

        return $data;
    }

    

  
} 