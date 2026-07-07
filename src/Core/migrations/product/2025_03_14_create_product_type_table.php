<?php

declare(strict_types=1);

class CreateProductTypeTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the product_type table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `product_type` (
                `product_type_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` varchar(191) NOT NULL DEFAULT '',
                `type` varchar(191) NOT NULL DEFAULT '',
                `plural` varchar(191) NOT NULL DEFAULT '',
                `icon` varchar(191) NOT NULL DEFAULT '',
                `image` varchar(191) NOT NULL DEFAULT '',
                `source` varchar(191) NOT NULL DEFAULT '',
                `site_id` tinyint unsigned NOT NULL,
                PRIMARY KEY (`product_type_id`),
                CONSTRAINT `fk_product_type_site` FOREIGN KEY (`site_id`) REFERENCES `site` (`site_id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_type' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'product_type': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the product_type table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `product_type`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_type' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'product_type': " . $e->getMessage() . "\n";
        }
    }
} 