<?php

declare(strict_types=1);

namespace App\Core\Repositories\Email;

interface EmailRepositoryInterface
{
    /**
     * Send email using a plain body, or a Twig template with context array data.
     * When $templateName is set, Twig is used and $context supplies template variables
     * (subject is merged in for templates that expect it). Otherwise $body is used.
     */
    public function sendEmail(
        string|array $to,
        string $subject,
        string|null $body,
        array|null $context,
        string|null $templateUrl,
        string|null $templateName,
        array|string|null $cc = null,
        bool $otpVerification = false
    ): bool;
}
