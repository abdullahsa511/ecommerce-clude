<?php

declare(strict_types=1);

class CreateProductVariantTable
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
        $query = "
            CREATE TABLE IF NOT EXISTS `product_variant` (
                `product_variant_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `product_id` INT UNSIGNED NOT NULL,
                `variant_name` VARCHAR(191) NOT NULL,
                `variant_description` VARCHAR(500) NULL,
                `sort_order` int(3) NOT NULL DEFAULT 0,
                `active_status` tinyint(1) NOT NULL DEFAULT 1,
                `is_accessories` tinyint(1) NOT NULL DEFAULT 0,
                `image` JSON NULL,
                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                `deleted_at` datetime DEFAULT NULL,
                `is_default` tinyint(1) NOT NULL DEFAULT 0,
                PRIMARY KEY (`product_variant_id`),
                UNIQUE KEY `uq_product_variant_product_id_variant_name` (`product_id`,`variant_name`),
                INDEX idx_product_variant_is_accessories (is_accessories),
                FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_variant' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'product_variant': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the product_variant table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `product_variant`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_variant' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'product_variant': " . $e->getMessage() . "\n";
        }
    }
} 