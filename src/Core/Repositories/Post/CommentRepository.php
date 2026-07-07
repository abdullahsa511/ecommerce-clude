<?php

declare(strict_types=1);

namespace App\Core\Repositories\Post;

use App\Core\Repositories\Base\BaseRepository;
use App\Core\Models\Post\Comment;
use App\Core\Models\Base\Model;
use App\Core\Models\Post\CommentPhoto;
use App\Core\Models\Post\CommentUpvote;
use App\Core\Models\User;
use PDO;

class CommentRepository extends BaseRepository implements CommentRepositoryInterface
{
    private CommentPhoto $commentPhoto;
    private CommentUpvote $commentUpvote;
    private User $user;

    public function __construct(PDO $db, CommentPhoto $commentPhoto, CommentUpvote $commentUpvote, User $user)
    {
        parent::__construct($db, 'comment', Comment::class);
        $this->commentPhoto = $commentPhoto;
        $this->commentPhoto->setDb($db);
        $this->commentUpvote = $commentUpvote;
        $this->commentUpvote->setDb($db);
        $this->user = $user;
        $this->user->setDb($db);
    }

    public function getAll(
        ?int $languageId =null,
        ?int $postId = null,
        ?int $userId = null,
        ?int $status = null,
        bool $postTitle = false,
        int $start = 0,
        int $limit = 10
    ): array {
        $query = $this->model;

        if ($postId !== null) {
            $query->where('post_id', '=', $postId);
        }

        if ($userId !== null) {
            $query->where('user_id', '=', $userId);
        }

        if ($status !== null) {
            $query->where('status', '=', $status);
        }

        if ($postTitle) {
            $query->with(['postContent' => function($model) use ($languageId) {
                $model->where('language_id', '=', $languageId);
            }]);
        }


        $query->orderBy('parent_id', 'ASC')->orderBy('comment_id', 'ASC');


        if ($limit !== null) {
            $query->limit($limit);
        }

        if ($start !== null) {
            $query->offset($start);
        }
        

        // Get results
        $results = $query->findAll() ?? [];
        $total = $query->countAll();
        $perPage = $limit ?? $this->model->limitValue;

        return [
            'items' => collect($results),
            'total' => $total,
            "total_pages" => (int)ceil($total / $perPage),
            "current_page" => (int)($start / $perPage + 1),
            "per_page" => $perPage
        ];
    }

    public function findAll(): array
    {
        $comments = parent::findAll();
        
        // Add question_count to each question
        foreach ($comments as &$comment) {
            $comment['comment_count'] = $this->getCommentCount($comment['comment_id']);
        }
        
        return $comments;
    }


    private function getCommentCount(int $parentId): int
    {
        $sql = "SELECT COUNT(*) as count FROM comment WHERE parent_id = :parent_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['parent_id' => $parentId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return (int) $result['count'];
    }

    public function findCommentsById_new(int $id, string $modelType):array
    {
        $comments = $this->model
        ->join('user', 'comment.user_id', '=', 'user.user_id')
        ->where('model_id', '=', $id)
        ->where('model_type', '=', $modelType)
        ->select(['comment.*', 'user.username as user_name', 'user.email as user_email'])
        ->with(['commentPhoto'])
        ->orderBy('comment_id', 'ASC')
        ->orderBy('is_reply', 'ASC')
        ->findAll();

        foreach ($comments as &$comment) {
            $comment['commentPhoto'] = json_decode($comment['commentPhoto'], true);
                foreach ($comment['commentPhoto'] as &$photo) {
                    if (
                        isset($photo['image']) &&
                        is_array($photo['image']) &&
                        isset($photo['image']['objectURL'])
                    ) {
                        $photo['objectURL'] = $photo['image']['objectURL'];
                        unset($photo['image']);
                    } else {
                        $photo['objectURL'] = '/img/account-dashboard/profile-pic.png';
                    }
                }
        }

        return $comments;
    }

    public function findCommentsById(int $id, string $modelType, ?int $userId = null): array
    {
        $comments = $this->model
            ->join('user', 'comment.user_id', '=', 'user.user_id')
            ->where('model_id', '=', $id)
            ->where('model_type', '=', $modelType)
            ->select(['comment.*', 'user.username as user_name', 'user.email as user_email'])
            ->with(['commentPhoto'])
            ->orderBy('comment_id', 'DESC')
            ->orderBy('is_reply', 'ASC')
            ->findAll();


        // print_r($comments);
        // exit;

        // Collect all the comment ids in this thread so we can fetch the real
        // upvote totals (and the current user's liked rows) in a single query each.
        $commentIds = [];
        if (!empty($comments)) {
            $commentIds = array_values(array_unique(array_filter(
                array_map(static fn($c) => (int) ($c['comment_id'] ?? 0), $comments),
                static fn($v) => $v > 0
            )));
        }

        // Real, cross-user vote counts straight from comment_upvote so the UI is
        // not at the mercy of the cached comment.votes column.
        $voteCounts = $this->countActiveUpvotesByCommentIds($commentIds);

        // Resolve which of these comments the current user has actively upvoted.
        $likedCommentIds = [];
        if ($userId !== null && $userId > 0 && !empty($commentIds)) {
            // Clear commentUpvote model's own state - parent clearQuery() does not touch it.
            $this->commentUpvote->clearQuery();
            $likedRows = $this->commentUpvote
                ->whereIn('comment_id', $commentIds)
                ->where('user_id', '=', $userId)
                ->whereNull('deleted_at')
                ->findAll();
            $this->commentUpvote->clearQuery();

            foreach ($likedRows as $row) {
                $rowCommentId = is_array($row)
                    ? (int) ($row['comment_id'] ?? 0)
                    : (int) ($row->comment_id ?? 0);
                if ($rowCommentId > 0) {
                    $likedCommentIds[$rowCommentId] = true;
                }
            }
        }

        $normalizedById = [];

        foreach ($comments as $comment) {

            $rawCommentPhoto = $comment['commentPhoto'] ?? [];
            $commentPhoto = is_string($rawCommentPhoto)
                ? json_decode($rawCommentPhoto, true)
                : $rawCommentPhoto;

            if (!is_array($commentPhoto) || count($commentPhoto) === 0 || !isset($commentPhoto[0]['comment_photo_id'])) {
                $comment['commentPhoto'] = [];
            } else {
                $normalizedPhotos = [];
                foreach ($commentPhoto as $photo) {
                    if (!is_array($photo)) {
                        continue;
                    }

                    if (
                        isset($photo['image']) &&
                        is_array($photo['image']) &&
                        isset($photo['image']['objectURL']) &&
                        !empty($photo['image']['objectURL'])
                    ) {
                        $fileName = $photo['image']['name'] ?? '';
                        $objectURL = $photo['image']['objectURL'];
                        $fileIcon = $this->getFileIcon($fileName, $objectURL);
                        $photo['objectURL'] = $fileIcon['objectURL'] ?? '';
                        $photo['extension'] = $fileIcon['extension'] ?? '';
                        $photo['download_url'] = $objectURL ?? '';
                        unset($photo['image']);
                    } elseif (empty($photo['objectURL'])) {
                        $photo['objectURL'] = '/img/account-dashboard/profile-pic.png';
                    }

                    $normalizedPhotos[] = $photo;
                }

                $comment['commentPhoto'] = $normalizedPhotos;
            }

            $commentIdInt = (int) $comment['comment_id'];
            $comment['replay'] = [];
            $comment['liked'] = isset($likedCommentIds[$commentIdInt]);
            $comment['votes'] = $voteCounts[$commentIdInt] ?? 0;

            $normalizedById[$commentIdInt] = $comment;
        }

        // Parents first — replies may have a higher comment_id and appear earlier
        // when ordering DESC, so a single pass cannot nest them reliably.
        $data = [];
        foreach ($normalizedById as $commentId => $comment) {
            if (empty($comment['parent_id'])) {
                $data[$commentId] = $comment;
            }
        }

        foreach ($normalizedById as $comment) {
            $parentId = (int) ($comment['parent_id'] ?? 0);
            if ($parentId > 0 && isset($data[$parentId])) {
                $data[$parentId]['replay'][] = $comment;
            }
        }

        return array_values($data);
    }

    /**
     * Return [comment_id => active_upvote_count] for the given comment ids.
     *
     * @param array<int,int> $commentIds
     * @return array<int,int>
     */
    private function countActiveUpvotesByCommentIds(array $commentIds): array
    {
        if (empty($commentIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($commentIds), '?'));
        $sql = "SELECT comment_id, COUNT(*) AS vote_count
                FROM comment_upvote
                WHERE deleted_at IS NULL AND comment_id IN ({$placeholders})
                GROUP BY comment_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_values($commentIds));

        $counts = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $counts[(int) $row['comment_id']] = (int) $row['vote_count'];
        }

        return $counts;
    }

    public function createComment(array $data ,array $files):array
    {
        try{
            $this->db->beginTransaction();

            // insert comment table
            $commentData = [
                'uuid' => isset($data['uuid']) ? $data['uuid'] : $this->generateUuid(),
                'model_id' => $data['model_id'],
                'post_id' => $data['post_id'] ?? 1,
                'model_type' => $data['model_type'],
                'user_id' => $data['user_id']?? 1,
                'author' => $data['author'] ?? 'Nazmul Hossen',
                'email' => $data['email'] ?? 'nazmul@satechnology.com.au',
                'url' => $data['url'] ?? 'https://www.satechnology.com.au',
                'ip' => $data['ip'] ?? '127.0.0.1',
                'content' => $data['content'] ?? '',
                'status' => $data['status'] ?? 1,
                'votes' => $data['votes'] ?? 0,
                'type' => $data['type'],
                'parent_id' => $data['parent_id'] ?? null,
                // 'quoteAccount' => $data['quoteAccount'] ?? '',
            ];
            $comment = $this->model->create($commentData);
            $commentId = $comment->comment_id;
            $commentData['comment_id'] = $commentId;

            // $commentData['created_at'] = $comment->created_at;
            // $commentData['updated_at'] = $comment->updated_at;

            $imageUrl = [];
            $photoFormat = [];
            if($files && count($files) > 0 && isset($files)){       
                $commentPhotoData = [];
                foreach ($files as $item) {
                    $img = [
                        'model_id' => $data['model_id'],
                        'name' => $item['name'] ?? '',
                        'size' => $item['size'] ?? '',
                        'type' => $item['type'] ?? '',
                        'image' => $item['image'] ?? '',
                        'status' => isset($item['status']) && is_array($item['status'])
                            ? $item['status']
                            : ['name' => 'Uploaded', 'severity' => 'success'],
                        'media_id' => $item['media_id'] ?? null,
                        'objectURL' => ($item['objectURL'] ?? ''),
                        'created_at' => date('Y-m-d H:i:s'),
                        'product_id' => null,
                        'description' => $item['description'] ?? '',
                        'post_image_id' => null,
                        'product_image_id' => null,
                        'comment_photo_id' => null,
                    ];
                    // $imageUrl[] = $item['objectURL'] ?? '';
                    $fileIcon = $this->getFileIcon($item['name'], $item['objectURL']);
                    $photoFormat[] = [
                        "media_id" => $item['media_id'] ?? null,
                        "comment_id" => $commentId,
                        "created_at" => date('Y-m-d H:i:s'),
                        "sort_order" => 0,
                        "updated_at" => date('Y-m-d H:i:s'),
                        "active_status" => 1,
                        "comment_photo_id" => null,
                        "objectURL" => $fileIcon['objectURL'] ?? '',
                        "extension" => $fileIcon['extension'] ?? '',
                        'download_url' => $item['objectURL'] ?? '',
                    ];
                    $commentPhotoData[] = [
                        'image' => json_encode($img),
                        'comment_id' => $commentId,
                        'media_id' => $item['media_id'] ?? null,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ];
                   
                }
                $this->commentPhoto->insert($commentPhotoData);
            }

            $this->db->commit();
            // $recentComment = $this->findCommentsById($data['model_id'], $data['model_type'], $data['user_id']);
            // return
            
            $found = $this->model->find($commentId);
            $recentComment = [];
            if ($found !== null) {
                $decoded = json_decode(json_encode($found->data), true);
                $recentComment = is_array($decoded) ? $decoded : [];
            }
            $recentComment['commentPhoto'] = $photoFormat;

            return [
                'comment' => $recentComment,
                'comment_photos' => $commentPhotoData ?? [],
                'image_url' => $imageUrl,
            ];

        }catch(\Exception $e){
            $this->db->rollBack();
            throw $e;
        }
    }

    private function generateUuid(): string
    {
        $uuid = \uniqid('', true);
        $uuid = str_replace('.', '', $uuid);
        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($uuid, 0, 8),
            substr($uuid, 8, 4),
            substr($uuid, 12, 4),
            substr($uuid, 16, 4),
            substr($uuid, 20)
        );
    }

    public function upvoteComment(int $comment_id, int $user_id)
    {
        try{
            $this->db->beginTransaction();

            // Find existing upvote row for this (comment, user) pair, regardless of soft-delete state.
            // NOTE: BaseRepository::clearQuery() only clears $this->model, NOT $this->commentUpvote,
            // so we must clear the upvote model's own where/params state between queries to avoid
            // filters leaking across calls (which would, e.g., make the count only include this user).
            $this->commentUpvote->clearQuery();
            $userVoted = $this->commentUpvote
                ->where('comment_id', '=', $comment_id)
                ->where('user_id', '=', $user_id)
                ->first();

            if ($userVoted) {
                $isCurrentlyActive = empty($userVoted->deleted_at);
                // Toggle: if currently active, soft-delete (unlike); if soft-deleted, restore (like).
                $userVoted->update([
                    'deleted_at' => $isCurrentlyActive ? date('Y-m-d H:i:s') : null,
                ]);
                $liked = !$isCurrentlyActive;
            } else {
                // First time the user is liking this comment.
                $this->commentUpvote->clearQuery();
                $this->commentUpvote->create([
                    'comment_id' => $comment_id,
                    'user_id' => $user_id,
                ]);
                $liked = true;
            }

            // Count only active (not soft-deleted) upvotes for this comment across ALL users.
            $this->commentUpvote->clearQuery();
            $countUpvotes = $this->commentUpvote
                ->where('comment_id', '=', $comment_id)
                ->whereNull('deleted_at')
                ->countAll();
            $this->commentUpvote->clearQuery();

            $this->clearQuery();
            $comment = $this->model->find($comment_id);
            if ($comment) {
                $comment->update(['votes' => $countUpvotes]);
            }
            $this->clearQuery();

            $this->db->commit();

            return [
                'comment_id' => $comment_id,
                'votes' => (int) $countUpvotes,
                'liked' => $liked,
            ];

        }catch(\Exception $e){
            $this->db->rollBack();
            throw $e;
        }
    }
    public function checkedComment(int $comment_id, int $user_id)
    {
        try{
            $this->db->beginTransaction();
            $this->commentUpvote->clearQuery();
            $userVoted = $this->model
                ->where('comment_id', '=', $comment_id)
                ->where('user_id', '=', $user_id)
                ->first();

            if ($userVoted) {
                $userVoted->update([
                    'is_checked' => 1,
                ]);
            } 

            $this->db->commit();

            return [
                'comment_id' => $comment_id
            ];

        }catch(\Exception $e){
            $this->db->rollBack();
            throw $e;
        }
    }

    public function createReplyComment(array $data):array
    {
        try{
            $this->db->beginTransaction();

            $comment = $this->model->where('comment_id', '=', $data['comment_id'])->where('user_id', '=', $data['user_id'])->first();

            if($comment){
                $this->clearQuery();
                $replyCommentData = [
                    'uuid' => $comment->uuid ?? $this->generateUuid(),
                    'post_id' => $comment->post_id,
                    'model_id' => $comment->model_id,
                    'model_type' => $comment->model_type,
                    'author' => $comment->author,
                    'email' => $comment->email,
                    'url' => $comment->url,
                    'ip' => $comment->ip,
                    'type' => $comment->type,
                    'user_id' => $data['user_id'],
                    'content' => $data['content'],
                    'status' => $comment->status,
                    'is_reply' => isset($data['is_reply']) && $data['is_reply'] ? 1 : 0,
                    'votes' => $comment->votes,
                    'parent_id' => $data['comment_id'],
                ];
                $replyComment = $this->model->create($replyCommentData);
                $replyComment = $replyComment ? $replyComment->toArray() : [];
            }else{
                return [];
            }
            $this->db->commit();
            return ['comment' => $replyComment];
        }catch(\Exception $e){
            $this->db->rollBack();
            throw $e;
        }
    }

    public function deleteCommentById(int $id): bool
    {
        try{
            $this->db->beginTransaction();
            $result = $this->model->delete($id);
            if($result){
                $this->commentPhoto->deleteWhere(['comment_id' => $id]);
            }
            
            $this->db->commit();

            return $result;
        }catch(\Exception $e){
            $this->db->rollBack();
            return false;
        }
    }

    private function getFileIcon(?string $fileName, ?string $objectURL = null): array
    {
        $fileFormatImages = [
            'GSM' => '/media/design-resource/icons/gsm.png',
            'DWG' => '/media/design-resource/icons/dwg.png',
            'MAX' => '/media/design-resource/icons/max.png',
            'SKP' => '/media/design-resource/icons/skp.png',
            'RFA' => '/media/design-resource/icons/rfa.png',
            'ZIP' => '/media/design-resource/icons/zip.png',
            'PDF' => '/media/design-resource/icons/pdf.png',
            'DOC' => '/media/design-resource/icons/doc.png',
            'DOCX' => '/media/design-resource/icons/docx.png',
            'XLS' => '/media/design-resource/icons/xls.png',
            'XLSX' => '/media/design-resource/icons/xlsx.png',
            'PPT' => '/media/design-resource/icons/ppt.png',
            'PPTX' => '/media/design-resource/icons/pptx.png',
        ];

        $extension = strtoupper(trim(pathinfo((string) $fileName, PATHINFO_EXTENSION)));
        if ($extension !== '' && isset($fileFormatImages[$extension])) {
            return ['objectURL' => $fileFormatImages[$extension], 'extension' => $extension];
        }

        return ['objectURL' => $objectURL ?? '/media/design-resource/icons/default.png', 'extension' => $extension ?? ''];
    }
}