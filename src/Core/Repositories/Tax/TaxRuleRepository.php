<?php

declare(strict_types=1);

namespace App\Core\Repositories\Tax;

use App\Core\Models\Tax\TaxRule;
use App\Core\Repositories\Base\BaseRepository;
use PDO;

class TaxRuleRepository extends BaseRepository implements TaxRuleRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'tax_rule', TaxRule::class);
    }

    public function getAll(?int $taxTypeId, int $start, int $limit): array
    {
        if ($taxTypeId !== null) {
            $this->model->where('tax_type_id', '=', $taxTypeId);
        }

        $this->model->orderBy('sort_order', 'ASC');
        $this->model->offset($start)->limit($limit);

        $items = $this->model->findAll();
        $total = $this->model->countAll();

        return [
            'list' => $items,
            'total' => $total
        ];
    }

    public function get(int $taxRuleId): ?TaxRule
    {
        $this->model->where('tax_rule_id', '=', $taxRuleId);
        $result = $this->model->findAll();
        
        return !empty($result) ? $this->model->set($result[0]) : null;
    }

    public function add(array $taxRules, int $taxTypeId): int
    {
        // Delete existing rules for this tax type
        $this->model->where('tax_type_id', '=', $taxTypeId);
        $existingRules = $this->model->findAll();
        foreach ($existingRules as $rule) {
            $this->model->delete($rule['tax_rule_id']);
        }

        // Insert new rules
        $insertedIds = [];
        foreach ($taxRules as $rule) {
            $rule['tax_type_id'] = $taxTypeId;
            $result = $this->model->create($rule);
            if ($result) {
                $insertedIds[] = $result->getId();
            }
        }

        return count($insertedIds);
    }

} 