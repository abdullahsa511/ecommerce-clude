<?php

declare(strict_types=1);

namespace App\Core\Repositories\PostCategory;

use App\Core\Repositories\Base\BaseRepository;
use App\Core\Models\PostCategory\TaxonomyItem;
use App\Core\Models\PostCategory\TaxonomyItemContent;
use App\Core\Models\PostCategory\TaxonomyItemData;
use PDO;

class TaxonomyItemRepository extends BaseRepository implements TaxonomyItemRepositoryInterface
{
    private TaxonomyItemContent $taxonomyItemContent;
    public function __construct(PDO $db, TaxonomyItemContent $taxonomyItemContent)
    {
        parent::__construct($db, 'taxonomy_item', TaxonomyItem::class);
        $this->taxonomyItemContent = $taxonomyItemContent;
        $this->taxonomyItemContent->setDb($db);
    }

    public function getAll(int $languageId, int $start, int $limit): array
    {
        $query = $this->model->with(['taxonomyContent']);

        $total = $query->countAll();
        $list = $query->limit($limit)->offset($start)->orderBy('taxonomy_item_id', 'DESC')->findAll();

        return [
            'list' => $list,
            'total' => $total
        ];
    }

    public function get(int $taxonomyItemId): ?TaxonomyItem
    {
        return $this->model->with(['taxonomyContent'])
            ->where('taxonomy_item_id', (string)$taxonomyItemId)
            ->find('taxonomy_item_id');
    }

    public function getTaxonomyItems(int $taxonomyId, array $fields): array
    {
        // Prefix each field with its table name
        $prefixedFields = array_map(function($field) {
            if (strpos($field, '.') === false) {
                // If field doesn't already have a table prefix, add taxonomy_item_content prefix
                return 'taxonomy_item_content.' . $field;
            }
            return $field;
        }, $fields);

        $result = $this->model
            ->join('post_to_taxonomy_item', 'post_to_taxonomy_item.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
            ->join('taxonomy_item_content', 'taxonomy_item_content.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
            // ->where('post_to_taxonomy_item.post_id', '!=', null)
            ->select($prefixedFields);

        $result = $result->findAll(false);

        return $result;
    }

    public function getTaxonomyItemsByTaxonomyId(int $taxonomyId, array $fields, array $taxonomyItemIds = []): array
    {
        // Handle field prefixing properly - don't modify fields that already have table prefixes
        $prefixedFields = array_map(function($field) {
            // If field already has a table prefix (contains '.'), keep it as is
            if (strpos($field, '.') !== false) {
                return $field;
            }
            // If field doesn't have a table prefix, add taxonomy_item_content prefix
            return 'taxonomy_item_content.' . $field;
        }, $fields);

        $query = $this->model
            ->join('taxonomy_item_content', 'taxonomy_item_content.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
            ->where('taxonomy_item.taxonomy_id', '=', $taxonomyId)
            ->where('taxonomy_item_content.language_id', '=', 1); // Default language

        // If specific taxonomy item IDs are provided, filter by them
        if (!empty($taxonomyItemIds)) {
            $query->whereIn('taxonomy_item.taxonomy_item_id', $taxonomyItemIds);
        }

        $result = $query->select($prefixedFields)->findAll(false);

        return $result;
    }

    public function getTaxonomyItemsByTaxonomyIds(array $taxonomyIds, array $fields, array $taxonomyItemIds = []): array
    {
        // Handle field prefixing properly - don't modify fields that already have table prefixes
        $prefixedFields = array_map(function($field) {
            // If field already has a table prefix (contains '.'), keep it as is
            if (strpos($field, '.') !== false) {
                return $field;
            }
            // If field doesn't have a table prefix, add taxonomy_item_content prefix
            return 'taxonomy_item_content.' . $field;
        }, $fields);

        $query = $this->model
            ->join('taxonomy_item_content', 'taxonomy_item_content.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')
            ->whereIn('taxonomy_item.taxonomy_id', $taxonomyIds) 
            ->where('taxonomy_item_content.language_id', '=', 1); 

        if (!empty($taxonomyItemIds)) {
            $query->whereIn('taxonomy_item.taxonomy_item_id', $taxonomyItemIds);
        }

        $result = $query->select($prefixedFields)->orderBy('taxonomy_item.taxonomy_item_id', 'DESC')->findAll(false);

        return $result;
    }

    public function insertTaxonomyItemContents(array $data): bool
    {
        return $this->taxonomyItemContent->insert($data);
    }
    
    /**
     * Create a new taxonomy item with content
     */
    public function createTaxonomyItem(TaxonomyItemData $data): ?TaxonomyItem
    {
        try {
            $this->db->beginTransaction();
            
            // Create taxonomy item
            $dataArray = $data->toArray();
            $taxonomyItem = $this->create($dataArray);
            if (!$taxonomyItem) {
                $this->db->rollBack();
                return null;
            }
            
            // Create taxonomy item content
            $contentData = $data->content->toArray();
            $contentData['taxonomy_item_id'] = $taxonomyItem->taxonomy_item_id;
            
            $contentCreated = $this->insertTaxonomyItemContents([$contentData]);
            if (!$contentCreated) {
                $this->db->rollBack();
                return null;
            }
            
            $this->db->commit();
            return $taxonomyItem;
            
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    /**
     * Update a taxonomy item with content
     */
    public function updateTaxonomyItem(int $taxonomyItemId, TaxonomyItemData $data): ?TaxonomyItem
    {
        try {
            $this->db->beginTransaction();
            
            // Update taxonomy item
            $taxonomyItemData = $data->toArray();
            $taxonomyItem = $this->update($taxonomyItemId, $taxonomyItemData);
            if (!$taxonomyItem) {
                $this->db->rollBack();
                return null;
            }
            
            // Update taxonomy item content
            $contentData = $data->content->toArray();
            
            $sql = "UPDATE `taxonomy_item_content` SET 
                        `name` = :name, 
                        `slug` = :slug, 
                        `content` = :content,
                        `meta_title` = :meta_title,
                        `meta_description` = :meta_description,
                        `meta_keywords` = :meta_keywords,
                        `link` = :link,
                        `products_link` = :products_link
                    WHERE `taxonomy_item_id` = :taxonomy_item_id AND `language_id` = :language_id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':name', $contentData['name']);
            $stmt->bindValue(':slug', $contentData['slug']);
            $stmt->bindValue(':content', $contentData['content']);
            $stmt->bindValue(':meta_title', $contentData['meta_title']);
            $stmt->bindValue(':meta_description', $contentData['meta_description']);
            $stmt->bindValue(':meta_keywords', $contentData['meta_keywords']);
            $stmt->bindValue(':link', $contentData['link']);
            $stmt->bindValue(':products_link', $contentData['products_link']);
            $stmt->bindValue(':taxonomy_item_id', $taxonomyItemId, PDO::PARAM_INT);
            $stmt->bindValue(':language_id', $contentData['language_id'], PDO::PARAM_INT);
            
            $contentUpdated = $stmt->execute();
            if (!$contentUpdated) {
                $this->db->rollBack();
                return null;
            }
            
            $this->db->commit();
            return $this->model->join('taxonomy_item_content', 'taxonomy_item_content.taxonomy_item_id', '=', 'taxonomy_item.taxonomy_item_id')->where('taxonomy_item.taxonomy_item_id', '=', $taxonomyItemId)
            ->select(['taxonomy_item.*', 'taxonomy_item_content.content'])->first();
            
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    /**
     * Delete a taxonomy item and its content
     */
    public function deleteTaxonomyItem(int $taxonomyItemId): bool
    {
        try {
            $this->db->beginTransaction();
            
            // Delete taxonomy_item_content first (foreign key constraint)
            $sql = "DELETE FROM `taxonomy_item_content` WHERE `taxonomy_item_id` = :taxonomy_item_id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':taxonomy_item_id', $taxonomyItemId, PDO::PARAM_INT);
            $stmt->execute();
            
            // Delete the taxonomy item
            $deleted = $this->delete($taxonomyItemId);
            
            if (!$deleted) {
                $this->db->rollBack();
                return false;
            }
            
            $this->db->commit();
            return true;
            
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
    
    /**
     * Show a taxonomy item with content
     */
    public function showTaxonomyItem(int $taxonomyItemId, int $languageId = 1): ?TaxonomyItem
    {
        $taxonomyItem = $this->model
            ->where('taxonomy_item_id', '=', $taxonomyItemId)
            ->with(['taxonomyItemContent' => function($query) use ($languageId) {
                return $query->where('language_id', '=', $languageId);
            }])
            ->first();
            
        return $taxonomyItem;
    }
    public function updateCategoryOrder(array $data): array
    {
        $category = $data;
        // upsert taxonmy item with sort order
        $upserted = $this->model->upsert($category, ['taxonomy_item_id']);
        if (!$upserted) {
            return ['message' => 'Failed to update category order'];
        }
        return ['message' => 'Category order updated successfully'];

    }

    public function updateTaxonomyItemImage(array $files, string $property, int $taxonomy_item_id): bool
    {
        $taxonomyItem = $this->model->where('taxonomy_item_id', '=', $taxonomy_item_id)->first();
        if (!$taxonomyItem) {
            return false; // taxonomy item not found
        }
        
        $imageData = [];
        foreach ($files as $image) {
            $img = [];
            $img['taxonomy_item_id'] = $taxonomy_item_id;
            $img['image_link'] = $image['image'];
            $img['name'] = $image['name'];
            $img['size'] = $image['size'];
            $img['type'] = $image['type'];
            $img['image'] = $image['image'];
            $img['media_id'] = null;
            $img['objectURL'] = $image['objectURL'];
            $img['file'] = [
                'name' => $image['name'],
                'objectURL' => $image['objectURL'],
                'size' => $image['size'],
                'type' => $image['type'],
                'path' => ROOT_DIR . PUBLIC_PATH . $image['image'],
                'status' => $image['status']
            ];
            $img['status'] = [
                'name' => $image['status']['name'],
                'severity' => $image['status']['severity']
            ];
            $imageData[] = $img;
        }
        $imgJson = json_encode($imageData);
        $this->db->beginTransaction();
        if ($property == 'banner_image') {
            $property = 'image';
        }
        try {
            $taxonomyItem->update([$property => $imgJson]);
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    // delete taxonomy item image
    public function deleteTaxonomyItemImage(int $taxonomy_item_id, string $property): bool
    {
        if ($property == 'banner_image') {
           $property = 'image';
        }
        $taxonomyItem = $this->model->where('taxonomy_item_id', '=', $taxonomy_item_id)->first();
        if (!$taxonomyItem) {
            return false; // taxonomy item not found
        }
        $taxonomyItem->update([$property => null]);
        return true;
    }

} 