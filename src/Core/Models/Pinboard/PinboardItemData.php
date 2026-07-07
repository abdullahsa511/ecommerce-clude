<?php

namespace App\Core\Models\Pinboard;

use stdClass;

class PinboardItemData
{
    public stdClass $data;
    public string $model;

    public function __construct(array $data, string $model)
    {
        $this->data = new stdClass();
        $this->model = $model;

        $this->data->name = $data['name'] ?? null;
        $this->data->description = $data['description'] ?? null;
        $this->data->image = $data['image'] ?? null;
        $this->data->quantity = $data['quantity'] ?? 1;

        switch ($model) {
            case 'product':
              
            case 'product_variant':
                $this->data->price = $data['price'] ?? 0;
                break;

            case 'project':
                $this->data->image = $data['image'] ?? null;
                break;

            case 'media':
                $this->data->file = $data['file'] ?? null;
                $this->data->type = $data['type'] ?? null;
                break;

            case 'comment':
                $this->data->author = $data['author'] ?? null;
                break;
        }
    }

    public function toArray(): array
    {
        return (array) $this->data;
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }
}
