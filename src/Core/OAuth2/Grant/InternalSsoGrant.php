<?php

declare(strict_types=1);

namespace App\Core\OAuth2\Grant;

use App\Core\OAuth2\AdminTokenLifetime;
use DateInterval;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\AbstractGrant;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use League\OAuth2\Server\RequestAccessTokenEvent;
use League\OAuth2\Server\RequestEvent;
use League\OAuth2\Server\RequestRefreshTokenEvent;
use League\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Server-only grant: issues access (and refresh) tokens for a user id after verifying a shared secret.
 * Used after Google/Microsoft OAuth when password grant is not available.
 */
final class InternalSsoGrant extends AbstractGrant
{
    public function __construct(
        UserRepositoryInterface $userRepository,
        RefreshTokenRepositoryInterface $refreshTokenRepository
    ) {
        $this->setUserRepository($userRepository);
        $this->setRefreshTokenRepository($refreshTokenRepository);
        $this->refreshTokenTTL = new DateInterval('P1M');
    }

    public function getIdentifier(): string
    {
        return 'internal_sso';
    }

    public function respondToAccessTokenRequest(
        ServerRequestInterface $request,
        ResponseTypeInterface $responseType,
        DateInterval $accessTokenTTL
    ): ResponseTypeInterface {
        $client = $this->validateClient($request);

        $secret = $this->getRequestParameter('internal_secret', $request)
            ?? throw OAuthServerException::invalidRequest('internal_secret');
        $expected = $_ENV['OAUTH_INTERNAL_TOKEN_SECRET'] ?? '';
        if ($expected === '' || !hash_equals($expected, $secret)) {
            throw OAuthServerException::accessDenied();
        }

        $userIdParam = $this->getRequestParameter('user_id', $request)
            ?? throw OAuthServerException::invalidRequest('user_id');

        $user = $this->userRepository->getUserEntityByUserCredentials(
            (string) $userIdParam,
            $secret,
            'internal_sso',
            $client
        );

        if (!$user instanceof UserEntityInterface) {
            $this->getEmitter()->emit(new RequestEvent(RequestEvent::USER_AUTHENTICATION_FAILED, $request));
            throw OAuthServerException::invalidCredentials();
        }

        $scopes = $this->validateScopes($this->getRequestParameter('scope', $request, $this->defaultScope));
        $finalizedScopes = $this->scopeRepository->finalizeScopes(
            $scopes,
            $this->getIdentifier(),
            $client,
            $user->getIdentifier()
        );

        $effectiveAccessTokenTtl = AdminTokenLifetime::accessTokenInterval($finalizedScopes, $accessTokenTTL);

        $accessToken = $this->issueAccessToken($effectiveAccessTokenTtl, $client, $user->getIdentifier(), $finalizedScopes);
        $this->getEmitter()->emit(new RequestAccessTokenEvent(RequestEvent::ACCESS_TOKEN_ISSUED, $request, $accessToken));
        $responseType->setAccessToken($accessToken);

        $refreshToken = $this->issueRefreshToken($accessToken);
        if ($refreshToken !== null) {
            $this->getEmitter()->emit(new RequestRefreshTokenEvent(RequestEvent::REFRESH_TOKEN_ISSUED, $request, $refreshToken));
            $responseType->setRefreshToken($refreshToken);
        }

        return $responseType;
    }
}
