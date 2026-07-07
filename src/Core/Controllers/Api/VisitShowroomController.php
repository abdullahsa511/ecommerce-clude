<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\NotFoundHttpException;
use App\Core\Exceptions\ValidationException;
use App\Core\Http\Response;
use App\Core\Http\Request;
use App\Core\Http\ApiController;
use App\Core\Repositories\Visit\VisitShowroomRepositoryInterface;
use App\Core\Repositories\UserRepositoryInterface;
use App\Core\Repositories\Customer\CustomerRepositoryInterface;
use App\Core\Services\RecaptchaService;
use Exception;

use function App\Core\System\utils\generateUuidV4;
use function App\Core\System\utils\uuidToBin;
/**
 * VisitShowroomController handles the visit showroom page.
 */
class VisitShowroomController extends ApiController
{
    private VisitShowroomRepositoryInterface $visitShowroomRepository;
    private UserRepositoryInterface $userRepository;
    private CustomerRepositoryInterface $customerRepository;
    private RecaptchaService $recaptchaService;

    public function __construct(
        VisitShowroomRepositoryInterface $visitShowroomRepository,
        UserRepositoryInterface $userRepository,
        CustomerRepositoryInterface $customerRepository,
        ?RecaptchaService $recaptchaService = null
    ){
        parent::__construct();
        $this->visitShowroomRepository = $visitShowroomRepository;
        $this->userRepository = $userRepository;
        $this->customerRepository = $customerRepository;
        $this->recaptchaService = $recaptchaService ?? new RecaptchaService();
    }

    private function verifyBookingRecaptcha(Request $request): ?Response
    {
        if (!$this->recaptchaService->isEnabled()) {
            return null;
        }

        $remoteIp = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? null;
        if (is_string($remoteIp) && str_contains($remoteIp, ',')) {
            $remoteIp = trim(explode(',', $remoteIp)[0]);
        }

        $recaptchaResult = $this->recaptchaService->verify(
            (string) ($request->all()['g-recaptcha-response'] ?? ''),
            is_string($remoteIp) ? $remoteIp : null,
            $this->recaptchaService->getBookingAction()
        );

        if (!$recaptchaResult['ok']) {
            return $this->renderError(
                422,
                $recaptchaResult['message'] ?? 'reCAPTCHA verification failed.'
            );
        }

        return null;
    }

    public function checkExistingBooking(Request $request): Response
    {
        $data = $request->all();
        $email = $data['email']??"shofiul@krost.com.au";
        $name = $data['customer_name']??"Shofiul Islam";
 
        try {
            // check exsiting data 
            $existingData = $this->visitShowroomRepository->checkExistingData($data);
            if (isset($existingData) && !empty($existingData)) {
                return $this->renderResponse($existingData);
            }
            
            if ($data instanceof Response) {
                return $data;
            }
            if (empty($data['customer_id']) && !empty($email)) {
                $customerInfo = $this->getCustomerInfo($email, $name);
                $data['customer_id'] = $customerInfo['customer_id'];
            }

            // check exsiting data by customer id
            $existingDataByCustomerId = $this->visitShowroomRepository->checkExistingDataByCustomerId($data);
            if (isset($existingDataByCustomerId) && !empty($existingDataByCustomerId)) {
                return $this->renderResponse($existingDataByCustomerId);
            }

            return $this->renderResponse(['success' => true, 'message' => 'No existing booking found']);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    public function bookNow(Request $request): Response
    {
        $recaptchaFailure = $this->verifyBookingRecaptcha($request);
        if ($recaptchaFailure !== null) {
            return $recaptchaFailure;
        }

        $data = $request->all();
        $email = $data['email']??"shofiul@krost.com.au";
        $name = $data['customer_name']??"Shofiul Islam";
        $errorMessage = 'Something went wrong. Please check your connection and try again.';
 
        try {
            // check exsiting data 
            $existingData = $this->visitShowroomRepository->checkExistingData($data);
            if (isset($existingData) && !empty($existingData)) {
                return $this->renderResponse($existingData);
            }
            
            if ($data instanceof Response) {
                return $data;
            }
            if (empty($data['customer_id']) && !empty($email)) {
                $customerInfo = $this->getCustomerInfo($email, $name);
                $data['customer_id'] = $customerInfo['customer_id'];
            }

            // check exsiting data by customer id
            $existingDataByCustomerId = $this->visitShowroomRepository->checkExistingDataByCustomerId($data);
            if (isset($existingDataByCustomerId) && !empty($existingDataByCustomerId)) {
                return $this->renderResponse($existingDataByCustomerId);
            }

            $data['duration'] = 30;
            $visitShowroom = $this->visitShowroomRepository->bookNow($data);
            if (isset($visitShowroom['error'])) {
                return $this->renderError(400, $visitShowroom['error']);
            }
            return $this->renderResponse($visitShowroom);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (NotFoundHttpException $e) {
            return $this->renderError(404, $e->getMessage());
        } catch (Exception $e) {
            return $this->renderError(500, $errorMessage);
        }
    }
    
    public function rescheduleBooking(Request $request): Response
    {
        $data = $request->all();
        $email = $data['email'] ?? null;
        $name = $data['customer_name'] ?? null;
        try {
            // check exsiting data 
            $existingData = $this->visitShowroomRepository->checkExistingData($data);
            if (isset($existingData) && !empty($existingData)) {
                return $this->renderResponse($existingData);
            }
            
            if ($data instanceof Response) {
                return $data;
            }
            if (empty($data['customer_id']) && !empty($email)) {
                $customerInfo = $this->getCustomerInfo($email, $name);
                $data['customer_id'] = $customerInfo['customer_id'];
            }

            // check exsiting data by customer id
            // $existingDataByCustomerId = $this->visitShowroomRepository->checkExistingDataByCustomerId($data);
            // if (isset($existingDataByCustomerId) && !empty($existingDataByCustomerId)) {
            //     return $this->renderResponse($existingDataByCustomerId);
            // }

            $data['duration'] = 30;
            $visitShowroom = $this->visitShowroomRepository->rescheduleBooking($data);
            if (isset($visitShowroom['error'])) {
                return $this->renderError(400, $visitShowroom['error']);
            }
            return $this->renderResponse($visitShowroom);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    private function validateBooking24Hours(array $data): ?Response
    {
        if (empty($data['date']) || empty($data['time'])) {
            return $this->renderError(
                422,
                'Booking date and time are required.'
            );
        }

        $timezone = new \DateTimeZone('Australia/Sydney');

        $bookingDateTime = new \DateTime(
            $data['date'] . ' ' . $data['time'],
            $timezone
        );

        $now = new \DateTime('now', $timezone);

        $diffSeconds = $bookingDateTime->getTimestamp() - $now->getTimestamp();

        if ($diffSeconds < 24 * 60 * 60) {
            return $this->renderError(
                422,
                'Bookings must be made at least 24 hours before the selected time.'
            );
        }

        return null;
    }

    // cancel booking
    public function cancelBooking(Request $request): Response
    {
        $data = $request->all();
        $visitShowroomId   = isset($data['visit_showroom_id']) ? (int) $data['visit_showroom_id'] : null;
        try {
            $visitShowroom = $this->visitShowroomRepository->cancelBooking((int) $visitShowroomId);
            if (isset($visitShowroom['error'])) {
                return $this->renderError(400, $visitShowroom['error']);
            }
            return $this->renderResponse($visitShowroom);
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }
    // cancel booking by admin
    public function cancelBookingByAdmin(Request $request, int $visit_showroom_id): Response
    {
        try {
            $visitShowroom = $this->visitShowroomRepository->cancelBooking((int) $visit_showroom_id);
            if (isset($visitShowroom['error'])) {
                return $this->renderError(400, $visitShowroom['error']);
            }
            return $this->renderResponse($visitShowroom);
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    public function bookingManagement(Request $request, $userId, $showroom_id): Response
    {
        try {
            $data = $this->visitShowroomRepository->bookingManagement($userId, $showroom_id);
            return $this->renderResponse($data);
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }
    public function addVisitShowroom(Request $request): Response
    {
        try {
            $data = $request->all();
            // check exsiting data 
            $data['date'] = $data['start_date'];
            $data['meeting_time'] = $data['start_time'];
            $existingData = $this->visitShowroomRepository->checkExistingData($data);
            if (isset($existingData) && !empty($existingData)) {
                return $this->renderResponse($existingData);
            }
            $visitShowroom = $this->visitShowroomRepository->addVisitShowroom($data);
            return $this->renderResponse($visitShowroom);
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    public function updateVisitShowroom(Request $request, $visit_showroom_id): Response
    {
        try {
            $data = $request->all();
            $data['date'] = $data['start_date'];
            $data['meeting_time'] = $data['start_time'];
            // check exsiting data 
            $existingData = $this->visitShowroomRepository->checkExistingData($data, $visit_showroom_id);
            if (isset($existingData) && !empty($existingData)) {
                return $this->renderResponse($existingData);
            }
            $visitShowroom = $this->visitShowroomRepository->updateVisitShowroom($data, (int) $visit_showroom_id);
            return $this->renderResponse($visitShowroom);
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    public function getCustomerInfo(string $email, ?string $name = null): array
    {
        // CHECK IF USER EXISTS
        $user = $this->userRepository->findByEmail($email);
        if (!$user) {
            $userData = [
                'user_group_id' => 1,
                'username' => str_replace(' ', '-', strtolower(trim($name ?? $email))),
                'password' => '123456',
                'email' => $email,
                'uuid' => uuidToBin(generateUuidV4()),
                'phone_number' => '01849XXXXXXX'
            ];
            // CREATE THE USER
            $user = $this->userRepository->create($userData);
        }

        // CHECK IF CUSTOMER EXISTS
        $customer = $this->customerRepository->findByUserId($user->user_id);
        if (empty($customer)) {
            $customerData = [
                'user_id' => $user->user_id,
                'organisation_id' => 1,
                'org_code' => 'ORG-' . $user->user_id,
                'name' => $name ?? $email,
                'gmail_Id' => $email,
                'company_name' => ''
            ];
            // CREATE THE CUSTOMER
            $customer = $this->customerRepository->createCustomer($customerData);
        }
        return [
            'user_id' => $user->user_id,
            'customer_id' => $customer['customer_id']
        ];
    }

    /**
     * Showroom timezone rows (showroom_id 1–3): same data as ShowroomDateTimeRepository / time_zone.php.
     * Optional query: ?at=2026-01-11T12:00:00
     */
    public function getShowroomDateTimes(Request $request): Response
    {
        $at = $request->query('at');
        $at = ($at !== null && $at !== '') ? (string) $at : null;

        return $this->renderResponse(
            $this->visitShowroomRepository->getShowroomDateTimes($at)
        );
    }

    public function fetchBookedDataByShowroomId(Request $request, $showroom_id, $date): Response
    {
        $visit_showroom_id = $request->query('id')??null;
        $tour_type = $request->query('tour_type')??'physicalTour';
        try {
            $bookedData = $this->visitShowroomRepository->fetchBookedDataByShowroomId((int) $showroom_id, $date, $visit_showroom_id, $tour_type);
            if (isset($bookedData['error'])) {
                return $this->renderError(400, $bookedData['error']);
            }
            return $this->renderResponse($bookedData);
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    public function getContactRequestList(): Response
    {
        $contactRequestList = $this->visitShowroomRepository->getContactRequestList();
        return $this->renderResponse($contactRequestList['data'] ?? []);    
    }

    public function getContactRequestById(Request $request, $id): Response
    {
        $contactRequest = $this->visitShowroomRepository->getContactRequestById( (int) $id);
        if (isset($contactRequest['error'])) {
            return $this->renderError(400, $contactRequest['error']);
        }
        return $this->renderResponse($contactRequest['data'] ?? []);
    }

    public function updateContactRequest(Request $request, $id): Response
    {
        // $data = $request->all();
        // $contactRequest = $this->visitShowroomRepository->updateContactRequest( (int) $id, $data);
        // return $this->renderResponse($contactRequest);

        try {
            $data = $request->validate([
                'tour_type' => 'string|nullable',
                'date' => 'string|nullable',
                'meeting_time' => 'string|nullable',
                'time_zone' => 'string|nullable',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        // check duplicate name
        $existingContactRequest = $this->visitShowroomRepository->isExistsContactRequest($data['meeting_time'], (int) $id);
        if ($existingContactRequest) {
            throw new ValidationException(['meeting_time' => ['Meeting time is already in use.']]);
        }

        $contactRequest = $this->visitShowroomRepository->updateContactRequest($data, (int) $id);
        if (!$contactRequest) {
            throw new ValidationException(['meeting_time' => ['Failed to update contact request.']]);
        }
        
        return $this->renderResponse($contactRequest->data ?? null);


    }

    public function deleteContactRequest(Request $request, $visit_showroom_id): Response
    {
        try {
            $contactRequest = $this->visitShowroomRepository->deleteContactRequest( (int) $visit_showroom_id);
            if (isset($contactRequest['error'])) {
                return $this->renderError(400, $contactRequest['error']);
            }
            return $this->renderResponse($contactRequest);
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    public function sendMessage(Request $request): Response
    {
        try {
            $data = $request->all();

           // max 600 characters validation
            if (strlen($data['note']) > 600) {
                return $this->renderError(422, 'Note must be less than 600 characters');
            }
            if ($data instanceof Response) {
                return $data;
            }
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }
        $message = $this->visitShowroomRepository->sendMessage($data);
        if (isset($message['error'])) {
            return $this->renderError(400, $message['error']);
        }
        return $this->renderResponse($message);
    }

    public function sendOneDayPriorVisitShowroomNotification(): Response
    {
        $notification = $this->visitShowroomRepository->oneDayPriorVisitShowroomNotification();
        if (isset($notification['error'])) {
            return $this->renderError(400, $notification['error']);
        }
        return $this->renderResponse($notification);
    }
    public function sendOneDayPriorOnlineMeetingNotification(): Response
    {
        $notification = $this->visitShowroomRepository->oneDayPriorOnlineMeetingNotification();
        if (isset($notification['error'])) {
            return $this->renderError(400, $notification['error']);
        }
        return $this->renderResponse($notification);
    }

    public function sendAbsentCustomerNotification(Request $request, $visit_showroom_id): Response
    {
        $notification = $this->visitShowroomRepository->absentCustomerNotification( (int) $visit_showroom_id);
        return $this->renderResponse($notification);
    }
}
