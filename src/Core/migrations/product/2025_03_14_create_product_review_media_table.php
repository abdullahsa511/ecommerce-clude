<?php

declare(strict_types=1);


class CreateProductReviewMediaTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the product_review_media table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS product_review_media (
                product_review_media_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                product_review_id INT UNSIGNED NOT NULL,
                product_id INT UNSIGNED NOT NULL,
                user_id INT UNSIGNED NOT NULL,
                image varchar(191) NOT NULL,
                sort_order int(3) NOT NULL DEFAULT '0',
                PRIMARY KEY (product_review_media_id),
                KEY product_review_id (product_review_id),
                KEY product_id (product_id,user_id)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_review_media' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'product_review_media': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the product_review_media table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS product_review_media;";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_review_media' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'product_review_media': " . $e->getMessage() . "\n";
        }
    }
}