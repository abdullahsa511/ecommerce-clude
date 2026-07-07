<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use App\Core\Repositories\Service\ServiceRequestRepositoryInterface;
use App\Core\Repositories\Media\MediaRepositoryInterface;
use App\Core\Services\AuthService;
use App\Core\Services\RecaptchaService;

class ServiceRequestController extends ApiController
{
    private const EMAIL_ALLOWED_UPLOAD_EXTENSIONS = ['pdf', 'png', 'jpg', 'jpeg', 'docx', 'csv', 'xlsx'];

    private const EMAIL_ALLOWED_UPLOAD_MIME_TYPES = [
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

    private const ALLOWED_UPLOAD_EXTENSIONS = ['pdf', 'png', 'jpg', 'jpeg', 'dwg', 'docx'];

    private const ALLOWED_UPLOAD_MIME_TYPES = [
        'application/pdf',
        'image/png',
        'image/jpeg',
        'image/jpg',
        'application/dwg',
        'image/vnd.dwg',
        'application/x-dwg',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/docx',
    ];

    private ServiceRequestRepositoryInterface $serviceRequestRepository;
    private MediaRepositoryInterface $mediaRepository;
    private AuthService $authService;
    private RecaptchaService $recaptchaService;

    public function __construct(
            ServiceRequestRepositoryInterface $serviceRequestRepository,
            MediaRepositoryInterface $mediaRepository,
            AuthService $authService,
            ?RecaptchaService $recaptchaService = null
        ) {
        parent::__construct();
        $this->serviceRequestRepository = $serviceRequestRepository;
        $this->mediaRepository = $mediaRepository;
        $this->authService = $authService;
        $this->recaptchaService = $recaptchaService ?? new RecaptchaService();
    }


    public function serviceRequests(Request $request): Response
    {
        // $data = $request->all();
        $serviceRequests = $this->serviceRequestRepository->getServiceRequests();
        return $this->renderResponse($serviceRequests);
    }

    public function deleteServiceRequest(Request $request, $id): Response
    {
        $this->serviceRequestRepository->delete((int) $id);
        return $this->renderResponse(['message' => 'Service request deleted successfully']);
    }

    /**
     * Create a new service request
     *
     * @param Request $request
     * @return Response
     */
    public function create(Request $request): Response
    {
        $data = $request->all();

        if (trim((string) ($data['submission_type'] ?? '')) !== '') {
            $remoteIp = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? null;
            if (is_string($remoteIp) && str_contains($remoteIp, ',')) {
                $remoteIp = trim(explode(',', $remoteIp)[0]);
            }

            $recaptchaResult = $this->recaptchaService->verify(
                (string) ($data['g-recaptcha-response'] ?? ''),
                is_string($remoteIp) ? $remoteIp : null,
                $this->recaptchaService->getProjectAction()
            );
            if (!$recaptchaResult['ok']) {
                return $this->renderError(
                    422,
                    $recaptchaResult['message'] ?? 'reCAPTCHA verification failed.'
                );
            }
        }

        $folder = 'media/Services/';
        $size = [
            'width' => 945,
            'height' => 630,
        ];
        $result = null;
        if ($request->files() || isset($_FILES['files'])) {
            $rawFiles = $request->file('files') ?? ($_FILES['files'] ?? []);
            $files = $this->normalizeUploadedFiles($rawFiles);

            if (!count($files)) {
                return $this->renderError(422, 'No files uploaded');
            }

            $fileTypeValidation = $this->validateAllowedFileTypes(
                $files,
                self::EMAIL_ALLOWED_UPLOAD_EXTENSIONS,
                self::EMAIL_ALLOWED_UPLOAD_MIME_TYPES,
                'Only PDF, PNG, JPG, DOCX, CSV, and XLSX files are allowed'
            );
            if (!($fileTypeValidation['success'] ?? false)) {
                return $this->renderError(422, (string) ($fileTypeValidation['message'] ?? 'Invalid file type'));
            }

            $uploadData = [
                'files' => $files,
                'upload_dir' => $request->input('upload_dir', $folder)
            ];

            // $result = $this->mediaRepository->upload($uploadData, $size, $folder);
            $result = $this->mediaRepository->upload($uploadData, $size, $folder, null, false, 15);
            if (!$result) {
                return $this->renderError(500, 'Failed to upload media');
            }
           
        }
        $serviceRequest = $this->serviceRequestRepository->createRequest($data, isset($result['files']) ? $result['files'] : []);
        return $this->renderResponse($serviceRequest);
    }

    public function accountCreateRequest(Request $request): Response
    {
        $data = $request->all();
        $user = $this->authService->getAuthUser();

        if (!$user) {
            return $this->renderError(401, 'Unauthorized');
        }

        $remoteIp = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? null;
        if (is_string($remoteIp) && str_contains($remoteIp, ',')) {
            $remoteIp = trim(explode(',', $remoteIp)[0]);
        }

        $recaptchaResult = $this->recaptchaService->verify(
            (string) ($data['g-recaptcha-response'] ?? ''),
            is_string($remoteIp) ? $remoteIp : null,
            $this->recaptchaService->getServiceAction()
        );
        if (!$recaptchaResult['ok']) {
            return $this->renderError(
                422,
                $recaptchaResult['message'] ?? 'reCAPTCHA verification failed.'
            );
        }

        if ($user) {
            if ($user->user_id !== (int) $data['user_id']) {
                return $this->renderError(401, 'Unauthorized');
            }
            if (empty($data['email']) && !empty($user->email)) {
                $data['email'] = $user->email;
            }
        }

        $folder = 'media/Services/';
        $size = [
            'width' => 945,
            'height' => 630,
        ];
        $result = null;
        if ($request->files() || isset($_FILES['files'])) {
            $rawFiles = $request->file('files') ?? ($_FILES['files'] ?? []);
            $files = $this->normalizeUploadedFiles($rawFiles);

            if (!count($files)) {
                return $this->renderError(422, 'No files uploaded');
            }

            $fileTypeValidation = $this->validateAllowedFileTypes(
                $files,
                self::ALLOWED_UPLOAD_EXTENSIONS,
                self::ALLOWED_UPLOAD_MIME_TYPES,
                'Only PDF, PNG, JPG, DWG, and DOCX files are allowed'
            );
            if (!($fileTypeValidation['success'] ?? false)) {
                return $this->renderError(422, (string) ($fileTypeValidation['message'] ?? 'Invalid file type'));
            }

            // $uploadValidation = $this->validateUploadConstraints($files);
            // if (!($uploadValidation['success'] ?? false)) {
            //     return $this->renderError(422, (string) ($uploadValidation['message'] ?? 'Invalid upload payload'));
            // }
            $uploadData = [
                'files' => $files,
                'upload_dir' => $request->input('upload_dir', $folder)
            ];

            // $result = $this->mediaRepository->upload($uploadData, $size, $folder);
            $result = $this->mediaRepository->upload($uploadData, $size, $folder, null, false, 15);
            if (!$result) {
                return $this->renderError(500, 'Failed to upload media');
            }
           
        }
        $serviceRequest = $this->serviceRequestRepository->accountCreateRequest($data, isset($result['files']) ? $result['files'] : []);
        return $this->renderResponse($serviceRequest);
    }

    public function contactSalesGetInTouch(Request $request): Response
    {
        $data = $request->all();
        $folder = 'media/Services';
        $size = [
            'width' => 945,
            'height' => 630,
        ];
        $result = null;
        if ($request->files() || isset($_FILES['files'])) {
            $rawFiles = $request->file('files') ?? ($_FILES['files'] ?? []);
            $files = $this->normalizeUploadedFiles($rawFiles);

            if (!count($files)) {
                return $this->renderError(422, 'No files uploaded');
            }
            // $uploadValidation = $this->validateUploadConstraints($files);
            // if (!($uploadValidation['success'] ?? false)) {
            //     return $this->renderError(422, (string) ($uploadValidation['message'] ?? 'Invalid upload payload'));
            // }
            $uploadData = [
                'files' => $files,
                'upload_dir' => $request->input('upload_dir', $folder)
            ];

            // $result = $this->mediaRepository->upload($uploadData, $size, $folder);
            $result = $this->mediaRepository->upload($uploadData, $size, $folder, null, false, 15);
            if (!$result) {
                return $this->renderError(500, 'Failed to upload media');
            }
           
        }
        $serviceRequest = $this->serviceRequestRepository->contactSalesGetInTouch($data, isset($result['files']) ? $result['files'] : []);
        return $this->renderResponse($serviceRequest);
    }

    /**
     * Normalize PHP's multi-file payload shape into a flat descriptor list.
     *
     * Supports incoming structure from files[]:
     * ['name' => [], 'type' => [], 'tmp_name' => [], 'error' => [], 'size' => []]
     * and already-normalized file entries.
     */
    private function normalizeUploadedFiles(array $files): array
    {
        if (empty($files)) {
            return [];
        }

        // Single/multi files sent under one field (e.g. files[]).
        if (isset($files['name'])) {
            if (is_array($files['name'])) {
                $normalized = [];
                foreach (array_keys($files['name']) as $index) {
                    $normalized[] = [
                        'name' => $files['name'][$index] ?? '',
                        'type' => $files['type'][$index] ?? '',
                        'tmp_name' => $files['tmp_name'][$index] ?? '',
                        'error' => $files['error'][$index] ?? UPLOAD_ERR_NO_FILE,
                        'size' => $files['size'][$index] ?? 0,
                    ];
                }
                return $normalized;
            }

            return [$files];
        }

        // Already split by keys (e.g. ['0' => [...], '1' => [...]]).
        $normalized = [];
        foreach ($files as $file) {
            if (is_array($file) && isset($file['name'])) {
                $normalized[] = $file;
            }
        }

        return $normalized;
    }

    /**
     * Validate allowed file extensions and MIME types for service request uploads.
     *
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
     * Validate controller-level upload limits before repository upload.
     *
     * @param array<int, array<string, mixed>> $files
     * @return array{success: bool, errors: array, message: ?string}
     */
    private function validateUploadConstraints(array $files): array
    {
        $maxFiles = 3;
        $maxTotalSizeInBytes = 15 * 1024 * 1024; // 15MB total

        if (count($files) > $maxFiles) {
            return ['success' => false, 'errors' => [], 'message' => 'You can upload up to 3 files only'];
        }

        $totalSize = 0;
        foreach ($files as $file) {
            $uploadError = (int) ($file['error'] ?? UPLOAD_ERR_OK);
            if ($uploadError !== UPLOAD_ERR_OK) {
                $message = in_array($uploadError, [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)
                    ? 'Total upload size must not exceed 15 MB'
                    : 'One or more files could not be uploaded';

                return ['success' => false, 'errors' => [], 'message' => $message];
            }

            $size = (int) ($file['size'] ?? 0);
            $totalSize += max(0, $size);
        }

        if ($totalSize > $maxTotalSizeInBytes) {
            return ['success' => false, 'errors' => [], 'message' => 'Total upload size must not exceed 15 MB'];
        }

        return ['success' => true, 'errors' => [], 'message' => null];
    }
} 