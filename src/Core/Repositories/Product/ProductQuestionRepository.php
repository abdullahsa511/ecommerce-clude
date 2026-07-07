<?php

declare(strict_types=1);

namespace App\Core\Repositories\Product;

use PDO;
use App\Core\Models\Product\ProductQuestion;
use App\Core\Repositories\Base\BaseRepository;

class ProductQuestionRepository extends BaseRepository implements ProductQuestionRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'product_question', ProductQuestion::class);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        $questions = parent::findAll();
        
        // Add question_count to each question
        foreach ($questions as &$question) {
            $question['question_count'] = $this->getQuestionCount($question['product_question_id']);
        }
        
        return $questions;
    }


    private function getQuestionCount(int $parentId): int
    {
        $sql = "SELECT COUNT(*) as count FROM product_question WHERE parent_id = :parent_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['parent_id' => $parentId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return (int) $result['count'];
    }

} 