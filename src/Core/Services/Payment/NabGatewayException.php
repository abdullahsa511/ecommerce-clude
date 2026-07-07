<?php

declare(strict_types=1);

namespace App\Core\Services\Payment;

use RuntimeException;

/**
 * Raised when NAB Gateway returns a non-success HTTP status.
 */
class NabGatewayException extends RuntimeException
{
    public function __construct(
        string $resource,
        public readonly int $statusCode,
        public readonly string $responseBody,
    ) {
        parent::__construct('NAB ' . $resource . ' responded ' . $statusCode . ': ' . $responseBody);
    }

    public function userMessage(): string
    {
        if ($this->statusCode === 404) {
            return 'Your card session has expired. Go back and enter your card details again.';
        }

        if ($this->statusCode === 401) {
            return 'Payment configuration error. Please contact support.';
        }

        $parsed = $this->parsedReason();
        if ($parsed !== '') {
            return $parsed;
        }

        return 'We could not process your payment. No funds have been taken.';
    }

    private function parsedReason(): string
    {
        $json = json_decode($this->responseBody, true);
        if (!is_array($json)) {
            return '';
        }

        $paths = [
            ['message'],
            ['reason'],
            ['response', 'rmsg'],
            ['response', 'message'],
            ['errorInformation', 'message'],
            ['errorInformation', 'reason'],
        ];

        foreach ($paths as $path) {
            $value = $json;
            foreach ($path as $segment) {
                if (!is_array($value) || !array_key_exists($segment, $value)) {
                    $value = null;
                    break;
                }
                $value = $value[$segment];
            }
            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        }

        return '';
    }
}
