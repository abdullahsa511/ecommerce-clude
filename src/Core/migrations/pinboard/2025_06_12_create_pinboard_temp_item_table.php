<?php

declare(strict_types=1);

class CreatePinboardTempItemTable
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
        $query = "CREATE TABLE `pinboard_temp_item` (
                `pinboard_temp_item_id` int unsigned NOT NULL AUTO_INCREMENT,
                `language_id` int unsigned NULL,
                `uuid` char(36) NULL,
                `pinboard_temp_id` int unsigned NULL,
                `model_id` int unsigned DEFAULT NULL,
                `model_type` varchar(50) DEFAULT NULL,
                `title` varchar(255) NULL,
                `description` varchar(500) NULL,
                `options` json DEFAULT NULL,
                `comments` json DEFAULT NULL,
                `quantity` int NULL DEFAULT '0',
                `unit_price` decimal(13, 2) NULL DEFAULT '0.00',
                `total_price` decimal(13, 2) NULL DEFAULT '0.00',
                `photo` varchar(255) DEFAULT NULL,
                `product_url` varchar(255) DEFAULT NULL,
                `sort_order` int NULL DEFAULT '0',
                `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
                KEY `pinboard_temp_id` (`pinboard_temp_id`),
                KEY `model_id` (`model_id`),
                KEY `model_type` (`model_type`),
                PRIMARY KEY (
                    `pinboard_temp_item_id`,
                    `language_id`
                ),
                UNIQUE KEY `uuid` (`uuid`)
            ) ENGINE = InnoDB AUTO_INCREMENT = 53 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci";

        try {
            $this->pdo->exec($query);
            echo "Table 'pinboard_temp_item' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'pinboard_temp_item': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the order table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `pinboard_temp_item`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'pinboard_temp_item' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'pinboard_temp_item': " . $e->getMessage() . "\n";
        }
    }
} 