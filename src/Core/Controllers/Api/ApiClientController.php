<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Repositories\Auth\ClientRepositoryInterface;
use App\Core\Services\AuthService;


class ApiClientController
{
    private ClientRepositoryInterface $clientRepository;
    private AuthService $authService;

    public function __construct(
        ClientRepositoryInterface $clientRepository,
        AuthService $authService
    )
    {
        $this->clientRepository = $clientRepository;
        $this->authService = $authService;
    }

    /**
     * Create a new client.
     *
     * @param Request $request
     * @return Response
     */
    public function createClient(Request $request): Response
    {
        $name = $request->input('name');
        $scopes = $request->input('scopes');

        // Validate inputs
        if (!$name || !$scopes || !is_array($scopes)) {
            return (new Response())
                ->withStatus(400)
                ->withHeader('Content-Type', 'application/json')
                ->withBody(json_encode(['error' => 'Invalid input.']));
        }

        try {
            $clientData = $this->authService->registerClient($name, $scopes);

            return (new Response())
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->withBody(json_encode([
                    'message' => 'Client created successfully.',
                    'client_id' => $clientData['client_id'],
                    'client_secret' => $clientData['client_secret'],
                ]));
        } catch (\Exception $e) {
            return (new Response())
                ->withStatus(500)
                ->withHeader('Content-Type', 'application/json')
                ->withBody(json_encode(['error' => $e->getMessage()]));
        }
    }

    /**
     * Revoke a client.
     *
     * @param Request $request
     * @return Response
     */
    public function revokeClient(Request $request): Response
    {
        $clientId = $request->input('client_id');

        // Validate input
        if (!$clientId) {
            return (new Response())
                ->withStatus(400)
                ->withHeader('Content-Type', 'application/json')
                ->withBody(json_encode(['error' => 'Client ID is required.']));
        }

        try {
            $this->clientRepository->revokeClient((int) $clientId);

            return (new Response())
                ->withStatus(200)
                ->withHeader('Content-Type', 'application/json')
                ->withBody(json_encode(['message' => 'Client revoked successfully.']));
        } catch (\Exception $e) {
            return (new Response())
                ->withStatus(500)
                ->withHeader('Content-Type', 'application/json')
                ->withBody(json_encode(['error' => $e->getMessage()]));
        }
    }
}
