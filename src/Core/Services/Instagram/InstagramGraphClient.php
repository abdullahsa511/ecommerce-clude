<?php

declare(strict_types=1);

namespace App\Core\Services\Instagram;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use RuntimeException;

use function App\Core\System\utils\env;

class InstagramGraphClient
{
    private const DEFAULT_API_VERSION = 'v21.0';

    private Client $client;

    public function __construct(?Client $client = null)
    {
        $this->client = $client ?? new Client([
            'base_uri' => 'https://graph.facebook.com/',
            'timeout' => 20,
            'connect_timeout' => 10,
            'http_errors' => false,
        ]);
    }

    public function apiVersion(): string
    {
        return trim((string) (env('INSTAGRAM_GRAPH_API_VERSION') ?: self::DEFAULT_API_VERSION));
    }

    public function get(string $path, array $query = [], ?string $accessToken = null): array
    {
        if ($accessToken !== null && $accessToken !== '') {
            $query['access_token'] = $accessToken;
        }

        try {
            $response = $this->client->get($this->apiVersion() . $path, [
                'query' => $query,
            ]);
        } catch (GuzzleException $e) {
            throw new RuntimeException('Instagram API request failed: ' . $e->getMessage(), 0, $e);
        }

        $status = $response->getStatusCode();
        $body = $response->getBody()->getContents();
        $decoded = json_decode($body, true);

        if (!is_array($decoded)) {
            throw new RuntimeException('Instagram API returned an invalid response.');
        }

        if ($status >= 400) {
            $message = (string) ($decoded['error']['message'] ?? 'Instagram API request failed.');
            throw new RuntimeException($message);
        }

        return $decoded;
    }
}
