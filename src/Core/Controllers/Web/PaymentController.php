<?php

declare(strict_types=1);

namespace App\Core\Controllers\Web;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Middlewares\PaymentSecurityMiddleware;
use App\Core\Repositories\Payment\PaymentIntentRepositoryInterface;
use App\Core\Repositories\Payment\PaymentInvoiceRepositoryInterface;
use App\Core\Repositories\Payment\PaymentOrganisationRepositoryInterface;
use App\Core\Repositories\Payment\PaymentRateLimitRepositoryInterface;
use App\Core\Repositories\Site\SiteRepositoryInterface;
use App\Core\Services\CsrfService;
use App\Core\Services\NabPaymentService;
use App\Core\Services\Payment\NabGatewayException;
use App\Core\Services\Payment\PaymentDebugLog;
use App\Core\Services\Payment\PaymentValidation;
use Throwable;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

use function App\Core\System\utils\env;

/**
 * NAB Unified Checkout payment pages and secure API endpoints.
 */
class PaymentController extends Controller
{
    private const CAPTURE_RATE_LIMIT = 15;
    private const PAY_RATE_LIMIT = 8;
    private const RATE_WINDOW_SECONDS = 60;

    private ?Environment $twig = null;

    public function __construct(
        private NabPaymentService $nabPaymentService,
        private PaymentOrganisationRepositoryInterface $organisations,
        private PaymentIntentRepositoryInterface $paymentIntents,
        private PaymentInvoiceRepositoryInterface $paymentInvoices,
        private PaymentRateLimitRepositoryInterface $rateLimiter,
        private CsrfService $csrfService,
        private PaymentSecurityMiddleware $paymentSecurity,
        protected SiteRepositoryInterface $siteRepository
    ) {
        parent::__construct($siteRepository);
    }

    public function showPay(Request $request): Response
    {
        return $this->show($request, 'pay');
    }

    public function showPayment(Request $request): Response
    {
        return $this->show($request, 'payment');
    }

    public function showMakePayment(Request $request): Response
    {
        return $this->show($request, 'makepayment');
    }

    public function captureContext(Request $request): Response
    {
        return $this->paymentSecurity->applySecurityHeaders(
            $request,
            $this->handleCaptureContext($request)
        );
    }

    public function pay(Request $request): Response
    {
        return $this->paymentSecurity->applySecurityHeaders(
            $request,
            $this->handlePay($request)
        );
    }

    public function createIntent(Request $request): Response
    {
        return $this->paymentSecurity->applySecurityHeaders(
            $request,
            $this->handleCreateIntent($request)
        );
    }

    private function show(Request $request, string $slug): Response
    {
        $org = $this->organisations->getBySlug($slug);
        if ($org === null) {
            return $this->renderPaymentPage([
                'screen' => 'invalid',
                'slug' => $slug,
            ]);
        }

        $brand = $this->organisations->getBrandConfig();
        $mode = $this->nabPaymentService->resolveMode($slug);
        $token = trim((string) $request->query('token'));

        if ($token !== '') {
            $intent = $this->resolveIntentFromToken($token, $slug);
            if ($intent === null) {
                return $this->renderPaymentPage([
                    'screen' => 'invalid',
                    'slug' => $slug,
                    'merchant_name' => $brand['merchantName'],
                    'support_email' => $brand['supportEmail'],
                ]);
            }

            return $this->renderPaymentPage($this->buildPageContext($slug, $org, $brand, $mode, $intent));
        }

        return $this->renderPaymentPage($this->buildManualEntryContext($slug, $org, $brand, $mode));
    }

    /**
     * @param array<string, mixed>|null $intent
     * @return array<string, mixed>|null
     */
    private function resolveIntentFromToken(string $token, string $slug): ?array
    {
        $intent = $this->paymentIntents->activateSignedToken($token);
        if ($intent === null) {
            return null;
        }

        if ($intent['slug'] !== $slug) {
            return null;
        }

        if (!empty($intent['paid'])) {
            return null;
        }

        if ($intent['invoice_type'] !== 'manual' && $intent['invoice_id'] > 0) {
            $invoice = $this->paymentInvoices->findPayableByReference($intent['reference'], $intent['amount']);
            if ($invoice === null || !empty($invoice['paid']) || !empty($invoice['expired'])) {
                return null;
            }

            $intent['amount'] = $invoice['amount'];
            $intent['email'] = $invoice['email'] !== '' ? $invoice['email'] : $intent['email'];
            $this->paymentIntents->storeIntent($intent, max(60, (int) $intent['exp'] - time()));
        }

        return $intent;
    }

    /**
     * @param array<string, mixed> $org
     * @param array{merchantName: string, supportEmail: string, requireEmail: bool} $brand
     * @param array{mock: bool} $mode
     * @param array<string, mixed> $intent
     * @return array<string, mixed>
     */
    private function buildPageContext(string $slug, array $org, array $brand, array $mode, array $intent): array
    {
        return [
            'screen' => 'details',
            'slug' => $slug,
            'state_label' => $org['label'],
            'currency' => $org['currency'],
            'merchant_name' => $brand['merchantName'],
            'support_email' => $brand['supportEmail'],
            'require_email' => $brand['requireEmail'],
            'mock' => $mode['mock'],
            'intent_id' => $intent['intent_id'],
            'amount' => $intent['amount'],
            'reference' => $intent['reference'],
            'email' => $intent['email'],
            'readonly_fields' => true,
            'nonce' => $this->csrfService->getToken(),
            'title' => $brand['merchantName'] . ' — Secure payment',
            'payment_debug' => $this->exposePaymentErrors(),
            'nab_environment' => strtolower((string) (env('NAB_ENVIRONMENT') ?: 'test')),
        ];
    }

    /**
     * @param array<string, mixed> $org
     * @param array{merchantName: string, supportEmail: string, requireEmail: bool} $brand
     * @param array{mock: bool} $mode
     * @return array<string, mixed>
     */
    private function buildManualEntryContext(string $slug, array $org, array $brand, array $mode): array
    {
        $intentId = '';
        if ($mode['mock']) {
            $intentId = bin2hex(random_bytes(16));
            $this->paymentIntents->storeIntent([
                'intent_id' => $intentId,
                'slug' => $slug,
                'reference' => '',
                'amount' => '',
                'currency' => $org['currency'],
                'email' => '',
                'invoice_type' => 'manual',
                'invoice_id' => 0,
                'exp' => time() + 3600,
                'paid' => false,
                'manual_mock' => true,
            ], 3600);
        }

        return [
            'screen' => 'details',
            'slug' => $slug,
            'state_label' => $org['label'],
            'currency' => $org['currency'],
            'merchant_name' => $brand['merchantName'],
            'support_email' => $brand['supportEmail'],
            'require_email' => $brand['requireEmail'],
            'mock' => $mode['mock'],
            'intent_id' => $intentId,
            'amount' => '',
            'reference' => '',
            'email' => '',
            'readonly_fields' => false,
            'nonce' => $this->csrfService->getToken(),
            'title' => $brand['merchantName'] . ' — Secure payment' . ($mode['mock'] ? ' (demo)' : ''),
            'payment_debug' => $this->exposePaymentErrors(),
            'nab_environment' => strtolower((string) (env('NAB_ENVIRONMENT') ?: 'test')),
        ];
    }

    private function handleCreateIntent(Request $request): Response
    {
        if (!$this->assertSameOrigin($request)) {
            return $this->json(['error' => 'Invalid request origin.'], 403);
        }

        if (!$this->rateLimiter->attempt('intent', $this->clientIp($request), self::CAPTURE_RATE_LIMIT, self::RATE_WINDOW_SECONDS)) {
            return $this->json(['error' => 'Too many requests. Please wait and try again.'], 429);
        }

        $nonce = (string) ($request->input('nonce') ?? $request->input('csrf_token') ?? '');
        if (!$this->csrfService->validateToken($nonce)) {
            return $this->json(['error' => 'Security token expired. Refresh the page and try again.'], 403);
        }

        $slug = (string) ($request->input('slug') ?? '');
        $org = $this->organisations->getBySlug($slug);
        if ($org === null) {
            return $this->json(['error' => 'Unknown payment page.'], 404);
        }

        $amount = PaymentValidation::parseAmount($request->input('amount'));
        if (!$amount['ok']) {
            return $this->json(['error' => $amount['error']], 400);
        }

        $reference = PaymentValidation::validateReference($request->input('reference'));
        if (!$reference['ok']) {
            return $this->json(['error' => $reference['error']], 400);
        }

        $brand = $this->organisations->getBrandConfig();
        $email = trim((string) ($request->input('email') ?? ''));
        if ($brand['requireEmail'] && !PaymentValidation::isValidEmail($email)) {
            return $this->json(['error' => 'Enter a valid email.'], 400);
        }

        try {
            $token = $this->paymentIntents->signPaymentIntent([
                'slug' => $slug,
                'amount' => $amount['value'],
                'reference' => $reference['value'],
                'currency' => $org['currency'],
                'email' => $email,
                'invoice_type' => 'manual',
                'invoice_id' => 0,
                'ttl_seconds' => 3600,
            ]);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }

        $path = $this->paymentPathForSlug($slug);

        return $this->json([
            'redirect' => $path . '?token=' . rawurlencode($token),
        ]);
    }

    private function paymentPathForSlug(string $slug): string
    {
        return match ($slug) {
            'pay' => '/pay',
            'payment' => '/payment',
            'makepayment' => '/makepayment',
            default => '/pay',
        };
    }

    private function handleCaptureContext(Request $request): Response
    {
        if (!$this->assertSameOrigin($request)) {
            return $this->json(['error' => 'Invalid request origin.'], 403);
        }

        if (!$this->rateLimiter->attempt('capture', $this->clientIp($request), self::CAPTURE_RATE_LIMIT, self::RATE_WINDOW_SECONDS)) {
            return $this->json(['error' => 'Too many requests. Please wait and try again.'], 429);
        }

        $nonce = (string) ($request->input('nonce') ?? $request->input('csrf_token') ?? '');
        if (!$this->csrfService->validateToken($nonce)) {
            return $this->json(['error' => 'Security token expired. Refresh the page and try again.'], 403);
        }

        $slug = (string) ($request->input('slug') ?? '');
        $intentId = (string) ($request->input('intent_id') ?? '');
        $org = $this->organisations->getBySlug($slug);
        if ($org === null) {
            return $this->json(['error' => 'Unknown payment page.'], 404);
        }

        $intent = $this->resolveIntentForApi($request, $slug, $intentId);
        if ($intent instanceof Response) {
            return $intent;
        }

        try {
            $targetOrigin = $this->resolveTargetOrigin($request);

            PaymentDebugLog::log('api.capture_context.start', [
                'slug' => $slug,
                'intent_id' => $intentId,
                'amount' => $intent['amount'],
                'reference' => $intent['reference'],
            ]);

            $result = $this->nabPaymentService->createCaptureContext($slug, [
                'amount' => $intent['amount'],
                'reference' => $intent['reference'],
                'currency' => $intent['currency'],
                'targetOrigin' => $targetOrigin,
                'email' => (string) ($intent['email'] ?? ''),
            ]);

            $sessionId = bin2hex(random_bytes(16));
            $this->paymentIntents->storeCaptureSession($sessionId, [
                'intent_id' => $intent['intent_id'],
                'slug' => $slug,
                'reference' => $intent['reference'],
                'amount' => $intent['amount'],
                'currency' => $intent['currency'],
                'email' => $intent['email'],
                'capture_context_hash' => hash('sha256', $result['captureContext']),
                'exp' => time() + 900,
            ], 900);

            $freshNonce = $this->csrfService->getToken();

            $response = [
                'mock' => $result['mock'],
                'captureContext' => $result['captureContext'],
                'complete_mandate' => $result['completeMandate'] ?? false,
                'clientLibrary' => $result['clientLibrary'] ?? null,
                'clientLibraryIntegrity' => $result['clientLibraryIntegrity'] ?? null,
                'capture_session_id' => $sessionId,
                'nonce' => $freshNonce,
            ];

            if ($this->exposePaymentErrors()) {
                $response['debug'] = [
                    'nab_environment' => strtolower((string) (env('NAB_ENVIRONMENT') ?: 'test')),
                    'requested_complete_mandate' => $this->nabPaymentService->usesCompleteMandate(),
                    'session_complete_mandate' => $result['completeMandate'] ?? false,
                    'session_portal_processing' => $this->nabPaymentService->captureContextHasPortalProcessing($result['captureContext']),
                    'capture_context' => $this->nabPaymentService->describeCaptureContextForLog($result['captureContext']),
                ];
            }

            return $this->json($response);
        } catch (Throwable $e) {
            error_log('capture-context failed: ' . $e->getMessage());

            $message = 'Unable to start a secure payment session. Please try again.';
            if ($this->exposePaymentErrors()) {
                $message = $e->getMessage();
            }

            return $this->json(['error' => $message], 502);
        }
    }

    private function handlePay(Request $request): Response
    {
        if (!$this->assertSameOrigin($request)) {
            return $this->json(['ok' => false, 'error' => 'Invalid request origin.'], 403);
        }

        if (!$this->rateLimiter->attempt('pay', $this->clientIp($request), self::PAY_RATE_LIMIT, self::RATE_WINDOW_SECONDS)) {
            return $this->json(['ok' => false, 'error' => 'Too many payment attempts. Please wait and try again.'], 429);
        }

        $nonce = (string) ($request->input('nonce') ?? $request->input('csrf_token') ?? '');
        if (!$this->csrfService->validateToken($nonce)) {
            return $this->json(['ok' => false, 'error' => 'Security token expired. Refresh the page and try again.'], 403);
        }

        $slug = (string) ($request->input('slug') ?? '');
        $intentId = (string) ($request->input('intent_id') ?? '');
        $captureSessionId = (string) ($request->input('capture_session_id') ?? '');
        $idempotencyKey = trim((string) ($request->input('idempotency_key') ?? ''));
        $token = (string) ($request->input('token') ?? '');
        $paymentMode = (string) ($request->input('payment_mode') ?? '');

        if ($idempotencyKey === '' || !$this->paymentIntents->consumeIdempotencyKey($idempotencyKey)) {
            return $this->json(['ok' => false, 'error' => 'Duplicate payment attempt detected.'], 409);
        }

        $releaseIdempotency = function () use ($idempotencyKey): void {
            $this->paymentIntents->releaseIdempotencyKey($idempotencyKey);
        };

        $org = $this->organisations->getBySlug($slug);
        if ($org === null) {
            $releaseIdempotency();

            return $this->json(['ok' => false, 'error' => 'Unknown payment page.'], 404);
        }

        if ($token === '') {
            $releaseIdempotency();

            return $this->json(['ok' => false, 'error' => 'Missing card details.'], 400);
        }

        $captureSession = $this->paymentIntents->getCaptureSession($captureSessionId);
        if ($captureSession === null || (int) ($captureSession['exp'] ?? 0) < time()) {
            $releaseIdempotency();

            return $this->json(['ok' => false, 'error' => 'Your secure session expired. Please start again.'], 400);
        }

        if (($captureSession['slug'] ?? '') !== $slug || ($captureSession['intent_id'] ?? '') !== $intentId) {
            $releaseIdempotency();

            return $this->json(['ok' => false, 'error' => 'Invalid payment session.'], 400);
        }

        $intent = $this->paymentIntents->getIntent($intentId);
        if ($intent === null || !empty($intent['paid'])) {
            $releaseIdempotency();

            return $this->json(['ok' => false, 'error' => 'This payment link is no longer valid.'], 400);
        }

        if (!$this->amountsMatch($intent['amount'], $captureSession['amount'])) {
            $releaseIdempotency();

            return $this->json(['ok' => false, 'error' => 'Payment amount mismatch.'], 400);
        }

        $tokenAmount = $this->nabPaymentService->extractTokenAmount($token);
        if ($tokenAmount !== null && !$this->amountsMatch($tokenAmount, $captureSession['amount'])) {
            $releaseIdempotency();

            return $this->json(['ok' => false, 'error' => 'Card token amount does not match the authorised amount.'], 400);
        }

        $brand = $this->organisations->getBrandConfig();
        $email = (string) ($intent['email'] ?? $captureSession['email'] ?? '');
        if ($brand['requireEmail'] && !PaymentValidation::isValidEmail($email)) {
            $releaseIdempotency();

            return $this->json(['ok' => false, 'error' => 'Enter a valid email.'], 400);
        }

        if ($this->nabPaymentService->isTransientTokenExpired($token) && !in_array($paymentMode, ['uc_complete', 'uc_auto'], true)) {
            $releaseIdempotency();

            return $this->json([
                'ok' => false,
                'error' => 'Your card session has expired. Go back and enter your card details again.',
            ], 400);
        }

        try {
            $mode = $this->nabPaymentService->resolveMode($slug);
            $tokenSummary = $this->nabPaymentService->describeTokenForLog($token);

            PaymentDebugLog::log('api.pay.start', [
                'slug' => $slug,
                'intent_id' => $intentId,
                'payment_mode' => $paymentMode,
                'mock' => $mode['mock'],
                'token' => $tokenSummary,
            ]);

            $paymentInput = [
                'amount' => $captureSession['amount'],
                'reference' => $captureSession['reference'],
                'currency' => $captureSession['currency'],
                'email' => $email !== '' ? $email : null,
                'token' => $token,
            ];
            if (in_array($paymentMode, ['uc_complete', 'uc_auto'], true) && !$mode['mock']) {
                // uc_complete: standard UC flow — browser called complete(), token is a payment-result JWT.
                //   Reject transient tokens (browser must call complete() first).
                // uc_auto: portal auto-processing fallback — complete() was blocked by the portal.
                //   If the token is a payment-result JWT, parse it. If it is still a transient token,
                //   the portal did NOT complete the payment (show() returned before portal processing
                //   finished), so fall through to server-side processPayment() instead.
                if ($paymentMode === 'uc_complete' && $this->nabPaymentService->isTransientTokenJwt($token)) {
                    PaymentDebugLog::log('api.pay.reject_transient', ['token' => $tokenSummary]);
                    $releaseIdempotency();

                    return $this->json([
                        'ok' => false,
                        'error' => 'Payment was not finalized in secure checkout. Go back, complete the card form, and try again.',
                    ], 400);
                }
                if ($paymentMode === 'uc_auto' && $this->nabPaymentService->isTransientTokenJwt($token)) {
                    // Portal processing did not complete — process server-side with the transient token.
                    PaymentDebugLog::log('api.pay.route', ['handler' => 'processPayment', 'mode' => 'uc_auto_transient']);
                    $result = $this->nabPaymentService->processPayment($slug, $paymentInput);
                } else {
                    PaymentDebugLog::log('api.pay.route', ['handler' => 'parseCompletionResult', 'mode' => $paymentMode]);
                    $result = $this->nabPaymentService->parseCompletionResult($token, [
                        'amount' => $captureSession['amount'],
                        'currency' => $captureSession['currency'],
                    ]);
                }
            } else {
                PaymentDebugLog::log('api.pay.route', ['handler' => $mode['mock'] ? 'mock' : 'processPayment']);
                $result = $this->nabPaymentService->processPayment($slug, $paymentInput);
            }

            PaymentDebugLog::log('api.pay.result', [
                'approved' => $result['approved'],
                'status' => $result['status'],
                'transactionId' => $result['transactionId'] ?? '',
            ]);

            if ($result['approved']) {
                $this->paymentIntents->markIntentPaid($intentId);
            } else {
                $releaseIdempotency();
            }

            $response = [
                'ok' => $result['approved'],
                'status' => $result['status'],
                'transactionId' => $result['transactionId'],
                'message' => $result['message'],
                'amount' => $captureSession['amount'],
                'reference' => $captureSession['reference'],
                'currency' => $captureSession['currency'],
                'last4' => $result['last4'] ?? null,
            ];

            if ($this->exposePaymentErrors()) {
                $response['debug'] = [
                    'nab_environment' => strtolower((string) (env('NAB_ENVIRONMENT') ?: 'test')),
                    'payment_mode' => $paymentMode,
                    'token' => $tokenSummary,
                    'handler' => $mode['mock'] ? 'mock' : (in_array($paymentMode, ['uc_complete', 'uc_auto'], true) ? ($paymentMode === 'uc_auto' && $this->nabPaymentService->isTransientTokenJwt($token) ? 'processPayment' : 'parseCompletionResult') : 'processPayment'),
                ];
            }

            return $this->json($response);
        } catch (NabGatewayException $e) {
            $releaseIdempotency();
            error_log('payment failed: ' . $e->getMessage());

            $message = $e->userMessage();
            if ($this->exposePaymentErrors()) {
                $message = $e->getMessage();
            }

            return $this->json(['ok' => false, 'error' => $message], 502);
        } catch (Throwable $e) {
            $releaseIdempotency();
            error_log('payment failed: ' . $e->getMessage());

            $message = 'We could not process your payment. No funds have been taken.';
            if ($this->exposePaymentErrors()) {
                $message = $e->getMessage();
            }

            return $this->json(['ok' => false, 'error' => $message], 502);
        }
    }

    /**
     * @return array<string, mixed>|Response
     */
    private function resolveIntentForApi(Request $request, string $slug, string $intentId): array|Response
    {
        $mode = $this->nabPaymentService->resolveMode($slug);
        $org = $this->organisations->getBySlug($slug);
        if ($org === null) {
            return $this->json(['error' => 'Unknown payment page.'], 404);
        }

        $storedIntent = $this->paymentIntents->getIntent($intentId);
        if ($storedIntent !== null) {
            if (!empty($storedIntent['paid'])) {
                return $this->json(['error' => 'This payment link is no longer valid.'], 400);
            }

            if (!empty($storedIntent['manual_mock']) && ($storedIntent['amount'] ?? '') === '') {
                return $this->hydrateManualMockIntent($request, $slug, $org, $intentId, $storedIntent);
            }

            return $storedIntent;
        }

        if (!$mode['mock']) {
            return $this->json(['error' => 'This payment link is no longer valid.'], 400);
        }

        return $this->hydrateManualMockIntent($request, $slug, $org, $intentId, null);
    }

    /**
     * @param array<string, mixed> $org
     * @param array<string, mixed>|null $existing
     * @return array<string, mixed>|Response
     */
    private function hydrateManualMockIntent(Request $request, string $slug, array $org, string $intentId, ?array $existing): array|Response
    {
        $amount = PaymentValidation::parseAmount($request->input('amount'));
        $reference = PaymentValidation::validateReference($request->input('reference'));
        if (!$amount['ok']) {
            return $this->json(['error' => $amount['error']], 400);
        }
        if (!$reference['ok']) {
            return $this->json(['error' => $reference['error']], 400);
        }

        $brand = $this->organisations->getBrandConfig();
        $email = trim((string) ($request->input('email') ?? ($existing['email'] ?? '')));
        if ($brand['requireEmail'] && !PaymentValidation::isValidEmail($email)) {
            return $this->json(['error' => 'Enter a valid email.'], 400);
        }

        $intent = [
            'intent_id' => $intentId !== '' ? $intentId : bin2hex(random_bytes(16)),
            'slug' => $slug,
            'reference' => $reference['value'],
            'amount' => $amount['value'],
            'currency' => $org['currency'],
            'email' => $email,
            'invoice_type' => 'manual',
            'invoice_id' => 0,
            'exp' => time() + 3600,
            'paid' => false,
            'manual_mock' => true,
        ];

        $this->paymentIntents->storeIntent($intent, 3600);

        return $intent;
    }

    /**
     * @param array<string, mixed> $variables
     */
    private function renderPaymentPage(array $variables): Response
    {
        $response = $this->renderTwig('index.html.twig', $variables);

        return $this->paymentSecurity->applySecurityHeaders($this->request, $response);
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function renderTwig(string $template, array $payload): Response
    {
        if ($this->twig === null) {
            $loader = new FilesystemLoader(ROOT_DIR . '/src/themes/landing/payment');
            $this->twig = new Environment($loader, [
                'cache' => false,
                'autoescape' => 'html',
            ]);
        }

        $html = $this->twig->render($template, $payload);

        return $this->response
            ->withStatus(200)
            ->withHeader('Content-Type', 'text/html; charset=UTF-8')
            ->withBody($html);
    }

    /**
     * @param array<string, mixed> $data
     */
    private function json(array $data, int $status = 200): Response
    {
        return $this->response
            ->withStatus($status)
            ->withHeader('Content-Type', 'application/json')
            ->withBody(json_encode($data));
    }

    private function resolveTargetOrigin(Request $request): string
    {
        $explicit = trim((string) (env('PAYMENT_TARGET_ORIGIN') ?: ''));
        if ($explicit !== '') {
            return rtrim($explicit, '/');
        }

        $host = trim((string) ($request->header('Host') ?? $request->getServerParams()['HTTP_HOST'] ?? 'localhost'));

        // NAB rejects http:// in targetOrigins (400 Invalid URL). Unified Checkout also
        // requires the browser page origin to match this value exactly.
        return 'https://' . $host;
    }

    private function exposePaymentErrors(): bool
    {
        $environment = strtolower((string) env('ENVIRONMENT', ''));

        return in_array($environment, ['development', 'staging', 'test'], true)
            || filter_var(env('PAYMENT_DEBUG_ERRORS') ?: 'false', FILTER_VALIDATE_BOOL);
    }

    private function assertSameOrigin(Request $request): bool
    {
        $host = strtolower(explode(':', trim((string) ($request->header('Host') ?? $request->getServerParams()['HTTP_HOST'] ?? '')))[0]);
        if ($host === '') {
            return false;
        }

        $origin = trim((string) ($request->header('Origin') ?? ''));
        if ($origin !== '') {
            $originHost = parse_url($origin, PHP_URL_HOST);

            return is_string($originHost) && strtolower($originHost) === $host;
        }

        $referer = trim((string) ($request->header('Referer') ?? ''));
        if ($referer !== '') {
            $refererHost = parse_url($referer, PHP_URL_HOST);

            return is_string($refererHost) && strtolower($refererHost) === $host;
        }

        return false;
    }

    private function clientIp(Request $request): string
    {
        $forwarded = trim((string) ($request->header('X-Forwarded-For') ?? ''));
        if ($forwarded !== '') {
            return trim(explode(',', $forwarded)[0]);
        }

        return trim((string) ($request->getServerParams()['REMOTE_ADDR'] ?? 'unknown'));
    }

    private function isHttpsRequest(Request $request): bool
    {
        $server = $request->getServerParams();
        if (!empty($server['HTTPS']) && $server['HTTPS'] !== 'off') {
            return true;
        }

        return strtolower((string) ($server['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https';
    }

    private function amountsMatch(string $left, string $right): bool
    {
        return abs((float) $left - (float) $right) < 0.001;
    }
}
