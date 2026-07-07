<?php

declare(strict_types=1);

class CreateProductInstagram
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `product_instagram` (
                `product_instagram_id` int unsigned NOT NULL AUTO_INCREMENT,
                `product_id` int unsigned NOT NULL,
                `product_url` varchar(500) NOT NULL DEFAULT '',
                `instagram_url` varchar(500) NOT NULL,
                `instagram_media_id` varchar(100) DEFAULT NULL,
                `thumbnail_url` varchar(1000) DEFAULT NULL,
                `caption` text,
                `shortcode` varchar(100) DEFAULT NULL,
                `hashtag` varchar(191) DEFAULT NULL,
                `media_type` varchar(50) DEFAULT NULL,
                `sort_order` int NOT NULL DEFAULT 0,
                `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`product_instagram_id`),
                KEY `product_id` (`product_id`),
                KEY `instagram_media_id` (`instagram_media_id`),
                KEY `product_url` (`product_url`(191)),
                KEY `hashtag` (`hashtag`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_instagram' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'product_instagram': " . $e->getMessage() . "\n";
        }
    }

    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `product_instagram`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_instagram' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'product_instagram': " . $e->getMessage() . "\n";
        }
    }
}
