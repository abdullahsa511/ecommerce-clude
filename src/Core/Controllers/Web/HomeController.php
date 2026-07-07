<?php

declare(strict_types=1);

namespace App\Core\Controllers\Web;

use App\Core\Exceptions\UnauthorizedHttpException;
use App\Core\Exceptions\ValidationException;
use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Repositories\Site\SiteRepositoryInterface;
use App\Core\Repositories\Subscription\SubscriptionRepositoryInterface;
use App\Core\Services\CsrfService;

/**
 * HomeController handles the home page.
 */
class HomeController extends Controller
{
    private CsrfService $csrfService;
    private SubscriptionRepositoryInterface $subscriptionRepository;

    public function __construct(CsrfService $csrfService, SubscriptionRepositoryInterface $subscriptionRepository, SiteRepositoryInterface $siteRepository)
    {
        parent::__construct($siteRepository);
        $this->csrfService = $csrfService;
        $this->subscriptionRepository = $subscriptionRepository;
    }

    public function index(Request $request): Response
    {
        $csrfToken = $this->csrfService->getToken();
        $email = $request->query('email');
        // CHANGE URL TO THE INDEX PAGE
        // return $this->renderResponse('index',[
        //     'email' => $email ?? '',
        //     'nonce' => $csrfToken,
        //     'page' => 'login',
        //     'title' => "Krost Business Furniture"
        // ]);

        $seoSettings = [
                'meta_title' =>  $this->site['descriptionSettings']['title'] ?? 'Commercial Office Furniture Australia | Krost Business Furniture',
                'meta_description' => $this->site['descriptionSettings']['meta_description'] ?? "Australia's commercial furniture specialist since 1989 — workstations, seating, desks and joinery designed and supplied nationwide. Sydney, Melbourne & Brisbane.",
                'meta_keywords' => $this->site['descriptionSettings']['meta_keywords'] ?? 'Commercial furniture, office furniture Australia, Krost, workstations, joinery, Sydney Melbourne Brisbane, ISO certified furniture, office chairs, workstations',
        ];
        array_merge($seoSettings, $this->site['seoSettings']??[]);
        $currentUrl =
            'https'
            . '://'
            . $_SERVER['HTTP_HOST']
            . $_SERVER['REQUEST_URI'];
        $imageUrl = $currentUrl . 'img/bg/Krost_Business_Furniture_2026.png';

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'website',
            'name' => "Commercial Office Furniture Australia | Krost Business Furniture",
            'image' => [$imageUrl],
            'description' => "Australia's commercial furniture specialist since 1989 — workstations, seating, desks and joinery designed and supplied nationwide. Sydney, Melbourne & Brisbane",
            'brand' => [
                '@type' => 'Brand',
                'name' => 'Commercial Office Furniture Australia | Krost Business Furniture'
            ],
            'material' => '',
            'url' => $currentUrl
        ];
        
        $productSchema = json_encode(
            $schema,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );
        return $this->renderResponse('index', 
        [
            'email' => $email ?? '',
            'nonce' => $csrfToken,
            'page' => 'login',
            'manufacturingprocessdata' => [
            'videogallerymanufacturingprocess_section_title' => 'Manufacturing Process',
            'videogallerymanufacturingprocess_section_subtitle' => 'Manufacturing Process',
        ],
        'metaData' => $seoSettings,
        'canonical' => $currentUrl,
        'url' => $currentUrl,
        'is_admin' => $this->isAdmin(), 
        'title' => "Krost Business Furniture",
        'og_image'=> $imageUrl,
        'type'=> 'website',
        'product_schema' => $productSchema
        ]);
    }

    public function redirectToIndex(Request $request): Response
    {
        return $this->redirect('/');
    }


    public function subscribeEmail(Request $request): Response
    {
        // validate request is POST
        $requestMethod = $request->getMethod();
        $data = $request->all();

        if (!$this->csrfService->validateToken((string) ($data['nonce'] ?? ''))) {
            $csrfToken = $this->csrfService->getToken();
            // throw new UnauthorizedHttpException('Invalid CSRF token');
            return $this->renderResponse('index', ['page' => 'index', 'nonce' => $csrfToken, 'errors' => ['nonce' => ['Invalid CSRF token']], 'data' => $data]);
        }
        $csrfToken = $this->csrfService->getToken();

        try {
            $data = $request->validate([
                'email' => 'required|email|max:255',
            ]);

            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                throw new ValidationException([
                    'email' => ['The email address format is invalid.']
                ]);
                return $this->redirect('/');
            }

            $subscription = $this->subscriptionRepository->findByEmail($data['email']);
            if($subscription){
                throw new ValidationException(['email' => ['This email is already subscribed']]);
                // return $this->renderResponse('index', ['page' => 'index', 'nonce' => $csrfToken, 'errors' => $errors, 'data' => $data]);
                return $this->redirect('/');
            }

        } catch (ValidationException $e) {
            $errors = $e->getErrors();
            foreach($errors as $key => $error){
                $errors[$key] = implode(PHP_EOL, $error);
            }
            return $this->renderResponse('index', ['page' => 'index', 'nonce' => $csrfToken, 'errors' => $errors, 'data' => $data]);
        }

        $subscription = $this->subscriptionRepository->subscribeEmail($data['email']);
        if(!$subscription){
            return $this->renderResponse('index', ['page' => 'index', 'nonce' => $csrfToken, 'errors' => ['email' => ['Failed to subscribe email']], 'data' => $data]);
        }

        // return $this->redirect('/#subscription-form');
        return $this->renderResponse('index',[
            'email' => $data['email'],
            'nonce' => $csrfToken,
            'page' => 'index',
            'success' => 'Subscription successful',
        ]);
    }

    public function test(): Response
    {
        $this->view->set('test_variable', 'Test Variable');
        return $this->renderResponse('test');
    }
}
