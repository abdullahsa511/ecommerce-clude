<?php

declare(strict_types=1);

namespace App\Core\Repositories\Option;

use App\Core\Models\Base\Model;
use App\Core\Models\Option\OptionValue;
use App\Core\Repositories\Base\BaseRepository;
use PDO;

class OptionValueRepository extends BaseRepository implements OptionValueRepositoryInterface
{
    protected Model $model;
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->model = new OptionValue();
        $this->model->setDb($db);
    }

    /**
     * {@inheritDoc}
     */
    public function getAll(int $language_id, ?int $option_id = null, int $start = 0, int $limit = 10): array
    {
        $this->model->select([
            'ov.option_value_id',
            'ov.option_id',
            'ov.sort_order'
        ])
        ->with(['optionValueContent' => function($query) use ($language_id) {
            $query->where('language_id', '=', $language_id);
        }]);

        if ($option_id !== null) {
            $this->model->where('option_id', '=', $option_id);
        }

        $this->model->limit($limit)->offset($start);

        return [
            'data' => $this->model->findAll(),
            'total' => $this->model->countAll()
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function get(int $option_value_id, int $language_id): ?OptionValue
    {
        /** @var OptionValue|null */
        return $this->model->select([
            'ov.option_value_id',
            'ov.option_id',
            'ov.sort_order'
        ])
        ->with(['optionValueContent' => function($query) use ($language_id) {
            $query->where('language_id', '=', $language_id);
        }])
        ->find($option_value_id);
    }

    /**
     * {@inheritDoc}
     */
    public function add(array $option_value): int
    {
        // First insert the main option value data
        $optionValueData = [
            'option_id' => $option_value['option_id'],
            'sort_order' => $option_value['sort_order'] ?? 0
        ];

        $newOptionValue = $this->model->create($optionValueData);
        $option_value_id = $newOptionValue->getId();

        // Then insert the option value content
        if (isset($option_value['option_value_content'])) {
            $contents = [];
            foreach ($option_value['option_value_content'] as $language_id => $content) {
                $contents[] = [
                    'option_value_id' => $option_value_id,
                    'language_id' => $language_id,
                    'name' => $content['name']
                ];
            }
            
            // Insert all contents at once
            $this->db->beginTransaction();
            try {
                $stmt = $this->db->prepare(
                    "INSERT INTO option_value_content (option_value_id, language_id, name) 
                     VALUES (:option_value_id, :language_id, :name)"
                );
                
                foreach ($contents as $content) {
                    $stmt->execute($content);
                }
                
                $this->db->commit();
            } catch (\Exception $e) {
                $this->db->rollBack();
                throw $e;
            }
        }

        return $option_value_id;
    }

    /**
     * {@inheritDoc}
     */
    public function edit(int $option_value_id, array $option_value): bool
    {
        // Update main option value data
        $optionValueData = [
            'option_id' => $option_value['option_id'],
            'sort_order' => $option_value['sort_order'] ?? 0
        ];

        $success = $this->model->update($optionValueData) !== null;

        // Update option value content
        if (isset($option_value['option_value_content']) && $success) {
            $this->db->beginTransaction();
            try {
                // Delete existing content
                $stmt = $this->db->prepare(
                    "DELETE FROM option_value_content WHERE option_value_id = ?"
                );
                $stmt->execute([$option_value_id]);

                // Insert new content
                $stmt = $this->db->prepare(
                    "INSERT INTO option_value_content (option_value_id, language_id, name) 
                     VALUES (:option_value_id, :language_id, :name)"
                );

                foreach ($option_value['option_value_content'] as $language_id => $content) {
                    $stmt->execute([
                        'option_value_id' => $option_value_id,
                        'language_id' => $language_id,
                        'name' => $content['name']
                    ]);
                }

                $this->db->commit();
                return true;
            } catch (\Exception $e) {
                $this->db->rollBack();
                throw $e;
            }
        }

        return $success;
    }
} 