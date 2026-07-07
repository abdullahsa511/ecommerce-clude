<?php

declare(strict_types=1);

class CreatePinboardItemAccessoriesTable
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
        $query = "CREATE TABLE `pinboard_item_accessories` (
                        `pinboard_item_accessories_id` int unsigned NOT NULL AUTO_INCREMENT,
                        `pinboard_id` int unsigned NOT NULL,
                        `pinboard_item_id` int unsigned NOT NULL,
                        `accessories_product_id` int unsigned NOT NULL,
                        `accessories_item_id` int unsigned NOT NULL,
                        `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                        `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        `deleted_at` datetime DEFAULT NULL,
                        PRIMARY KEY (
                            `pinboard_item_accessories_id`
                        ),
                        KEY `idx_pinboard_item_id` (`pinboard_item_id`),
                        KEY `idx_accessories_product_id` (`accessories_product_id`),
                        KEY `idx_accessories_item_id` (`accessories_item_id`),
                        KEY `pinboard_id` (`pinboard_id`),
                        CONSTRAINT `pinboard_item_accessories_ibfk_1` FOREIGN KEY (`pinboard_id`) REFERENCES `pinboard` (`pinboard_id`),
                        CONSTRAINT `pinboard_item_accessories_ibfk_4` FOREIGN KEY (`accessories_product_id`) REFERENCES `product` (`product_id`)
                    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci
                ";

        try {
            $this->pdo->exec($query);
            echo "Table 'pinboard_item_accessories' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'pinboard_item_accessories': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the order table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `pinboard_item_accessories`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'pinboard_item_accessories' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'pinboard_item_accessories': " . $e->getMessage() . "\n";
        }
    }
} 