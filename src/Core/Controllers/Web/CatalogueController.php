<?php

declare(strict_types=1);

namespace App\Core\Controllers\Web;

use App\Core\Exceptions\UnauthorizedHttpException;
use App\Core\Exceptions\ValidationException;
use App\Core\Repositories\Service\ServiceRequestRepositoryInterface;
use App\Core\Http\Controller;
use App\Core\Http\Response;
use App\Core\Http\Request;
use App\Core\Services\CsrfService;
use App\Core\Services\RecaptchaService;
use App\Core\Repositories\Email\EmailRepositoryInterface;
use App\Core\Repositories\Site\SiteRepositoryInterface;
use Exception;
use function App\Core\System\utils\env;

/**
 * CatalogueController handles the catalogue page.
 */
class CatalogueController extends Controller
{
    private CsrfService $csrfService;
    private RecaptchaService $recaptchaService;
    private ServiceRequestRepositoryInterface $serviceRequestRepository;
    private EmailRepositoryInterface $emailRepository;

    public function __construct(
        CsrfService $csrfService,
        ServiceRequestRepositoryInterface $serviceRequestRepository,
        EmailRepositoryInterface $emailRepository,
        SiteRepositoryInterface $siteRepository,
        ?RecaptchaService $recaptchaService = null
    ) {
        parent::__construct($siteRepository);
        $this->csrfService = $csrfService;
        $this->recaptchaService = $recaptchaService ?? new RecaptchaService();
        $this->serviceRequestRepository = $serviceRequestRepository;
        $this->emailRepository = $emailRepository;
    }

    private function catalogueViewData(array $extra = []): array
    {
        return array_merge([
            'recaptcha_site_key' => $this->recaptchaService->getSiteKey(),
            'recaptcha_action' => $this->recaptchaService->getCatalogueAction(),
        ], $extra);
    }

    public function index(): Response
    {
        $csrfToken = $this->csrfService->getToken();
        $currentUrl = env('APP_URL') . '/catalogue';            
        $imageUrl = env('APP_URL') . '/media/Components/Catalogue-request-banner.webp';

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Catalogue Page',
            'name' => "Catalogue | Krost Business Furniture",
            'image' => [$imageUrl],
            'description' => 'Browse or request the latest Krost commercial furniture catalogue — the full range of workstations, seating, desks, tables, storage and joinery.',
            'brand' => [
                '@type' => 'Brand',
                'name' => 'Catalogue | Krost Business Furniture'
            ],
            'material' => '',
            'url' => $currentUrl
        ];
        
        $productSchema = json_encode(
            $schema,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );


        return $this->renderResponse('index', $this->catalogueViewData([
            'page' => 'catalogue',
            'nonce' => $csrfToken,
            'is_admin' => $this->isAdmin(),
            'title' => "Krost 2026 Catalogue Out Now! | Krost Business Furniture",
            'metaData' => [
                'meta_title' =>  'Catalogue | Krost Business Furniture',
                'meta_description' => 'Browse or request the latest Krost commercial furniture catalogue — the full range of workstations, seating, desks, tables, storage and joinery.',
                'meta_keywords' => 'Commercial furniture, office furniture Australia, Krost, workstations, joinery, Sydney Melbourne Brisbane, ISO certified furniture, office chairs, workstations',
            ],
            'canonical' => $currentUrl,
            'url' => $currentUrl,
            'og_title' => 'Request the Krost 2026 Workspace Catalogue',
            'og_image'=> $imageUrl,
            'type'=> 'website',
            'product_schema' => $productSchema
        ]));
    }
    public function requestCatalogueConfirmaiton(): Response
    {
        $data = []; // useing serviceRequestRepository to get the data
        return $this->renderResponse('index', ['data' => $data]);
    }

    // public function requestCatalogue(Request $request): Response
    // {
    //     $data = $request->all();
    //     if(!$this->csrfService->validateToken($data['nonce'])){
    //         return $this->renderResponse('request-catalogue', ['page' => 'request-catalogue']);
    //     }
    //     $result = $this->serviceRequestRepository->requestCatalogue($data, isset($result['files']) ? $result['files'] : []);
    //     $redirectUrl = "/catalogue-confirmation/".$result['uuid'];
    //     return $this->redirect($redirectUrl);
    // }
    public function requestCatalogue(Request $request): Response
    {
        $data = $request->all();        
        if (!$this->csrfService->validateToken((string) ($data['nonce'] ?? ''))) {
            $csrfToken = $this->csrfService->getToken();
            throw new UnauthorizedHttpException('Invalid CSRF token');
            return $this->renderResponse('index', $this->catalogueViewData([
                'page' => 'catalogue',
                'nonce' => $csrfToken,
                'errors' => ['nonce' => ['Invalid CSRF token']],
                'data' => $data,
            ]));
        }

        $remoteIp = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? null;
        if (is_string($remoteIp) && str_contains($remoteIp, ',')) {
            $remoteIp = trim(explode(',', $remoteIp)[0]);
        }

        $recaptchaResult = $this->recaptchaService->verify(
            (string) ($data['g-recaptcha-response'] ?? ''),
            is_string($remoteIp) ? $remoteIp : null,
            $this->recaptchaService->getCatalogueAction()
        );
        if (!$recaptchaResult['ok']) {
            return $this->returnCatalogueFormWithErrors(
                ['recaptcha' => $recaptchaResult['message'] ?? 'reCAPTCHA verification failed.'],
                $data
            );
        }

        $csrfToken = $this->csrfService->getToken();
        try {
            $catalogue_format = $data['catalogue_format'];

            $data = $request->validate([
                'form_type' => 'required|string|max:255',
                'catalogue_format' => 'required|in:physical,digital',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone_number' => 'nullable|string|max:255',
                'company' => 'nullable|string|max:255',
                'mailing_address' => 'nullable|string|max:255',
                'state' => 'nullable|string|max:255',
                'source_of_enquiry' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            $errors = $e->getErrors();
            foreach($errors as $key => $error){
                $errors[$key] = implode(PHP_EOL, $error);
            }
            return $this->returnCatalogueFormWithErrors($errors, $data);
        }

        $folder = 'media/Services';
        try {
            $serviceRequest = $this->serviceRequestRepository->requestCatalogue($data, isset($result['files']) ? $result['files'] : [], $folder);            
        } catch (ValidationException $e) {
            $errors = $e->getErrors();
            foreach($errors as $key => $error){
                $errors[$key] = implode(PHP_EOL, $error);
            }
            return $this->returnCatalogueFormWithErrors($errors, $data);
        }

        $redirectUrl = $catalogue_format === 'physical_catalogue' ? "/catalogue-mail-confirmation/".$serviceRequest['data'] : "/catalogue-online-confirmation/".$serviceRequest['data'];
        return $this->redirect($redirectUrl);
    }

    private function returnCatalogueFormWithErrors(array $errors, array $data): Response
    {
        $csrfToken = $this->csrfService->getToken();

        return $this->renderResponse('index', $this->catalogueViewData([
            'page' => 'catalogue',
            'nonce' => $csrfToken,
            'errors' => $errors,
            'data' => $data,
        ]));
    }

    public function catalogueConfirmation(Request $request, string $uuid, ?string $formType = null): Response
    {
        // formType can come from path (/uuid/formType) or query string (?formType=...)
        // $formType = $formType ?? $request->query('formType');
        return $this->renderResponse('catalogue-confirmation', [
            'page' => 'catalogue-confirmation', 
            'uuid' => $uuid,
            'formType' => $formType,
            'is_admin' => $this->isAdmin()
        ]);
    }

}
