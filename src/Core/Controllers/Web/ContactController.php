<?php

declare(strict_types=1);

namespace App\Core\Controllers\Web;

use App\Core\Exceptions\UnauthorizedHttpException;
use App\Core\Exceptions\ValidationException;
use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Services\CsrfService;
use App\Core\Services\RecaptchaService;
use App\Core\Repositories\Media\MediaRepositoryInterface;
use App\Core\Repositories\Service\ServiceRequestRepositoryInterface;
use App\Core\Repositories\Site\SiteRepositoryInterface;

use function App\Core\System\utils\env;

/**
 * ContactController handles the contact sales page.
 */
class ContactController extends Controller
{
    private const ALLOWED_UPLOAD_EXTENSIONS = ['pdf', 'png', 'jpg', 'jpeg', 'docx', 'csv', 'xlsx'];

    private const ALLOWED_UPLOAD_MIME_TYPES = [
        'application/pdf',
        'image/png',
        'image/jpeg',
        'image/jpg',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/docx',
        'text/csv',
        'application/csv',
        'application/x-csv',
        'text/comma-separated-values',
        'text/x-comma-separated-values',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ];

    private CsrfService $csrfService;
    private RecaptchaService $recaptchaService;
    private MediaRepositoryInterface $mediaRepository;
    private ServiceRequestRepositoryInterface $serviceRequestRepository;

    public function __construct(
        CsrfService $csrfService,
        MediaRepositoryInterface $mediaRepository,
        ServiceRequestRepositoryInterface $serviceRequestRepository,
        SiteRepositoryInterface $siteRepository,
        ?RecaptchaService $recaptchaService = null
    ) {
        parent::__construct($siteRepository);
        $this->csrfService = $csrfService;
        $this->recaptchaService = $recaptchaService ?? new RecaptchaService();
        $this->mediaRepository = $mediaRepository;
        $this->serviceRequestRepository = $serviceRequestRepository;
    }

    private function contactViewData(array $extra = []): array
    {
        return array_merge([
            'recaptcha_site_key' => $this->recaptchaService->getSiteKey(),
            'recaptcha_action' => $this->recaptchaService->getContactAction(),
        ], $extra);
    }

    public function index(): Response
    {
        $csrfToken = $this->csrfService->getToken();
        return $this->renderResponse('contact-sales', [
            'page' => 'contact-sales', 
            'nonce' => $csrfToken, 
            'is_admin' => $this->isAdmin(), 
            'title' => "Contact Sales | Krost Business Furniture"
        ]);
    }

    public function state()
    {
        $catalogue_format = 'physical_catalogue';
        
        // Use a structured, realistic US address for testing
        $mailing_address = "Morisset Rd, Kenny ACT 2911, Australia";

        $stateData = null;
        if ($catalogue_format === 'physical_catalogue') {
            $stateData = $this->serviceRequestRepository->getStateFromAddress($mailing_address);
            
            if ($stateData) {
                echo "Full State Name: " . $stateData['long_name'] . "\n";
                echo "State Code: " . $stateData['short_name'] . "\n";
            } else {
                echo "Failed to retrieve state information.\n";
            }
        }

        return $stateData;
    }

    public function contactUs(): Response
    {
        $csrfToken = $this->csrfService->getToken();
        // return $this->renderResponse('contact-us', $this->contactViewData([
        //     'page' => 'contact',
        //     'nonce' => $csrfToken,
        //     'is_admin' => $this->isAdmin(),
        //     'title' => "Contact Us | Krost Business Furniture",
        // ]));


        $currentUrl = env('APP_URL') . '/contact-us';            
        $imageUrl =  env('APP_URL') . '/img/bg/Krost_Business_Furniture_2026.png';

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Catalogue Page',
            'name' => "Contact Us | Sydney, Melbourne & Brisbane | Krost Business Furniture",
            'image' => [$imageUrl],
            'description' => 'Contact Krost Business Furniture — showrooms and offices in Sydney, Melbourne and Brisbane. Talk to our team about your next commercial fit-out.',
            'brand' => [
                '@type' => 'Brand',
                'name' => 'Contact Us | Sydney, Melbourne & Brisbane | Krost Business Furniture'
            ],
            'material' => '',
            'url' => $currentUrl
        ];
        
        $productSchema = json_encode(
            $schema,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );

        return $this->renderResponse('contact-us', $this->contactViewData([
            'page' => 'contact',
            'nonce' => $csrfToken,
            'is_admin' => $this->isAdmin(),
            'title' => "Krost 2026 Catalogue Out Now! | Krost Business Furniture",
            'metaData' => [
                'meta_title' =>  'Contact Us | Sydney, Melbourne & Brisbane | Krost Business Furniture',
                'meta_description' => 'Contact Krost Business Furniture — showrooms and offices in Sydney, Melbourne and Brisbane. Talk to our team about your next commercial fit-out.',
                'meta_keywords' => 'Commercial furniture, office furniture Australia, Krost, workstations, joinery, Sydney Melbourne Brisbane, ISO certified furniture, office chairs, workstations',
            ],
            'canonical' => $currentUrl,
            'url' => $currentUrl,
            'og_image'=> $imageUrl,
            'type'=> 'website',
            'product_schema' => $productSchema
        ]));
    }


    public function contactUsGetInTouch(Request $request): Response
    {
        $data = $request->all();
        $csrfToken = $this->csrfService->getToken();
        if (!$this->csrfService->validateToken((string) ($data['nonce'] ?? ''))) {
            $csrfToken = $this->csrfService->getToken();
            // throw new UnauthorizedHttpException('Invalid CSRF token');
            return $this->renderResponse('contact-us', $this->contactViewData([
                'page' => 'contact-us',
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
            $this->recaptchaService->getContactAction()
        );
        if (!$recaptchaResult['ok']) {
            return $this->returnContactFormWithErrors(
                ['recaptcha' => $recaptchaResult['message'] ?? 'reCAPTCHA verification failed.'],
                $data
            );
        }

        try {
            $data = $request->validate([
                'form_type' => 'nullable|string|max:255',
                'catalogue-format' => 'nullable|string|max:255',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email',
                'phone_number' => 'nullable|string|max:255',
                'company' => 'nullable|string|max:255',
                'mailing_address' => 'nullable|string|max:255',
                'add_text' => 'nullable|string|max:65535',
                'project_details' => 'nullable|string',
                'state' => 'nullable|string',
            ]);
        } catch (ValidationException $e) {
            $errors = $e->getErrors();
            foreach($errors as $key => $error){
                $errors[$key] = implode(PHP_EOL, $error);
            }
            return $this->renderResponse('contact-us', $this->contactViewData([
                'page' => 'contact-us',
                'nonce' => $csrfToken,
                'errors' => $errors,
                'data' => $data,
            ]));
        }

        $size = [
            'width' => 400,
            'height' => 420,
        ];

        $folder = 'media/Services/';
        $uploadedFiles = [];

        if ($request->files() || isset($_FILES['files'])) {
            $rawFiles = $request->file('files') ?? ($_FILES['files'] ?? []);
            if (!is_array($rawFiles)) {
                $rawFiles = [];
            }

            if ($this->hasFileUploadAttempt($rawFiles)) {
                $files = $this->normalizeUploadedFiles($rawFiles);

                if (!count($files)) {
                    return $this->returnContactFormWithErrors(
                        ['files' => 'No files uploaded'],
                        $data
                    );
                }

                $fileTypeValidation = $this->validateAllowedFileTypes(
                    $files,
                    self::ALLOWED_UPLOAD_EXTENSIONS,
                    self::ALLOWED_UPLOAD_MIME_TYPES,
                    'Only PDF, PNG, JPG, DOCX, CSV, and XLSX files are allowed'
                );
                if (!($fileTypeValidation['success'] ?? false)) {
                    return $this->returnContactFormWithErrors(
                        [
                            'files' => (string) ($fileTypeValidation['message'] ?? 'Invalid file type'),
                        ],
                        $data
                    );
                }

                $uploadValidation = $this->validateUploadConstraints($files);
                if (!($uploadValidation['success'] ?? false)) {
                    return $this->returnContactFormWithErrors(
                        [
                            'files' => (string) ($uploadValidation['message'] ?? 'Invalid upload'),
                        ],
                        $data
                    );
                }

                $fileData = [
                    'files' => $files,
                    'upload_dir' => $request->input('upload_dir', $folder),
                ];

                $uploadResult = $this->mediaRepository->upload($fileData, [], $folder, null, false, 15);

                if (
                    !empty($uploadResult['error'])
                    || !isset($uploadResult['files'])
                    || (is_array($uploadResult['files']) ? count($uploadResult['files']) === 0 : !$uploadResult['files'])
                ) {
                    $messages = [];
                    if (!empty($uploadResult['error']) && is_array($uploadResult['error'])) {
                        foreach ($uploadResult['error'] as $fileErrors) {
                            if (!is_array($fileErrors)) {
                                continue;
                            }
                            foreach ($fileErrors as $msg) {
                                if (is_string($msg) && $msg !== '') {
                                    $messages[] = $msg;
                                }
                            }
                        }
                    }
                    $fileMessage = $messages !== []
                        ? implode(PHP_EOL, array_values(array_unique($messages)))
                        : 'File upload failed. Please try again.';

                    return $this->returnContactFormWithErrors(['files' => $fileMessage], $data);
                }

                $uploadedFiles = is_array($uploadResult['files']) ? $uploadResult['files'] : [];
            }
        }

        try {
            $serviceRequest = $this->serviceRequestRepository->requestCatalogue($data, $uploadedFiles, $folder);
        } catch (ValidationException $e) {
            $errors = $e->getErrors();
            foreach ($errors as $key => $error) {
                $errors[$key] = implode(PHP_EOL, $error);
            }
            return $this->returnContactFormWithErrors($errors, $data);
        }

        if (!($serviceRequest['success'] ?? false)) {
            return $this->returnContactFormWithErrors(
                [
                    'files' => (string) ($serviceRequest['message'] ?? 'Request failed. Please try again.'),
                ],
                $data
            );
        }

        // $redirectUrl = '/catalogue-confirmation/' . ($serviceRequest['data'] ?? ''); // old route
        $redirectUrl = '/contact-get-in-touch/' . ($serviceRequest['data'] ?? ''); // new route
        return $this->redirect($redirectUrl);
    }


    public function downloadRequestImages(Request $request): Response
    {
        $uuid = $request->query('uuid');
        $link = $request->query('file') ?? $request->query('link');
        $filePath = $this->serviceRequestRepository->downloadRequestImages((string)$uuid, (string)$link);

        if ($filePath === '') {
            return $this->redirect('/');
        }

        $safeFilePath = htmlspecialchars($filePath, ENT_QUOTES, 'UTF-8');
        $safeFileName = htmlspecialchars((string) basename(parse_url($filePath, PHP_URL_PATH) ?? ''), ENT_QUOTES, 'UTF-8');

        $content = '<!doctype html>
                <html lang="en">
                <head>
                    <meta charset="utf-8">
                    <title>Downloading...</title>
                </head>
                <body>
                    <p>Your download will start automatically.</p>
                    <script>
                        (function () {
                            var link = document.createElement("a");
                            link.href = "' . $safeFilePath . '";
                            link.download = "' . $safeFileName . '";
                            document.body.appendChild(link);
                            link.click();
                            link.remove();
                            window.setTimeout(function () {
                                window.location.replace("/");
                            }, 800);
                        })();
                    </script>
                    <noscript>
                        <meta http-equiv="refresh" content="2;url=/">
                        <a href="' . $safeFilePath . '" download="' . $safeFileName . '">Download file</a>
                    </noscript>
                </body>
                </html>';

        return $this->response
            ->withStatus(200)
            ->withHeader('Content-Type', 'text/html; charset=UTF-8')
            ->withBody($content);
    }
    private function returnContactFormWithErrors(array $errors, array $data): Response
    {
        $csrfToken = $this->csrfService->getToken();

        return $this->renderResponse('contact-us', $this->contactViewData([
            'page' => 'contact-us',
            'nonce' => $csrfToken,
            'errors' => $errors,
            'data' => $data,
        ]));
    }

    private function hasFileUploadAttempt(array $rawFiles): bool
    {
        if ($rawFiles === []) {
            return false;
        }

        if (isset($rawFiles['name'])) {
            if (is_array($rawFiles['name'])) {
                foreach ($rawFiles['name'] as $name) {
                    if (is_string($name) && trim($name) !== '') {
                        return true;
                    }
                }

                return false;
            }

            return trim((string) $rawFiles['name']) !== '';
        }

        foreach ($rawFiles as $file) {
            if (is_array($file) && trim((string) ($file['name'] ?? '')) !== '') {
                return true;
            }
        }

        return false;
    }

    private function normalizeUploadedFiles(array $files): array
    {
        if (empty($files)) {
            return [];
        }

        $normalized = [];

        // Single/multi files sent under one field (e.g. files[]).
        if (isset($files['name'])) {
            if (is_array($files['name'])) {
                foreach (array_keys($files['name']) as $index) {
                    $normalized[] = [
                        'name' => $files['name'][$index] ?? '',
                        'type' => $files['type'][$index] ?? '',
                        'tmp_name' => $files['tmp_name'][$index] ?? '',
                        'error' => $files['error'][$index] ?? UPLOAD_ERR_NO_FILE,
                        'size' => $files['size'][$index] ?? 0,
                    ];
                }
            } else {
                $normalized[] = $files;
            }
        } else {
            // Already split by keys (e.g. ['0' => [...], '1' => [...]]).
            foreach ($files as $file) {
                if (is_array($file) && isset($file['name'])) {
                    $normalized[] = $file;
                }
            }
        }

        return array_values(array_filter($normalized, static function (array $file): bool {
            return ((int) ($file['error'] ?? UPLOAD_ERR_NO_FILE)) === UPLOAD_ERR_OK
                && trim((string) ($file['name'] ?? '')) !== ''
                && trim((string) ($file['tmp_name'] ?? '')) !== '';
        }));
    }

    /**
     * @param array<int, array<string, mixed>> $files
     * @param list<string> $allowedExtensions
     * @param list<string> $allowedMimeTypes
     * @return array{success: bool, errors: array, message: ?string}
     */
    private function validateAllowedFileTypes(
        array $files,
        array $allowedExtensions,
        array $allowedMimeTypes,
        string $errorMessage = 'Invalid file type'
    ): array {
        foreach ($files as $file) {
            $extension = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
            if ($extension === '' || !in_array($extension, $allowedExtensions, true)) {
                return [
                    'success' => false,
                    'errors' => [],
                    'message' => $errorMessage,
                ];
            }

            $mimeType = strtolower((string) ($file['type'] ?? ''));
            if ($mimeType !== '' && !in_array($mimeType, $allowedMimeTypes, true)) {
                return [
                    'success' => false,
                    'errors' => [],
                    'message' => $errorMessage,
                ];
            }
        }

        return ['success' => true, 'errors' => [], 'message' => null];
    }

    /**
     * @param array<int, array<string, mixed>> $files
     * @return array{success: bool, errors: array, message: ?string}
     */
    private function validateUploadConstraints(array $files): array
    {
        $maxFiles = 3;
        $maxFileSizeInBytes = 15 * 1024 * 1024;

        if (count($files) > $maxFiles) {
            return ['success' => false, 'errors' => [], 'message' => 'You can upload up to 3 files only'];
        }

        foreach ($files as $file) {
            $uploadError = (int) ($file['error'] ?? UPLOAD_ERR_OK);
            if ($uploadError !== UPLOAD_ERR_OK) {
                $message = in_array($uploadError, [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)
                    ? 'Each file must be 15 MB or less'
                    : 'One or more files could not be uploaded';

                return ['success' => false, 'errors' => [], 'message' => $message];
            }

            if ((int) ($file['size'] ?? 0) > $maxFileSizeInBytes) {
                return ['success' => false, 'errors' => [], 'message' => 'Each file must be 15 MB or less'];
            }
        }

        return ['success' => true, 'errors' => [], 'message' => null];
    }
}
