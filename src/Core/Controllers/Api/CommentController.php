<?php

declare(strict_types=1);

namespace App\Core\Controllers\Api;

use App\Core\Exceptions\ValidationException;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Http\ApiController;
use Throwable;
use App\Core\Repositories\Post\CommentRepositoryInterface;
use App\Core\Repositories\Media\MediaRepositoryInterface;
use App\Core\Repositories\Quote\QuoteRepositoryInterface;
use App\Core\Repositories\Order\OrderRepositoryInterface;
use App\Core\Repositories\UserRepositoryInterface;
use App\Core\Services\AuthService;
class CommentController extends ApiController
{
    private CommentRepositoryInterface $commentRepository;
    private MediaRepositoryInterface $mediaRepository;
    private QuoteRepositoryInterface $quoteRepository;
    private OrderRepositoryInterface $orderRepository;
    private UserRepositoryInterface $userRepository;
    private AuthService $authService;
    public function __construct(
        CommentRepositoryInterface $commentRepository,
        MediaRepositoryInterface $mediaRepository,
        QuoteRepositoryInterface $quoteRepository,
        OrderRepositoryInterface $orderRepository,
        UserRepositoryInterface $userRepository,
        AuthService $authService
    )
    {
        parent::__construct();
        $this->commentRepository = $commentRepository;
        $this->mediaRepository = $mediaRepository;
        $this->quoteRepository = $quoteRepository;
        $this->orderRepository = $orderRepository;
        $this->userRepository = $userRepository;
        $this->authService = $authService;
    }

    /**
     * Get all comments with pagination and filtering.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $result = $this->commentRepository->findAll();
        return $this->renderResponse($result);
    }

    /**
     * Get a comment by ID.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function show(Request $request, $id): Response
    {
        $comment = $this->commentRepository->find((int)$id);
        if(!$comment){
            return $this->renderError(404, 'Comment not found');
        }
        return $this->renderResponse($comment->data);
    }

    public function getCommentsById(Request $request, string $uuid): Response
    {
        $modelType = $request->query('model_type');
        $id = '';
        if($modelType == 'Order'){
            $order = $this->orderRepository->getIdByUuid((string)$uuid);
            $id = $order->order_id;
        }else if($modelType == 'Quote'){
            $quote = $this->quoteRepository->getIdByUuid((string)$uuid);
            $id = $quote->quote_id;
        }else if($modelType == 'PinboardItem'){
            $id = $uuid;
        }else{
            return $this->renderError(403, 'Invalid model type for comment');
        }

        if(!$id){
            return $this->renderError(404, 'No model id found for comment');
        }

        // $quote = $this->quoteRepository->getIdByUuid((string)$uuid);

        // $id = $quote->quote_id;   

        $authUser = $this->authUser();
        $authUserId = ($authUser && isset($authUser->user_id)) ? (int) $authUser->user_id : null;

        $comments = $this->commentRepository->findCommentsById((int)$id, (string) $modelType, $authUserId);
        if(!$comments){
            // return $this->renderError(404, 'Comments not found');
            return $this->renderResponse([]);
        }
        return $this->renderResponse($comments);
    }

    public function saveComment(Request $request): Response
    {
        $authUser = $this->authUser();
        if (!isset($authUser) || !isset($authUser->user_id)) {
            return $this->renderError(403, 'Unauthorized');
        }
        $user = $this->userRepository->find($authUser->user_id);
        if (!isset($user) || !isset($user->user_id) || $user->user_id !== $authUser->user_id) {
            return $this->renderError(403, 'Unauthorized');
        }
   
        $data = $request->all();
        $uuid = $data['model_uuid'] ?? null;
        $modelType = $data['model_type'] ?? null;

        if($uuid){
            if($modelType == 'Order'){
                $model = $this->orderRepository->getOrderByUuid($uuid);
            }else if($modelType == 'Quote'){
                $model = $this->quoteRepository->getQuoteByUuid($uuid);
            }else{
                return $this->renderError(404, 'Model not found');
            }
            $data['model_id'] = isset($model['order_id']) ? $model['order_id'] : (isset($model['quote_id']) ? $model['quote_id'] : null);
        }
        
        if(!$authUser){
            return $this->renderError(403, 'Unauthorized');
        }else{
            $data['user_id'] = $authUser->user_id;
            $data['author'] = $authUser->first_name . ' ' . $authUser->last_name ?? 'Nazmul Hossen';
            $data['email'] = $authUser->email ?? 'nazmul@satechnology.com.au';
            $data['url'] = $authUser->url ?? 'https://www.satechnology.com.au';
            $data['ip'] = $authUser->ip ?? '127.0.0.1';
            $data['status'] = 1;
            $data['votes'] = 0;
            $data['parent_id'] = null;
        }

        $folder = 'media/Comments';
        $size = [
            'width' => 945,
            'height' => 630,
        ];

        $result = null;

        // Handle uploaded files safely
        $rawFiles = $request->files() ?? ($_FILES['files'] ?? null);
        if (!empty($rawFiles)) {
            $normalizedFiles = $this->normalizeUploadedFiles($rawFiles);

            if (empty($normalizedFiles)) {
                return $this->renderError(422, 'No files uploaded');
            }

            $uploadData = [
                'files' => $normalizedFiles,
                'upload_dir' => $request->input('upload_dir', $folder)
            ];

            $result = $this->mediaRepository->upload($uploadData, $size, $folder);

            if (empty($result) || empty($result['files'])) {
                return $this->renderError(500, 'Failed to upload media');
            }
        }

        // Save comment
        $comment = $this->commentRepository->createComment($data, $result ? $result['files'] :[]);

        if (!$comment) {
            return $this->renderError(500, 'Failed to save comment');
        }

        return $this->renderResponse($comment);
    }

    public function savePinboardComment(Request $request): Response
    {
        $authUser = $this->authUser();
        if (!isset($authUser) || !isset($authUser->user_id)) {
            return $this->renderError(403, 'Unauthorized');
        }
        $user = $this->userRepository->find($authUser->user_id);
        if (!isset($user) || !isset($user->user_id) || $user->user_id !== $authUser->user_id) {
            return $this->renderError(403, 'Unauthorized');
        }
   
        $data = $request->all();
        // $uuid = $data['model_uuid'] ?? $data['model_id'];
        // $modelType = $data['model_type'] ?? 'pinboard_comment';
        $data['model_id'] = (int) $data['model_uuid'];
        if(!$authUser){
            return $this->renderError(403, 'Unauthorized');
        }else{
            $data['user_id'] = $authUser->user_id;
            $data['author'] = $authUser->first_name . ' ' . $authUser->last_name ?? 'Nazmul Hossen';
            $data['email'] = $authUser->email ?? 'nazmul@satechnology.com.au';
            $data['url'] = $authUser->url ?? 'https://www.satechnology.com.au';
            $data['ip'] = $authUser->ip ?? '127.0.0.1';
            $data['status'] = 1;
            $data['votes'] = 0;
            $data['parent_id'] = null;
        }

        $folder = 'media/Comments';
        $size = [
            'width' => 945,
            'height' => 630,
        ];

        $result = null;

        // Handle uploaded files safely
        $rawFiles = $request->files() ?? ($_FILES['files'] ?? null);
        if (!empty($rawFiles)) {
            $normalizedFiles = $this->normalizeUploadedFiles($rawFiles);

            if (empty($normalizedFiles)) {
                return $this->renderError(422, 'No files uploaded');
            }

            $uploadData = [
                'files' => $normalizedFiles,
                'upload_dir' => $request->input('upload_dir', $folder)
            ];

            $result = $this->mediaRepository->upload($uploadData, $size, $folder);

            if (empty($result) || empty($result['files'])) {
                return $this->renderError(500, 'Failed to upload media');
            }
        }

        // Save comment
        $comment = $this->commentRepository->createComment($data, $result ? $result['files'] :[]);

        if (!$comment) {
            return $this->renderError(500, 'Failed to save comment');
        }

        return $this->renderResponse($comment);
    }


    /**
     * Update a comment.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id): Response
    {
        try {
            $data = $request->validate([
                'post_id' => 'integer|nullable',
                'user_id' => 'integer|nullable',
                'author' => 'string|nullable',
                'email' => 'email|nullable',
                'url' => 'string|nullable',
                'ip' => 'string|nullable',
                'content' => 'string|nullable',
                'status' => 'integer|nullable',
                'votes' => 'integer|nullable',
                'type' => 'string|nullable',
                'parent_id' => 'integer|nullable',
            ]);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        }

        $existingComment = $this->commentRepository->find((int)$id);
        if (!$existingComment) {
            return $this->renderError(404, 'Comment not found');
        }

        $comment = $this->commentRepository->update((int) $id, $data);
        if (!$comment) {
            return $this->renderError(500, 'Failed to update comment');
        }
        
        return $this->renderResponse($comment->data);
    }
    public function upvoteComment(Request $request): Response
    {
        $data = $request->all();
        $commentId = isset($data['comment_id']) ? (int) $data['comment_id'] : 0;
        if ($commentId <= 0) {
            return $this->renderError(422, 'comment_id is required');
        }

        // Prefer the authenticated user; fall back to the user_id supplied by the client
        // so callers that have not yet been migrated keep working. Liking is per-user,
        // so we must NOT use the comment owner's id here.
        $authUser = $this->authUser();
        $userId = ($authUser && isset($authUser->user_id))
            ? (int) $authUser->user_id
            : (int) ($data['user_id'] ?? 0);

        if ($userId <= 0) {
            return $this->renderError(403, 'Unauthorized');
        }

        $result = $this->commentRepository->upvoteComment($commentId, $userId);
        if (!$result) {
            return $this->renderError(500, 'Failed to upvote comment');
        }
        return $this->renderResponse($result);
    }
    public function checkedComment(Request $request): Response
    {
        $data = $request->all();
        $commentId = isset($data['comment_id']) ? (int) $data['comment_id'] : 0;
        if ($commentId <= 0) {
            return $this->renderError(422, 'comment_id is required');
        }

        // Prefer the authenticated user; fall back to the user_id supplied by the client
        // so callers that have not yet been migrated keep working. Liking is per-user,
        // so we must NOT use the comment owner's id here.
        $authUser = $this->authUser();
        $userId = ($authUser && isset($authUser->user_id)) ? $authUser->user_id : null;

        if ($userId <= 0) {
            return $this->renderError(403, 'Unauthorized');
        }

        $result = $this->commentRepository->checkedComment($commentId, $userId);
        if (!$result) {
            return $this->renderError(500, 'Failed to upvote comment');
        }
        return $this->renderResponse($result);
    }

    public function submitReplyComment(Request $request): Response
    {
        try {
            $authUser = $this->authUser();
            if (!$authUser) {
                return $this->renderError(403, 'Unauthorized');
            }

            $user = $this->userRepository->find($authUser->user_id);
            if (!$user || $user->user_id !== $authUser->user_id) {
                return $this->renderError(403, 'Unauthorized');
            }

            $data = $request->all();
            $data['user_id'] = $authUser->user_id;

            $comment = $this->commentRepository->createReplyComment($data);
            if ($comment === [] || empty($comment['comment'])) {
                return $this->renderError(403, 'Forbidden: You are not allowed to reply to this comment.');
            }

            return $this->renderResponse($comment);
        } catch (ValidationException $e) {
            return $this->renderError(422, $e->getMessage(), $e->getErrors());
        } catch (Throwable) {
            return $this->renderError(500, 'Failed to submit reply comment');
        }
    }

    /**
     * Delete a comment.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function deleteComment(Request $request, $id): Response
    {
        $result = $this->commentRepository->deleteCommentById((int) $id);
        return $this->renderResponse($result);
    }

    /**
     * Get all comments for a post.
     *
     * @param Request $request
     * @param int $postId
     * @return Response
     */
    public function getByPost(Request $request, $postId): Response
    {
        $comment = $this->commentRepository->findOneBy(['post_id' => (int)$postId]);
        if (!$comment) {
            return $this->renderError(404, 'No comments found for this post');
        }
        return $this->renderResponse($comment->data);
    }

    /**
     * Get all comments by a user.
     *
     * @param Request $request
     * @param int $userId
     * @return Response
     */
    public function getByUser(Request $request, $userId): Response
    {
        $comment = $this->commentRepository->findOneBy(['user_id' => (int)$userId]);
        if (!$comment) {
            return $this->renderError(404, 'No comments found for this user');
        }
        return $this->renderResponse($comment->data);
    }

    /**
     * Get all replies to a comment.
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function getReplies(Request $request, $id): Response
    {
        $comment = $this->commentRepository->findOneBy(['parent_id' => (int)$id]);
        if (!$comment) {
            return $this->renderError(404, 'No replies found for this comment');
        }
        return $this->renderResponse($comment->data);
    }

    /**
     * Normalize the various $_FILES shapes into a simple list that the media repository expects.
     *
     * @param mixed $files
     * @return array<int,array<string,mixed>>
     */
    private function normalizeUploadedFiles(mixed $files): array
    {
        if (!is_array($files)) {
            return [];
        }

        if (array_key_exists('name', $files)) {
            if (is_array($files['name'])) {
                $normalized = [];
                foreach ($files['name'] as $index => $name) {
                    if ($name === null || $name === '') {
                        continue;
                    }

                    $normalized[] = [
                        'name' => $name,
                        'type' => $files['type'][$index] ?? null,
                        'tmp_name' => $files['tmp_name'][$index] ?? null,
                        'error' => $files['error'][$index] ?? null,
                        'size' => $files['size'][$index] ?? null,
                    ];
                }

                return $normalized;
            }

            if ($files['name'] === null || $files['name'] === '') {
                return [];
            }

            return [[
                'name' => $files['name'],
                'type' => $files['type'] ?? null,
                'tmp_name' => $files['tmp_name'] ?? null,
                'error' => $files['error'] ?? null,
                'size' => $files['size'] ?? null,
            ]];
        }

        $normalized = [];
        foreach ($files as $value) {
            foreach ($this->normalizeUploadedFiles($value) as $file) {
                $normalized[] = $file;
            }
        }

        return $normalized;
    }
} 