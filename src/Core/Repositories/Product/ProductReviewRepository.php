<?php

declare(strict_types=1);

namespace App\Core\Repositories\Product;

use PDO;
use App\Core\Models\Product\ProductReview;
use App\Core\Repositories\Base\BaseRepository;

class ProductReviewRepository extends BaseRepository implements ProductReviewRepositoryInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'product_review', ProductReview::class);
    }

    public function findAll(): array
    {
        $questions = parent::findAll();
        
        // Add question_count to each question
        foreach ($questions as &$review) {
            $review['review_count'] = $this->getReviewCount($review['product_review_id']);
        }
        
        return $questions;
    }


    private function getReviewCount(int $parentId): int
    {
        $sql = "SELECT COUNT(*) as count FROM product_review WHERE parent_id = :parent_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['parent_id' => $parentId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return (int) $result['count'];
    }

} 