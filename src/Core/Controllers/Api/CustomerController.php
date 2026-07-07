<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\Customer\CustomerRepositoryInterface;
use App\Core\Models\Pinboard\PinboardData;
use App\Core\Models\Pinboard\PinboardItemData;
use App\Core\Models\Pinboard\PinboardResponse;
use App\Core\Repositories\Pinboard\PinboardItemRepositoryInterface;
use App\Core\Repositories\Pinboard\PinboardRepositoryInterface;
use App\Core\Services\AuthService;
use App\Core\Repositories\Product\ProductRepositoryInterface;
use Exception;
use function App\Core\System\utils\env;
use App\Core\Repositories\Email\EmailRepositoryInterface;

/**
 * All customer setup related data control from here.
 */
class CustomerController extends ApiController
{
    /**
     * OAuth scopes requested when issuing customer session tokens.
     * Empty: AuthService resolves scopes from the database (user scopes).
     *
     * @var list<string>
     */
    private const CUSTOMER_LOGIN_OAUTH_SCOPES = [];

    private CustomerRepositoryInterface $customerRepository;
    private AuthService $authService;
    private PinboardRepositoryInterface $pinboardRepository;
    private PinboardItemRepositoryInterface $pinboardItemRepository;
    private ProductRepositoryInterface $productRepository;
    private EmailRepositoryInterface $emailRepository;
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        AuthService $authService,
        PinboardRepositoryInterface $pinboardRepository,
        PinboardItemRepositoryInterface $pinboardItemRepository,
        ProductRepositoryInterface $productRepository,
        EmailRepositoryInterface $emailRepository
    ) {
        parent::__construct();
        $this->customerRepository = $customerRepository;
        $this->authService = $authService;
        $this->pinboardRepository = $pinboardRepository;
        $this->pinboardItemRepository = $pinboardItemRepository;
        $this->productRepository = $productRepository;
        $this->emailRepository = $emailRepository;
    }

    /**
     * Fetch all customers
     */
    public function getAllCustomers(Request $request): Response
    {
        $data = $this->customerRepository->getAllCustomers();
        return $this->renderResponse($data);
    }

    public function searchCustomers(Request $request): Response
    {
        $query = $request->query('query');
        $data = $this->customerRepository->searchCustomers($query);
        return $this->renderResponse($data);
    }

    /**
     * Get customer by ID
     */
    public function getCustomerById(Request $request, $id): Response
    {
        $data = $this->customerRepository->getCustomerById((int) $id);
        return $this->renderResponse($data);
    }

    /**
     * Create a new customer
     */
    public function create(Request $request): Response
    {
        $data = $request->all();
        try {
            if ($data instanceof Response) {
                return $data;
            }
            $customer = $this->customerRepository->createCustomer($data);
            return $this->renderResponse($customer);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    /**
     * Update an existing customer
     */
    public function update(Request $request, $id): Response
    {
        $data = $request->all();
        try {
            if ($data instanceof Response) {
                return $data;
            }
            $customer = $this->customerRepository->updateCustomer($data, (int) $id);
            return $this->renderResponse($customer);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    /**
     * Delete a customer
     */
    public function delete(Request $request, $id): Response
    {
        try {
            $customer = $this->customerRepository->deleteCustomer((int) $id);
            return $this->renderResponse($customer);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    /**
     * Delete multiple customers
     */
    public function deleteMultiple(Request $request): Response
    {
        $data = $request->all();
        $customer_ids = $data['customer_ids'] ?? [];
        
        if (empty($customer_ids) || !is_array($customer_ids)) {
            return $this->renderError(400, 'customer_ids array is required');
        }
        
        try {
            $result = $this->customerRepository->deleteMultipleCustomers($customer_ids);
            return $this->renderResponse($result);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    /**
     * Import customers from CSV file
     */
    public function importCustomers(Request $request): Response
    {
        $csv_file = $request->file('csv_file');
        
        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';
        
        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->customerRepository->importCustomers($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Get all customers with pagination
     */
    public function getAll(Request $request): Response
    {
        $start = (int) ($request->get('start') ?? 0);
        $limit = (int) ($request->get('limit') ?? 10);
        $search = $request->get('search');
        
        try {
            $result = $this->customerRepository->getAll($start, $limit, $search);
            return $this->renderResponse($result);
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    /**
     * Get a single customer by ID
     */
    public function get(Request $request, $id): Response
    {
        try {
            $customer = $this->customerRepository->get((int) $id);
            if (!$customer) {
                return $this->renderError(404, 'Customer not found');
            }
            return $this->renderResponse($customer);
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    /**
     * Check if customer exists
     */
    public function checkExistingCustomer(Request $request): Response
    {
        $data = $request->all();
        if (empty($data['email'])) {
            return $this->renderError(400, 'Email is required');
        }
        $customer = $this->customerRepository->checkExistingCustomer($data['email']);
        return $this->renderResponse($customer);
    }

    // send email verification
    public function sendEmailVerification(Request $request): Response
    {
        $email = $request->input('email');
        $name = $request->input('customer_name');
        if (empty($email)) {
            return $this->renderError(400, 'Email or Gmail ID is required');
        }
        $customer = $this->customerRepository->getCustomerInfo($email, $name);
        if (!$customer) {
            return $this->renderError(404, 'Customer not found');
        }
        try {
            try {
                $result = $this->customerRepository->sendEmailVerification($email);
            } catch (Exception $e) {
                return $this->renderError(500, $e->getMessage());
            }
            if(isset($result['status']) && $result['status'] == 200) {
                return $this->renderResponse($result);
            } else if(isset($result['status']) && $result['message']) {
                return $this->renderError($result['status'], $result['message']);
            }else{
                return $this->renderError(500, 'Failed to send email verification');
            }
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

     /**
     * Get pinboard by logged in user
     *
     * @param Request $request
     * @return Response
     */
    public function pinboard(Request $request): Response
    {
        $userAuthDetails = $this->authService->getAuthUser();
        if (!$userAuthDetails) {
            return $this->renderError(401, 'Unauthorized');
        }
        $userAuthDetails = (array) $userAuthDetails->data;
        $userId = (int)($userAuthDetails['user_id'] ?? 0);
        if ($userId <= 0) {
            return $this->renderError(401, 'Unauthorized');
        }
        $customer = $this->customerRepository->findByUserId($userId);
        if (!$customer || $customer['customer_id'] <= 0) {
            return $this->renderError(401, 'Unauthorized');
        }
        $pinboard = $this->pinboardRepository->getCustomerPinboard((int)$customer['customer_id']);
        if (!$pinboard) {
            return $this->renderError(404, 'Pinboard not found');
        }

        $response = new PinboardResponse($pinboard->data);

        if (isset($response->productIds) && count($response->productIds) > 0) {
            $productTitles = $this->productRepository->getProductTitlesByProductIds($response->productIds);
            foreach ($response->pinboardItems as $index => $item) {
                if (($item['type'] ?? '') === 'product' && isset($item['model_id'])) {
                    $modelId = (int) $item['model_id'];
                    if (isset($productTitles[$modelId])) {
                        $response->pinboardItems[$index]['title'] = $productTitles[$modelId];
                    }
                }
            }
        }
        return $this->renderResponse($response);
    }

    // verify email
    public function verifyEmail(Request $request): Response
    {
        $data = $request->all();
        $email = isset($data['email']) ? $data['email'] : (isset($data['gmail_Id']) ? $data['gmail_Id'] : '');
        $otpCode = isset($data['otp']) ? $data['otp'] : '';
        if (empty($email) || empty($otpCode)) {
            return $this->renderError(400, 'Email or OTP code is required');
        }
        $result = $this->customerRepository->verifyEmail($email, $otpCode);
        if ($result['status'] == 200 && isset($result['user']['user_id'])) {
            $auth = $this->authService->login((int) $result['user']['user_id'], self::CUSTOMER_LOGIN_OAUTH_SCOPES);
            return $this->renderResponse($auth);
        } else {
            return $this->renderError($result['status'], $result['message']);
        }
    }

    /**
     * Verify email OTP, create pinboard + items, send confirmation email, then establish
     * Redis-backed browser session and OAuth tokens via AuthService::login (same payload shape as web OTP login).
     */
    public function verifyEmailAthenticateAndCreatePinboard(Request $request): Response
    {
        $data = $request->all();
        $email = isset($data['email']) ? $data['email'] : (isset($data['gmail_Id']) ? $data['gmail_Id'] : '');
        $otpCode = isset($data['otp']) ? $data['otp'] : '';
        $subject = isset($data['subject']) ? $data['subject'] : 'OTP Verification with Krost';
        if (empty($email) || empty($otpCode)) {
            return $this->renderError(400, 'Email or OTP code is required');
        }
        $result = $this->customerRepository->verifyEmail($email, $otpCode, $subject);
        if ($result['status'] == 200) {
            $userId = (int)($result['user']['user_id'] ?? 0);
            if ($userId <= 0) {
                return $this->renderError(500, 'User not found after verification');
            }

            try {
                $pinboardPayload = $request->input('pinboard');
                if (!is_array($pinboardPayload)) {
                    return $this->renderError(422, 'pinboard payload is required');
                }

                // Ensure verified customer/user ids are available for pinboard creation.
                $pinboardPayload['user_id'] = $pinboardPayload['user_id'] ?? $userId;
                if (isset($result['customer']['customer_id'])) {
                    $pinboardPayload['customer_id'] = $result['customer']['customer_id'];
                }else{
                    return $this->renderError(404, 'Customer not found');
                }
                $pinboardPayload['is_active'] = 1;
                $pinboardData = new PinboardData($pinboardPayload);
                $itemProducts = isset($data['pinboard']['pinboard_items']) ?$data['pinboard']['pinboard_items']: [];
            } catch (ValidationException $e) {
                return $this->renderError(422, $e->getMessage(), $e->getErrors());
            }

            $pinboard = $this->pinboardRepository->createPinboard($pinboardData);
            if (!$pinboard) {
                return $this->renderError(500, 'Failed to create pinboard');
            }

            if (!empty($itemProducts)) {
                $this->pinboardItemRepository->deleteByPinboardId($pinboard->pinboard_id);

                $pinboardItems = array_map(function ($product) use ($pinboard) {
                    $product['uuid'] = $this->generateUuid();
                    $product['pinboard_id'] = $pinboard->pinboard_id;
                    $pinboardItem = new PinboardItemData($product);
                    return $pinboardItem->toArray();
                }, $itemProducts);

                $this->pinboardItemRepository->createPinboardItems($pinboardItems);
            }

            $pinboard = $this->pinboardRepository->showPinboard($pinboard->pinboard_id);
            $pinboard = new PinboardResponse($pinboard->data);
            if (isset($pinboard->productIds) && count($pinboard->productIds) > 0) {
                $productTitles = $this->productRepository->getProductTitlesByProductIds($pinboard->productIds);
                foreach ($pinboard->pinboardItems as $index => $item) {
                    if (($item['type'] ?? '') === 'product' && isset($item['model_id'])) {
                        $modelId = (int) $item['model_id'];
                        if (isset($productTitles[$modelId])) {
                            $pinboard->pinboardItems[$index]['title'] = $productTitles[$modelId];
                        }
                    }
                }
            }


            $appUrlSales = rtrim((string) env('APP_ADMIN_URL'), '/');
            $appUrlGuest = rtrim((string) env('APP_URL'), '/');
            $context = [
                'subject' => 'KROST | ' . $pinboard->pinboard_name,
                'project_title' => $pinboard->pinboard_name,
                'project_name' => $pinboard->pinboard_name,
                'saved_at' => $pinboard->created_at,
                'items_count' => count($pinboard->pinboardItems),
                // 'items' => $pinboard->pinboardItems,
                'items' => $this->normalizePinboardEmailItems($pinboard->pinboardItems ?? []),
                'app_url' => $appUrlGuest,
                'pinboard_url' => $appUrlGuest . '/account/virtual-pinboards',
                'dashboard_project_url' => $appUrlSales . '/pinboards/' . $pinboard->pinboard_id . '/overview',
                'share_url' => $appUrlGuest . '/projects',
                'showroom_tour_url' => $appUrlGuest . '/contact-sales#book-now',
                'consultation_url' => $appUrlGuest . '/contact-sales#th-contact-members',
                'next_steps_intro' => 'Ready to see these pieces in person? Book a showroom tour or request a consultation directly through your dashboard.',
                'showroom_locations_text' => 'Sydney - Melbourne - Brisbane',
                'consultation_subtext' => 'Spec advice from our specialists',
                'sender_name' => 'Krost Sales Team',
                'team_name' => 'Sales Team',
                'client_email' => $email,
                'board_url' => $appUrlGuest . '/account/virtual-pinboards',
                'board_link_label' => 'URL to see the board',
                'submission_date' => date('d F Y'),
                'submission_date_short' => date('d/m/Y'),
                'icon_chat_src_briefcase' => $appUrlGuest . '/images/icons/briefcase.png',
                'icon_chat_src_message' => $appUrlGuest . '/images/icons/message.png',
            ];

            // send email to the client
            $this->emailRepository->sendEmail(
                $email,
                'Project saved - Krost',
                'Project saved - Krost',
                $context,
                ROOT_DIR . '/src/themes/landing/src/emailtemplate',
                'project-submission-confirmation-client.html.twig'
            );

            // send email to the admin
            $this->emailRepository->sendEmail(
                'sales@krost.com.au',
                'Project saved - Krost',
                'Project saved - Krost',
                $context,
                ROOT_DIR . '/src/themes/landing/src/emailtemplate',
                'project-submission-confirmation-admin.html.twig'
            );

            try {
                $payload = $this->authService->login($userId, self::CUSTOMER_LOGIN_OAUTH_SCOPES);
            } catch (\Throwable $e) {
                return $this->renderError(500, $e->getMessage());
            }

            return $this->renderResponse($payload);
        } else {
            return $this->renderError($result['status'], $result['message']);
        }
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

    /**
     * @param list<array<string, mixed>> $items
     * @return list<array<string, mixed>>
     */
    private function normalizePinboardEmailItems(array $items): array
    {
        return array_map(function (array $item): array {
            if (!empty($item['photo']) && is_string($item['photo'])) {
                $item['photo'] = $this->normalizeMediaUrl($item['photo']);
            }

            return $item;
        }, $items);
    }

    private function normalizeMediaUrl(?string $url): string
    {
        if ($url === null || $url === '') {
            return '';
        }
        if (str_starts_with($url, 'data:') || str_starts_with($url, 'blob:')) {
            return $url;
        }

        $query = '';
        $fragment = '';
        if (($pos = strpos($url, '?')) !== false) {
            $query = substr($url, $pos);
            $url = substr($url, 0, $pos);
        }
        if (($pos = strpos($url, '#')) !== false) {
            $fragment = substr($url, $pos);
            $url = substr($url, 0, $pos);
        }

        return str_replace(['+', ' '], '%20', $url) . $query . $fragment;
    }

    /**
     * Inline image src for email templates (data URI).
     * Mail clients cannot fetch localhost URLs; embed icons in the HTML instead.
     */
    private function emailEmbeddedIconSrc(string $iconFileName): string
    {
        $fallbacks = [
            'briefcase.png' => 'data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2228%22%20height%3D%2228%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%23333333%22%20stroke-width%3D%221.5%22%3E%3Crect%20x%3D%222%22%20y%3D%227%22%20width%3D%2220%22%20height%3D%2214%22%20rx%3D%222%22%2F%3E%3Cpath%20d%3D%22M16%207V5a2%202%200%200%200-2-2h-4a2%202%200%200%200-2%202v2%22%2F%3E%3C%2Fsvg%3E',
            'message.png' => 'data:image/svg+xml,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2228%22%20height%3D%2228%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%23333333%22%20stroke-width%3D%221.5%22%3E%3Cpath%20d%3D%22M21%2015a2%202%200%2001-2%202H7l-4%204V5a2%202%200%20012-2h14a2%202%200%20012%202z%22%2F%3E%3C%2Fsvg%3E',
        ];

        if (defined('DIR_MEDIA')) {
            $mediaPath = DIR_MEDIA . 'design-resource' . DIRECTORY_SEPARATOR . 'icons' . DIRECTORY_SEPARATOR . $iconFileName;
            if (is_readable($mediaPath)) {
                $contents = file_get_contents($mediaPath);
                if ($contents !== false) {
                    $mime = mime_content_type($mediaPath) ?: 'image/png';

                    return 'data:' . $mime . ';base64,' . base64_encode($contents);
                }
            }
        }

        return $fallbacks[$iconFileName] ?? '';
    }
}




