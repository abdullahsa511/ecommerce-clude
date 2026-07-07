<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Http\Response;
use App\Core\Http\Request;
use App\Core\Http\ApiController;
use App\Core\Repositories\UserRepositoryInterface;
use App\Core\Repositories\Customer\CustomerRepositoryInterface;
use function App\Core\System\utils\generateUuidV4;
use function App\Core\System\utils\uuidToBin;
/**
 * RegisterController handles the register page.
 */
class RegisterController extends ApiController
{
    private UserRepositoryInterface $userRepository;
    private CustomerRepositoryInterface $customerRepository;
    public function __construct(
        UserRepositoryInterface $userRepository,
        CustomerRepositoryInterface $customerRepository
    ){
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->customerRepository = $customerRepository;
    }

    public function registerCustomer(Request $request): Response
    {
        $data = $request->validate([
            'customer' => 'required|array',
            'customer.job_title' => 'required|string',
            'customer.name' => 'required|string',
            'customer.email' => 'required|email',
            'customer.companyName' => 'nullable|string',
            'customer.phone' => 'nullable|phone',
        ]);
        try {
            if ($data instanceof Response) {
                return $data;
            }
            // CHECK IF USER EXISTS
            $user = $this->userRepository->findByEmail($data['email']);
            if (!$user) {
                $userData = [
                    'user_group_id' => 1,
                    'username' => str_replace(' ', '-', strtolower(trim($data['name'] ?? ''))), 
                    'password' => '123456', 
                    'email' => $data['email'], 
                    'phone_number' => $data['phone'] ?? '01849XXXXXXX',
                    'uuid' => uuidToBin(generateUuidV4())
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
                    'name' => $data['name'], 
                    'gmail_Id' => $data['email'],
                    'company_name' => $data['companyName']??$data['name']
                ];
                // CREATE THE CUSTOMER
                $customer = $this->customerRepository->createCustomer($customerData);
            }
            $subject = 'Email Verification with Krost';
            try {
                $result = $this->customerRepository->sendEmailVerification($data['email'], $subject);
            } catch (Exception $e) {
                // return $this->renderError(500, $e->getMessage());
            }
            
            return $this->renderResponse($result);
        } catch (ValidationException $e) {
            return $this->renderError(422, 'Validation error. ' . $e->getMessage(), $e->getErrors());
        } catch (Exception $e) {
            return $this->renderError(500, $e->getMessage());
        }
    }
}
