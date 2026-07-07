<?php

declare(strict_types=1);

namespace App\Core\Repositories\Component;

use App\Core\Models\Component\ComponentItem;
use App\Core\Models\Component\ComponentData;
use App\Core\Models\Component\ComponentItemData;
use App\Core\Repositories\Base\BaseRepository;

use PDO;

class ComponentItemRepository extends BaseRepository implements ComponentItemRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'component_item', ComponentItem::class);
    }

    public function addComponentItem(array $data): array
    {
        $componentId = $data['component_id'];
        $this->model->create($data);

        $componentItems = $this->model->where('component_id', "=",  $componentId)->findAll();
        $items = [];
        foreach($componentItems as $componentItem){
            $i = new ComponentItemData($componentItem);
            $items[] = $i->toArray();
        }
        
        return $items;
    }

    public function updateComponentItems(array $data, int $id): array
    {
       
        $componentItem = $this->model->find($id);
        $componentItem->update($data);

        $componentId = $data['component_id'];

        $componentItems = $this->model->where('component_id', "=",  $componentId)->findAll();
        $items = [];
        foreach($componentItems as $componentItem){
            $i = new ComponentItemData($componentItem);
            $items[] = $i->toArray();
        }
        
        return $items;
    }
    
} 