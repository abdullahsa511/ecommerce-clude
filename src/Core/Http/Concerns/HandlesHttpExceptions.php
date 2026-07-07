<?php

declare(strict_types=1);

namespace App\Core\Http\Concerns;

use App\Core\Exceptions\AccessDeniedHttpException;
use App\Core\Exceptions\BadRequestHttpException;
use App\Core\Exceptions\ConflictHttpException;
use App\Core\Exceptions\GoneHttpException;
use App\Core\Exceptions\HttpExceptionInterface;
use App\Core\Exceptions\InternalServerErrorHttpException;
use App\Core\Exceptions\MethodNotAllowedHttpException;
use App\Core\Exceptions\NotFoundHttpException;
use App\Core\Exceptions\ServiceUnavailableHttpException;
use App\Core\Exceptions\TooManyRequestsHttpException;
use App\Core\Exceptions\UnauthorizedHttpException;
use App\Core\Exceptions\ValidationException;
use App\Core\Http\ApiController;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Routes\Route;
use Throwable;

use function App\Core\System\utils\app;
use function App\Core\System\utils\env;

trait HandlesHttpExceptions
{
    /**
     * Error page routes registered in WebRoute (production only).
     *
     * @return array<int, string>
     */
    protected function productionErrorRoutes(): array
    {
        return [
            400 => '/400',
            401 => '/401',
            403 => '/403',
            404 => '/404',
            409 => '/409',
            410 => '/410',
            426 => '/426',
            429 => '/429',
            500 => '/500',
            503 => '/503',
        ];
    }

    protected function isDevelopmentEnvironment(): bool
    {
        $environment = strtolower((string) env('ENVIRONMENT', env('APP_ENV', 'production')));

        return in_array($environment, ['development', 'dev', 'local'], true);
    }

    protected function resolveProductionErrorRoute(int $statusCode): string
    {
        $routes = $this->productionErrorRoutes();

        return $routes[$statusCode] ?? '/500';
    }

    /**
     * Whether the current request targets an API route (ApiRoute or /api/*).
     */
    protected function isApiRequest(?Request $request): bool
    {
        if ($request === null) {
            return false;
        }

        if ($request->getAttribute('route_type') === 'api') {
            return true;
        }

        $uri = $request->getUri();
        if ($uri !== '/') {
            $uri = rtrim($uri, '/') ?: '/';
        }

        if (str_starts_with($uri, '/api')) {
            return true;
        }

        $controllerClass = $this->resolveMatchedControllerClass();
        if ($this->isApiControllerClass($controllerClass)) {
            return true;
        }

        $accept = strtolower((string) ($request->header('Accept') ?? ''));
        if (str_contains($accept, 'application/json')) {
            return true;
        }

        return false;
    }

    protected function resolveMatchedControllerClass(): string
    {
        try {
            /** @var Route $router */
            $router = app(Route::class);

            return (string) ($router->getModule() ?: '');
        } catch (Throwable) {
            return '';
        }
    }

    protected function isApiControllerClass(string $controllerClass): bool
    {
        if ($controllerClass === '' || !class_exists($controllerClass)) {
            return false;
        }

        return is_subclass_of($controllerClass, ApiController::class);
    }

    protected function buildHttpErrorResponse(
        Throwable $e,
        Response $response,
        string $defaultMessage = 'An error occurred.',
        bool $preferJson = false
    ): Response {
        $statusCode = 500;
        $exception = $e;
        $headers = [];

        if ($e instanceof HttpExceptionInterface) {
            $statusCode = $e->getStatusCode();
            $headers = $e->getHeaders();
            $exception = $e;
        } elseif (method_exists($e, 'getStatusCode')) {
            $statusCode = (int) $e->getStatusCode();
            $message = $e->getMessage() ?: $defaultMessage;

            $exception = match ($statusCode) {
                400 => new BadRequestHttpException($message, $e, $statusCode),
                401 => new UnauthorizedHttpException($message, $e),
                403 => new AccessDeniedHttpException($message, $e),
                404 => new NotFoundHttpException($message, $e),
                405 => new MethodNotAllowedHttpException([], $message, $e),
                409 => new ConflictHttpException($message, $e),
                410 => new GoneHttpException($message, $e),
                422 => new ValidationException(json_decode($message, true) ?: [], $message, $statusCode),
                429 => new TooManyRequestsHttpException(0, $message, $e),
                503 => new ServiceUnavailableHttpException(0, $message, $e),
                default => new InternalServerErrorHttpException($message, $e),
            };
        }

        // Web production: redirect to error pages. API and development always use JSON.
        if (!$preferJson && !$this->isDevelopmentEnvironment()) {
            return $response
                ->withStatus(302)
                ->withHeader('Location', $this->resolveProductionErrorRoute($statusCode));
        }

        $payload = [
            'message' => $exception->getMessage() ?: $defaultMessage,
            'status' => $statusCode,
        ];

        if ($exception instanceof ValidationException) {
            $payload['errors'] = $exception->getErrors();
        }

        if ($this->isDevelopmentEnvironment()) {
            $payload['details'] = method_exists($exception, 'getHeaders') ? $exception->getHeaders() : [];
            $payload['trace'] = $exception->getTrace();
        }

        $response = $response
            ->withStatus($statusCode)
            ->withHeader('Content-Type', 'application/json')
            ->withBody(json_encode($payload));

        foreach ($headers as $name => $value) {
            $response = $response->withHeader($name, $value);
        }

        return $response;
    }
}
