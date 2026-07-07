<?php

declare(strict_types=1);

class CreateProductOptionTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the product_option_value table.
     */
    public function up(): void
    {
        $query = "CREATE TABLE `product_option` (
                    `product_option_id` int unsigned NOT NULL AUTO_INCREMENT,
                    `product_id` int unsigned NOT NULL,
                    `product_variant_id` int unsigned NOT NULL,
                    `product_option_group_id` int unsigned NOT NULL,
                    `type_id` int unsigned DEFAULT NULL,
                    `option_name` varchar(191) NOT NULL,
                    `price` decimal(15, 4) NOT NULL DEFAULT '0.0000',
                    `option_description` varchar(500) DEFAULT NULL,
                    `sort_order` int NOT NULL DEFAULT '0',
                    `active_status` tinyint(1) NOT NULL DEFAULT '1',
                    `hex_color` varchar(50) DEFAULT NULL,
                    `option_image` json DEFAULT NULL,
                    `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    `deleted_at` datetime DEFAULT NULL,
                    PRIMARY KEY (`product_option_id`),
                    UNIQUE KEY `uq_product_variant_id_product_option_group_id_name` (
                        `product_id`,
                        `product_variant_id`,
                        `product_option_group_id`,
                        `option_name`
                    ),
                    KEY `product_variant_id` (`product_variant_id`),
                    KEY `product_option_group_id` (`product_option_group_id`),
                    KEY `idx_product_option_sort_order` (`sort_order`),
                    KEY `idx_product_option_active_status` (`active_status`),
                    KEY `idx_product_option_deleted_at` (`deleted_at`),
                    KEY `idx_product_option_created_at` (`created_at`),
                    KEY `idx_product_option_updated_at` (`updated_at`),
                    CONSTRAINT `product_option_ibfk_1` FOREIGN KEY (`product_variant_id`) REFERENCES `product_variant` (`product_variant_id`) ON DELETE CASCADE ON UPDATE CASCADE,
                    CONSTRAINT `product_option_ibfk_2` FOREIGN KEY (`product_option_group_id`) REFERENCES `product_option_group` (`product_option_group_id`) ON DELETE CASCADE ON UPDATE CASCADE,
                    CONSTRAINT `product_option_ibfk_3` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
                ) ENGINE = InnoDB AUTO_INCREMENT = 5077 DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_option' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'product_option': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the product_option_value table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `product_option`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_option' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'product_option': " . $e->getMessage() . "\n";
        }
    }
} 