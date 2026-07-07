<?php

declare(strict_types=1);

namespace App\Core\Repositories\Pinboard;

use App\Core\Models\Localisation\Language;
use PDO;
use DateTime;
use App\Core\Models\Pinboard\Pinboard;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Models\Pinboard\PinboardData;
use App\Core\Models\Pinboard\PinboardItem;
use App\Core\Validation\PinboardDataValidation;
use League\Csv\Reader;
use App\Core\Models\Pinboard\PinboardJoinMap;
use App\Core\Repositories\UserRepositoryInterface;
use App\Core\Repositories\Customer\CustomerRepositoryInterface;
use App\Core\Repositories\Pinboard\PinboardItemRepositoryInterface;
use App\Core\Repositories\Media\MediaRepositoryInterface;
use App\Core\Models\Post\CommentPhoto;
use App\Core\Models\Post\Comment;
use App\Core\Models\Visit\VisitShowroom;
use App\Core\Models\Showroom\ShowroomContact;
use App\Core\Models\Service\ServiceRequest;
use App\Core\Repositories\Email\EmailRepositoryInterface;
use App\Core\Models\Design\GlobalSearchData;
use App\Core\Models\Product\Product;
use App\Core\Models\Pinboard\PinboardResponse;
use App\Core\Models\Pinboard\PinboardStatusResponse;
use App\Core\Models\Order\OrderStatus;
use App\Core\Repositories\Variant\ProductVariantRepositoryInterface;
use function App\Core\System\utils\config;
use Exception;
use function App\Core\System\utils\currentDateTime;

class PinboardRepository extends BaseRepository implements PinboardRepositoryInterface
{
    private $pinboardItem;
    private $language;
    private UserRepositoryInterface $userRepository;
    private CustomerRepositoryInterface $customerRepository;
    private PinboardItemRepositoryInterface $pinboardItemRepository;
    private MediaRepositoryInterface $mediaRepository;
    private CommentPhoto $commentPhoto;
    private Comment $comment;
    private VisitShowroom $visitShowroom;
    private ServiceRequest $serviceRequest;
    private EmailRepositoryInterface $emailRepository;
    private Product $product;
    private ShowroomContact $showroomContact;
    private ProductVariantRepositoryInterface $productVariantRepository;
    public function __construct(
        PDO $db,       
        PinboardItem $pinboardItem, 
        Language $language,
        UserRepositoryInterface $userRepository,
        CustomerRepositoryInterface $customerRepository,
        PinboardItemRepositoryInterface $pinboardItemRepository,
        MediaRepositoryInterface $mediaRepository,
        CommentPhoto $commentPhoto,
        Comment $comment,
        VisitShowroom $visitShowroom,
        ServiceRequest $serviceRequest,
        EmailRepositoryInterface $emailRepository,
        Product $product,
        ShowroomContact $showroomContact,
        ProductVariantRepositoryInterface $productVariantRepository
    ){
        parent::__construct($db, 'pinboard', Pinboard::class);
        $this->pinboardItem = $pinboardItem;
        $this->pinboardItem->setDb($db);
        $this->language = $language;
        $this->language->setDb($db);
        $this->userRepository = $userRepository;
        $this->customerRepository = $customerRepository;
        $this->pinboardItemRepository = $pinboardItemRepository;
        $this->mediaRepository = $mediaRepository;
        $this->commentPhoto = $commentPhoto;
        $this->commentPhoto->setDb($db);
        $this->comment = $comment;
        $this->comment->setDb($db);
        $this->visitShowroom = $visitShowroom;
        $this->visitShowroom->setDb($db);
        $this->serviceRequest = $serviceRequest;
        $this->serviceRequest->setDb($db);
        $this->emailRepository = $emailRepository;
        $this->product = $product;
        $this->product->setDb($db);
        $this->showroomContact = $showroomContact;
        $this->showroomContact->setDb($db);
        $this->productVariantRepository = $productVariantRepository;
    }

    /**
     * Get all pinboards
     *
     * @return array
     */
    public function all(): array
    {
        return $this->model->orderBy('created_at', 'DESC')->findAll();
    }

    /**
     * Get all pinboards
     *
     * @return array
     */
    public function findAll(): array
    {
        return $this->model->orderBy('pinboard_id', 'DESC')->findAll();
    }

    /**
     * Get pinboards by company ID
     *
     * @param int $companyId
     * @return array
     */
    public function findByCompanyId(int $companyId): array
    {
        return $this->model->where('company_id', '=', $companyId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get pinboards by user ID
     *
     * @param int $userId
     * @return array
     */
    public function findByUserId(int $userId): array
    {
        return $this->model->where('user_id', '=', $userId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get pinboard by reference number
     *
     * @param string $referenceNumber
     * @return Pinboard|null
     */
    public function findByReferenceNumber(string $referenceNumber): ?Pinboard
    {
        return $this->model->where('reference_number', '=', $referenceNumber)->first();
    }

    public function createPinboard(PinboardData $pinboardData): Pinboard
    {
        $pinboardDataArray = $pinboardData->toArray();
        $pinboardDataArray['uuid'] = $this->generateUuid();
        $pinboard = $this->model->create($pinboardDataArray);
        if ($pinboard) {
           $this->model->clearQuery();
           $this->model
            ->updateWhere(['is_active' => 0], 
                [
                   ['field' => 'pinboard_id', 'operator' => '!=', 'value' => $pinboard->pinboard_id],
                   ['field' => 'customer_id', 'operator' => '=', 'value' => $pinboard->customer_id],
                   ['field' => 'user_id', 'operator' => '=', 'value' => $pinboard->user_id]
                ]
            );
        }
        return $pinboard;
    }

    public function updatePinboard(PinboardData $pinboardData, array $fields = []): Pinboard
    {
        $dataToUpdate = $pinboardData->toArrayForUpdate($fields);
        $dataToUpdate['updated_at'] = $pinboardData->updated_at;
        unset($dataToUpdate['pinboard_id'], $dataToUpdate['created_at']);

        $pinboard = $this->model->find($pinboardData->pinboard_id);
        $pinboard = $pinboard->update($dataToUpdate);

        return $pinboard;
    }

    /**
     * @author sa techonology
     * @created by abdullah
     * @created at 
     * @updated by abdullah
     * @updated at 29-01-2026
     * Save and update pinboard and pinboard items from the frontend
     * 
     * @param array $data
     * @return array
     */
    public function savePinboard(array $data): array
    {
        // validate the pinboard data
        if (empty($data) || !is_array($data)) {
            throw new \InvalidArgumentException('Invalid pinboard payload');
        }

        // validate the pinboard items
        if (
            empty($data['pinboard_items']) ||
            !is_array($data['pinboard_items'])
        ) {
            throw new \InvalidArgumentException('Pinboard items are required');
        }
    
        try {
            $this->db->beginTransaction();
    
            /** ----------------------------------------
             * Guest / User registration
             * ---------------------------------------- */
            if (isset($data['user_id']) && isset($data['customer_id'])) {
                $guestData = [
                    'user_id' => $data['user_id'],
                    'customer_id' => $data['customer_id'],
                ];
            } else {
                $guestData = $this->registerGuestData([
                    'name' => isset($data['name']) ? $data['name'] : (isset($data['companyName']) ? $data['companyName'] : 'anonymous'),
                    'company_name' => isset($data['companyName']) ? $data['companyName'] : (isset($data['name']) ? $data['name'] : 'anonymous'),
                    'email' => isset($data['customer_email']) ? $data['customer_email'] : 'abdullah@sa-technology.com',
                ]);
            }
    
            /** ----------------------------------------
             * Parent: Pinboard
             * ---------------------------------------- */
            $pinboardId = $data['pinboard_id'] ?? null;
    
            $pinboardData = [
                'uuid'               => $this->generateUuid(),
                'company_id'         => 1,
                'reference_number'   => $this->generateReference($guestData['user_id']),
                'job_id'             => 1,
                'pinboard_name'      => $data['job_title'] ?? '',
                'job_title'          => $data['job_title'] ?? '',
                'user_id'            => $guestData['user_id'],
                'customer_id'        => $guestData['customer_id'],
                'pinboard_status_id' => 0,
            ];
    
            if ($pinboardId) {
                $this->model->clearQuery();
    
                $pinboard = $this->model
                    ->where('pinboard_id', '=', $pinboardId)
                    ->where('user_id', '=', $guestData['user_id']) // ownership check
                    ->first();
    
                if (!$pinboard) {
                    throw new \RuntimeException('Pinboard not found or access denied');
                }
    
                $pinboard->update($pinboardData);
            } else {
                $pinboard = $this->model->create($pinboardData);
                $pinboardId = $pinboard->data->pinboard_id ?? null;
    
                if (!$pinboardId) {
                    throw new \RuntimeException('Failed to create pinboard');
                }
            }
    
            /** ----------------------------------------
             * Child: Pinboard Items
             * ---------------------------------------- */
            $createItems = [];
            $updateItems = [];
            $keepItemIds = []; // keep the item id for use later
    
            foreach ($data['pinboard_items'] as $item) {
    
                if (empty($item['model_id'])) {
                    throw new \InvalidArgumentException('Model ID is required for pinboard item');
                }
    
                $payload = [
                    'uuid'         => $this->generateUuid(),
                    'pinboard_id'  => $pinboardId,
                    'model_id'     => $item['model_id'],
                    'model_type'   => $item['model_type'] ?? null,
                    'description'  => $item['description'] ?? null,
                    'comments'     => json_encode($item['comments'] ?? []),
                    'photo'        => $item['photo'] ?? null,
                    'product_url'  => $item['product_url'] ?? null,
                    'quantity'     => (int) ($item['quantity'] ?? 1),
                    'unit_price'   => 100,
                    'total_price'  => 100,
                    'language_id'  => 1,
                    'sort_order'   => (int) ($item['sort_order'] ?? 1),
                ];
    
                if (!empty($item['pinboard_item_id'])) {
                    $payload['pinboard_item_id'] = $item['pinboard_item_id'];
                    $updateItems[] = $payload;
                    $keepItemIds[] = $item['pinboard_item_id']; // keep the item id for use later
                } else {
                    $createItems[] = $payload;
                }
            }
    
            if (!empty($createItems) && count($createItems) > 0) {
                $this->pinboardItem->insert($createItems);
            }
    
            if (!empty($updateItems) && count($updateItems) > 0) {
                $this->pinboardItem->upsert($updateItems, ['pinboard_item_id']);
            }

            // send email to the guest
            $email = isset($data['customer_email']) ? $data['customer_email'] : 'abdullah@sa-technology.com';
            $phone = isset($data['phone']) ? $data['phone'] : '0400000000';
            $pinboardName = isset($data['job_title']) ? $data['job_title'] : 'Pinboard';
            $companyName = isset($data['companyName']) ? $data['companyName'] : 'Krost';
            $countItems = count($createItems);
            try {
                $this->sendEmailToGuest($email, $phone, $pinboardName, $companyName, $countItems, $pinboardId);
            } catch (\Exception $e) {
                // Swallow email sending errors to avoid masking the original exception.
            }
    
            $this->db->commit();
    
            $response = $this->pinboardItemRepository->getPinboard(null, $pinboardId);
    
            $response['user_data'] = array_merge(
                $guestData,
                $data['guest_data'] ?? []
            );
            $this->updateProjectStatus($pinboardId, $guestData['user_id']);
    
            return $response;
    
        } catch (\Exception $e) {
            try {
                if ($this->db && method_exists($this->db, 'inTransaction') && $this->db->inTransaction()) {
                    $this->db->rollBack();
                }
            } catch (\Throwable $rollbackEx) {
                // Swallow rollback errors to avoid masking the original exception.
            }
            throw $e;
        }
    }

    public function createNewProject(array $data): array
    {
        // validate the pinboard data
        if (empty($data) || !is_array($data)) {
            throw new \InvalidArgumentException('Invalid pinboard payload');
        }

        try {
            $this->db->beginTransaction();
            $user_id = isset($data['user_id']) ? $data['user_id'] : null;
            $customer_id = isset($data['customer_id']) ? $data['customer_id'] : null;

            $form_type = isset($data['form_type']) ? $data['form_type'] : null;
            if($form_type === 'web_login'){
                // check is_active is 1 pinboard for this user_id
                $pinboard = $this->model->where('user_id', '=', $user_id)->where('is_active', '=', 1)->first();
                if($pinboard){
                    return [
                        'success' => false,
                        'message' => 'You have an active project. Please complete the project first.'
                    ];
                }
            }

            // if null then get the customer from the user_id
            if ($customer_id === null && $user_id === null) {
                throw new \InvalidArgumentException('User ID and customer ID are required');
            }

            $job_title = isset($data['job_title']) ? $data['job_title'] : null;
            $pinboardData = [
                'uuid'               => $this->generateUuid(),
                'company_id'         => 1,
                'reference_number'   => $this->generateReference($user_id),
                'job_id'             => 1,
                'pinboard_name'      => $job_title,
                'job_title'          => $job_title,
                'user_id'            => $user_id,
                'customer_id'        => $customer_id,
                'pinboard_status_id' => 0,
                'created_at'         => currentDateTime(),
                'updated_at'         => currentDateTime(),
            ];
    
            $this->model->clearQuery();
            $pinboard = $this->model->create($pinboardData);
            $pinboardId = $pinboard->data->pinboard_id ?? null;

            if (!$pinboardId) {
                throw new \RuntimeException('Failed to create pinboard');
            }

            $customer = $this->customerRepository->get($customer_id);
    
            $this->updateProjectStatus($pinboardId, $user_id);
            // send email to the guest
            $email = isset($customer->gmail_Id) ? $customer->gmail_Id : 'abdullah@sa-technology.com';
            $phone = isset($customer->phone_number) ? $customer->phone_number : '0400000000';
            $pinboardName = isset($job_title) ? $job_title : 'Pinboard';
            $companyName = isset($customer->company_name) ? $customer->company_name : 'Krost';
            try {
                $this->sendEmailToGuest($email, $phone, $pinboardName, $companyName, 0, $pinboardId);
            } catch (\Exception $e) {
                // Swallow email sending errors to avoid masking the original exception.
            }

            $this->db->commit();
            $response = $this->pinboardItemRepository->getPinboard($user_id, $pinboardId);
            return $response;
    
        } catch (\Exception $e) {
            try {
                if ($this->db && method_exists($this->db, 'inTransaction') && $this->db->inTransaction()) {
                    $this->db->rollBack();
                }
            } catch (\Throwable $rollbackEx) {
                // Swallow rollback errors to avoid masking the original exception.
            }
            throw $e;
        }
    }

    private function sendEmailToGuest(string $email, $phone, $pinbaordName, $companyName, $countItems, $pinboardId): void
    {
        $context = [
            'subject' => 'Pinboard submission received',
            'team_name' => 'Krost Team',
            'client_name' => $email,
            'client_email' => $email,
            'company' => $companyName,
            'phone' => $phone,
            'pinboard_name' => $pinbaordName,
            'items_count' => $countItems,
            'board_url' => 'https://krost.com.au/pinboard/' . $pinboardId,
            'board_link_label' => 'URL to see the board',
            'pinboard_phone' => $phone,
            'client_notes' => 'No notes provided',
            'project_name'    => $pinbaordName,
            'submission_date' => date('d F Y'),
        ];

        // send email to the admin
        $this->emailRepository->sendEmail(
            'sales@krost.com.au',
            'PINBOARD SUBMISSION - ' . $pinbaordName . ' from ' . $email,
            'PINBOARD SUBMISSION - ' . $pinbaordName . ' from ' . $email,
            $context,
            ROOT_DIR . '/src/themes/landing/src/emailtemplate',
            'pinboard-submission-admin.html.twig'
        );

        // send email to the client
        $this->emailRepository->sendEmail(
            $email,
            'We’ve received your project! – ' . $pinbaordName,
            'We’ve received your project! – ' . $pinbaordName,
            $context,
            ROOT_DIR . '/src/themes/landing/src/emailtemplate',
            'pinboard-submission-client.html.twig'
        );
       
    }

    private function updateProjectStatus(int $pinboardId, int $userId): array
    {
        // Deactivate all pinboards for this customer
        $deactivateStmt = $this->db->prepare("UPDATE `pinboard` SET `is_active` = 0 WHERE `user_id` = :user_id");
        $deactivateStmt->execute([':user_id' => $userId]);

        // Activate only the selected pinboard
        $activateStmt = $this->db->prepare("UPDATE `pinboard` SET `is_active` = 1 WHERE `pinboard_id` = :pinboard_id AND `user_id` = :user_id");
        $activateStmt->execute([':pinboard_id' => $pinboardId, ':user_id' => $userId]);

        return [
            'success' => true,
            'message' => 'Project status updated successfully'
        ];
    }

    private function generateReference(int $userId): string
    {
        return sprintf(
            'REF-%d-%s',
            $userId,
            bin2hex(random_bytes(4))
        );
    }


    private function registerGuestData(array $data): array
    {
        // CHECK IF USER EXISTS
        $user = $this->userRepository->findByEmail($data['email']);
        if (!$user) {
            $userData = [
                'user_group_id' => 1,
                'username' => str_replace(' ', '-', strtolower(trim($data['name'] ?? ''))), 
                'password' => '123456', 
                'email' => $data['email'], 
                'phone_number' => $data['phone_number'] ?? '',
                'otp_code' => str_pad(strval(random_int(100000, 999999)), 6, '0', STR_PAD_LEFT),
                'otp_created_at' => date('Y-m-d H:i:s'),
                'otp_expiry_time' => date('Y-m-d H:i:s', strtotime('+10 minutes'))
            ];
            // CREATE THE USER
            $user = $this->userRepository->create($userData);
        }

        // CHECK IF CUSTOMER EXISTS
        $customer = $this->customerRepository->findByUserId($user->user_id);
        if (empty($customer)) {
            $customerData = ['user_id' => $user->user_id, 
            'company_id' => 1,
            'organisation_id' => 1, 
            'org_code' => 'ORG-' . $user->user_id, 
            'name' => $data['name'], 
            'gmail_Id' => $data['email'],
            'company_name' => $data['companyName']??$data['name']
        ];
        // CREATE THE CUSTOMER
        $customer = $this->customerRepository->createCustomer($customerData);
        
        }

        return [
            'user_id' => $user->data->user_id,
            'customer_id' => $customer['customer_id']
        ];
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

    public function showPinboard(int $pinboardId, ?int $userId = null, ?int $customerId = null): Pinboard
    {
        $this->model->clearQuery();
        $query = $this->model->where('pinboard_id', '=', $pinboardId);
        if ($userId) {
            $query = $query->where('pinboard.user_id', '=', $userId);
        }
        if ($customerId) {
            $query = $query->where('pinboard.customer_id', '=', $customerId);
        }
        $pinboard = $query
            ->join('customer', 'customer.customer_id', '=', 'pinboard.customer_id')
            ->join('user', 'user.user_id', '=', 'pinboard.user_id')
            ->with(['pinboard_items' => function ($query) {
                return $query->with(['model'])->orderBy('sort_order', 'ASC');
            }])
            ->select(['pinboard.*', 'customer.name as customer_name', 'customer.phone as customer_phone', 'user.email as customer_email'])
            // ->where('pinboard.is_active', '=', 1)
            ->first();

        if ($pinboard) {
            $this->attachPinboardStatusToModel($pinboard);
        }

        return $pinboard;
    }
    public function getCustomerPinboard(int $customerId): ?Pinboard
    {
        $query = $this->model->where('customer_id', '=', $customerId);
        $pinboard = $query->join('customer', 'customer.customer_id', '=', 'pinboard.customer_id')
                ->with(['pinboard_items' => function ($query) {
                    return $query->with(['model'])->orderBy('sort_order', 'ASC');
                }])
                ->select(['pinboard.*', 'customer.gmail_Id as customer_email'])
                ->where('pinboard.is_active', '=', 1)
                ->first();
        return $pinboard;
    }
    public function getUserPinboard(int $userId): ?Pinboard
    {
        $query = $this->model->where('user_id', '=', $userId);
        $pinboard = $query->join('customer', 'customer.customer_id', '=', 'pinboard.customer_id')
                ->with(['pinboard_items' => function ($query) {
                    return $query->with(['model'])->orderBy('sort_order', 'ASC');
                }])
                ->select(['pinboard.*', 'customer.gmail_Id as customer_email'])
                ->where('pinboard.is_active', '=', 1)
                ->first();
        return $pinboard;
    }

    public function insertPinboards(array $data): bool
    {
        $this->db->beginTransaction();
        $this->model->insert($data['pinboards']);
        $this->pinboardItem->insert($data['pinboardItems']);
        $this->db->commit();
        return true;
    }



    public function getPinboardDataForComponent(int $pinboardId): array
    {
        $pinboard = $this->model->where('pinboard_id', '=', $pinboardId)->first();
        $pinboardItems = $this->pinboardItem->where('pinboard_id', '=', $pinboardId)->findAll();

        if (!$pinboard) {
            throw new \Exception("Pinboard not found with ID: {$pinboardId}");
        }

        $results =  $pinboard->data;
        $results->pinboardItems = $pinboardItems;

        return (array) $results;
        // Process each pinboard item
        foreach ($pinboardItems as $item) {
            // Determine item type based on available IDs
            $itemType = 'Product';
            $relatedData = null;

            if ($item->product_id) {
                $itemType = 'Product';
                // Could fetch product details from product table
                // $relatedData = $this->getProductDetails($item->product_id);
            } elseif ($item->project_id) {
                $itemType = 'Project';
                // Could fetch project details from project table
                // $relatedData = $this->getProjectDetails($item->project_id);
            } elseif ($item->media_id) {
                $itemType = 'Media';
                // Could fetch media details from media table
                // $relatedData = $this->getMediaDetails($item->media_id);
            } elseif ($item->comment_id) {
                $itemType = 'Comment';
                // Could fetch comment details from comment table
                // $relatedData = $this->getCommentDetails($item->comment_id);
            } elseif ($item->post_id) {
                $itemType = 'Post';
                // Could fetch post details from post table
                // $relatedData = $this->getPostDetails($item->post_id);
            }

            // Get item name/description based on type
            $itemName = $this->getItemNameByType($itemType, $item, $relatedData);

            // Get item image based on type
            $itemImage = $this->getItemImageByType($itemType, $item, $relatedData);

            // Create options array (this could be enhanced to pull from related tables)
            $options = $this->getItemOptionsByType($itemType, $item, $relatedData);

            // Format price information
            $unitPrice = $item->unit_price ? '$' . number_format($item->unit_price, 2) : 'N/A';
            $totalPrice = $item->total_price ? '$' . number_format($item->total_price, 2) : 'N/A';

            $itemData = [
                'image' => $itemImage,
                'type' => $itemType,
                'name' => $itemName,
                'options' => $options,
                'quote' => $pinboard->pinboard_description ?? 'No description available',
                'comment_placeholder' => 'Add A Comment',
                'white_btn' => $this->getWhiteButtonText($itemType),
                'black_btn' => $this->getBlackButtonText($itemType),
                'unit_price' => $unitPrice,
                'total_price' => $totalPrice,
                'quantity' => $item->quantity ?? 0,
                'item_id' => $item->pinboard_item_id,
                'product_id' => $item->product_id,
                'project_id' => $item->project_id,
                'media_id' => $item->media_id,
                'comment_id' => $item->comment_id,
                'post_id' => $item->post_id ?? null,
                'related_data' => $relatedData
            ];

            $results['items'][] = $itemData;
        }

        // Add pinboard summary information
        $results['pinboard_summary'] = [
            'title' => $pinboard->job_title ?? 'Pinboard #' . $pinboard->reference_number,
            'reference_number' => $pinboard->reference_number ?? '#' . $pinboard->pinboard_id,
            'description' => $pinboard->pinboard_description ?? 'No description available',
            'organisation' => $pinboard->organisation_name ?? 'N/A',
            'created_date' => $pinboard->created_at ? date('F jS, Y', strtotime($pinboard->created_at)) : 'N/A',
            'expiry_date' => $pinboard->expiry_date ? date('F jS, Y', strtotime($pinboard->expiry_date)) : 'N/A',
            'status' => $pinboard->pinboard_status_id ?? 0,
            'total_items' => count($pinboardItems),
            'total_value' => '$' . number_format($pinboard->total ?? 0, 2)
        ];

        // Add billing and shipping information
        $results['billing_info'] = [
            'bill_to' => $pinboard->bill_to ?? 'N/A',
            'bill_address' => $pinboard->bill_address ?? 'N/A',
            'bill_suburb' => $pinboard->bill_suburb ?? 'N/A',
            'bill_state' => $pinboard->bill_state ?? 'N/A',
            'bill_country' => $pinboard->bill_country ?? 'N/A',
            'bill_instructions' => $pinboard->bill_instructions ?? 'N/A'
        ];

        $results['shipping_info'] = [
            'ship_to' => $pinboard->ship_to ?? 'N/A',
            'ship_address' => $pinboard->ship_address ?? 'N/A',
            'ship_suburb' => $pinboard->ship_suburb ?? 'N/A',
            'ship_state' => $pinboard->ship_state ?? 'N/A',
            'ship_country' => $pinboard->ship_country ?? 'N/A',
            'ship_instructions' => $pinboard->ship_instructions ?? 'N/A'
        ];

        // Add team information (could be enhanced to pull from users table)
        $results['team_info'] = [
            'account_manager_id' => $pinboard->account_manager_id,
            'project_manager_id' => $pinboard->project_manager_id,
            'account_manager_name' => 'Account Manager', // Could be pulled from users table
            'project_manager_name' => 'Project Manager'  // Could be pulled from users table
        ];

        return $results;
    }

    /**
     * Get item name based on type
     */
    private function getItemNameByType(string $itemType, $item, $relatedData = null): string
    {
        switch ($itemType) {
            case 'Product':
                return $item->description ?? 'Product Item';
            case 'Project':
                return $item->description ?? 'Project Item';
            case 'Media':
                return $item->description ?? 'Media Item';
            case 'Comment':
                return $item->description ?? 'Comment';
            case 'Post':
                return $item->description ?? 'Blog Post';
            default:
                return $item->description ?? 'Unnamed Item';
        }
    }

    /**
     * Get item image based on type
     */
    private function getItemImageByType(string $itemType, $item, $relatedData = null): string
    {
        switch ($itemType) {
            case 'Product':
                return $item->photo ?? '/img/dashboard-pinboard/product-default.png';
            case 'Project':
                return $item->photo ?? '/img/dashboard-pinboard/project-default.png';
            case 'Media':
                return $item->photo ?? '/img/dashboard-pinboard/media-default.png';
            case 'Comment':
                return '/img/dashboard-pinboard/comment-default.png';
            case 'Post':
                return $item->photo ?? '/img/dashboard-pinboard/post-default.png';
            default:
                return $item->photo ?? '/img/dashboard-pinboard/pinboard-img-01.png';
        }
    }

    /**
     * Get item options based on type
     */
    private function getItemOptionsByType(string $itemType, $item, $relatedData = null): array
    {
        switch ($itemType) {
            case 'Product':
                return [
                    ['src' => '/img/product-detail/second circle.png', 'alt' => 'Product Option 1'],
                    ['src' => '/img/product-detail/second circle.png', 'alt' => 'Product Option 2'],
                    ['src' => '/img/product-detail/third circle.png', 'alt' => 'Product Option 3'],
                    ['src' => '/img/product-detail/first circle.png', 'alt' => 'Product Option 4']
                ];
            case 'Project':
                return [
                    ['src' => '/img/project-detail/option-1.png', 'alt' => 'Project View 1'],
                    ['src' => '/img/project-detail/option-2.png', 'alt' => 'Project View 2'],
                    ['src' => '/img/project-detail/option-3.png', 'alt' => 'Project View 3'],
                    ['src' => '/img/project-detail/option-4.png', 'alt' => 'Project View 4']
                ];
            case 'Media':
                return [
                    ['src' => '/img/media-detail/thumbnail-1.png', 'alt' => 'Media Thumbnail 1'],
                    ['src' => '/img/media-detail/thumbnail-2.png', 'alt' => 'Media Thumbnail 2'],
                    ['src' => '/img/media-detail/thumbnail-3.png', 'alt' => 'Media Thumbnail 3'],
                    ['src' => '/img/media-detail/thumbnail-4.png', 'alt' => 'Media Thumbnail 4']
                ];
            case 'Comment':
                return [
                    ['src' => '/img/comment-detail/reply.png', 'alt' => 'Reply'],
                    ['src' => '/img/comment-detail/edit.png', 'alt' => 'Edit'],
                    ['src' => '/img/comment-detail/delete.png', 'alt' => 'Delete'],
                    ['src' => '/img/comment-detail/flag.png', 'alt' => 'Flag']
                ];
            case 'Post':
                return [
                    ['src' => '/img/post-detail/share.png', 'alt' => 'Share'],
                    ['src' => '/img/post-detail/bookmark.png', 'alt' => 'Bookmark'],
                    ['src' => '/img/post-detail/like.png', 'alt' => 'Like'],
                    ['src' => '/img/post-detail/comment.png', 'alt' => 'Comment']
                ];
            default:
                return [
                    ['src' => '/img/product-detail/second circle.png', 'alt' => 'Option 1'],
                    ['src' => '/img/product-detail/second circle.png', 'alt' => 'Option 2'],
                    ['src' => '/img/product-detail/third circle.png', 'alt' => 'Option 3'],
                    ['src' => '/img/product-detail/first circle.png', 'alt' => 'Option 4']
                ];
        }
    }

    /**
     * Get white button text based on item type
     */
    private function getWhiteButtonText(string $itemType): string
    {
        switch ($itemType) {
            case 'Product':
                return 'View Product';
            case 'Project':
                return 'View Project';
            case 'Media':
                return 'View Media';
            case 'Comment':
                return 'Reply';
            case 'Post':
                return 'Read More';
            default:
                return 'View Details';
        }
    }

    /**
     * Get black button text based on item type
     */
    private function getBlackButtonText(string $itemType): string
    {
        switch ($itemType) {
            case 'Product':
                return 'Add to Quote';
            case 'Project':
                return 'View Details';
            case 'Media':
                return 'Download';
            case 'Comment':
                return 'Edit';
            case 'Post':
                return 'Share';
            default:
                return 'Accept Quote';
        }
    }

    public function getVirtualPinboardComponentData(array $param = [])
    {
        $query = $this->pinboardItem
            ->join('pinboard', 'pinboard.pinboard_id', '=', 'pinboard_item.pinboard_id')
            ->leftJoin('product', 'product.product_id', '=', 'pinboard_item.product_id')
            ->leftJoin('product_content', 'product_content.product_id', '=', 'product.product_id')
            ->leftJoin('project', 'project.project_id', '=', 'pinboard_item.project_id')
            ->select([
                'pinboard_item.pinboard_item_id',
                'pinboard_item.description',
                'pinboard_item.photo',
                'pinboard_item.quantity',
                'pinboard_item.unit_price',
                'pinboard_item.total_price',
                'pinboard_item.product_id',
                'pinboard_item.project_id',
                'product_content.name as product_name',
                'product.description as product_description',
                'project.name as project_name',
                'project.description as project_description',
                'project.image as project_image'
            ]);

        // Filter by language if specified
        if (isset($param['language_id'])) {
            $query->where('pinboard_item.language_id', '=', $param['language_id']);
        } else {
            $query->where('pinboard_item.language_id', '=', 1); // Default language
        }

        // Filter by pinboard if specified
        if (isset($param['pinboard_id'])) {
            $query->where('pinboard_item.pinboard_id', '=', $param['pinboard_id']);
        }

        // Limit results if specified
        if (isset($param['item_count']) && $param['item_count'] > 0) {
            $query->limit($param['item_count']);
        }

        $query->orderBy('pinboard_item.sort_order', 'ASC')
            ->orderBy('pinboard_item.pinboard_item_id', 'ASC');

        $results = $query->findAll();

        $items = [];
        foreach ($results as $index => $result) {
            // Determine item type and name
            $itemType = 'Product';
            $itemName = '';
            $itemDescription = '';

            if ($result['product_id']) {
                $itemType = 'Product - ' . ($index + 1);
                $itemName = $result['product_name'] ?? $result['product_description'] ?? 'Product Item';
            } elseif ($result['project_id']) {
                $itemType = 'Project - ' . ($index + 1);
                $itemName = $result['project_name'] ?? $result['project_description'] ?? 'Project Item';
                $itemDescription = $result['project_description'] ?? 'Lorem Ipsum Dolor Sit Amet, Consectetur Adipiscing Elit. Sed Do Eiusmod Tempor Incididunt Ut Labore Et Dolore Magna Aliqua.';
            }

            // Get image
            $image = $result['photo'] ?? '/img/pinboard/pinboard img ' . ($index + 1) . '.png';

            // Generate options based on item type
            $options = [];
            if ($result['product_id']) {
                $options = [
                    ['src' => '/img/product-detail/second circle.png', 'alt' => 'Option 1'],
                    ['src' => '/img/product-detail/second circle.png', 'alt' => 'Option 2'],
                    ['src' => '/img/product-detail/third circle.png', 'alt' => 'Option 3'],
                    ['src' => '/img/product-detail/first circle.png', 'alt' => 'Option 4']
                ];
            }

            $item = [
                'image' => $image,
                'type' => $itemType,
                'name' => $itemName,
                'options' => $options,
                'comment_placeholder' => 'Add A Comment'
            ];

            // Add description for projects
            if ($result['project_id'] && $itemDescription) {
                $item['description'] = $itemDescription;
            }

            $items[] = $item;
        }

        // If no results found, return default structure
        if (empty($items)) {
            $items = [
                [
                    'image' => '/img/pinboard/pinboard img 1.png',
                    'type' => 'Product - 1',
                    'name' => 'Miro Task Chair - Black Fabric Seat / Black Mesh Black',
                    'options' => [
                        ['src' => '/img/product-detail/second circle.png', 'alt' => 'Option 1'],
                        ['src' => '/img/product-detail/second circle.png', 'alt' => 'Option 2'],
                        ['src' => '/img/product-detail/third circle.png', 'alt' => 'Option 3'],
                        ['src' => '/img/product-detail/first circle.png', 'alt' => 'Option 4']
                    ],
                    'comment_placeholder' => 'Add A Comment'
                ],
                [
                    'image' => '/img/pinboard/pinboard img 2.png',
                    'type' => 'Project - 1',
                    'name' => 'Fiorelli Packing',
                    'description' => 'Lorem Ipsum Dolor Sit Amet, Consectetur Adipiscing Elit. Sed Do Eiusmod Tempor Incididunt Ut Labore Et Dolore Magna Aliqua.',
                    'options' => [],
                    'comment_placeholder' => 'Add A Comment'
                ],
                [
                    'image' => '/img/pinboard/pinboard img 3.png',
                    'type' => 'Product - 2',
                    'name' => 'Arc Screen - Black Fabric Seat / Black Mesh Black',
                    'options' => [
                        ['src' => '/img/product-detail/second circle.png', 'alt' => 'Option 1'],
                        ['src' => '/img/product-detail/second circle.png', 'alt' => 'Option 2'],
                        ['src' => '/img/product-detail/third circle.png', 'alt' => 'Option 3'],
                        ['src' => '/img/product-detail/first circle.png', 'alt' => 'Option 4']
                    ],
                    'comment_placeholder' => 'Add A Comment'
                ]
            ];
        }

        return [
            'items' => $items
        ];
    }

    // import pinboards
    public function importPinboards(string $csv_file): array
    {
        $reader = Reader::createFromPath($csv_file, 'r');
        $reader->setHeaderOffset(0);
        $headers = $reader->getHeader();
        if (empty($headers)) {
            throw new Exception("CSV file has no headers");
        }
        $defaultFields = $this->getDefaultFields($headers);
        $requiredFields = $this->getRequiredFields();
        $records = $reader->getRecords();

        $validData = [
            'pinboard' => [],
            'pinboard_item' => [],
        ];
        $invalid = [];
        $updated = [];
        $processed = [];
        // existing data maps for mapping // default values
        $productMap = [];
        $projectMap = [];
        $mediaMap = [];
        $companyMap = [];
        $jobMap = [];
        $organisationMap = [];
        // end existing data maps for mapping // default values
        $languageMap = $this->language->select(['language_id', 'code'])->findAll(false);
        $languageMap = array_column($languageMap, 'language_id', 'code');
        // project name
        $importingProjectCodes = array_column(iterator_to_array($records), 'project_name');
        // media name
        $importingMediaCodes = array_column(iterator_to_array($records), 'media_name');
        // product map
        $importingProductCodes = array_column(iterator_to_array($records), 'product_code');
        if (count($importingProductCodes) > 0) {
            $productMap = $this->product->select(['product_id', 'product_code'])->whereIn('product_code', $importingProductCodes)->limit(0)->findAll(false);
            $productMap = array_column($productMap, 'product_id', 'product_code');
        }
        if (count($importingProjectCodes) > 0) {
            $projectMap = $this->project->select(['project_id', 'project_code'])->whereIn('project_code', $importingProjectCodes)->limit(0)->findAll(false);
            $projectMap = array_column($projectMap, 'project_id', 'project_code');
        }
        if (count($importingMediaCodes) > 0) {
            $mediaMap = $this->media->select(['media_id', 'name'])->whereIn('name', $importingMediaCodes)->limit(0)->findAll(false);
            $mediaMap = array_column($mediaMap, 'media_id', 'name');
        }
        $existingDataMaps = [
            'productMap' => $productMap,
            'projectMap' => $projectMap,
            'mediaMap' => $mediaMap,
            'languageMap' => $languageMap,
            'companyMap' => $companyMap,
            'jobMap' => $jobMap,
            'organisationMap' => $organisationMap
        ];

        foreach ($records as $offset => $record) {
            try {
                $record = $this->prepareRecord($record, $defaultFields);
                $validator = new PinboardDataValidation($record, $requiredFields, array_keys($defaultFields), $existingDataMaps);
                $validated = $validator->validate();

                if ($validated === false) {
                    $invalid[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'errors' => $validator->getErrors()
                    ];
                    continue;
                }

                $unique = $validator->getUniqueIdentifier();

                if (in_array($unique, $processed, true)) {
                    $updated[] = [
                        'row' => $offset + 2,
                        'data' => $record,
                        'identifier' => $unique
                    ];
                    continue;
                }
                $validData['pinboard'][] = (array) $validated->pinboard;
                $validData['pinboard_item'][] = (array) $validated->pinboard_item;

                $processed[] = $unique;
            } catch (Exception $e) {
                // Capture any runtime exception per record
                $invalid[] = [
                    'row' => $offset + 2,
                    'data' => $record,
                    'errors' => ['processing_error' => $e->getMessage()]
                ];
                continue;
            }
        }

        try {
            $this->db->beginTransaction();
            if (count($validData['pinboard']) > 0) {
                $this->model->upsert($validData['pinboard'], ['uuid']);

                // fetch all uuid from pinboard table
                $allUuids = array_column($validData['pinboard'], 'uuid');
                $pinboardData = $this->model->select(['pinboard_id', 'uuid'])->whereIn('uuid', $allUuids)->limit(0)->findAll();
                $pinboardDataMap = array_column($pinboardData, 'pinboard_id', 'uuid');
            }
            if (count($validData['pinboard_item']) > 0) {
                // all uuid 
                foreach ($validData['pinboard_item'] as &$item) {   // get pinboard id from uuid
                    $uuid = explode('-', $item['uuid'])[0];
                    $item['pinboard_id'] = $pinboardDataMap[$uuid];
                }
                $this->pinboardItem->upsert($validData['pinboard_item'], ['uuid']);
            }
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new \Exception("Failed to insert/update pinboards: " . $e->getMessage());
        }

        return [
            'success' => true,
            'total_records' => iterator_count($records),
            'valid_records' => count($validData['pinboard_item']),
            'valid_data' => $validData['pinboard_item'],
            'invalid_records' => count($invalid),
            'updated_records' => count($updated),
            'updated_data' => $updated,
            'duplicated_records' => count($updated),
            'duplicated_data' => $updated,
            'pinboards' => [
                'inserted_count' => count($validData['pinboard']),
                'valid_data' => $validData['pinboard']
            ],
            'pinboard_items' => [
                'inserted_count' => count($validData['pinboard_item']),
                'valid_data' => $validData['pinboard_item']
            ],
            'invalid_data' => $invalid,

            'summary' => [
                'success_rate' => iterator_count($records) > 0
                    ? round((count($validData['pinboard_item']) / iterator_count($records)) * 100, 2) . '%'
                    : '0%',
                'pinboard_item_processed' => count($validData['pinboard_item']),
                'pinboard_item_records_created' => $validData['pinboard_item'],
                'errors' => count($invalid),
            ],
            'language_map' => array_flip($languageMap)
        ];
    }

    private function getDefaultFields(array $headers): array
    {
        $defaultFields = [];
        // Initialize all CSV headers as null by default
        foreach ($headers as $header) {
            $defaultFields[$header] = null;
        }

        // Set default values for required fields
        $defaultFields['language_code'] = 'en_US';
        $defaultFields['sort_order'] = 1;
        $defaultFields['total_bp_ex_gst'] = 0.00;
        $defaultFields['total_bp_inc_gst'] = 0.00;
        $defaultFields['total_sp_ex_gst'] = 0.00;
        $defaultFields['total_sp_inc_gst'] = 0.00;
        $defaultFields['order_discount'] = 0.00;
        $defaultFields['discount_rate'] = 0.00;
        $defaultFields['discount_amount'] = 0.00;
        $defaultFields['grand_total_sp_ex_gst'] = 0.00;
        $defaultFields['grand_total_sp_inc_gst'] = 0.00;

        return $defaultFields;
    }

    private function getRequiredFields(): array
    {

        $pinboard = [
            'reference_number', // reference number from pinboard table
            'company_id',
            'job_id', // foreign key from job table
            'deposit_percentage',
            'total_bp_ex_gst',
            'total_bp_inc_gst',
            'total_sp_ex_gst',
            'total_sp_inc_gst',
            'order_discount',
            'discount_rate',
            'discount_amount',
            'grand_total_sp_ex_gst',
            'grand_total_sp_inc_gst',
            'pinboard_status_id',
            'total'
        ];
        $pinboardItem = [
            'language_id',
            'uuid', // uuid from pinboard table
            'pinboard_id', // foreign key from pinboard table
            'comment_id',
            'description',
            'quantity',
            'unit_price',
            'total_price',
        ];

        return array_merge($pinboard, $pinboardItem);
    }

    private function prepareRecord(array $record, array $defaultFields): array
    {
        return isset($record['variant_id']) && $record['variant_id'] ? $record : array_merge($defaultFields, $record);
    }

    /**
     * Get the pinboard widget data for dashboard widget
     * last 15 data from pinboards with customer 
     *
     * @param int $limit
     * @return array
     */
    public function getPinboardWidget(int $limit = 15): array
    {
        // SQL (prepared once, reused) // parameters: limit.
        $pinboards = $this->model
        ->join('user', 'user.user_id', '=', 'pinboard.user_id')
        ->join('customer', 'customer.customer_id', '=', 'pinboard.customer_id')
        ->select([
            'pinboard.pinboard_id', 
            'pinboard.reference_number',
            'DATE_FORMAT(pinboard.created_at, "%M %d, %Y") as date',
            'pinboard.grand_total_sp_inc_gst as amount', 
            'pinboard.user_id',
            'user.avatar',
            'customer.customer_id',
            'customer.name as customer_name',
            '(CASE
                WHEN pinboard.pinboard_status_id = 1 THEN "pending"
                WHEN pinboard.pinboard_status_id = 2 THEN "processing"
                WHEN pinboard.pinboard_status_id = 3 THEN "processed"
                WHEN pinboard.pinboard_status_id = 4 THEN "complete"
                WHEN pinboard.pinboard_status_id = 5 THEN "canceled"
                WHEN pinboard.pinboard_status_id = 6 THEN "archived"
                WHEN pinboard.pinboard_status_id = 7 THEN "requires_action"
                ELSE "no status"
            END) as status'
        ])
        ->limit($limit)
        ->orderBy('created_at', 'DESC')
        ->findAll();

        // Convert pinboard_item JSON string into array for each pinboard
        // foreach ($pinboards as &$pinboard) {
        //     if (isset($pinboard['pinboard_item']) && is_string($pinboard['pinboard_item'])) {
        //         $decoded = json_decode($pinboard['pinboard_item'], true);
        //         if (json_last_error() === JSON_ERROR_NONE) {
        //             $pinboard['pinboard_item'] = $decoded;
        //         }
        //     }
        // }
        // unset($pinboard);
        return $pinboards;
    }

    public function getPinboardItems(int $pinboardId)
    {
        $query = $this->pinboardItem;

        // Base select
        $select = ['pi.*'];

        // Dynamic joins map
        $map = PinboardJoinMap::MAP;

        foreach ($map as $type => $cfg) {

            // LEFT JOIN main table
            $query->join(
                "{$cfg['table']} as {$cfg['alias']}",
                "{$cfg['alias']}.{$cfg['pk']}",
                '=',
                'pi.model_id',
                'LEFT'
            );

            $query->where('pi.model_type', '=', $type);

            // Add select columns for this join
            if (!empty($cfg['select'])) {
                foreach ($cfg['select'] as $col) {
                    $select[] = $col;
                }
            }

            // Extra joins (like product_content)
            if (!empty($cfg['joins'])) {
                foreach ($cfg['joins'] as $extra) {
                    $query->join(
                        "{$extra['table']} as {$extra['alias']}",
                        "{$extra['on'][0]}",
                        '=',
                        "{$extra['on'][1]}",
                        'LEFT'
                    );

                    if (!empty($extra['select'])) {
                        foreach ($extra['select'] as $col) {
                            $select[] = $col;
                        }
                    }
                }
            }
        }

        // Apply select
        $query->select($select);

        // Filter by pinboard_id
        $query->where('pi.pinboard_id', '=', $pinboardId);

        // Order
        $query->orderBy('pi.sort_order', 'ASC');

        return $query->findAll(false);
    }

    public function getPinboardListComponentData(array $param = []): array
    {
        $customerId = isset($param['customer_id']) && $param['customer_id'] ? $param['customer_id'] : null;
        $userId = isset($param['user_id']) && $param['user_id'] ? $param['user_id'] : null;
      
        $query = $this->model;
        $query->join('user', 'user.user_id', '=', 'pinboard.user_id');
        $query->join('customer', 'customer.customer_id', '=', 'pinboard.customer_id');
        $query->join('order_status', 'order_status.order_status_id', '=', 'pinboard.pinboard_status_id');
        $query->select([
            'pinboard.pinboard_id',
            'pinboard.uuid',
            'pinboard.user_id',
            'pinboard.customer_id',
            'pinboard.pinboard_status_id',
            'pinboard.is_active',
            'pinboard.is_visible',
            'pinboard.pinboard_name',
            "DATE_FORMAT(pinboard.created_at, '%e %b %Y') as created_at",
            "DATE_FORMAT(pinboard.updated_at, '%e %b %Y') as updated_at",
            'customer.name as customer_name',
            'customer.gmail_Id as customer_email',
            'pinboard.grand_total_sp_inc_gst as total_price',
            'order_status.name as pinboard_status_name',
            'order_status.order_status_id as pinboard_status_id',
            '(SELECT COUNT(*) FROM pinboard_item WHERE pinboard_item.pinboard_id = pinboard.pinboard_id) as item_count',
        ]);
        if($userId){
            $query->where('pinboard.user_id', '=', $userId);
        }else{
            $query->where('pinboard.customer_id', '=', $customerId);
        }
        $query->orderBy('pinboard.is_visible', 'DESC');
        $query->orderBy('pinboard.is_active', 'DESC');
        // $query->orderBy('pinboard.created_at', 'DESC');
        $results = $query->findAll(false);
        $result = [
            'pinboards' => $results,
        ];
        return $result;
    }

    public function automaticSendEmailClient(): array
    {      
        $query = $this->model;
        $query->join('user', 'user.user_id', '=', 'pinboard.user_id');
        $query->join('customer', 'customer.customer_id', '=', 'pinboard.customer_id');
        $query->join('order_status', 'order_status.order_status_id', '=', 'pinboard.pinboard_status_id');

        $query->select([
            'pinboard.pinboard_id',
            'pinboard.uuid',
            'pinboard.user_id',
            'pinboard.customer_id',
            'pinboard.pinboard_status_id',
            'pinboard.is_active',
            'pinboard.is_visible',
            'pinboard.pinboard_name',
            "DATE_FORMAT(pinboard.created_at, '%e %b %Y') as created_at",
            "DATE_FORMAT(pinboard.updated_at, '%e %b %Y') as updated_at",
            'customer.name as customer_name',
            'customer.gmail_Id as customer_email',
            'order_status.name as pinboard_status_name',
            'order_status.order_status_id as pinboard_status_id',
            '(SELECT COUNT(*) FROM pinboard_item WHERE pinboard_item.pinboard_id = pinboard.pinboard_id) as item_count',
        ]);

        $query->where('pinboard.pinboard_status_id', '=', ''); // null is draft
        $query->orderBy('pinboard.is_visible', 'DESC');
        $query->orderBy('pinboard.is_active', 'DESC');
        $results = $query->findAll(false);
        
        $emailArray = array_values(array_unique(array_filter(array_column($results, 'customer_email'))));

        $context = [
            'subject' => 'Pinboard submission received',
            'team_name' => 'Krost Team',
            'client_name' => '',
            'client_email' => '',
            'company' => '',
            'phone' => '',
            'pinboard_name' => '',
            'items_count' => '',
            'board_url' => 'https://krost.com.au/pinboard/' . '',
            'board_link_label' => 'URL to see the board',
            'pinboard_phone' => '',
            'client_notes' => 'No notes provided',
            'project_name'    => '',
            'submission_date' => date('d F Y'),
        ];

        // send email to the client
        $this->emailRepository->sendEmail(
            $emailArray,
            'We’ve received your project!',
            'We’ve received your project!',
            $context,
            ROOT_DIR . '/src/themes/landing/src/emailtemplate',
            'pinboard-submission-client.html.twig',
            'sales@krost.com.au'
        );

        return $emailArray;
    }

    public function allPinboards(): array
    {
        $query = $this->model;
        $query->join('user', 'user.user_id', '=', 'pinboard.user_id');
        $query->join('customer', 'customer.customer_id', '=', 'pinboard.customer_id');
        $query->join('order_status', 'order_status.order_status_id', '=', 'pinboard.pinboard_status_id');
        $query->select([
            'pinboard.*',
            '(SELECT COUNT(*) FROM pinboard_item WHERE pinboard_item.pinboard_id = pinboard.pinboard_id) as item_count',
            'order_status.name as pinboard_status_name',
            'customer.name as customer_name',
            'customer.phone as customer_phone',
            'user.email as customer_email',
        ]);
        $query->orderBy('pinboard.is_active', 'DESC');
        $query->orderBy('pinboard.created_at', 'DESC');
        $results = $query->findAll(false);

        return $this->enrichPinboardsWithStatus($results ?? []);
    }

    public function attachPinboardStatusToResponse(PinboardResponse $pinboard): PinboardResponse
    {
        if ($pinboard->pinboard_status instanceof PinboardStatusResponse) {
            return $pinboard;
        }

        $pinboard->pinboard_status = $this->getPinboardOrderStatus(
            (int) ($pinboard->pinboard_status_id ?? 0)
        );

        return $pinboard;
    }

    private function attachPinboardStatusToModel(Pinboard $pinboard): Pinboard
    {
        $statusId = (int) ($pinboard->pinboard_status_id ?? $pinboard->data->pinboard_status_id ?? 0);
        $pinboard->data->pinboard_status = $this->getPinboardOrderStatus($statusId);

        return $pinboard;
    }

    public function savePinboardComment(array $data, array $files): array
    {
        try{
            $this->db->beginTransaction();
            $pinboardId = isset($data['pinboard_id']) ? $data['pinboard_id'] : 7; // static pinboard id for testing
            // insert comment table
            $commentData = [
                'uuid' => $this->generateUuid(),
                'model_id' => $pinboardId,
                'post_id' => 1,
                'model_type' => 'pinboard',
                'user_id' => $data['user_id'],
                'author' => $data['name'] ?? '',
                'content' => $data['content'] ?? '',
                'status' => 1,
            ];
            $comment = $this->comment->create($commentData);
            $commentId = $comment->comment_id;

            $imageUrl = [];
            $commentPhotoData = [];
            if($files && count($files) > 0){       
                foreach ($files as $item) {
                    $img = [
                        'pinboard_id' => $pinboardId,
                        'name' => $item['name'] ?? '',
                        'size' => $item['size'] ?? '',
                        'type' => $item['type'] ?? '',
                        'image' => $item['image'] ?? '',
                        'status' => isset($item['status']) && is_array($item['status'])
                            ? $item['status']
                            : ['name' => 'Uploaded', 'severity' => 'success'],
                        'media_id' => $item['media_id'] ?? null,
                        'objectURL' => ($item['objectURL'] ?? ''),
                        'created_at' => currentDateTime(),
                        'product_id' => null,
                        'description' => $item['description'] ?? '',
                        'post_image_id' => null,
                        'product_image_id' => null,
                        'comment_photo_id' => null,
                    ];
                    $imageUrl[] = $item['objectURL'] ?? '';
                    $commentPhotoData[] = [
                        'image' => json_encode($img),
                        'comment_id' => $commentId,
                        'media_id' => $item['media_id'] ?? null,
                        'created_at'         => currentDateTime(),
                        'updated_at'         => currentDateTime(),
                    ];
                }
                $this->commentPhoto->insert($commentPhotoData);
            }
            $commentPhoto = null;
            if(empty($imageUrl) || count($imageUrl) == 0){
                $commentPhoto = '/media/Pinboard/comment-bubble-placeholder-img.jpg';
            }else{
                $commentPhoto = $imageUrl[0] ?? '/media/Pinboard/comment-bubble-placeholder-img.jpg';
            }

            // insert pinboard_item table
            $pinboardItemData = [
                'pinboard_id' => $pinboardId,
                'description' => $data['content'] ?? '',
                'comments' => json_encode([$data['content'] ?? '']),
                'photo' => $commentPhoto,
                'quantity' => 1,
                'unit_price' => 0,
                'total_price' => 0,
                'language_id' => 1,
                'uuid' => $data['uuid'] ?? $this->generateUuid(),
                'model_id' => $commentId,
                'model_type' => 'comment',
            ];
            $pinboardItem = $this->pinboardItem->create($pinboardItemData);
            $pinboardItemData = (array) $pinboardItem->data;
            $this->model->updateWhere(['updated_at' => currentDateTime()],['pinboard_id' => $pinboardId]);

            $this->db->commit();
           return $pinboardItemData;
        }catch(\Exception $e){
            $this->db->rollBack();
            throw $e;
        }
    }

    private function uploadCommentFiles(array $files): array
    {
        $uploadDir = 'media/Comments/';

        // Set default size
        $size = [
            'width' => 945,
            'height' => 630,
        ];



        $uploadData = [
            'files' => $files,
            'upload_dir' => $uploadDir
        ];
        $folder ='media/Comments/';
        if ($files && count($files) > 0) {
            $result = $this->mediaRepository->upload($uploadData, $size, $folder);
            if (!$result) {
                return ['success' => false, 'message' => 'Failed to upload media'];
            }
            return ['success' => true, 'message' => 'Media uploaded successfully', 'data' => $result];
        }
        return ['success' => false, 'message' => 'No files uploaded'];
    }

    // pinboard booking phone call
    public function bookingPhoneCall(array $data): array
    {
        // find pinboard by pinboard_id
        $pinboard = $this->model->where('pinboard_id', '=', $data['pinboard_id'])->first();

        if (!$pinboard) {
            return ['success' => false, 'message' => 'Pinboard not found', 'data' => []];
        }
        // pinboard item count
        $pinboardItems = $this->pinboardItem->where('pinboard_id', '=', $pinboard->pinboard_id)->findAll(false);
        $pinboardItemsCount = count($pinboardItems);
       
        $contactNumbers = json_decode($pinboard->contact_number ?? '[]', true);

        // Normalize phone number: +614... -> +61 4... (Australian international)
        $formatAuInternational = static function (string $phone): string {
            $phone = trim($phone);
            if ($phone === '') {
                return $phone;
            }
            if (preg_match('/^\+61\s*(\d[\d\s]*)$/', $phone, $m)) {
                $national = preg_replace('/\D/', '', $m[1]);
                if ($national !== '') {
                    return '+61 ' . $national;
                }
            }

            return $phone;
        };

        $newPhone = isset($data['phone_number']) ? $formatAuInternational($data['phone_number']) : '';

        $note = isset($data['note']) ? $data['note'] : '';

        // Check duplicate using collection (compare normalized AU numbers)
        $exists = collect($contactNumbers)->contains(function ($item) use ($newPhone, $formatAuInternational) {
            return isset($item['contact_number'])
                && $formatAuInternational($item['contact_number']) === $formatAuInternational($newPhone);
        });

        if (!$exists) {
            $contactNumbers[] = [
                'name' => isset($data['name']) ? $data['name'] : '',
                'contact_number' => $newPhone,
            ];
        }

        $pinboard->update([
            'contact_number' => json_encode($contactNumbers),
            'note' => $note,
            'pinboard_status_id' => 8,
        ]);

        // send email to customer
        $user = $this->userRepository->findByUserId($pinboard->user_id);
        if($user){
            $email = $user->email;
            $name = $user->first_name;
            $phone = $newPhone;
            $pinboardName = $pinboard->pinboard_name;
            $companyName = $pinboard->company_name;
            $adminBaseUrl = config('APP_ADMIN_URL');

            $context = [
                'subject' => 'Request for a phone call',
                'team_name' => 'Team',
                'client_name' => $name,
                'client_email' => $email,
                'company' => $companyName,
                'phone' => $phone,
                'project_name' => $pinboardName,
                'dashboard_project_url' => $adminBaseUrl . '/pinboards/' . $pinboard->pinboard_id . '/overview',
                'board_link_label' => 'URL to see the board',
                'pinboard_phone' => $phone,
                'client_notes' => $note,
                'items_count'     => $pinboardItemsCount,
                'submission_date' => date('d/m/Y'),
                'attachment_link'  => $adminBaseUrl . '/pinboards/' . $pinboard->pinboard_id . '/overview',
                'attachment_label' => 'Link to download File 1',
            ];

            $this->emailRepository->sendEmail(
                'sales@krost.com.au',
                'Krost Website | Call Back Requested – ' . $phone,
                'Krost Website | Call Back Requested – ' . $phone,
                $context,
                ROOT_DIR . '/src/themes/landing/src/emailtemplate',
                'booking-phone-call.html.twig',
                ['sales@krost.com.au']
            );
        }

        return [
            'success' => true,
            'message' => $exists
                ? 'Phone number already exists'
                : 'Contact number added successfully',
            'data' => $contactNumbers
        ];
    }

    public function getPinboardFinalBookingComponent(int|string $pinboardIdentifier, string $type = 'phone_call'): array
    {
        // Allow booking screens to resolve by either pinboard_id or uuid.
        $pinboardQuery = $this->model
        ->with(['pinboard_items'])
        ->join('customer', 'customer.customer_id', '=', 'pinboard.customer_id')
        ->select([
            'pinboard.pinboard_id',
            'pinboard.pinboard_name',
            'pinboard.pinboard_description',
            'pinboard.note',
            'pinboard.contact_number',
            'customer.gmail_Id',
            'customer.name',
            'customer.phone',
            'customer.address',
        ]);

        if (is_int($pinboardIdentifier) || ctype_digit((string) $pinboardIdentifier)) {
            $pinboardQuery->where('pinboard.pinboard_id', '=', (int) $pinboardIdentifier);
        } else {
            $pinboardQuery->where('pinboard.uuid', '=', (string) $pinboardIdentifier);
        }

        $pinboard = $pinboardQuery->first();
        $pinboardItems = [];

        if (!$pinboard) {
            return [];
        }

        $pinboardId = $pinboard->pinboard_id;
        // showroom visit booking
        if ($type == 'showroom_visit' || $type == 'virtual_meeting') {
                $showroomVisit = $this->visitShowroom
                ->join('showroom_contact', 'showroom_contact.showroom_contact_id', '=', 'visit_showroom.showroom_contact_id')
                ->join('showrooms', 'showrooms.showrooms_id', '=', 'visit_showroom.showroom_id')
                ->select([
                    'visit_showroom.visit_showroom_id',
                    'showrooms.title',
                    'showrooms.address',
                    'showrooms.phone as showroom_phone',
                    'showrooms.email',
                    'showrooms.mobile',
                    'showrooms.image',
                    'visit_showroom.pinboard_id',
                    'visit_showroom.customer_id',
                    'visit_showroom.showroom_id',
                    'visit_showroom.tour_type',
                    'date_format(visit_showroom.date, "%W, %e %M %Y") as date',
                    'visit_showroom.time_zone',
                    'visit_showroom.meeting_time',
                    'visit_showroom.note',
                    'showroom_contact.name',
                    'showroom_contact.email',
                    'showroom_contact.phone',
                    'showroom_contact.designation',
                ])
                ->where('pinboard_id', '=', $pinboardId)
                ->orderBy('visit_showroom.created_at', 'DESC')
                ->first();
                
                if ($showroomVisit) {
                    // convert stdClass to array
                    $showroomData = json_decode(json_encode($showroomVisit->data), true);
            
                    // format meeting_time
                    $time = isset($showroomData['meeting_time']) ? $showroomData['meeting_time'] : '';
                    $start = date('g:i a', strtotime($time));
                    $end   = date('g:i a', strtotime($time . ' +60 minutes'));
                    $formatted = $time ? $start . ' - ' . $end : '';

                    $image = isset($showroomData['image']) ? json_decode($showroomData['image'], true) : [];
                    $imageUrl = isset($image[0]) && isset($image[0]['objectURL']) ? $image[0]['objectURL'] : '';
                    $showroomData['image'] = $imageUrl;
                
                    // replace meeting_time
                    $showroomData['meeting_time'] = $start;
                    $showroomData['meeting_time_label'] = $formatted . ' [' . $showroomData['time_zone'] . ']';
                    $pinboardItems['showroom_visit'] = $showroomData;
                }
        }

        // email booking
        if ($type == 'email') {
                $pinboardItems['comment_photos'] = [
                    [
                        'photo_url' => '',
                        'photo_name' => 'Select Attachment',
                        'comment_photo_id' => '',
                        'comment_id' => '',
                    ]
                ];

                $commentPhotos = $this->commentPhoto
                ->join('comment', 'comment.comment_id', '=', 'comment_photo.comment_id')
                ->select([
                    'comment_photo.comment_photo_id',
                    'comment_photo.comment_id',
                    'comment_photo.image',
                ])
                ->where('comment.model_id', '=', $pinboardId)
                ->where('comment.model_type', '=', 'pinboard')
                ->findAll(false);
    
                foreach ($commentPhotos as $photoItem) {
                    $imageData = json_decode($photoItem['image'], true);
                    $pinboardItems['comment_photos'][] = [
                        'comment_photo_id' => $photoItem['comment_photo_id'],
                        'comment_id' => $photoItem['comment_id'],
                        'photo_url' => $imageData['objectURL'] ?? null,
                        'photo_name' => $imageData['name'] ?? null,
                    ];
                }

                // email service request
                $serviceRequest = $this->serviceRequest
                ->select([
                    'service_request.service_request_id',
                    'service_request.email',
                    'service_request.content',
                    'service_request.comment_attachment',
                    'service_request.attachments',
                ])
                ->where('service_request.pinboard_id', '=', $pinboardId)
                ->first();

                if($serviceRequest) {
                    $pinboardItems['service_request'] = $serviceRequest->data;
                }else{
                    $pinboardItems['service_request'] = [];
                }
        }

        $items = json_decode($pinboard->data->pinboard_items, true);
        $pinboardItems['items'] = $items;
        $pinboardItems['count_items'] = count($items);
        $pinboardItems['pinboard_note'] = isset($pinboard->data->note) ? $pinboard->data->note : '';
        $pinboardItems['pinboard_id'] = isset($pinboard->data->pinboard_id) ? $pinboard->data->pinboard_id : '';
        $pinboardItems['project_name'] = isset($pinboard->data->pinboard_name) ? $pinboard->data->pinboard_name : '';
        $pinboardItems['customer_email'] = isset($pinboard->data->gmail_Id) ? $pinboard->data->gmail_Id : '';
        $pinboardItems['customer_name'] = isset($pinboard->data->name) ? $pinboard->data->name : '';
        $pinboardItems['customer_phone'] = isset($pinboard->data->phone) ? $pinboard->data->phone : '';
        $pinboardItems['customer_address'] = isset($pinboard->data->address) ? $pinboard->data->address : '';
        $pinboardItems['contact_number'] = isset($pinboard->data->contact_number) ? json_decode($pinboard->data->contact_number, true) : [];
        $pinboardItems['total_amount'] = '$'.number_format(array_sum(array_column($items, 'total_price')) ?? 0, 2);
        // team members
        $pinboardItems['team_members'] = $this->getTeamMembers();

        return $pinboardItems;
    }

    private function getTeamMembers(): array
    {
        $this->showroomContact->clearQuery();
        $salesTeams = $this->showroomContact
        ->join('showrooms', 'showrooms.showrooms_id', '=', 'showroom_contact.showroom_id')
        ->select([
            'showroom_contact.showroom_contact_id',
            'showroom_contact.image',
            'showroom_contact.name',
            'showroom_contact.email',
            'showroom_contact.phone',
            'showroom_contact.designation',
            'showroom_contact.message',
            'showrooms.title as showroom_title',
            'showrooms.address as showroom_address',
            'showrooms.showrooms_id as showroom_id'
        ])
        ->where('showroom_contact.status', '=', 1)
        ->where('showroom_contact.sales_team_contact', '=', 0)
        ->orderBy('showrooms.showrooms_id', 'asc')
        ->orderBy('showroom_contact.sort_order', 'asc');
        $salesTeams = $salesTeams->findAll();

        $formattedTeams = [];
        foreach ($salesTeams as $teamMember) {
            $member = is_array($teamMember) ? $teamMember : (array) $teamMember;
            $city = isset($member['showroom_title']) ? $member['showroom_title'] : '';

            if (!isset($formattedTeams[$city])) {
                $formattedTeams[$city] = [
                    'city' => $city,
                    'members' => [],
                ];
            }

            $image = isset($member['image']) ? $member['image'] : '';
            $decodedImage = is_string($image) ? json_decode($image, true) : null;
            if (is_array($decodedImage) && isset($decodedImage[0]['objectURL'])) {
                $image = $decodedImage[0]['objectURL'];
            }

            $formattedTeams[$city]['members'][] = [
                'name' => isset($member['name']) ? $member['name'] : '',
                'designation' => isset($member['designation']) ? $member['designation'] : '',
                'image' => $image,
                'calendar' => true,
                'email' => isset($member['email']) ? $member['email'] : '',
                'showroom_address' => isset($member['showroom_address']) ? $member['showroom_address'] : '',
                'phone' => isset($member['phone']) ? $member['phone'] : '',
            ];
        }

        return array_values($formattedTeams);
    }

    public function getBookingComponentContactSales(int $visitShowroomId): array
    {
        $this->visitShowroom->clearQuery();
        $showroomVisit = $this->visitShowroom
        ->join('showroom_contact', 'showroom_contact.showroom_contact_id', '=', 'visit_showroom.showroom_contact_id')
        ->join('showrooms', 'showrooms.showrooms_id', '=', 'visit_showroom.showroom_id')
        ->join('customer', 'customer.customer_id', '=', 'visit_showroom.customer_id')
        ->join('user', 'user.user_id', '=', 'customer.user_id')
        ->select([
            'visit_showroom.pinboard_id',
            'visit_showroom.visit_showroom_id',
            'showrooms.title',
            'showrooms.address',
            'showrooms.phone as showroom_phone',
            'showrooms.email as showroom_email',
            // 'showrooms.mobile',
            'showrooms.google_map_link',
            'showrooms.image as image',
            'visit_showroom.pinboard_id',
            'visit_showroom.uuid',
            'visit_showroom.showroom_contact_id',
            'visit_showroom.customer_id',
            'visit_showroom.showroom_id',
            'visit_showroom.tour_type',
            'date_format(visit_showroom.date, "%W, %e %M %Y") as date',
            'visit_showroom.time_zone',
            'visit_showroom.meeting_time',
            'visit_showroom.note',
            'showroom_contact.name',
            'showroom_contact.email',
            'showroom_contact.phone',
            // 'showroom_contact.image',
            'showroom_contact.designation',
            'user.user_id',
            'user.email as customer_email',
            'customer.name as customer_name',
            'customer.phone as customer_phone'
        ])
        ->where('visit_showroom.visit_showroom_id', '=', $visitShowroomId)
        ->first();
        
        if ($showroomVisit) {
            // convert stdClass to array
            $showroomData = json_decode(json_encode($showroomVisit->data), true);
    
            // format meeting_time
            $time = isset($showroomData['meeting_time']) ? $showroomData['meeting_time'] : '';
            $start = date('g:i a', strtotime($time));
            $end   = date('g:i a', strtotime($time . ' +60 minutes'));
            $formatted = $time ? $start . ' - ' . $end : '';
       

            $image = isset($showroomData['image']) ? json_decode($showroomData['image'], true) : [];
            $imageUrl = isset($image[0]) && isset($image[0]['objectURL']) ? $image[0]['objectURL'] : '';
            $showroomData['image'] = $imageUrl;
        
            // replace meeting_time
            $showroomData['meeting_time'] = $start;
            $showroomData['meeting_time_label'] = $formatted . ' [' . $showroomData['time_zone'] . ']';

            // pinboard id
            $pinboardId = isset($showroomData['pinboard_id']) ? $showroomData['pinboard_id'] : 0;
            $pinboard = $this->showPinboard($pinboardId);
            $pinboard = new PinboardResponse($pinboard->data);
            if ($pinboard) {
                $showroomData['pinboard_name'] = isset($pinboard->pinboard_name) ? $pinboard->pinboard_name : '';
                $showroomData['pinboard_item_count'] = count($pinboard->pinboardItems ?? []);
                $showroomData['pinboard_items'] = isset($pinboard->pinboardItems) ? $pinboard->pinboardItems : [];
            }
        }else{
            return [];
        }

        // $pinboard = $this->model->first();
        // $showroomData['note'] = isset($pinboard->data->pinboard_description) ? $pinboard->data->pinboard_description : '';
        $showroomData['customer_email'] = isset($showroomData['customer_email']) ? $showroomData['customer_email'] : '';
        $showroomData['customer_name'] = isset($showroomData['customer_name']) ? $showroomData['customer_name'] : '';
        $showroomData['customer_phone'] = isset($showroomData['customer_phone']) ? $showroomData['customer_phone'] : '';
        $showroomData['customer_address'] = isset($showroomData['customer_address']) ? $showroomData['customer_address'] : '';
        $showroomData['contact_number'] = isset($showroomData['contact_number']) ? $showroomData['contact_number'] : '';

        return $showroomData;
    }


    public function updatePinboardStatus(int $pinboardId, int $pinboardStatusId = 2): array
    {
        $pinboard = $this->model->where('pinboard_id', '=', $pinboardId)->first();
        if (!$pinboard) {
            return ['success' => false, 'message' => 'Pinboard not found', 'data' => []];
        }
        // update pinboard status to 2 (accepted)
        $pinboard->update([
            'pinboard_status_id' => $pinboardStatusId
        ]);
        return ['success' => true, 'message' => 'Pinboard status updated successfully', 'data' => $pinboard->data];
    }

    public function searchPinboardProducts(string $queryString)
    {
        $results = $this->prepareQuerySearchPinboardProducts($queryString);
        $allItems = array_map(function($item){
            return new GlobalSearchData($item);
        }, $results);
        $total_count = count($allItems);
        return [
            'total_result' => 'Results: ' . $total_count . ' Results',
            'results' => $allItems,
        ];
    }

    private function prepareQuerySearchPinboardProducts(string $queryString): array
    {
        $productQuery = $this->product;

        $products = $productQuery
        ->join('product_content', 'product_content.product_id', '=', 'product.product_id')
        ->join('product_to_taxonomy_item', 'product_to_taxonomy_item.product_id', '=', 'product.product_id')
        ->join('taxonomy_item', 'taxonomy_item.taxonomy_item_id', '=', 'product_to_taxonomy_item.taxonomy_item_id')
        ->join('taxonomy_item_content', 'taxonomy_item_content.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
        ->select([
            'product_id as id',
            'product_content.title as title', 
            'product_content.name as name', 
            'image_thumb as image', 
            'product_content.tag_line as description', 
            'product_content.slug as slug',
            'CONCAT("products reference: ", product.product_id) as reference',
            'CONCAT("products/", taxonomy_item_content.slug, "/", product_content.slug) as href',
            'CONCAT("Product-", product.product_id) as model_type',
         ])
        ->where('product.status', '=', 1)
        ->where(function($q) use ($queryString) {
            return $q->where('product.product_code', 'LIKE', '%' . $queryString . '%')
            ->orWhere('product_content.title', 'LIKE', '%' . $queryString . '%')
            ->orWhere('product_content.tag_line', 'LIKE', '%' . $queryString . '%');
        })
        ->limit(50)
        ->findAll();

        $mergedResults = array_merge($products);

        return $mergedResults;
    }

    // updateProjectTitle
    public function updateProjectTitle(array $data): array
    {
        $pinboard = $this->model->where('pinboard_id', '=', $data['pinboard_id'])->first();
        if (!$pinboard) {
            return ['success' => false, 'message' => 'Pinboard not found', 'data' => []];
        }
        $pinboard->update(['pinboard_name' => $data['pinboard_name'], 'job_title' => $data['pinboard_name']]);
        if (!$pinboard) {
            return ['success' => false, 'message' => 'Failed to update project title', 'data' => []];
        }
        return ['success' => true, 'message' => 'Project title updated successfully', 'data' => $pinboard->data];
    }

    public function updatePinboardVisibility(array $data): array
    {
        $pinboard = $this->model->where('pinboard_id', '=', $data['pinboard_id'])->first();
        if (!$pinboard) {
            return ['success' => false, 'message' => 'Pinboard not found', 'data' => []];
        }
        $pinboard->update(['is_visible' => $data['is_visible']]);
        return ['success' => true, 'message' => 'Pinboard visibility updated successfully', 'data' => $pinboard->data];
    }

    public function getPinboardIdByUuid(string $uuid): int
    {
        $pinboard = $this->model->where('uuid', '=', $uuid)->first();
        if (!$pinboard) {
            return 0;
        }
        return $pinboard->pinboard_id;
    }

    public function createLead(int $pinboard_id): array
    {
        $existingPinboard = $this->model
            ->where('pinboard_id', '=', $pinboard_id)
            ->first();

        if (!$existingPinboard->data) {
            return [
                'success' => false,
                'message' => 'Pinboard not found',
                'data' => []
            ];
        }
        

        /*
        |--------------------------------------------------------------------------
        | Get Pinboard Full Data
        |--------------------------------------------------------------------------
        */
        $pinboardData = $this->showPinboard($pinboard_id);
        $pinboard = $this->attachPinboardStatusToResponse(new PinboardResponse($pinboardData->data));

        $allowedStatusKeys = [
            $this->normalizePinboardStatusKey('Draft'),
            $this->normalizePinboardStatusKey('In Discussion'),
            $this->normalizePinboardStatusKey('Project Submitted'),
        ];
        $currentStatusKey = $this->normalizePinboardStatusKey(
            (string) ($pinboard->pinboard_status?->name ?? '')
        );

        if (!in_array($currentStatusKey, $allowedStatusKeys, true) && !!$pinboard->pinboard_status_id) {
            return [
                'success' => false,
                'message' => 'It is already converted',
                'data' => [],
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Customer Info
        |--------------------------------------------------------------------------
        */
        $customerId = (int) ($pinboard->customerDetails->customer_id ?? 0);

        if ($customerId > 0) {
            $pinboard->customerInfo = $this->customerRepository
                ->getCustomerUserInfoByCustomerId($customerId);
        } else {
            $pinboard->customerInfo = [];
        }

       

        $pinboard = $this->createLeadItems($pinboard);
        $leadItems = $pinboard->leadItems ?? [];

        return [
            'success' => !empty($leadItems),
            'message' => !empty($leadItems)
                ? 'Lead items resolved'
                : 'No valid lead items found',
            'data' => $pinboard,
        ];
    }

    private function createLeadItems(PinboardResponse $pinboard): PinboardResponse
    {
        $leadItems = [];

        foreach ($pinboard->pinboardItems ?? [] as $item) {
            if (!is_array($item)) {
                continue;
            }

            $leadItem = $this->transformPinboardItemToLeadItem($item);
            if ($leadItem !== null) {
                $leadItems[] = $leadItem;
            }
        }

        $pinboard->leadItems = $leadItems;

        return $pinboard;
    }

    private function transformPinboardItemToLeadItem(array $item): ?array
    {
        $modelType = strtolower(trim((string) ($item['model_type'] ?? $item['type'] ?? '')));
        if ($modelType === '') {
            return null;
        }

        $title = trim((string) ($item['title'] ?? ''));
        $description = trim((string) ($item['description'] ?? ''));
        $instructions = $this->extractPinboardItemInstructions($item['comments'] ?? null);
        $quantity = (int) ($item['quantity'] ?? 0);
        $photo = trim((string) ($item['photo'] ?? ''));

        switch ($modelType) {
            case 'product':
                $type = 'product';
                $label = $title !== '' ? $title : $description;
                $itemDescription = $description !== '' ? $description : $title;
                break;

            case 'project':
                $type = 'project';
                $label = 'Project';
                $itemDescription = $title;
                break;

            case 'post':
                $type = 'post';
                $label = 'Blog';
                $itemDescription = $title;
                break;

            case 'media':
            case 'image':
            case 'images':
                $type = 'image';
                $label = 'Image';
                $itemDescription = $title !== '' ? $title : $description;
                break;

            case 'comment':
                $type = 'comment';
                $label = 'Comment';
                $itemDescription = $title !== '' ? $title : ($description !== '' ? $description : $instructions);
                break;

            default:
                return null;
        }

        if ($itemDescription === '' && $label !== '') {
            $itemDescription = $label;
        }

        return [
            'type' => $type,
            'label' => $label,
            'item_type' => 'pinboard_item',
            'quantity' => $quantity > 0 ? $quantity : 1,
            'photo' => $photo,
            'instructions' => $instructions,
            'description' => $itemDescription,
        ];
    }

    private function extractPinboardItemInstructions(mixed $comments): string
    {
        if ($comments === null || $comments === '') {
            return '';
        }

        if (is_string($comments)) {
            $decoded = json_decode($comments, true);
            $comments = json_last_error() === JSON_ERROR_NONE ? $decoded : $comments;
        }

        if (is_array($comments)) {
            if ($comments === []) {
                return '';
            }

            $first = $comments[0];
            if (is_array($first)) {
                return trim((string) ($first['content'] ?? $first['comment'] ?? $first['text'] ?? $first['message'] ?? ''));
            }

            return trim((string) $first);
        }

        return trim((string) $comments);
    }

    public function updatePinboardAfterLeadCreated(int $pinboardId, int $leadId): array
    {
        $pinboard = $this->model->where('pinboard_id', '=', $pinboardId)->first();
        if (!$pinboard) {
            return ['success' => false, 'message' => 'Pinboard not found', 'data' => []];
        }

        $updateData = ['lead_id' => $leadId];

        $statusId = $this->resolveOrCreatePinboardOrderStatusId('Converted To Lead');
        if ($statusId !== null) {
            $updateData['pinboard_status_id'] = $statusId;
        }

        $pinboard = $pinboard->update($updateData);

        $pinboard = $this->attachPinboardStatusToResponse(new PinboardResponse($pinboard->data));

        return [
            'success' => true,
            'message' => 'Pinboard updated after lead creation',
            'data' => $pinboard,
        ];
    }

    public function updatePinboardStatusByName(int $pinboardId, string $statusName): array
    {
        $canonicalStatus = $this->normalizePinboardStatusName($statusName);
        if ($canonicalStatus === null) {
            return [
                'success' => false,
                'message' => 'Invalid status. Allowed values: Draft, In Discussion, Converted To Lead, Quoted',
                'data' => [],
            ];
        }

        $pinboard = $this->model->where('pinboard_id', '=', $pinboardId)->first();
        if (!$pinboard) {
            return ['success' => false, 'message' => 'Pinboard not found', 'data' => []];
        }

        $statusId = $this->resolveOrCreatePinboardOrderStatusId($canonicalStatus);
        if ($statusId === null) {
            return ['success' => false, 'message' => 'Failed to resolve pinboard status', 'data' => []];
        }

        $pinboard->update(['pinboard_status_id' => $statusId]);

        return [
            'success' => true,
            'message' => 'Pinboard status updated successfully',
            'data' => $pinboard->data,
        ];
    }

    private function enrichPinboardsWithStatus(array $pinboards): array
    {
        $statusIds = [];
        foreach ($pinboards as $row) {
            $data = is_array($row) ? $row : (array) $row;
            $statusId = (int) ($data['pinboard_status_id'] ?? 0);
            if ($statusId > 0) {
                $statusIds[] = $statusId;
            }
        }

        $statusMap = $this->getPinboardOrderStatusesByIds($statusIds);

        foreach ($pinboards as $index => $row) {
            $data = is_array($row) ? $row : (array) $row;
            $statusId = (int) ($data['pinboard_status_id'] ?? 0);
            $status = $statusMap[$statusId] ?? null;

            if (is_array($pinboards[$index])) {
                $pinboards[$index]['pinboard_status'] = $status;
            } elseif (is_object($pinboards[$index])) {
                $pinboards[$index]->pinboard_status = $status;
            }
        }

        return $pinboards;
    }

    /**
     * @param int[] $statusIds
     * @return array<int, PinboardStatusResponse>
     */
    private function getPinboardOrderStatusesByIds(array $statusIds): array
    {
        $statusIds = array_values(array_unique(array_filter(
            array_map(static fn($id): int => (int) $id, $statusIds),
            static fn(int $id): bool => $id > 0
        )));

        if ($statusIds === []) {
            return [];
        }

        $orderStatus = new OrderStatus();
        $orderStatus->setDb($this->db);
        $rows = $orderStatus
            ->where('language_id', '=', 1)
            ->whereIn('order_status_id', $statusIds)
            ->findAll(false) ?? [];

        $map = [];
        foreach ($rows as $row) {
            $payload = is_array($row) ? $row : (array) ($row->data ?? $row);
            $id = (int) ($payload['order_status_id'] ?? 0);
            if ($id > 0) {
                $map[$id] = new PinboardStatusResponse($payload);
            }
        }

        return $map;
    }

    private function getPinboardOrderStatus(int $pinboardStatusId): ?PinboardStatusResponse
    {
        if ($pinboardStatusId <= 0) {
            return null;
        }

        $statusMap = $this->getPinboardOrderStatusesByIds([$pinboardStatusId]);

        return $statusMap[$pinboardStatusId] ?? null;
    }

    private function normalizePinboardStatusName(string $statusName): ?string
    {
        $key = strtolower(preg_replace('/[\s\-_]+/', ' ', trim($statusName)));

        $map = [
            'draft' => 'Draft',
            'in discussion' => 'In Discussion',
            'converted to lead' => 'Converted To Lead',
            'quoted' => 'Quoted',
        ];

        return $map[$key] ?? null;
    }

    private function normalizePinboardStatusKey(string $statusName): string
    {
        return strtolower(preg_replace('/[\s\-_]+/', ' ', trim($statusName)));
    }

    private function resolveOrCreatePinboardOrderStatusId(string $canonicalStatusName): ?int
    {
        $existingId = $this->findPinboardOrderStatusIdByName($canonicalStatusName);
        if ($existingId !== null) {
            return $existingId;
        }

        $orderStatus = new OrderStatus();
        $orderStatus->setDb($this->db);
        $createdStatus = $orderStatus->create([
            'language_id' => 1,
            'name' => $canonicalStatusName,
        ]);

        if ($createdStatus && isset($createdStatus->order_status_id)) {
            return (int) $createdStatus->order_status_id;
        }

        return null;
    }

    private function findPinboardOrderStatusIdByName(string $canonicalStatusName): ?int
    {
        $orderStatus = new OrderStatus();
        $orderStatus->setDb($this->db);
        $statuses = $orderStatus->where('language_id', '=', 1)->findAll(false) ?? [];
        $targetKey = $this->normalizePinboardStatusKey($canonicalStatusName);

        foreach ($statuses as $row) {
            $name = is_array($row) ? (string) ($row['name'] ?? '') : (string) ($row->name ?? '');
            $statusId = is_array($row)
                ? (int) ($row['order_status_id'] ?? 0)
                : (int) ($row->order_status_id ?? 0);

            if ($statusId > 0 && $this->normalizePinboardStatusKey($name) === $targetKey) {
                return $statusId;
            }
        }

        return null;
    }

    public function countComment(int $pinboardId): array
    {
        $sql = "SELECT COUNT(*) AS `count`, c.`model_id` AS `pinboard_item_id`
                FROM `comment` c
                INNER JOIN `pinboard_item` pi ON pi.`pinboard_item_id` = c.`model_id`
                WHERE pi.`pinboard_id` = :pinboard_id
                GROUP BY pi.`pinboard_item_id`";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':pinboard_id' => $pinboardId]);

        $counts = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $pinboardItemId = (int) ($row['pinboard_item_id'] ?? 0);
            if ($pinboardItemId > 0) {
                $counts[$pinboardItemId] = (int) ($row['count'] ?? 0);
            }
        }

        return $counts;
    }


}
