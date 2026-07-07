<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\Pinboard\PinboardRepositoryInterface;
use App\Core\Models\Pinboard\PinboardData;
use App\Core\Models\Pinboard\PinboardResponse;
use App\Core\Models\Pinboard\PinboardResponseData;
use App\Core\Repositories\Pinboard\PinboardItemRepositoryInterface;
use App\Core\Repositories\Showroom\ShowroomRepositoryInterface;
use App\Core\Repositories\Pinboard\PinboardTempRepositoryInterface;
use GuzzleHttp\Client;
use App\Core\Repositories\Media\MediaRepositoryInterface;
use App\Core\Repositories\Product\ProductRepositoryInterface;
use App\Core\Services\AuthService;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Container\Attributes\Log;

use function App\Core\System\utils\env;

class PinboardController extends ApiController
{
    private PinboardRepositoryInterface $pinboardRepository;
    private PinboardItemRepositoryInterface $pinboardItemRepository;
    private ShowroomRepositoryInterface $showroomRepository;
    private MediaRepositoryInterface $mediaRepository;
    private PinboardTempRepositoryInterface $pinboardTempRepository;
    private ProductRepositoryInterface $productRepository;
    private AuthService $authService;
    public function __construct(
        PinboardRepositoryInterface $pinboardRepository, 
        PinboardItemRepositoryInterface $pinboardItemRepository, 
        ShowroomRepositoryInterface $showroomRepository, 
        MediaRepositoryInterface $mediaRepository,
        PinboardTempRepositoryInterface $pinboardTempRepository,
        ProductRepositoryInterface $productRepository,
        AuthService $authService
    )
    {
        parent::__construct();
        $this->pinboardRepository = $pinboardRepository;
        $this->pinboardItemRepository = $pinboardItemRepository;
        $this->showroomRepository = $showroomRepository;
        $this->mediaRepository = $mediaRepository;
        $this->pinboardTempRepository = $pinboardTempRepository;
        $this->productRepository = $productRepository;
        $this->authService = $authService;
    }

    /**
     * Get all pinboards
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $pinboards = $this->pinboardRepository->allPinboards();
        return $this->renderResponse($pinboards);
    }
    public function temporaryPinboardIndex(Request $request): Response
    {
        $pinboards = $this->pinboardTempRepository->allTemporaryPinboards();
        return $this->renderResponse($pinboards);
    }

    /**
     * Get pinboard by ID
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function show(Request $request, $pinboard_id): Response
    {
        $pinboard = $this->pinboardRepository->showPinboard((int)$pinboard_id);
        if (!$pinboard) {
            return $this->renderError(404, 'Pinboard not found');
        }

        $response = new PinboardResponse($pinboard->data);
        $this->pinboardRepository->attachPinboardStatusToResponse($response);

        $commentCounts = $this->pinboardRepository->countComment((int) $pinboard_id);
        $productTitles = [];
        if (isset($response->productIds) && count($response->productIds) > 0) {
            $productTitles = $this->productRepository->getProductTitlesByProductIds($response->productIds);
        }

        foreach ($response->pinboardItems as $index => $item) {
            $pinboardItemId = (int) ($item['pinboard_item_id'] ?? 0);
            if ($pinboardItemId > 0) {
                $response->pinboardItems[$index]['comment_count'] = $commentCounts[$pinboardItemId] ?? 0;
            }

            if (($item['type'] ?? '') === 'product' && isset($item['model_id'])) {
                $modelId = (int) $item['model_id'];
                if (isset($productTitles[$modelId])) {
                    $response->pinboardItems[$index]['title'] = $productTitles[$modelId];
                }
            }
        }
        return $this->renderResponse($response);
    }

    public function showPinboardItem(Request $request, $id): Response
    {
        $pinboard = $this->pinboardRepository->getPinboardDataForComponent((int)$id);
        if (!$pinboard) {
            return $this->renderError(404, 'Pinboard item not found');
        }
        return $this->renderResponse($pinboard);
    }

    /**
     * Create a new pinboard
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        try {
            $pinboard = $request->input('pinboard');
            $pinboardData = new PinboardData($pinboard);
            $itemProducts = $pinboard['pinboardItems'] ?? [];
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $pinboard = $this->pinboardRepository->createPinboard($pinboardData);
        if (!$pinboard) {
            return $this->renderError(500, 'Failed to create pinboard');
        }

        if (!empty($itemProducts)) {
            $this->pinboardItemRepository->deleteByPinboardId($pinboard->pinboard_id);

            // Add coupon_id to each product
            $itemProductsWithId = array_map(function ($product) use ($pinboard) {
                return [
                    'pinboard_id' => $pinboard->pinboard_id,
                    'model_id' => $product['model_id'],
                    'model_type' => $product['model_type'],
                    'description' => isset($product['item_description']) ? $product['item_description'] : null,
                    'comments' => isset($product['item_comments']) ? $product['item_comments'] : null,
                    'quantity' => $product['item_quantity'],
                    'unit_price' => $product['item_unit_price'],
                    'total_price' => $product['item_total'],
                    'uuid' => $this->generateUuid(),
                    'language_id' => $product['language_id'] ?? 1,
                    'sort_order' => 1
                ];
            }, $itemProducts);

            $this->pinboardItemRepository->createPinboardItems($itemProductsWithId);
        }

        $pinboard = new PinboardResponse($pinboard->data);
        return $this->renderResponse($pinboard);
    }
    public function saveTempPinboard(Request $request): Response
    {
        $data = $request->all();
        $pinboard = $this->pinboardTempRepository->savePinboard((array) $data);
        if (!$pinboard) {
            return $this->renderError(500, 'Failed to save pinboard');
        }

        return $this->renderResponse($pinboard);
    }

    public function savePinboard(Request $request): Response
    {
        $data = $request->all();
        $pinboard = $this->pinboardRepository->savePinboard((array) $data);
        if (!$pinboard) {
            return $this->renderError(500, 'Failed to save pinboard');
        }

        return $this->renderResponse($pinboard);
    }

    public function createNewProject(Request $request): Response
    {
        $data = $request->all();
        $pinboard = $this->pinboardRepository->createNewProject((array) $data);
        if (!$pinboard) {
            return $this->renderError(500, 'Failed to create new project');
        }

        return $this->renderResponse($pinboard);
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
     * Update a pinboard
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id): Response
    {
        try {
            $pinboard = $request->input('pinboard');
            $fields = PinboardData::getUpdateFieldsFromRequest($pinboard);
            $pinboardData = new PinboardData($pinboard);
            $itemProducts = $pinboard['pinboardItems'] ?? [];
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }
        $pinboard = $this->pinboardRepository->updatePinboard($pinboardData, $fields);
        if (!$pinboard) {
            return $this->renderError(500, 'Failed to update pinboard');
        }

        if (!empty($itemProducts)) {
            $this->pinboardItemRepository->deleteByPinboardId($pinboard->pinboard_id);

            $pinboardItemResult = $this->pinboardItemRepository->createPinboardItem($itemProducts);
            if (!$pinboardItemResult) {
                return $this->renderError(500, 'Failed to create pinboard item');
            }
        }

        $pinboard = new PinboardResponse($pinboard->data);
        return $this->renderResponse($pinboard);
    }

    /**
     * Delete a pinboard
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function delete(Request $request, $id): Response
    {
        $pinboard = $this->pinboardRepository->showPinboard((int) $id);
        if (!$pinboard) {
            return $this->renderError(404, 'Pinboard not found');
        }

        try {
            $this->pinboardRepository->delete((int) $id);
            return $this->renderResponse(['message' => 'Pinboard deleted successfully']);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Failed to delete pinboard: ' . $e->getMessage());
        }
    }
    public function deleteTemporaryPinboard(Request $request, $id): Response
    {
        // $pinboard = $this->pinboardTempRepository->showTemporaryPinboard((int) $id);
        // if (!$pinboard) {
        //     return $this->renderError(404, 'Pinboard not found');
        // }

        try {
            $this->pinboardTempRepository->delete((int) $id);
            return $this->renderResponse(['message' => 'Pinboard deleted successfully']);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Failed to delete pinboard: ' . $e->getMessage());
        }
    }

    public function importPinboards(Request $request): Response
    {
        $csv_file = $request->file('csv_file');

        // Extract the file path from the uploaded file array
        $csv_file_path = $csv_file['tmp_name'] ?? $csv_file['name'] ?? '';

        if (empty($csv_file_path)) {
            return $this->renderError(400, 'No CSV file uploaded or file path not found');
        }

        try {
            $result = $this->pinboardRepository->importPinboards($csv_file_path);
            return $this->renderResponse($result);
        } catch (\Exception $e) {
            return $this->renderError(500, 'Import failed: ' . $e->getMessage());
        }
    }

    // automatic send sales team - This api will call to our ERP system API (I will make it). For now create a dummy api to test.
    public function automaticSendSalesTeam(Request $request): Response
    {
        $data = $request->all();
        $client = new Client();

        try {
            // dummy get api call
            $response = $client->get('https://jsonplaceholder.typicode.com/users');
            // dummy post api call
            // $response = $client->post('https://jsonplaceholder.typicode.com/posts', [
            //     'json' => [
            //         'title' => 'foo',
            //         'body' => 'bar',
            //         'userId' => 1,
            //     ]
            // ]);

            $status = $response->getStatusCode();
            $body = $response->getBody()->getContents();

            if ($status !== 200) {
                return $this->renderError(500, 'Failed to get users: ' . $body);
            }

            $users = json_decode($body, true); 
            $users = array_slice($users, 0, 10);

            return $this->renderResponse([
                'status' => 200,
                'message' => 'Users retrieved successfully',
                'data' => $users,
            ]);

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            return $this->renderError(500, 'Request failed: ' . $e->getMessage());
        }
    }
   
    public function automaticSendEmailClient(Request $request): Response
    {
        $result = $this->pinboardRepository->automaticSendEmailClient();
        if (!$result) {
            return $this->renderError(500, 'Failed to get account pinboard list');
        }
        return $this->renderResponse($result);
    }
    public function getUserNearestShowroomByIp_backup(Request $request): Response
    {
        $client = new Client();
    
        try {
            // Get user IP geolocation
            $response = $client->get('https://ipwho.is/');
            $data = json_decode($response->getBody()->getContents(), true);
    
            if (!($data['success'] ?? true)) {
                return $this->renderError(500, 'IP lookup failed');
            }
            $regionCode = $data['region_code'] ?? 'NSW';
    
            // Get all showrooms (already has 'group' field)
            $showrooms = $this->showroomRepository->getShowroomForPinboard();
            $showroomsContact = $this->showroomRepository->getMembersData();
            $salesTeams = isset($showroomsContact['sales_teams']) ? $showroomsContact['sales_teams'][0] : [];

    
            // Define region code → showroom group mapping
            $SHOWROOM_GROUP_STATE_MAPPING = [
                'SYDNEY' => ['NSW', 'NT', 'ACT'],
                'MELBOURNE' => ['VIC', 'TAS', 'SA'],
                'BRISBANE' => ['QLD', 'WA'],
            ];

            // Find which group this region code belongs to
            $showroomGroup = null;
            foreach ($SHOWROOM_GROUP_STATE_MAPPING as $group => $states) {
                if (in_array($regionCode, $states)) {
                    $showroomGroup = $group;
                    break;
                }else{
                    $showroomGroup = 'BRISBANE';
                }
            }
    
            // Find nearest showroom by group
            $nearestShowroom = collect($showrooms)->firstWhere('group', $showroomGroup);
            $nearestShowroom['showroom_contact_id'] = isset($salesTeams['showroom_contact_id']) ? $salesTeams['showroom_contact_id'] : null;
            $nearestShowroom['contact_name'] = isset($salesTeams['name']) ? $salesTeams['name'] : null;
            $showroomId = isset($nearestShowroom['showrooms_id']) ? $nearestShowroom['showrooms_id'] : null;

            // get data from timezone json file
            // $timezoneJsonPath = base_path('themes/landing/js/lib/timezones.json');
            // $timezoneData = file_get_contents($timezoneJsonPath);
            // $timezoneData = json_decode($timezoneData, true);
            // $timezoneData = collect($timezoneData)->firstWhere('showroom_id', $showroomId);
            // $nearestShowroom['timezone'] = isset($timezoneData['value']) ? $timezoneData['value'] : null;
    
            // Return response
            return $this->renderResponse([
                'status' => 200,
                'message' => 'IP address geolocation retrieved successfully',
                'data' => [
                    'geolocation' => $data,
                    'all_showrooms' => $showrooms,
                    'nearest_showroom' => $nearestShowroom,
                ]
            ]);
    
        } catch (\Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    public function getUserNearestShowroomByIp(Request $request): Response
    {
        $client = new Client();
        $data = []; // Default empty array inline rakha holo fallback er jonno

        try {
            $response = $client->get('https://ipwho.is/');
            $apiData = json_decode($response->getBody()->getContents(), true);

            if (isset($apiData) && ($apiData['success'] ?? false)) {
                $data = $apiData;
            }
        } catch (\Exception $e) {
           
        }

        try {
            $regionCode = isset($data['region_code']) ? $data['region_code'] : 'NSW';

            $showrooms = $this->showroomRepository->getShowroomForPinboard();
            $showroomsContact = $this->showroomRepository->getMembersData();
            $salesTeams = isset($showroomsContact['sales_teams']) ? $showroomsContact['sales_teams'][0] : [];

            $SHOWROOM_GROUP_STATE_MAPPING = [
                'SYDNEY' => ['NSW', 'NT', 'ACT'],
                'MELBOURNE' => ['VIC', 'TAS', 'SA'],
                'BRISBANE' => ['QLD', 'WA'],
            ];

            // Find which group this region code belongs to
            $showroomGroup = 'BRISBANE'; // Fallback default directly outside the loop
            foreach ($SHOWROOM_GROUP_STATE_MAPPING as $group => $states) {
                if (in_array($regionCode, $states)) {
                    $showroomGroup = $group;
                    break;
                }
            }

            // Find nearest showroom by group
            $nearestShowroom = collect($showrooms)->firstWhere('group', $showroomGroup);
            $nearestShowroom['showroom_contact_id'] = isset($salesTeams['showroom_contact_id']) ? $salesTeams['showroom_contact_id'] : null;
            $nearestShowroom['contact_name'] = isset($salesTeams['name']) ? $salesTeams['name'] : null;
            $showroomId = isset($nearestShowroom['showrooms_id']) ? $nearestShowroom['showrooms_id'] : null;

            // Return response
            return $this->renderResponse([
                'status' => 200,
                'message' => 'IP address geolocation retrieved successfully',
                'data' => [
                    'geolocation' => $data, // API fail korle eta empty default array [] pathabe
                    'all_showrooms' => $showrooms,
                    'nearest_showroom' => $nearestShowroom,
                ]
            ]);

        } catch (\Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }
    
    public function saveComment(Request $request): Response
    {
        $authUser = $this->authUser();
        if (!isset($authUser) || !isset($authUser->user_id)) {
            return $this->renderError(403, 'Unauthorized');
        }

        $data = $request->all();
        $userId = isset($data['user_id']) ? (int) $data['user_id'] : null;
        if (!isset($userId) || $userId !== $authUser->user_id) {
            return $this->renderError(403, 'Unauthorized');
        }

        if(!isset($data['user_id']) || $data['user_id'] == 'undefined'){
            $data['user_id'] = $authUser->user_id;
            $data['author'] = 'Shofiul Alam';
        }
        $folder = 'media/Comments/';
        $size = [
            'width' => 945,
            'height' => 630,
        ];
        $result = null;
        if ($request->files() || isset($_FILES['files'])) {
            $files = $request->files() ?? $_FILES['files'];

            if (!count($files)) {
                return $this->renderError(422, 'No files uploaded');
            }
            $uploadData = [
                'files' => $files,
                'upload_dir' => $request->input('upload_dir', $folder)
            ];

            $result = $this->mediaRepository->upload($uploadData, $size, $folder, null, false, 5);
            // if (!$result) {
            //     return $this->renderError(500, 'Failed to upload media');
            // }

            $normalizedResult = $this->normalizeMediaResult($result);
            if (isset($normalizedResult['error'])) {
                return $this->renderError(500, $normalizedResult['error']);
            }          
        }

        $comment = $this->pinboardRepository->savePinboardComment((array) $data, $result['files'] ?? []);
        if (!$comment) {
            return $this->renderError(500, 'Failed to save comment');
        }   
        return $this->renderResponse($comment);
    }

    private function normalizeMediaResult(array $result): array
    {
        if (
            !empty($result['error']) // 'error' is non-empty array
            || (isset($result['files']) && (is_array($result['files']) ? count($result['files']) === 0 : !$result['files']))
        ) {
            $messages = [];
            if (!empty($result['error']) && is_array($result['error'])) {
                foreach ($result['error'] as $fileErrors) {
                    if (!is_array($fileErrors)) {
                        continue;
                    }
                    foreach ($fileErrors as $msg) {
                        if (is_string($msg) && $msg !== '') {
                            $messages[] = $msg . ' ';
                        }
                    }
                }
            }
            $fileMessage = $messages !== []
                ? implode(PHP_EOL, array_values(array_unique($messages)))
                : 'File upload failed. Please try again.';
            return ['error' => $fileMessage];
        }
        return ['files' => $result['files']];
    }

    public function bookingPhoneCall(Request $request): Response
    {
        if (!$this->authService->getAuthUser()) {
            return $this->renderError(401, 'Unauthorized');
        }
        $data = $request->all();
        $result = $this->pinboardRepository->bookingPhoneCall((array) $data);
        if (!$result) {
            return $this->renderError(500, 'Failed to book a phone call');
        }
        return $this->renderResponse($result);
    }

    public function updatePinboardStatus(Request $request, $id, $pinboard_status_id): Response
    {
        $result = $this->pinboardRepository->updatePinboardStatus((int) $id, (int) $pinboard_status_id);
        if (!$result) {
            return $this->renderError(500, 'Failed to accept pinboard');
        }
        return $this->renderResponse($result);
    }

    public function accountPinboardList(Request $request): Response
    {
        $customer_id = $request->query('customer_id');
        $result = $this->pinboardRepository->getPinboardListComponentData([
            'customer_id' => $customer_id,
        ]);
        if (!$result) {
            return $this->renderError(500, 'Failed to get account pinboard list');
        }
        return $this->renderResponse($result);
    }

    public function getProjectList(Request $request, $user_id): Response
    {
        $result = $this->pinboardRepository->getPinboardListComponentData([
            'user_id' => $user_id,
        ]);
        if (!$result) {
            return $this->renderError(500, 'Failed to get project list');
        }
        return $this->renderResponse($result);
    }

    public function searchPinboardProducts(Request $request): Response
    {
        $queryString = $request->query('query')??'';
        $results = $this->pinboardRepository->searchPinboardProducts($queryString);
        return $this->renderResponse($results);
    }

    public function updateProjectTitle(Request $request): Response
    {
        $data = $request->all();
        $result = $this->pinboardRepository->updateProjectTitle((array) $data);
        if (!$result) {
            return $this->renderError(500, 'Failed to update project title');
        }
        return $this->renderResponse($result);
    }

    public function updatePinboardVisibility(Request $request): Response
    {
        $data = $request->all();
        // return $this->renderResponse(['success' => true, 'message' => 'Pinboard visibility updated successfully']);
        $result = $this->pinboardRepository->updatePinboardVisibility((array) $data);
        if (!$result) {
            return $this->renderError(500, 'Failed to update pinboard visibility');
        }
        return $this->renderResponse($result);
    }

    public function submitProjectSubmission(Request $request): Response
    {
        $data = $request->all();
        return $this->renderResponse(['success' => true, 'message' => 'Project submission submitted successfully']);

        // $data = $request->all();
        // $result = $this->pinboardRepository->submitProjectSubmission((array) $data);
        // if (!$result) {
        //     return $this->renderError(500, 'Failed to submit project submission');
        // }
        // return $this->renderResponse($result);
    }


    public function createLead(Request $request, int $pinboard_id): Response
    {    
        try {
            $result = $this->pinboardRepository->createLead($pinboard_id);
            if (empty($result['success'])) {
                return $this->renderError(422, (string) ($result['message'] ?? 'Failed to create lead'));
            }
            if ($result && isset($result['data'])) {
                $kingmakerBase = rtrim((string) env('KINGMAKER', ''), '/');
                $krostToken = trim((string) env('KROST_TOKEN', ''), " \t\n\r\0\x0B\"'");
                if ($kingmakerBase !== '' && $krostToken !== '') {
                    $notifyUrl = $kingmakerBase . '/leads/import-pinboard?XDEBUG_SESSION_START=PHPSTORM';
                    try {
                        $httpResponse = (new Client(['timeout' => 150, 'verify' => false]))->post($notifyUrl, [
                            'headers' => [
                                'Accept' => 'application/json',
                                'Content-Type' => 'application/json',
                                'Authorization' => 'Bearer ' . $krostToken,
                            ],
                            'json' => $result['data'],
                        ]);
                        $responseBody = $httpResponse->getBody()->getContents();
                        if ($httpResponse->getStatusCode() >= 200 && $httpResponse->getStatusCode() < 300) {
                            $leadPayload = json_decode($responseBody, true);
                            $leadId = $this->extractLeadIdFromKingmakerResponse(
                                is_array($leadPayload) ? $leadPayload : null
                            );
                            if ($leadId > 0) {
                                $pinboard =$this->pinboardRepository->updatePinboardAfterLeadCreated($pinboard_id, $leadId);
                                if ($pinboard && isset($pinboard['data'])) {
                                    $result['pinboard'] = $pinboard['data'];
                                }
                            }
                        }
                    } catch (GuzzleException $e) {
                        Log::warning('Kingmaker pinboard notify failed', [
                            'url' => $notifyUrl,
                            'message' => $e->getMessage(),
                        ]);
                        return $this->renderError(500, $e->getMessage());
                    }
                }
            }
        } catch (\Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
        return $this->renderResponse($result);
    }

    public function leadCreated(Request $request): Response
    {
        $data = $request->all();
        print_r($data);
        exit;
        return $this->renderResponse(['success' => true, 'message' => 'Lead created successfully']);
    }

    private function extractLeadIdFromKingmakerResponse(?array $payload): int
    {
        if ($payload === null) {
            return 0;
        }

        foreach (['id', 'lead_id'] as $key) {
            if (isset($payload[$key]) && is_numeric($payload[$key]) && (int) $payload[$key] > 0) {
                return (int) $payload[$key];
            }

            if (
                isset($payload['data'])
                && is_array($payload['data'])
                && isset($payload['data'][$key])
                && is_numeric($payload['data'][$key])
                && (int) $payload['data'][$key] > 0
            ) {
                return (int) $payload['data'][$key];
            }
        }

        return 0;
    }

    public function updateStatus(Request $request): Response
    {
        $data = $request->all();
        $pinboardId = (int) ($data['pinboard_id'] ?? 0);
        $statusName = trim((string) ($data['status'] ?? $data['status_name'] ?? ''));

        if ($pinboardId <= 0) {
            return $this->renderError(422, 'pinboard_id is required');
        }

        if ($statusName === '') {
            return $this->renderError(422, 'status is required');
        }

        $result = $this->pinboardRepository->updatePinboardStatusByName($pinboardId, $statusName);

        if (empty($result['success'])) {
            $message = (string) ($result['message'] ?? 'Failed to update pinboard status');
            if (str_contains($message, 'Invalid status')) {
                return $this->renderError(422, $message);
            }
            if (str_contains($message, 'not found')) {
                return $this->renderError(404, $message);
            }
            return $this->renderError(500, $message);
        }

        return $this->renderResponse($result);
    }


}
