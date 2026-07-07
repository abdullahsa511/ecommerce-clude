<?php

declare(strict_types=1);

namespace App\Core\Repositories\Component;

use App\Core\Models\Component\Component;
use App\Core\Models\Component\ComponentData;
use App\Core\Models\Component\ComponentItem;
use App\Core\Repositories\Base\BaseRepository;

use PDO;
use function App\Core\System\utils\htmlToPlainText;

class ComponentRepository extends BaseRepository implements ComponentRepositoryInterface
{
    private ComponentItem $componentItemModel;
    public function __construct(PDO $db, ComponentItem $componentItemModel)
    {
        parent::__construct($db, 'component', Component::class);
        $this->componentItemModel = $componentItemModel;
        $this->componentItemModel->setDb($db);
    }

    public function getAll(
        ?int $status = null,
        ?string $search = null,
        int $start = 0,
        int $limit = 10
    ): array {
        $query = $this->model;

        if ($status !== null) {
            $query->where('status', '=', $status);
        }

        if ($search !== null) {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $query->orderBy('status', 'DESC')
              ->orderBy('country_id', 'ASC');

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

    public function get(int $componentId): ?Component
    {
        $query = $this->model
            ->where('component_id', '=', $componentId);

        $result = $query->findAll();
        if (empty($result)) {
            return null;
        }
        
        return $this->model->set($result[0]);
    }

    public function getComponentByName(string $name): ?ComponentData
    {
        $query = $this->model
            ->where('name', '=', $name)->with(['items', 'metaProperties']);
        $component = $query->first();
        $componentData = new ComponentData();
        $componentData->component_id = $component?->data->component_id;
        $componentData->name = $component?->data->name;
        $componentData->section_title = $component?->data->section_title;
        $componentData->section_subtitle = $component?->data->section_subtitle;
        $componentData->section_link = $component?->data->section_link;
        $componentData->title = $component?->data->title;
        $componentData->subtitle = $component?->data->subtitle;
        $componentData->description = $component?->data->description;
        $componentData->image = $component?->data->image?json_decode($component->data->image, true) : [];
        $componentData->mobile_banner = $component?->data->mobile_banner?json_decode($component->data->mobile_banner, true) : [];
        $componentData->images = $component?->data->images? json_decode($component->data->images, true) : [];
        $componentData->banner_way_points = $component?->data->banner_way_points?json_decode($component->data->banner_way_points, true) : [];
        $componentData->links = $component?->data->links? json_decode($component->data->links, true) : [];
        $componentData->buttons = $component?->data->buttons? json_decode($component->data->buttons, true) : [];
        $componentData->items = $component?->items? json_decode($component->items, true) : [];
        $componentData->items = array_filter($componentData->items, function($item) {
            return $item['property_name'] != null && $item['property_name'] != '';
        });
        // $componentData->items = $this->applyPlainTextToComponentItemFields($componentData->items);
        $componentData->properties = new \stdClass();
        $properties = $component?->metaProperties?json_decode($component->metaProperties, true) : [];
        foreach ($properties as $property) {
            if(isset($property['property'])) {
                $componentData->properties->{$property['property']} = $property['value'];
            }
        }
        $componentData->template = $component?->data->template;
        return $componentData;
    }

    // public function getComponentAndItems(string $name): ?ComponentData
    // {
    //     $component = $this->getComponentByName($name);
    //     $component->items = $this->getComponentItems($name);
    //     return $component;
    // }

    public function createComponent(array $data): ?Component
    {
        // Handle JSON encoding for known JSON columns
        if(isset($data['images'])){
            $data['images'] = json_encode($data['images']);
        } 
        if(isset($data['links'])){
            $data['links'] = json_encode($data['links']);
        }
        if(isset($data['buttons'])){
            $data['buttons'] = json_encode($data['buttons']);
        }
        if(isset($data['image'])) {
            if (is_array($data['image'])) {
                // Store the full array as JSON
                $data['image'] = json_encode($data['image']);
            } elseif (is_string($data['image'])) {
                // If it's a plain string, wrap it in JSON format
                $data['image'] = json_encode($data['image']);
            }
        }
        if(isset($data['mobile_banner'])) {
            if (is_array($data['mobile_banner'])) {
                // Store the full array as JSON
                $data['mobile_banner'] = json_encode($data['mobile_banner']);
            } elseif (is_string($data['mobile_banner'])) {
                // If it's a plain string, wrap it in JSON format
                $data['mobile_banner'] = json_encode($data['mobile_banner']);
            }
        }
        
        // Handle any other array values that might be passed
        foreach ($data as $key => $value) {
            if (is_array($value) && !in_array($key, ['images', 'links', 'buttons'])) {
                $data[$key] = json_encode($value);
            }
        }
        
        return $this->model->create($data);
    }

    public function getComponentById(int $id): ?ComponentData
    {
        $query = $this->model
            ->where('component_id', '=', $id)->with(['items', 'metaProperties']);
        $component = $query->first();
        $componentData = new ComponentData();
        $componentData->component_id = $component?->data->component_id;
        $componentData->name = $component?->data->name;
        $componentData->section_title = $component?->data->section_title;
        $componentData->section_subtitle = $component?->data->section_subtitle;
        $componentData->section_link = $component?->data->section_link;
        $componentData->title = $component?->data->title;
        $componentData->subtitle = $component?->data->subtitle;
        $componentData->description = $component?->data->description;
        $componentData->image = $component?->data->image?json_decode($component->data->image, true) : [];
        $componentData->mobile_banner = $component?->data->mobile_banner?json_decode($component->data->mobile_banner, true) : [];
        $componentData->images = $component?->data->images? json_decode($component->data->images, true) : [];
        $componentData->links = $component?->data->links? json_decode($component->data->links, true) : [];
        $componentData->buttons = $component?->data->buttons? json_decode($component->data->buttons, true) : [];

        $componentData->items = $component?->items? json_decode($component->items, true) : [];
        
        $componentData->items = array_filter($componentData->items??[], function($item) {
            return $item['property_name'] != null && $item['property_name'] != '';
        });
        // $componentData->items = $this->applyPlainTextToComponentItemFields($componentData->items);
        $componentData->properties = new \stdClass();
        $properties = $component?->metaProperties?json_decode($component->metaProperties, true) : [];
        foreach ($properties as $property) {
            if(isset($property['property'])) {
                $componentData->properties->{$property['property']} = $property['value'];
            }
        }
        $componentData->template = $component?->data->template;
        $componentData->banner_way_points = $component?->data->banner_way_points?json_decode($component->data->banner_way_points, true) : [];
        return $componentData;
    }

    /**
     * Delete a component and all its related component items
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $this->db->beginTransaction();

        try {
            // First, delete all component items related to this component
            $componentItemModel = new ComponentItem();
            $componentItemModel->setDb($this->db);
            
            $componentItemModel->where('component_id', '=', $id);
            $componentItemModel->deleteWhere(['component_id' => $id]);

            // Then delete the component itself
            $result = parent::delete($id);

            $this->db->commit();
            return $result;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getComponentItems(ComponentData $component): ?array
    {
        $items = [];
        foreach($component->items as $item){
            $itemData = [];
            if(isset($item['fields'])){
                foreach($item['fields'] as $field){
                    if(isset($field['name']) && isset($field['value'])){
                        if(isset($field['type']['type']['type']) && $field['type']['type']['type'] == 'JSON'){
                            $itemData[$field['name']] = json_decode($field['value'], true);
                            continue;
                        }
                        if(isset($field['type']['type']['type']) && $field['type']['type']['type'] == 'FileUpload'){
                            if(isset($field['value'][0]['objectURL'])){
                                $itemData[$field['name']] = $field['value'][0]['objectURL'];
                            }
                            continue;
                        }
                        $itemData[$field['name']] = htmlToPlainText($field['value']);
                    }
                }
            }
            $items[] = $itemData;
        }
        return $items;
    }

    // /**
    //  * Strip HTML / entities from string field values on component items (same rules as getComponentItems).
    //  *
    //  * @param array<int, array<string, mixed>> $items
    //  * @return array<int, array<string, mixed>>
    //  */
    // private function applyPlainTextToComponentItemFields(array $items): array
    // {
    //     foreach ($items as $i => $item) {
    //         if (!isset($item['fields']) || !is_array($item['fields'])) {
    //             continue;
    //         }
    //         foreach ($item['fields'] as $j => $field) {
    //             if (!isset($field['name']) || !array_key_exists('value', $field)) {
    //                 continue;
    //             }
    //             $innerType = $field['type']['type']['type'] ?? null;
    //             if ($innerType === 'JSON' || $innerType === 'FileUpload') {
    //                 continue;
    //             }
    //             if (is_string($field['value'])) {
    //                 // $items[$i]['fields'][$j]['value'] = htmlToPlainText($field['value']);
    //                 $items[$i]['fields'][$j]['value_editor'] = $field['value'];
    //             }
    //         }
    //     }

    //     return $items;
    // }

    public function seedData(array $data): void
    {
        // Get component names from the data to map to component IDs
        $components = $data['components'];
        
        // Handle JSON encoding for JSON columns before upsert
        foreach ($components as &$component) {
            // Image field is already JSON encoded in the seeder, so no additional processing needed
            if(isset($component['images'])){
                if(empty($component['images'])){
                    $component['images'] = null;
                } else {
                    $component['images'] = json_encode($component['images']);
                }
            } else {
                $component['images'] = null;
            }
            
            if(isset($component['links'])){
                if(empty($component['links'])){
                    $component['links'] = null;
                } else {
                    $component['links'] = json_encode($component['links']);
                }
            } else {
                $component['links'] = null;
            }
            
            if(isset($component['buttons'])){
                if(empty($component['buttons'])){
                    $component['buttons'] = null;
                } else {
                    $component['buttons'] = json_encode($component['buttons']);
                }
            } else {
                $component['buttons'] = null;
            }
        }
        
        $this->model->upsert($components, ['name']);

        $componentNames = array_column($components, 'name');
        
        // Get existing component IDs from database
        $componentIds = $this->model->whereIn('name', $componentNames)->select(['component_id', 'name'])->findAll();
        $nameComponentIds = [];
        foreach ($componentIds as $componentId) {
            $nameComponentIds[$componentId['name']] = $componentId['component_id'];
        }
        
        // Process component items and map component names to IDs
        $componentItems = $data['component_items'];
        $processedComponentItems = [];
        
        foreach ($componentItems as $componentName => $items) {
            if (isset($nameComponentIds[$componentName])) {
                $componentId = $nameComponentIds[$componentName];
                
                foreach ($items as $item) {
                    $item['component_id'] = $componentId;
                    
                    // Set default values for required fields
                    if (!isset($item['item_count'])) {
                        $item['item_count'] = 1;
                    }
                    if (!isset($item['is_recent'])) {
                        $item['is_recent'] = 1;
                    }
                    if (!isset($item['is_featured'])) {
                        $item['is_featured'] = 1;
                    }
                    
                    // Handle JSON encoding for JSON columns
                    if (isset($item['fields'])) {
                        $item['fields'] = json_encode($item['fields']);
                    }
                    if (isset($item['join'])) {
                        $item['join'] = json_encode($item['join']);
                    }
                    if (isset($item['with'])) {
                        $item['with'] = json_encode($item['with']);
                    }
                    
                    $processedComponentItems[] = $item;
                }
            }
        }

        // Upsert Component Items
        if (!empty($processedComponentItems)) {
            $this->componentItemModel->insert($processedComponentItems);
        }
    }

    public function updateWayPoints(array $data): array
    {
        $model_id = $data['model_id'];
        $model_type = $data['model_type'];
        $way_points = $data['way_points'];

        $model_type = $model_type;
        $query = null;
        if($model_type == 'component') {
           $query = $this->model->where('component_id', '=', $model_id)->first();
        }

        if (!$query) {
            return [
                'success' => false,
                'message' => 'Component not found'
            ];
        }
        $updatedData = $query->update(['banner_way_points' => json_encode($way_points)]);
        return [
            'success' => true,
            'message' => 'Way points updated successfully',
            'data' => $data
        ]; 
    }

    public function uploadImage(array $data, int|string $component_id, ?string $property = 'image'): bool
    {
        $component = $this->model->where('component_id', '=', $component_id)->first();
        if (!$component) {
            return false;
        }

        $imageData = [];
        // $config = app('config');
        // $imageServer = rtrim($config['APP_URL'], '/');

        foreach ($data as $image) {
            $img = [
                'component_id' => $component_id,
                'file' => [
                    'name' => $image['file']['name'] ?? $image['name'],
                    'size' => $image['file']['size'] ?? $image['size'],
                    'type' => $image['file']['type'] ?? $image['type'],
                    'error' => $image['file']['error'] ?? 0,
                    'tmp_name' => $image['file']['tmp_name'] ?? '',
                    'full_path' => $image['file']['full_path'] ?? $image['name'],
                ],
                'name' => $image['name'],
                'size' => $image['size'],
                'type' => $image['type'],
                'image' => $image['image'],
                'status' => [
                    'name' => $image['status']['name'] ?? 'Uploaded',
                    'severity' => $image['status']['severity'] ?? 'success',
                ],
                'media_id' => null,
                'objectURL' => $image['objectURL'],
                'created_at' => '',
                'description' => $image['description'] ?? '',
            ];

            $imageData[] = $img;
        }

        if ($property === 'images') {
            $existingImages = json_decode($component->images, true) ?? [];
            // new images first, then old images
            $imageData = array_merge($imageData, $existingImages);
        }

        $this->db->beginTransaction();
        try {
            // Convert array to JSON before saving
            $component->update([$property => json_encode($imageData)]);
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function deleteImage(string $objectUrl, int|string $component_id, ?string $property = 'image'): bool
    {
        print_r($component_id);
        $component = $this->model->where('component_id', '=', $component_id)->first();
        if (!$component) {
            return false;
        }

        $images = json_decode($component->$property, true) ?? [];
        $images = array_filter($images, function($image) use ($objectUrl) {
            return $image['objectURL'] != $objectUrl;
        });

        $this->db->beginTransaction();
        try {
            // Convert array to JSON before saving
            $component->update([$property => json_encode($images)]);
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
} 