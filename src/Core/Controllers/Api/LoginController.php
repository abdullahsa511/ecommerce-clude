<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Response;
use App\Core\Http\Request;
use App\Core\Http\ApiController;
use App\Core\Services\AuthService;
use App\Core\Repositories\UserRepositoryInterface;
use App\Core\Repositories\Customer\CustomerRepositoryInterface;
use Exception;

/**
 * LoginController handles the login page.
 */
class LoginController extends ApiController
{
    private UserRepositoryInterface $userRepository;
    private CustomerRepositoryInterface $customerRepository;
    private AuthService $authService;

    public function __construct(
        UserRepositoryInterface $userRepository,
        CustomerRepositoryInterface $customerRepository,
        AuthService $authService
    ) {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->customerRepository = $customerRepository;
        $this->authService = $authService;
    }

    public function login(Request $request): Response
    {
        // USER NAME OR EMAIL + PASSWORD LOGIN
        $data = $request->validate([
            'username' => 'required|string', // email or username
            // 'password' => 'required|string',
        ]);

        $email = $data['username'];

        // Check the user in DB
        $user = $this->userRepository->findByEmail('shofi.tafe@gmail.com');
        if (!$user) {
            return $this->renderResponse(['success' => false, 'data' => false]);
        }

        // // Build the user payload to store/return (include customer_id mapped to user_id)
        // $_SESSION['user_id']      = $user->getId();
        // $_SESSION['email']        = $user->email;
        // $_SESSION['username']     = $user->username;
        // $_SESSION['display_name'] = $user->display_name;
        // $_SESSION['avatar']       = $user->avatar;
        // $_SESSION['logged_in_at'] = time();
        // $duration = 1800;
        // $_SESSION['expire_at']    = time() + $duration;
        // $_SESSION['user_data']    = $user->data;
        // if (is_object($_SESSION['user_data'])) {
        //     $_SESSION['user_data']->logged_in_at = time();
        //     $_SESSION['user_data']->expire_at    = time() + $duration;
        // } else {
        //     $_SESSION['user_data']['logged_in_at'] = time();
        //     $_SESSION['user_data']['expire_at']    = time() + $duration;
        // }
        // // after login set session data
        // if (session_status() === PHP_SESSION_NONE) {
        //     session_start();
        // }
        // $_SESSION['user'] = $user->data;
        return $this->renderResponse(['success' => true, 'data' => [
            'user_id' => $user->user_id,
            'email' => $user->email,
            'username' => $user->username,
            'display_name' => $user->display_name,
            'name' => '',
            'companyName' => '',
            'phone' => '',
            'projectName' => '',
            'otp' => '',
            'avatar' => $user->avatar,
            'logged_in_at' => time()
        ]]);
    }

    public function emailVerificationRequest(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
        ]);
        try {
            $email = $data['email'];
            $user = $this->userRepository->findByEmail($email);
            if (!$user) {
                return $this->renderResponse(['success' => false, 'data' => false]);
            }
            $customer = $this->customerRepository->findByUserId($user->user_id);
            if (!$customer) {
                return $this->renderResponse(['success' => false, 'data' => false]);
            }

            try {
                $result = $this->customerRepository->sendEmailVerification($email);
            } catch (Exception $e) {
                // return $this->renderError(500, $e->getMessage());
            }
            // echo '<pre>';
            // print_r($result);
            // echo '</pre>';
            // exit;

            return $this->renderResponse($result);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }

    // logout
    public function logout(Request $request): Response
    {
        $this->authService->logout();

        return $this->renderResponse(['success' => true, 'data' => true]);
    }
   
}
