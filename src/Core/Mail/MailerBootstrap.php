<?php

declare(strict_types=1);

namespace App\Core\Mail;

use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;

/**
 * Builds Symfony Mailer from MAILER_DSN (Google Workspace SMTP relay, etc.).
 */
final class MailerBootstrap
{
    public static function createFromEnv(): MailerInterface
    {
        $dsn = $_ENV['MAILER_DSN'] ?? getenv('MAILER_DSN');
        $dsn = is_string($dsn) ? trim($dsn) : '';

        if ($dsn === '') {
            throw new \RuntimeException(
                'MAILER_DSN is not set. Add it to .env (e.g. smtp://smtp-relay.gmail.com:587 for Google Workspace relay, or null://null to discard mail in development).'
            );
        }

        return new Mailer(Transport::fromDsn($dsn));
    }
}
