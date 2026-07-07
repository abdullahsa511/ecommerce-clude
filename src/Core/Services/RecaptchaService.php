<?php

declare(strict_types=1);

namespace App\Core\Services;

use function App\Core\System\utils\env;

/**
 * Google reCAPTCHA v3 verification (score-based, no user challenge).
 */
class RecaptchaService
{
    private ?string $secretKey;

    private float $minScore;

    public function __construct()
    {
        $secret = env('RECAPTCHA_SECRET_KEY');
        $this->secretKey = is_string($secret) && $secret !== '' ? $secret : null;
        $this->minScore = (float) env('RECAPTCHA_MIN_SCORE', 0.5);
    }

    public function isEnabled(): bool
    {
        return $this->secretKey !== null && $this->getSiteKey() !== '';
    }

    public function getSiteKey(): string
    {
        return (string) env('RECAPTCHA_SITE_KEY', '');
    }

    /** reCAPTCHA action for contact / get-in-touch form (Google Admin analytics). */
    public function getContactAction(): string
    {
        return (string) env('RECAPTCHA_ACTION', 'contact_submit');
    }

    /** reCAPTCHA action for account service request form (Google Admin analytics). */
    public function getServiceAction(): string
    {
        return (string) env('RECAPTCHA_ACTION_SERVICE', 'service_request');
    }

    /** reCAPTCHA action for pinboard project submission modal (Google Admin analytics). */
    public function getProjectAction(): string
    {
        return (string) env('RECAPTCHA_ACTION_PROJECT', 'project_submission');
    }

    /** reCAPTCHA action for showroom / virtual booking time modal (Google Admin analytics). */
    public function getBookingAction(): string
    {
        return (string) env('RECAPTCHA_ACTION_BOOKING', 'showroom_booking');
    }

    /** reCAPTCHA action for catalogue request form (Google Admin analytics). */
    public function getCatalogueAction(): string
    {
        return (string) env('RECAPTCHA_ACTION_CATALOGUE', 'catalogue_request');
    }

    /**
     * @return array{ok: bool, score: ?float, message: ?string}
     */
    public function verify(string $token, ?string $remoteIp = null, ?string $action = null): array
    {
        $action = $action ?? $this->getContactAction();

        if (!$this->isEnabled()) {
            return ['ok' => true, 'score' => null, 'message' => null];
        }

        $token = trim($token);
        if ($token === '') {
            return [
                'ok' => false,
                'score' => null,
                'message' => 'reCAPTCHA verification is required. Please try again.',
            ];
        }

        $payload = http_build_query(array_filter([
            'secret' => $this->secretKey,
            'response' => $token,
            'remoteip' => $remoteIp,
        ]));

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => $payload,
                'timeout' => 10,
            ],
        ]);

        $raw = @file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
        if ($raw === false) {
            return [
                'ok' => false,
                'score' => null,
                'message' => 'Could not verify reCAPTCHA. Please try again.',
            ];
        }

        $data = json_decode($raw, true);
        if (!is_array($data) || empty($data['success'])) {
            return [
                'ok' => false,
                'score' => null,
                'message' => 'reCAPTCHA verification failed. Please try again.',
            ];
        }

        $score = isset($data['score']) ? (float) $data['score'] : null;
        $responseAction = isset($data['action']) ? (string) $data['action'] : '';

        if ($responseAction !== '' && $responseAction !== $action) {
            return [
                'ok' => false,
                'score' => $score,
                'message' => 'reCAPTCHA verification failed. Please try again.',
            ];
        }

        if ($score !== null && $score < $this->minScore) {
            return [
                'ok' => false,
                'score' => $score,
                'message' => 'Your submission could not be verified. Please try again later.',
            ];
        }

        return ['ok' => true, 'score' => $score, 'message' => null];
    }
}
