<?php

declare(strict_types=1);

namespace App\Core\Controllers\Web;

use App\Core\Http\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Repositories\Page\PageRepositoryInterface;
use App\Core\Repositories\Visit\VisitShowroomRepositoryInterface;
use App\Core\Repositories\Pinboard\PinboardRepositoryInterface;
use App\Core\Repositories\Post\PostRepositoryInterface;
use App\Core\Repositories\Site\SiteRepositoryInterface;
use App\Core\Services\AuthService;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * PageController handles pages and booking actions.
 */
class PageController extends Controller
{
    private VisitShowroomRepositoryInterface $visitShowroomRepository;
    private PinboardRepositoryInterface $pinboardRepository;
    private AuthService $authService;
    private PostRepositoryInterface $postRepository;
    private PageRepositoryInterface $pageRepository;
    private ?Environment $twig = null;

    public function __construct(
        VisitShowroomRepositoryInterface $visitShowroomRepository,
        PinboardRepositoryInterface $pinboardRepository,
        AuthService $authService,
        PostRepositoryInterface $postRepository,
        PageRepositoryInterface $pageRepository,
        SiteRepositoryInterface $siteRepository
    ) {
        parent::__construct($siteRepository);

        $this->visitShowroomRepository = $visitShowroomRepository;
        $this->pinboardRepository = $pinboardRepository;
        $this->authService = $authService;
        $this->postRepository = $postRepository;
        $this->pageRepository = $pageRepository;
    }

    /**
     * About Us page
     */
    public function aboutUs(Request $request): Response
    {
        return $this->renderResponse('about-us');
    }

    /**
     * Dynamic page details by slug
     */
    public function details(Request $request, string $page_slug): Response // details_with_twig
    {
        $postId = $this->postRepository->getPostIdBySlug($page_slug);

        if (!$postId || (int) $postId === 0) {
            return $this->redirect('/404');
        }

        $post = $this->pageRepository->get((int) $postId);

        if (!$post || !isset($post->data)) {
            return $this->redirect('/404');
        }

        // Supports JSON content OR raw HTML
        $postRawContent = trim((string) ($post->data->postContent ?? ''));

        $htmlContent = '';

        if ($postRawContent !== '') {
            $decodedContent = json_decode($postRawContent);

            if (
                json_last_error() === JSON_ERROR_NONE &&
                is_object($decodedContent) &&
                isset($decodedContent->content)
            ) {
                $htmlContent = (string) $decodedContent->content;
            } else {
                $htmlContent = $postRawContent;
            }
        }

        return $this->renderTwig('page-details.html.twig', [
            'page_slug' => $page_slug,
            'content'   => $htmlContent,
            'title'     => 'Page Details | Krost Business Furniture',
        ]);
    }


    /**
     * Normalize CMS page content (JSON wrapper, escaped HTML, double-encoded entities).
     */
    private function resolvePageHtmlContent(string $rawContent): string
    {
        $content = $rawContent;

        $decoded = json_decode($rawContent);
        if (json_last_error() === JSON_ERROR_NONE && is_object($decoded) && isset($decoded->content)) {
            $content = (string) $decoded->content;
        }

        $trimmed = ltrim($content);
        if ($trimmed !== '' && ($trimmed[0] === '"' || $trimmed[0] === '{')) {
            $inner = json_decode($content);
            if (json_last_error() === JSON_ERROR_NONE) {
                if (is_string($inner)) {
                    $content = $inner;
                } elseif (is_object($inner) && isset($inner->content)) {
                    $content = (string) $inner->content;
                }
            }
        }

        do {
            $previous = $content;
            $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        } while ($content !== $previous);

        return $content;
    }

    /**
     * Return a complete HTML document (skips Twig so CMS HTML renders on production).
     */
    private function renderRawHtml(string $html): Response
    {
        return new Response($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }

    /**
     * Render Twig template
     */
    private function renderTwig(string $template, array $payload = []): Response
    {
        if ($this->twig === null) {
            $loader = new FilesystemLoader(ROOT_DIR . '/src/themes/landing/page');

            $this->twig = new Environment($loader, [
                'cache'      => false,
                'autoescape' => 'html',
                'debug'      => false,
            ]);
        }

        $html = $this->twig->render($template, $payload);

        return $this->response
            ->withStatus(200)
            ->withHeader('Content-Type', 'text/html; charset=UTF-8')
            ->withBody($html);
    }

    /**
     * Cancel booking via UUID
     */
    public function bookingCancel(Request $request, string $uuid): Response
    {
        // $user = $this->authService->getAuthUser();

        // Optional login protection
        /*
        if (!$user) {
            return $this->redirect('/login');
        }
        */

        if (!empty($uuid)) {
            $visitShowroomId = $this->visitShowroomRepository->getVisitShowroomIdByUuid($uuid);

            if ($visitShowroomId) {
                $visitShowroomData = $this->pinboardRepository->getBookingComponentContactSales($visitShowroomId);

                if (!empty($visitShowroomData)) {
                    /*
                    $currentUserId = $user->user_id ?? null;
                    $contactUserId = $visitShowroomData['user_id'] ?? null;

                    if ($currentUserId !== $contactUserId) {
                        return $this->redirect('/login');
                    }
                    */

                    $data = [
                        'visit_showroom_id' => $visitShowroomId,
                        'email'             => $visitShowroomData['customer_email'] ?? '',
                        'location'          => $visitShowroomData['address'] ?? '',
                        'tour_type'         => $visitShowroomData['tour_type'] ?? '',
                        'date'              => $visitShowroomData['date'] ?? '',
                        'meeting_time'      => $visitShowroomData['meeting_time'] ?? '',
                        'customer_name'     => $visitShowroomData['customer_name'] ?? '',
                    ];

                    $this->visitShowroomRepository->cancelBooking($visitShowroomId);
                }
            }
            return $this->renderResponse('booking-cancel');
        }

        return $this->renderResponse('booking-cancel');
    }

    /**
     * Booking cancel landing page
     */
    public function bookingCancelLandingPage(Request $request): Response
    {
        return $this->renderResponse('booking-cancel');
    }

    public function detailsFuture(Request $request): Response
    {
        return $this->renderResponse('details-future',['is_admin' => $this->isAdmin(), 'title' => 'Details Future | Krost Business Furniture']);
    }
}