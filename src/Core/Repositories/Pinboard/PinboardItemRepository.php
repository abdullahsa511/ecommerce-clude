<?php

declare(strict_types=1);

namespace App\Core\Repositories\Pinboard;

use App\Core\Models\Product\Product;
use PDO;
use App\Core\Models\Pinboard\PinboardItem;
use App\Core\Models\Pinboard\Pinboard;
use App\Core\Models\Pinboard\PinboardItemAccessories;
use App\Core\Repositories\Base\BaseRepository;
use App\Core\Models\Pinboard\PinboardItemData;
use App\Core\Models\Post\CommentPhoto;
use App\Core\Models\Visit\VisitShowroom;
use App\Core\Models\Post\Comment;
use League\Csv\Query\Limit;
use function App\Core\System\utils\currentDateTime;

class PinboardItemRepository extends BaseRepository implements PinboardItemRepositoryInterface
{
    private Product $product;
    private Pinboard $pinboard;
    private PinboardItemAccessories $pinboardItemAccessories;
    private CommentPhoto $commentPhoto;
    private Comment $comment;
    private VisitShowroom $visitShowroom;
    public function __construct(
            PDO $db, 
            Product $product,
            Pinboard $pinboard, 
            CommentPhoto $commentPhoto, 
            Comment $comment, 
            VisitShowroom $visitShowroom,
            PinboardItemAccessories $pinboardItemAccessories
     ){
        parent::__construct($db, 'pinboard_item', PinboardItem::class);
        $this->product = $product;
        $this->product->setDb($db);
        $this->pinboard = $pinboard;
        $this->pinboard->setDb($db);
        $this->pinboardItemAccessories = $pinboardItemAccessories;
        $this->pinboardItemAccessories->setDb($db);
        $this->commentPhoto = $commentPhoto;
        $this->commentPhoto->setDb($db);
        $this->comment = $comment;
        $this->comment->setDb($db);
        $this->visitShowroom = $visitShowroom;
        $this->visitShowroom->setDb($db);
    }

    public function createPinboardItem(array $pinboardItems): array
    {
        $mappedItems = array_map(function($item) {
            return [
                'pinboard_id' => $item['pinboard_id'] ?? null,
                'product_id' => $item['product_id'] ?? null,
                'description' => $item['description'] ?? '',
                'quantity' => $item['quantity'] ?? 0,
                'unit_price' => $item['unit_price'] ?? 0,
                'total_price' => $item['total_price'] ?? 0,
                'uuid' => $this->generateUuid(),
                'language_id' => $item['language_id'] ?? 1,
                'project_id' => $item['project_id'] ?? null,
                'media_id' => $item['media_id'] ?? null,
                'comment_id' => $item['comment_id'] ?? null,
                'post_id' => $item['post_id'] ?? null,
                'sort_order' => $item['sort_order'] ?? 1,
            ];
        }, $pinboardItems);

        $this->model->upsert($mappedItems, ['pinboard_item_id', 'language_id']);
        return $mappedItems;
    }

    public function updatePinboardItem(PinboardItemData $pinboardItemData): PinboardItem
    {
        $pinboardDataArray = $pinboardItemData->toArray();
        $pinboard = $this->model->find($pinboardDataArray['pinboard_id']);
        $pinboard = $pinboard->update($pinboardDataArray);

        return $pinboard;
    }

    public function showPinboardItem(int $pinboardId): PinboardItem
    {
        $pinboard = $this->model->where('pinboard_item_id', '=', $pinboardId)
        ->first();

        return $pinboard;
    }

    public function productList(string $search): array
    {
        $result = $this->product
            // ->with([
            //     'prices' => function($query){
            //         return $query->select(['price']);
            //     }
            // ])
            ->join('product_content', 'product_content.product_id', '=', 'product.product_id')
            ->where('product_content.name', 'LIKE', '%' . $search . '%')
            ->select(['product.product_id', 'product.description', 'product.price', 'product_content.name'])
            ->orderBy('product.product_id', 'DESC')
            ->limit(50)
            ->findAll(false);
            
        return $result;
    }

    private function generateUuid(): string
    {
        $uuid = \uniqid('', true);
        $uuid = str_replace('.', '', $uuid);
        return sprintf('%s-%s-%s-%s-%s',
            substr($uuid, 0, 8),
            substr($uuid, 8, 4),
            substr($uuid, 12, 4),
            substr($uuid, 16, 4),
            substr($uuid, 20)
        );
    }

    public function createPinboardItems(array $pinboardItems): array
    {
        $mappedItems = array_map(function($item) {
            return [
                'pinboard_item_id' => $item['pinboard_item_id'] ?? null,
                'language_id' => $item['language_id'] ?? 1,
                'uuid' => $item['uuid'] ?? $this->generateUuid(),
                'pinboard_id' => $item['pinboard_id'] ?? null,
                'model_id' => $item['model_id'] ?? null,
                'model_type' => $item['model_type'] ?? null,
                'description' => $item['description'] ?? '',
                'options' => $item['options'] ?? null,
                'comments' => $item['comments'] ?? null,
                'quantity' => $item['quantity'] ?? 0,
                'unit_price' => $item['unit_price'] ?? 0,
                'total_price' => $item['total_price'] ?? 0,
                'photo' => $item['photo'] ?? null,
                'product_url' => $item['product_url'] ?? null,
                'sort_order' => $item['sort_order'] ?? 1,
                'created_at' => $item['created_at'] ?? null,
                'updated_at' => $item['updated_at'] ?? null,
            ];
        }, $pinboardItems);

        $this->model->upsert($mappedItems, ['pinboard_id', 'model_id', 'model_type']);
        return $pinboardItems;
    }

    public function deleteByPinboardId(int $pinboardId): bool
    {
        $pinboardItems = $this->model->where('pinboard_id', '=', $pinboardId)->findAll();
        $deleted = true;
        
        foreach ($pinboardItems as $pinboardItem) {
            // $pinboardItem is an array, not an object
            if (!$this->delete($pinboardItem['pinboard_item_id'])) {
                $deleted = false;
            }
        }
        
        return $deleted;
    }

    public function deleteByPinboardItemId(int $pinboardItemId): bool
    {
        $pinboardItem = $this->model->where('pinboard_item_id', '=', $pinboardItemId)->first();
        if (!$pinboardItem) {
            return false;
        }
        $deleted = $pinboardItem->delete((int) $pinboardItemId);  
        if ($deleted) {
            $pinboardId = $pinboardItem->pinboard_id;
            $this->pinboard->updateWhere(
                ['updated_at' => currentDateTime()],
                ['pinboard_id' => $pinboardId]
            );
        }

        if (!$deleted) {
            return false;
        }
        return true;
    }
    // get pinboard items by user id
    public function getPinboardItems_backup(int $userId): array
    {
        $pinboardItems = $this->model
        ->join('pinboard', 'pinboard.pinboard_id', '=', 'pinboard_item.pinboard_id')
        ->join('product', 'product.product_id', '=', 'pinboard_item.product_id')
        ->join('product_content', 'product_content.product_id', '=', 'product.product_id')
        ->select([
            'pinboard_item.pinboard_item_id as id', 
            'pinboard_item.description', 
            'pinboard_item.photo', 
            'pinboard_item.quantity',
            'pinboard_item.product_id',
            'pinboard_item.project_id',
            'product.product_id', 
            'product_content.name as title', 
            'product.image', 
            'product.product_code'
        ])
        ->where('pinboard.user_id', '=', $userId)
        ->orderBy('pinboard_item.sort_order', 'ASC')
        ->orderBy('pinboard_item.pinboard_item_id', 'ASC')
        ->findAll(false);

        // image format 
        $pinboardItems = array_map(function($item) {
            $item['image'] = json_decode($item['image'], true)[0]['objectURL'] ?? null;
            $item['title'] = $item['title'] ?? $item['product_code'];
            $item['type'] = $item['product_id'] ? 'product' : ($item['project_id'] ? 'project' : null);
            return $item;
        }, $pinboardItems);
        if (!$pinboardItems) {
            return [];
        }
        return $pinboardItems;
    }

    public function getPinboard(?int $userId = null, ?int $pinboardId = null, ?int $customerId = null): array
    {
        $query = $this->pinboard
            ->join('customer', 'customer.customer_id', '=', 'pinboard.customer_id')
            ->with(['pinboard_items' => function ($query) {
                return $query->with(['model'])->orderBy('sort_order', 'ASC');
            }])
            ->select(['pinboard.*', 'customer.gmail_Id as customer_email'])
            ->where('pinboard.is_active', '=', 1);
            if ($userId) {
                $query = $query->where('pinboard.user_id', '=', $userId);
            }
            if ($customerId) {
                $query = $query->where('pinboard.customer_id', '=', $customerId);
            }
            if ($pinboardId) {
                $query = $query->where('pinboard.pinboard_id', '=', $pinboardId);
            }
        $pinboard = $query->first();
    
        if (!$pinboard) {
            return [];
        }
        $pinboard = (array) $pinboard->data;
    
        // Decode JSON pinboard items
        $pinboard_items = isset($pinboard['pinboard_items']) ? json_decode($pinboard['pinboard_items'], true) : [];
    
        if (!is_array($pinboard_items)) {
            $pinboard['pinboard_items'] = [];
        } else {
            // Filter out null or empty items

            $item_images = array_values(array_filter($pinboard_items, function ($item) {
                return $item['model_type'] == 'images';
            }));
            $pinboard['pinboard_items'] = $pinboard_items;
            $pinboard['item_images'] = $item_images;
        }
    
        return $pinboard;
    }

    public function getPinboardWithAllStatus(?int $userId = null, ?int $pinboardId = null): array
    {
        $query = $this->pinboard
        ->join('customer', 'customer.customer_id', '=', 'pinboard.customer_id')
            ->select(['pinboard.*', 'customer.gmail_Id as customer_email'])
            ->with(['pinboard_items' => function ($query) {
                return $query->with(['model'])->orderBy('sort_order', 'ASC');
            }]);
            if ($userId) {
                $query = $query->where('pinboard.user_id', '=', $userId);
            }
            if ($pinboardId) {
                $query = $query->where('pinboard.pinboard_id', '=', $pinboardId);
            }
        $pinboard = $query->first();
    
        if (!$pinboard) {
            return [];
        }
        $pinboard = (array) $pinboard->data;
    
        // Decode JSON pinboard items
        $items = isset($pinboard['pinboard_items']) ? json_decode($pinboard['pinboard_items'], true) : [];
    
        if (!is_array($items)) {
            $pinboard['pinboard_items'] = [];
        } else {
            // Filter out null or empty items
            $items = array_values(array_filter($items, function ($item) {
                return !empty($item['pinboard_item_id']);
            }));
            usort($items, function ($a, $b) {
                $aSort = isset($a['sort_order']) ? (int) $a['sort_order'] : PHP_INT_MAX;
                $bSort = isset($b['sort_order']) ? (int) $b['sort_order'] : PHP_INT_MAX;
                if ($aSort === $bSort) {
                    $aId = isset($a['pinboard_item_id']) ? (int) $a['pinboard_item_id'] : PHP_INT_MAX;
                    $bId = isset($b['pinboard_item_id']) ? (int) $b['pinboard_item_id'] : PHP_INT_MAX;
                    return $aId <=> $bId;
                }
                return $aSort <=> $bSort;
            });
            $pinboard['pinboard_items'] = $items;
        }
    
        return $pinboard;
    }

    public function getPinboardItemsByPinboardId(int $pinboardId): array
    {
        $pinboardItems = $this->model
        ->join('pinboard', 'pinboard.pinboard_id', '=', 'pinboard_item.pinboard_id')
        ->join('product', 'product.product_id', '=', 'pinboard_item.product_id')
        ->join('product_content', 'product_content.product_id', '=', 'product.product_id')
        ->select([
            'pinboard.pinboard_name',
            'pinboard_item.pinboard_item_id', 
            'pinboard_item.pinboard_id',
            'pinboard_item.sort_order',
            'pinboard_item.model_id', 
            'pinboard_item.model_type',
            'pinboard_item.description', 
            'pinboard_item.photo', 
            'pinboard_item.quantity',
            'pinboard_item.comments',
            'pinboard_item.product_id',
            'product_content.name as title', 
            'product.image', 
            'product.product_code'
        ])
        ->where('pinboard.pinboard_id', '=', $pinboardId)
        ->orderBy('pinboard_item.sort_order', 'ASC')
        ->orderBy('pinboard_item.pinboard_item_id', 'ASC')
        ->findAll(false);

        // here use pinboardItemRData Model get and format the data
        // $pinboardItemData = new PinboardItemData($pinboardItems);
        // image format 
        $pinboardItems = array_map(function($item) {
            $item['image'] = isset($item['photo']) ? $item['photo'] : '/img/dashboard-pinboard/pinboard-img-01.png';
            $item['type'] = $item['model_type'] ? $item['model_type'] : 'product';
            $item['name'] = $item['product_code']; // 
            $item['options'] = [
                [ 'src' => '/img/product-detail/second circle.png', 'alt' => 'Option 1' ],
                [ 'src' => '/img/product-detail/second circle.png', 'alt' => 'Option 2' ],
                [ 'src' => '/img/product-detail/third circle.png',  'alt' => 'Option 3' ],
                [ 'src' => '/img/product-detail/first circle.png',  'alt' => 'Option 4' ]
            ];
            $item['quote'] = $item['description'] ?? null;
            $item['comments'] = $item['comments'] ?? null;
            $item['comment_placeholder'] = 'Add A Comment';
            $item['white_btn'] = 'Accept Quote';
            $item['black_btn'] = 'Accept Quote';
            return $item;
        }, $pinboardItems);

        if (!$pinboardItems) {
            return [];
        }
        return $pinboardItems;
    }
    public function authUser(): array
    {
        $sessionData = $_SESSION['user_data'];
        if (!$sessionData) {
            return [];
        }
        return is_array($sessionData) ? $sessionData : (array) $sessionData;
    }

    public function reorderPinboardItems(array $pinboardItems): array
    {

        // $sessionData = $this->authUser();
        $mappedItems = [];

        foreach (array_values($pinboardItems) as $index => $item) {

            if (!isset($item['pinboard_item_id'])) {
                continue;
            }

            $mappedItems[] = [
                'pinboard_item_id' => isset($item['pinboard_item_id']) ? $item['pinboard_item_id'] : null,
                'pinboard_id'      => isset($item['pinboard_id']) ? $item['pinboard_id'] : null,
                'model_id'         => isset($item['model_id']) ? $item['model_id'] : null,
                // 'model_type'       => isset($item['model_type']) ? $item['model_type'] : null,
                'description'      => isset($item['description']) ? $item['description'] : '',
                'comments'         => isset($item['comments']) ? $item['comments'] : null,
                'uuid'             => isset($item['uuid']) ? $item['uuid'] : $this->generateUuid(),
                'language_id'      => isset($item['language_id']) ? $item['language_id'] : 1,
                'sort_order'       => $index + 1,
            ];
        }

        $this->model->upsert($mappedItems, ['pinboard_item_id', 'language_id']);
        return $mappedItems;
    }
    public function addToPinboard(array $pinboardItem): array
    {
        // Check if item has pinboard_id property if not return error 
        if (!isset($pinboardItem['pinboard_id']) || empty($pinboardItem['pinboard_id'])) {
            return [
                'error' => true,
                'message' => 'pinboard_id is required'
            ];
        }

        // Check if model_id and model_type are provided
        if (!isset($pinboardItem['model_id']) || !isset($pinboardItem['model_type'])) {
            return [
                'error' => true,
                'message' => 'model_id and model_type are required'
            ];
        }

        $pinboardId = $pinboardItem['pinboard_id'];
        $modelId = $pinboardItem['model_id'];
        $modelType = $pinboardItem['model_type'];

        // Check if the item already exists in pinboard_item table under the same pinboard
        // Create a fresh model instance for the query
        $queryModel = new PinboardItem();
        if ($this->db) {
            $queryModel->setDb($this->db);
        }
        $existingItem = $queryModel->findOneBy([
            'pinboard_id' => $pinboardId,
            'model_id' => $modelId,
            'model_type' => $modelType
        ]);

        // check pinboard item is already exists in pinboard_item table under the same pinboard
        $pinboardData = $this->pinboard->where('pinboard_id', '=', $pinboardId)->first();
        if ($existingItem) {
            // Item exists, increase quantity
            $currentQuantity = $existingItem->quantity ?? 0;
            $newQuantity = $currentQuantity + ($pinboardItem['quantity'] ?? 1);
            
            $updateData = [
                'quantity' => $newQuantity
            ];
            
            // Update total_price if unit_price is provided
            if (isset($pinboardItem['unit_price'])) {
                $updateData['unit_price'] = $pinboardItem['unit_price'];
                $updateData['total_price'] = $newQuantity * $pinboardItem['unit_price'];
            } elseif (isset($existingItem->unit_price)) {
                $updateData['total_price'] = $newQuantity * $existingItem->unit_price;
            }elseif(isset($pinboardItem['options'])) {
                $updateData['options'] = json_encode($pinboardItem['options']);
            }

            $existingItem->update($updateData);
            $pinboardData->update(['updated_at' => date('Y-m-d H:i:s')]);
            // Return the updated item with id and quantity
            return [
                'error' => false,
                'pinboard_item_id' => $existingItem->pinboard_item_id,
                'quantity' => $newQuantity,
                'message' => 'Item quantity updated'
            ];
        } else {
            // Item doesn't exist, create new pinboard item
            $newItemData = [
                'pinboard_id' => $pinboardId,
                'model_id' => $modelId,
                'model_type' => $modelType,
                'description' => $pinboardItem['description'] ?? '',
                'comments' => $pinboardItem['comments'] ?json_encode($pinboardItem['comments']) : null,
                'quantity' => $pinboardItem['quantity'] ?? 1,
                'unit_price' => $pinboardItem['unit_price'] ?? 0,
                'total_price' => ($pinboardItem['quantity'] ?? 1) * ($pinboardItem['unit_price'] ?? 0),
                'uuid' => $this->generateUuid(),
                'language_id' => $pinboardItem['language_id'] ?? 1,
                'sort_order' => $pinboardItem['sort_order'] ?? 1,
                'photo' => $pinboardItem['photo'] ?? null,
                'product_url' => $pinboardItem['product_url'] ?? null,
                'options' => isset($pinboardItem['options']) ? json_encode($pinboardItem['options']) : null,
            ];

            // Create a fresh model instance for the create operation
            $newModel = new PinboardItem();
            if ($this->db) {
                $newModel->setDb($this->db);
            }
            $createdItem = $newModel->create($newItemData);
            $pinboardData->update(['updated_at' => date('Y-m-d H:i:s')]);
            
            if ($createdItem) {
                // Return the created item with id and quantity
                return [
                    'error' => false,
                    'pinboard_item_id' => $createdItem->pinboard_item_id,
                    'quantity' => $createdItem->quantity,
                    'message' => 'Item added to pinboard'
                ];
            } else {
                return [
                    'error' => true,
                    'message' => 'Failed to create pinboard item'
                ];
            }
        }
    }

    public function addToPinboardItemImages(array $pinboardItem): array
    {
        // Check if item has pinboard_id property if not return error 
        if (!isset($pinboardItem['pinboard_id']) || empty($pinboardItem['pinboard_id'])) {
            return [
                'error' => true,
                'message' => 'pinboard_id is required'
            ];
        }

        // Check if model_id and model_type are provided
        if (!isset($pinboardItem['model_type'])) {
            return [
                'error' => true,
                'message' => 'model_type is required'
            ];
        }

        $pinboardId = $pinboardItem['pinboard_id'];
        $modelId = $pinboardItem['model_id'];
        $modelType = 'images';

        $newItemData = [
            'pinboard_id' => $pinboardId,
            'model_id' => $modelId,
            'model_type' => $modelType,
            'description' => $pinboardItem['description'] ?? '',
            'comments' => $pinboardItem['comments'] ?json_encode($pinboardItem['comments']) : null,
            'quantity' => $pinboardItem['quantity'] ?? 1,
            'unit_price' => $pinboardItem['unit_price'] ?? 0,
            'total_price' => ($pinboardItem['quantity'] ?? 1) * ($pinboardItem['unit_price'] ?? 0),
            'uuid' => $this->generateUuid(),
            'language_id' => $pinboardItem['language_id'] ?? 1,
            'sort_order' => $pinboardItem['sort_order'] ?? 1,
            'photo' => $pinboardItem['photo'] ?? null,
            'options' => isset($pinboardItem['options']) ? json_encode($pinboardItem['options']) : null,
            'product_url' => $pinboardItem['product_url'] ?? null,
        ];

        // Create a fresh model instance for the create operation
        $newModel = new PinboardItem();
        if ($this->db) {
            $newModel->setDb($this->db);
        }
        $createdItem = $newModel->create($newItemData);
        if ($createdItem) {
            $this->pinboard->updateWhere(['updated_at' => date('Y-m-d H:i:s')],['pinboard_id' => $pinboardId]);
            // Return the created item with id and quantity
            return [
                'error' => false,
                'pinboard_item_id' => $createdItem->pinboard_item_id,
                'quantity' => $createdItem->quantity,
                'message' => 'Item added to pinboard'
            ];
        } else {
            return [
                'error' => true,
                'message' => 'Failed to create pinboard item'
            ];
        }
    }

    public function addToPinboardProductConfigurator(array $pinboardItem): array
    {
        try {
            $this->db->beginTransaction();

            // =======================
            // Validation
            // =======================
            if (empty($pinboardItem['pinboard_id'])) {
                throw new \Exception('pinboard_id is required');
            }

            if (empty($pinboardItem['model_id']) || empty($pinboardItem['model_type'])) {
                throw new \Exception('model_id and model_type are required');
            }

            $pinboardId     = (int) $pinboardItem['pinboard_id'];
            $pinboardItemId = (int) ($pinboardItem['pinboard_item_id'] ?? 0);
            $modelId        = (int) $pinboardItem['model_id'];
            $modelType      = $pinboardItem['model_type'];

            $optionsJson = !empty($pinboardItem['options'])
                ? json_encode($pinboardItem['options'])
                : null;

            // =======================
            // Model Init
            // =======================
            $queryModel = new PinboardItem();
            $queryModel->setDb($this->db);

            // =======================
            // Find Existing Item
            // =======================
            $existingItem = $queryModel->findOneBy([
                'pinboard_item_id' => $pinboardItemId,
                'pinboard_id'      => $pinboardId,
                'model_id'         => $modelId,
                'model_type'       => $modelType,
            ]);

            // =======================
            // UPDATE
            // =======================
            $optionsChanged = $pinboardItem['optionsChanged'] ?? false;
            if ($existingItem && $optionsChanged == false) {
                $quantity   = (int) ($pinboardItem['quantity'] ?? 1);
                $unitPrice  = $pinboardItem['unit_price'] ?? $existingItem->unit_price;

                $existingItem->update([
                    'quantity'    => $quantity,
                    'unit_price'  => $unitPrice,
                    'total_price' => $quantity * $unitPrice,
                    'comments'    => $pinboardItem['comments'] ?? $existingItem->comments,
                    'photo'       => $pinboardItem['photo'] ?? $existingItem->photo,
                    'sort_order'  => $pinboardItem['sort_order'] ?? $existingItem->sort_order,
                ]);

                $pinboardItemId = $existingItem->data->pinboard_item_id;

                $accessories = $this->addAccessoriesToPinboardItem(
                    $pinboardItem,
                    $pinboardId,
                    $pinboardItemId
                );

                $this->db->commit();

                return [
                    'error'             => false,
                    'pinboard_item_id'  => $pinboardItemId,
                    'quantity'          => $quantity,
                    'message'           => 'Item updated',
                    'accessories'       => $accessories['accessories']
                ];
            }

            // =======================
            // CREATE
            // =======================
            if (!$existingItem && $optionsChanged == true) {
                $newModel = new PinboardItem();
                $newModel->setDb($this->db);

                $createdItem = $newModel->create([
                    'pinboard_id'  => $pinboardId,
                    'model_id'     => $modelId,
                    'model_type'   => $modelType,
                    'description' => $pinboardItem['description'] ?? '',
                    'comments'    => $pinboardItem['comments'] ?? null,
                    'quantity'    => $pinboardItem['quantity'] ?? 1,
                    'unit_price'  => $pinboardItem['unit_price'] ?? 0,
                    'total_price' => ($pinboardItem['quantity'] ?? 1) * ($pinboardItem['unit_price'] ?? 0),
                    'uuid'        => $this->generateUuid(),
                    'language_id' => $pinboardItem['language_id'] ?? 1,
                    'sort_order'  => $pinboardItem['sort_order'] ?? 1,
                    'photo'       => $pinboardItem['photo'] ?? null,
                    'options'     => $optionsJson,
                    'product_url' => $pinboardItem['product_url'] ?? null,
                ]);

                if (!$createdItem) {
                    throw new \Exception('Failed to create pinboard item');
                }

                $pinboardItemId = $createdItem->data->pinboard_item_id;

                $accessories = $this->addAccessoriesToPinboardItem(
                    $pinboardItem,
                    $pinboardId,
                    $pinboardItemId
                );
            }
            $this->db->commit();

            return [
                'error'            => false,
                'pinboard_item_id' => $pinboardItemId,
                'quantity'         => $createdItem->data->quantity,
                'message'          => 'Item added',
            ];

        } catch (\Throwable $e) {
            $this->db->rollBack();

            return [
                'error'   => true,
                'message' => $e->getMessage()
            ];
        }
    }

    private function addAccessoriesToPinboardItem(array $pinboardItem, int $pinboardId, int $pinboardItemId): array
    {
        $existingAccessories = $this->pinboardItemAccessories
            ->where('pinboard_id', '=', $pinboardId)
            ->where('pinboard_item_id', '=', $pinboardItemId)
            ->findAll(false);

        // existing accessories in database
        $mapped = [];
        foreach ($existingAccessories as $item) {
            $key = "{$item['pinboard_id']}.{$item['pinboard_item_id']}.{$item['accessories_product_id']}.{$item['accessories_item_id']}";
            $mapped[$key] = $item['pinboard_item_accessories_id'] ?? 0;
        }

        // exsitings accessories in front end
        $frontKeys = [];
        foreach ($pinboardItem['accessories'] as $accessory) {
            $key = "{$pinboardId}.{$pinboardItemId}.{$accessory['product_id']}.{$accessory['item_id']}";
            $frontKeys[$key] = true;
        }

        $removeDataIds = [];
        foreach ($mapped as $dbKey => $dbId) {
            if (!isset($frontKeys[$dbKey])) {
                $removeDataIds[] = $dbId;
            }
        }
        
        if (!empty($removeDataIds)) {
            $this->pinboardItemAccessories
                ->whereIn('pinboard_item_accessories_id', $removeDataIds)
                ->deleteMultiple($removeDataIds);
        }
        
        $insertData = [];
        foreach ($pinboardItem['accessories'] as $accessory) {
            $key = "{$pinboardId}.{$pinboardItemId}.{$accessory['product_id']}.{$accessory['item_id']}";

            if (isset($mapped[$key])) {
                continue;
            }

            $insertData[] = [
                'pinboard_id'             => $pinboardId,
                'pinboard_item_id'        => $pinboardItemId,
                'accessories_product_id'  => $accessory['product_id'],
                'accessories_item_id'     => $accessory['item_id'],
            ];
        }

        if (!empty($insertData)) {
            $this->pinboardItemAccessories->insert($insertData);
        }

        return [
            'status'      => true,
            'accessories' => $insertData
        ];
    }


    public function pinboardItemForComponent(int $pinboardId): array
    {
        $results = $this->model
        ->join('pinboard', 'pinboard.pinboard_id', '=', 'pinboard_item.pinboard_id')
        ->select([
            'pinboard.pinboard_name as project_name',
            'pinboard.pinboard_description as pinboard_note',
            'pinboard_item.uuid',
            'pinboard_item.model_id',
            'pinboard_item.model_type',
            'pinboard_item.pinboard_item_id',
            'pinboard_item.pinboard_id',
            'pinboard_item.quantity',
            'pinboard_item.unit_price',
            'pinboard_item.total_price'
        ])
        ->where('pinboard.pinboard_id', '=', $pinboardId)
        ->orderBy('pinboard_item.sort_order', 'ASC')
        ->orderBy('pinboard_item.pinboard_item_id', 'ASC')
        ->findAll(false);


        $pinboardItems = [];
        $pinboardItems['items'] = $results;
        $pinboardItems['count_items'] = count($results);
        $pinboardItems['pinboard_note'] = isset($results[0]['pinboard_note']) ? $results[0]['pinboard_note'] : '';
        $pinboardItems['pinboard_id'] = isset($results[0]['pinboard_id']) ? $results[0]['pinboard_id'] : '';
        // total amount
        $pinboardItems['total_amount'] = number_format(array_sum(array_column($results, 'total_price')) ?? 0, 2) . ' $';

        return $pinboardItems;
    }
    public function updateCommentDescription(array $data): array
    {
        // find pinboard item by pinboard_item_id
        $pinboardItem = $this->model->findOneBy(['pinboard_item_id' => $data['pinboard_item_id']]);
        if ($pinboardItem) {
            $updateData = [];
            if (isset($data['description']) && !empty($data['description'])) {
                $updateData['description'] = $data['description'];
            }
            if (isset($data['comment']) && !empty($data['comment'])) {
                // save json data for comment
                $commentData =$data['comment'];

                $updateData['comments'] =  json_encode([$commentData]);
            }

            if (count($updateData) > 0) {
               $updatedPinboardItem = $pinboardItem->update($updateData);
               $pinboardId = $updatedPinboardItem->pinboard_id;
               $this->pinboard->updateWhere(['updated_at' => currentDateTime()],['pinboard_id' => $pinboardId]);

                return [
                    'status' => 200,
                    'success' => true,
                    'message' => 'Comment description updated successfully',
                    'data' => $updatedPinboardItem->data,
                ];
  
            }
           
        }

        throw new \Exception('Comment description not found');
    }

    // get project items by pinboard id and user id
    public function updateProjectPinboardId(int $pinboardId, int $userId): array
    {
        try {
            $this->db->beginTransaction();

            // Deactivate all pinboards for this user
            $deactivateStmt = $this->db->prepare(
                "UPDATE `pinboard`
                SET `is_active` = 0
                WHERE `user_id` = :user_id"
            );
            $deactivateStmt->execute([
                ':user_id' => $userId
            ]);

            // Activate only the selected pinboard
            $activateStmt = $this->db->prepare(
                "UPDATE `pinboard`
                SET `is_active` = 1
                WHERE `pinboard_id` = :pinboard_id
                AND `user_id` = :user_id"
            );
            $activateStmt->execute([
                ':pinboard_id' => $pinboardId,
                ':user_id' => $userId
            ]);

            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Project pinboard id updated successfully'
            ];

        } catch (\Throwable $e) {
            $this->db->rollBack();

            throw new \Exception(
                'Failed to update project pinboard id: ' . $e->getMessage()
            );
        }
    }
} 