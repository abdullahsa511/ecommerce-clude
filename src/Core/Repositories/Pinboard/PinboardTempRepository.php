<?php

declare(strict_types=1);

namespace App\Core\Repositories\Pinboard;

use App\Core\Models\Localisation\Language;
use PDO;
use DateTime;
use App\Core\Models\Pinboard\Pinboard;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Models\Pinboard\PinboardTemp;
use App\Core\Models\Pinboard\PinboardTempItem;
use App\Core\Models\Pinboard\PinboardTempItemAccessories;
use App\Core\Models\Post\CommentPhoto;
use App\Core\Models\Post\Comment;
use App\Core\Models\Order\OrderStatus;


class PinboardTempRepository extends BaseRepository implements PinboardTempRepositoryInterface
{
    private PinboardTempItem $pinboardTempItem;
    private PinboardTempItemAccessories $pinboardTempItemAccessories;
    private $language;
    private CommentPhoto $commentPhoto;
    private Comment $comment;
    
    public function __construct(
        PDO $db,       
        PinboardTempItem $pinboardTempItem, 
        PinboardTempItemAccessories $pinboardTempItemAccessories,
        Language $language,
        CommentPhoto $commentPhoto,
        Comment $comment,
    ){
        parent::__construct($db, 'pinboard_temp', PinboardTemp::class);
        $this->pinboardTempItem = $pinboardTempItem;
        $this->pinboardTempItem->setDb($db);
        $this->language = $language;
        $this->language->setDb($db);
        $this->pinboardTempItemAccessories = $pinboardTempItemAccessories;
        $this->pinboardTempItemAccessories->setDb($db);
        $this->commentPhoto = $commentPhoto;
        $this->commentPhoto->setDb($db);
        $this->comment = $comment;
        $this->comment->setDb($db);
    }

    public function allTemporaryPinboards(): array
    {
        $query = $this->model;
        $query->join('order_status', 'order_status.order_status_id', '=', 'pinboard_temp.pinboard_status_id');
        $query->select([
            'pinboard_temp.*',
            '(SELECT COUNT(*) FROM pinboard_temp_item WHERE pinboard_temp_item.pinboard_temp_id = pinboard_temp.pinboard_temp_id) as item_count',
            'order_status.name as pinboard_status_name'
        ]);
        $query->orderBy('pinboard_temp.created_at', 'DESC');
        $results = $query->findAll(false);

        return $this->enrichPinboardsWithStatus($results ?? []);
    }
    private function enrichPinboardsWithStatus(array $pinboards): array
    {
        $statusIds = [];
        foreach ($pinboards as $row) {
            $data = is_array($row) ? $row : (array) $row;
            $statusId = (int) ($data['pinboard_status_id'] ?? 0);
            if ($statusId > 0) {
                $statusIds[] = $statusId;
            }
        }

        $statusMap = $this->getPinboardOrderStatusesByIds($statusIds);

        foreach ($pinboards as $index => $row) {
            $data = is_array($row) ? $row : (array) $row;
            $statusId = (int) ($data['pinboard_status_id'] ?? 0);
            $status = $statusMap[$statusId] ?? null;

            if (is_array($pinboards[$index])) {
                $pinboards[$index]['pinboard_status'] = $status;
            } elseif (is_object($pinboards[$index])) {
                $pinboards[$index]->pinboard_status = $status;
            }
        }

        return $pinboards;
    }

    private function getPinboardOrderStatusesByIds(array $statusIds): array
    {
        $statusIds = array_values(array_unique(array_filter(
            array_map(static fn($id): int => (int) $id, $statusIds),
            static fn(int $id): bool => $id > 0
        )));

        if ($statusIds === []) {
            return [];
        }

        $orderStatus = new OrderStatus();
        $orderStatus->setDb($this->db);
        $rows = $orderStatus
            ->where('language_id', '=', 1)
            ->whereIn('order_status_id', $statusIds)
            ->findAll(false) ?? [];

        $map = [];
        foreach ($rows as $row) {
            $payload = is_array($row) ? $row : (array) ($row->data ?? $row);
            $id = (int) ($payload['order_status_id'] ?? 0);
            if ($id > 0) {
                $map[$id] = new PinboardStatusResponse($payload);
            }
        }

        return $map;
    }



    /**
     * @author sa techonology
     * @created by abdullah
     * @created at 
     * @updated by abdullah
     * @updated at 29-01-2026
     * Save and update pinboard and pinboard items from the frontend
     * 
     * @param array $data
     * @return array
     */
    public function savePinboard(array $data): array
    {
        // validate the pinboard data
        if (empty($data) || !is_array($data)) {
            throw new \InvalidArgumentException('Invalid pinboard payload');
        }

        // validate the pinboard items
        if (
            empty($data['pinboard_items']) ||
            !is_array($data['pinboard_items'])
        ) {
            throw new \InvalidArgumentException('Pinboard items are required');
        }
    
        try {
            $this->db->beginTransaction();
    
            /** ----------------------------------------
             * Parent: Pinboard
             * ---------------------------------------- */
            $pinboardId = $data['pinboard_temp_id'] ?? null;
    
            $pinboardData = [
                'uuid'               => $this->generateUuid(),
                'company_id'         => 1,
                'reference_number'   => $this->generateReference(rand(100, 10000)),
                'job_id'             => 1,
                'pinboard_name'      => $data['job_title'] ?? '',
                'job_title'          => $data['job_title'] ?? '',
                'user_id'            => null,
                'customer_id'        => null,
                'customer_email'     => $data['customer_email'] ?? '',
                'pinboard_status_id' => 0,
            ];
    
            if ($pinboardId) {
                $this->model->clearQuery();
    
                $pinboard = $this->model
                    ->where('pinboard_temp_id', '=', $pinboardId)
                    ->first();
    
                if (!$pinboard) {
                    throw new \RuntimeException('You do not have permission to update this pinboard');
                }
    
                $pinboard->update($pinboardData);
            } else {
                $pinboard = $this->model->create($pinboardData);
                $pinboardId = $pinboard->data->pinboard_temp_id ?? null;
    
                if (!$pinboardId) {
                    throw new \RuntimeException('Failed to create pinboard temp');
                }
            }
    
            /** ----------------------------------------
             * Child: Pinboard Items
             * ---------------------------------------- */
            $createItems = [];
            $updateItems = [];
            $keepItemIds = []; // keep the item id for use later
    
            foreach ($data['pinboard_items'] as $item) {
    
                // if (empty($item['model_id'])) {
                //     throw new \InvalidArgumentException('Model ID is required for pinboard item');
                // }
    
                $payload = [
                    'uuid'         => $this->generateUuid(),
                    'pinboard_temp_id'  => $pinboardId,
                    'model_id'     => $item['model_id'],
                    'model_type'   => $item['model_type'] ?? null,
                    'title'        => $item['title'] ?? null,
                    'description'  => $item['description'] ?? null,
                    'comments'     => json_encode($item['comments']) ?? null,
                    'photo'        => $item['photo'] ?? null,
                    'product_url'  => $item['product_url'] ?? null,
                    'quantity'     => (int) ($item['quantity'] ?? 1),
                    'unit_price'   => 100,
                    'total_price'  => 100,
                    'language_id'  => 1,
                    'sort_order'   => (int) ($item['sort_order'] ?? 1),
                ];
    
                if (!empty($item['pinboard_item_id'])) {
                    $payload['pinboard_temp_item_id'] = $item['pinboard_temp_item_id'];
                    $updateItems[] = $payload;
                    $keepItemIds[] = $item['pinboard_temp_item_id']; // keep the item id for use later
                } else {
                    $createItems[] = $payload;
                }
            }
    
            if (!empty($createItems) && count($createItems) > 0) {
                $this->pinboardTempItem->insert($createItems);
            }
    
            if (!empty($updateItems) && count($updateItems) > 0) {
                $this->pinboardTempItem->upsert($updateItems, ['pinboard_temp_item_id']);
            }
    
            $this->db->commit();
    
            $items = $this->pinboardTempItem->where('pinboard_temp_id', '=', $pinboardId)
                ->findAll();
            $pinboard = (array) $pinboard->data; 
            $pinboard['pinboard_items'] = $items;
    
            return $pinboard;
    
        } catch (\Exception $e) {
            try {
                if ($this->db && method_exists($this->db, 'inTransaction') && $this->db->inTransaction()) {
                    $this->db->rollBack();
                }
            } catch (\Throwable $rollbackEx) {
                // Swallow rollback errors to avoid masking the original exception.
            }
            throw $e;
        }
    }

    private function generateReference(int $userId): string
    {
        return sprintf(
            'REF-%d-%s',
            $userId,
            bin2hex(random_bytes(4))
        );
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

    public function showPinboard(int $pinboardId): PinboardTemp
    {
        $pinboard = $this->model->where('pinboard_temp_id', '=', $pinboardId)
            ->with([
                'pinboard_item' => function ($query) {
                    return $query->with([
                        'product' => function ($query) {
                            return $query
                                ->join('product_content', 'product_content.product_id', '=', 'product.product_id')
                                ->select(['product.product_id', 'product.description', 'product.price', 'product_content.name', 'product_content.content']);
                        },
                        'project' => function ($query) {
                            return $query->select(['project.project_id', 'project.description', 'project.image', 'project.name']);
                        },
                        'media' => function ($query) {
                            return $query->select(['media.media_id', 'media.name', 'media.file', 'media.type', 'media.meta']);
                        },
                        'comment' => function ($query) {
                            return $query->select(['comment.comment_id', 'comment.author', 'comment.content', 'comment.type']);
                        },
                        'post' => function ($query) {
                            return $query
                                ->join('post_content', 'post_content.post_id', '=', 'post.post_id')
                                ->select(['post.post_id', 'post.description', 'post.image', 'post.type', 'post_content.name', 'post_content.content', 'post_content.excerpt']);
                        }
                    ]);
                },
            ])
            ->first();

        return $pinboard;
    }

    public function showTemporaryPinboard(int $pinboardId, ?int $userId = null, ?int $customerId = null): PinboardTemp
    {
        $this->model->clearQuery();
        $query = $this->model->where('pinboard_temp_id', '=', $pinboardId);
        
        $pinboard = $query
            ->with(['pinboard_temp_items' => function ($query) {
                return $query->with(['model'])->orderBy('sort_order', 'ASC');
            }])
            ->select(['pinboard_temp.*'])
            // ->where('pinboard.is_active', '=', 1)
            ->first();

        if ($pinboard) {
            $this->attachPinboardStatusToModel($pinboard);
        }

        return $pinboard;
    }

    private function attachPinboardStatusToModel(PinboardTemp $pinboard): PinboardTemp
    {
        $statusId = (int) ($pinboard->pinboard_status_id ?? $pinboard->data->pinboard_status_id ?? 0);
        $pinboard->data->pinboard_status = $this->getPinboardOrderStatus($statusId);

        return $pinboard;
    }
    private function getPinboardOrderStatus(int $pinboardStatusId): ?PinboardStatusResponse
    {
        if ($pinboardStatusId <= 0) {
            return null;
        }

        $statusMap = $this->getPinboardOrderStatusesByIds([$pinboardStatusId]);

        return $statusMap[$pinboardStatusId] ?? null;
    }
    
}
