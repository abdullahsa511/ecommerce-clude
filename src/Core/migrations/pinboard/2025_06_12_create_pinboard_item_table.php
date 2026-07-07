<?php

declare(strict_types=1);

class CreatePinboardItemTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the pinboard table.
     */
    public function up(): void
    {
        $query = "CREATE TABLE `pinboard_item` (
                `pinboard_item_id` int unsigned NOT NULL AUTO_INCREMENT,
                `language_id` int unsigned NOT NULL,
                `uuid` char(36) NOT NULL,
                `pinboard_id` int unsigned NOT NULL,
                `model_id` int unsigned DEFAULT NULL,
                `model_type` varchar(50) DEFAULT NULL,
                `description` varchar(500) NOT NULL,
                `options` json DEFAULT NULL,
                `comments` json DEFAULT NULL,
                `quantity` int NOT NULL DEFAULT '0',
                `unit_price` decimal(13, 2) NOT NULL DEFAULT '0.00',
                `total_price` decimal(13, 2) NOT NULL DEFAULT '0.00',
                `photo` varchar(255) DEFAULT NULL,
                `product_url` varchar(255) DEFAULT NULL,
                `sort_order` int NOT NULL DEFAULT '0',
                `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                KEY `pinboard_id` (`pinboard_id`),
                KEY `model_id` (`model_id`),
                KEY `model_type` (`model_type`),
                PRIMARY KEY (
                    `pinboard_item_id`,
                    `language_id`
                ),
                UNIQUE KEY `uuid` (`uuid`)
            ) ENGINE = InnoDB AUTO_INCREMENT = 53 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci";

        try {
            $this->pdo->exec($query);
            echo "Table 'pinboard_item' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'pinboard_item': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the order table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `pinboard_item`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'pinboard_item' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'pinboard_item': " . $e->getMessage() . "\n";
        }
    }
} 