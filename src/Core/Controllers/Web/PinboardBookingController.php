<?php

declare(strict_types=1);

namespace App\Core\Controllers\Web;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Repositories\Pinboard\PinboardRepositoryInterface;
use App\Core\Repositories\Site\SiteRepositoryInterface;
use App\Core\Services\AuthService;
use App\Core\Services\CsrfService;
use App\Core\Repositories\Visit\VisitShowroomRepositoryInterface;
/**
 * HomeController handles the home page.
 */
class PinboardBookingController extends Controller
{
    private const BOOKING_PHONE_CALL_FLASH = 'booking_phone_call_flash';

    private PinboardRepositoryInterface $pinboardRepository;
    private CsrfService $csrfService;
    private AuthService $authService;
    private VisitShowroomRepositoryInterface $visitShowroomRepository;

    public function __construct(CsrfService $csrfService, PinboardRepositoryInterface $pinboardRepository, AuthService $authService, VisitShowroomRepositoryInterface $visitShowroomRepository, SiteRepositoryInterface $siteRepository)
    {
        parent::__construct($siteRepository);
        // Force view lookup to reuse shared booking templates
        $this->view->set('controller', 'booking');
        $this->pinboardRepository = $pinboardRepository;
        $this->csrfService = $csrfService;
        $this->authService = $authService;
        $this->visitShowroomRepository = $visitShowroomRepository;
    }

    public function bookingShowroomVisit(Request $request, string $id): Response
    {
        return $this->renderResponse('booking-showroom-visit', ['id' => $id,'title' => 'Pinboard Request | Krost Business Furniture']);
    }

    public function bookingShowroomVisitContactSales(Request $request, string $uuid): Response
    {
        $visitShowroom = $this->visitShowroomRepository->getVisitShowroomIdByUuid($uuid);
        if (!$visitShowroom) {
            return $this->redirect('/404');
        }
        return $this->renderResponse('booking-showroom-visit-contact-sales', ['uuid' => $uuid, 'title' => 'Pinboard Request | Krost Business Furniture']);
    }

    public function bookingVirtualMeeting(Request $request, string $uuid): Response
    {
        return $this->renderResponse('booking-virtual-meeting', ['uuid' => $uuid, 'title' => 'Pinboard Request | Krost Business Furniture']);
    }
    
    public function bookingEmail(Request $request, string $uuid): Response
    {
        return $this->renderResponse('booking-email', ['uuid' => $uuid, 'title' => 'Pinboard Request | Krost Business Furniture']);
    }

    public function bookingPhoneCall(Request $request, string $uuid): Response
    {

        $csrfToken = $this->csrfService->getToken();
        $payload = ['uuid' => $uuid, 'nonce' => $csrfToken, 'is_admin' => $this->isAdmin(), 'title' => 'Pinboard Request | Krost Business Furniture'];
        $flash = $this->session->get(self::BOOKING_PHONE_CALL_FLASH);
        if (is_array($flash)) {
            $this->session->delete(self::BOOKING_PHONE_CALL_FLASH);
            if (($flash['uuid'] ?? '') === $uuid) {
                if (isset($flash['nonce']) && is_string($flash['nonce']) && $flash['nonce'] !== '') {
                    $payload['nonce'] = $flash['nonce'];
                }
                if (isset($flash['errors']) && is_array($flash['errors'])) {
                    $payload['errors'] = $flash['errors'];
                }
                if (isset($flash['data']) && is_array($flash['data'])) {
                    $payload['data'] = $flash['data'];
                }
            }
        }
        return $this->renderResponse('booking-phone-call', $payload);
    }

    public function submissionConfirmation(Request $request, string $uuid): Response
    {
        return $this->renderResponse('submission-confirmation', ['uuid' => $uuid, 'title' => 'Pinboard Request | Krost Business Furniture']);
    }

    public function cancelPhoneCall(Request $request): Response
    {
        if (!$this->authService->getAuthUser()) {
            return $this->redirect('/login');
        }
        $data = $request->all();
        $uuid = $data['pinboard_uuid'] ?? '';
        $user = $this->authService->getAuthUser();
        if (!$this->csrfService->validateToken((string) ($data['nonce'] ?? ''))) {
            $csrfToken = $this->csrfService->getToken();
            return $this->renderResponse('booking-phone-call', ['uuid' => $uuid, 'nonce' => $csrfToken, 'errors' => ['nonce' => ['Invalid CSRF token']], 'data' => $data, 'title' => 'Pinboard Request | Krost Business Furniture']);
        }
        $csrfToken = $this->csrfService->getToken();
        try {
            $pinboardId = $this->pinboardRepository->getPinboardIdByUuid($uuid);
            if (!$pinboardId) {
                $this->session->set(self::BOOKING_PHONE_CALL_FLASH, [
                    'uuid' => $uuid,
                    'nonce' => $csrfToken,
                    'errors' => ['pinboard_uuid' => ['Pinboard not found']],
                    'data' => $data,
                    'title' => 'Pinboard Request | Krost Business Furniture'
                ]);
                return $this->redirect('/pinboards/' . rawurlencode($uuid) . '/phone-call-request');
            }
            $pinboard = $this->pinboardRepository->showPinboard($pinboardId);
            if($pinboard->user_id != $user->user_id){
                return $this->redirect('/login');
            }
            if (!$pinboard) {
                $this->session->set(self::BOOKING_PHONE_CALL_FLASH, [
                    'uuid' => $uuid,
                    'nonce' => $csrfToken,
                    'errors' => ['pinboard_uuid' => ['Pinboard not found']],
                    'data' => $data,
                    'title' => 'Pinboard Request | Krost Business Furniture'
                ]);
                return $this->redirect('/pinboards/' . rawurlencode($uuid) . '/phone-call-request');
            }
            $pinboard->update(['is_cancel_phone_call' => 1]);
        } catch (\Throwable $e) {
            $csrfToken = $this->csrfService->getToken();
            return $this->renderResponse('booking-phone-call', [
                'uuid' => $uuid,
                'nonce' => $csrfToken,
                'errors' => ['general' => ['Unable to cancel the phone call. Please try again.']],
                'data' => $data,
                'title' => 'Pinboard Request | Krost Business Furniture'
            ]);
        }
        return $this->redirect('/account/virtual-pinboards?success=true&message=Your request has been cancelled.');
    }
}
