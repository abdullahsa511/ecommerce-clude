<?php

namespace App\Core\Http;

use App\Core\Http\Concerns\HandlesHttpExceptions;
use App\Core\Http\Concerns\InteractsWithAuthUser;
use App\Core\Models\Site\SiteResponse;
use App\Core\Repositories\Site\SiteRepositoryInterface;
use App\Core\Routes\Route;
use App\Core\Services\AuthService;
use App\Core\System\Session;
use App\Core\View\View;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Throwable;
use function App\Core\System\utils\app;

abstract class Controller {
    use HandlesHttpExceptions;
    use InteractsWithAuthUser;

    protected Request $request;
    protected Response $response;
    protected Session $session;
    protected View $view;
    protected Route $route;
    protected array $site;
    protected SiteRepositoryInterface $siteRepository;
    protected ?array $auth = null;

    /**
     * @throws BindingResolutionException
     */
    public function __construct(
        SiteRepositoryInterface $siteRepository
    ) {
        $this->view = app(View::class);
        $controller = strtolower(basename(str_replace('\\', '/',$this::class)));
        $this->view->controller = preg_replace('/controller/', '', $controller);
        $this->request = app(Request::class);
        $this->response = app(Response::class);
        $this->session = app(Session::class);
        $this->route = app(Route::class);
        $auth = $this->request->getAttribute('auth');
        $this->auth = is_array($auth) ? $auth : null;
        $this->siteRepository = $siteRepository;
        $site = $siteRepository->find(1);
        $site = new SiteResponse($site?->data);
        $this->site = $site->toArray();
    }

    /**
     * Get the current module name.
     *
     * @return string
     */
    protected function getModuleName(): string {
        return $this->route->getModule();
    }

    /**
     * Get the current action name.
     *
     * @return string
     */
    protected function getActionName(): string {
        return $this->route->getAction();
    }

    /**
     * Render a view and return the content as a string.
     *
     * @param string $template The template name.
     * @param array $variables Variables to pass to the view.
     * @return string Rendered HTML content.
     * @throws Exception
     */
    protected function renderView(string $template, array $variables = []): string {
        return $this->view->render($template, $variables);
    }

    /**
     * Redirect to a specified URI.
     *
     * @param string $url The URL to redirect to.
     * @param int $statusCode HTTP status code for the redirection.
     * @return Response The redirect response.
     */
    protected function redirect(string $url, int $statusCode = 302): Response {
        return $this->response->withHeader('Location', $url)->withStatus($statusCode);
    }

    /**
     * Handle a 404 Not Found scenario.
     *
     * @return Response The 404 response.
     * @throws Exception
     */
    protected function notFound(): Response {
        $content = $this->view->render('errors/404');
        return $this->response->withStatus(404)->withBody($content);
    }

    /**
     * Call a specific action dynamically with arguments.
     *
     * @param string $action The action method name.
     * @param array $args Arguments to pass to the method.
     * @return Response The result of the action call.
     * @throws Exception
     */
    protected function callAction(string $action, array $args = []): Response
    {
        if (!method_exists($this, $action)) {
            throw new Exception("Action $action does not exist.");
        }

        return call_user_func_array([$this, $action], $args);
    }

    /**
     * Get the current route URI.
     *
     * @return string
     */
    protected function getRoute(): string {
        return $this->route->getCurrent();
    }


    protected function handleException(Throwable $e, string $defaultMessage = 'An error occurred.'): Response
    {
        return $this->buildHttpErrorResponse($e, $this->response, $defaultMessage);
    }

    /**
     * Render a view and create a response.
     */
    protected function renderResponse(string $template, array $variables = []): Response
    {
        $plugin = $this->request->getAttribute('plugin')??'';
        $variables = array_merge($this->resolveAuthViewData(), $variables);
        try {
            $content = $this->view->render($template, $variables, $plugin);
            return $this->response
                ->withStatus(200)
                ->withBody($content);
        } catch (Throwable $e) {
            return $this->handleException($e);
        }
    }

    /**
     * Shared auth state for all rendered templates.
     *
     * @return array{is_admin: bool, is_logged_in: bool}
     */
    protected function resolveAuthViewData(): array
    {
        $isAdmin = $this->isAdmin();
        $isLoggedIn = $this->authUser() !== null || $isAdmin;

        try {
            /** @var AuthService $authService */
            $authService = app(AuthService::class);
            $isLoggedIn = $isLoggedIn || $authService->isLoggedIn();
        } catch (Throwable $e) {
            // Keep conservative fallback from middleware/session state only.
        }

        return [
            'is_admin' => $isAdmin,
            'is_logged_in' => $isLoggedIn,
        ];
    }
}
