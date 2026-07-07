<?php

declare(strict_types=1);

namespace App\Core\Repositories\Email;

use Exception;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Throwable;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

use function App\Core\System\utils\env;

class EmailRepository implements EmailRepositoryInterface
{
    public function __construct(
        private MailerInterface $mailer
    ) {
    }

    public function sendEmail(
        string|array $to,
        string $subject,
        string|null $body,
        array|null $context,
        string|null $templateUrl,
        string|null $templateName,
        array|string|null $cc = null,
        bool $otpVerification = false
    ): bool {
        $from = $this->resolveFrom();
        if ($from === null) {
            return false;
        }

        $toRecipients = array_values(array_unique(array_filter(
            $this->normalizeRecipients($to)
        )));
        if ($toRecipients === []) {
            return false;
        }

        $message = (new Email())
            ->from($from)
            ->to(...$toRecipients)
            ->subject($subject);
        $resolvedCc = $cc ?? ($context['cc'] ?? null);
        $ccRecipients = array_values(array_unique(array_filter(
            $this->normalizeRecipients($resolvedCc)
        )));
        if ($ccRecipients !== []) {
            $message->cc(...$ccRecipients);
        }

        if ($templateName !== null && $templateName !== '') {
            $loaderPath = $this->resolveTemplateLoaderPath($templateUrl);
            if ($loaderPath === null) {
                return false;
            }

            try {
                $twigContext = array_merge(
                    ['subject' => $subject],
                    $context ?? []
                );
                $html = $this->renderTwig($loaderPath, $templateName, $twigContext);
            } catch (Exception $e) {
                throw new Exception('Failed to render Twig template: ' . $e->getMessage());
            }

            $message->html($html);
        } elseif ($body !== null && $body !== '') {
            if ($this->looksLikeHtml($body)) {
                $message->html($body);
            } else {
                $message->text($body);
            }
        } else {
            return false;
        }

        try {
            $this->mailer->send($message);
        } catch (\Exception $e) {
            throw $e;
        }
        return true;
    }

    private function resolveFrom(): Address|string|null
    {
        $fromAddress = $_ENV['MAIL_FROM_ADDRESS'] ?? env('MAIL_FROM_ADDRESS');
        // $fromAddress = 'noreply@satechnology.com';
        $fromAddress = is_string($fromAddress) ? trim($fromAddress) : '';
        if ($fromAddress === '') {
            return null;
        }

        $fromName = $_ENV['MAIL_FROM_NAME'] ?? env('MAIL_FROM_NAME');
        // $fromName = 'SA Technology';
        $fromName = is_string($fromName) ? trim($fromName) : '';

        return $fromName !== ''
            ? new Address($fromAddress, $fromName)
            : $fromAddress;
    }

    private function resolveTemplateLoaderPath(string|null $templateUrl): ?string
    {
        if ($templateUrl !== null && $templateUrl !== '') {
            return $templateUrl;
        }

        if (!defined('ROOT_DIR')) {
            return null;
        }

        return ROOT_DIR . '/src/themes/landing/src/emailtemplate';
    }

    /**
     * @param array<string, mixed> $context
     */
    private function renderTwig(string $loaderPath, string $templateName, array $context): string
    {
        $loader = new FilesystemLoader($loaderPath);
        $twig = new Environment($loader, [
            'cache' => false,
            'autoescape' => 'html',
        ]);

        return $twig->render($templateName, $context);
    }

    private function looksLikeHtml(string $body): bool
    {
        return preg_match('/<\s*\w+/i', $body) === 1;
    }

    /**
     * @return array<int, string>
     */
    private function normalizeRecipients(mixed $recipients): array
    {
        if (is_string($recipients)) {
            $recipients = trim($recipients);
            return $recipients !== '' ? [$recipients] : [];
        }

        if (!is_array($recipients)) {
            return [];
        }

        $normalized = [];
        foreach ($recipients as $recipient) {
            if (!is_string($recipient)) {
                continue;
            }

            $recipient = trim($recipient);
            if ($recipient === '') {
                continue;
            }

            $normalized[] = $recipient;
        }

        return $normalized;
    }
}
