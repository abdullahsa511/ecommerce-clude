<?php

declare(strict_types=1);

class CreateQuoteItemTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the quote table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `quote_item` (
                `quote_item_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `language_id` INT UNSIGNED NOT NULL,
                `uuid` CHAR(36) NOT NULL,
                `quote_id` INT UNSIGNED NOT NULL,
                `product_id` INT UNSIGNED NULL,
                `description` VARCHAR(500) NOT NULL,
                `quantity` INT NOT NULL DEFAULT 0,
                `unit_price` DECIMAL(13,2) NOT NULL DEFAULT 0,
                `total_price` DECIMAL(13,2) NOT NULL DEFAULT 0,
                `photo` VARCHAR(255) NULL,
                `sort_order` INT NOT NULL DEFAULT 0,
                `created_at` TIMESTAMP NULL DEFAULT current_timestamp,
                `updated_at` TIMESTAMP NULL DEFAULT current_timestamp,
                PRIMARY KEY (`quote_item_id`,`language_id`),
                UNIQUE KEY `uuid` (`uuid`),
                KEY `quote_id` (`quote_id`),
                KEY `product_id` (`product_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'quote_items' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'quote_items': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the order table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `quote_items`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'quote_items' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'quote_items': " . $e->getMessage() . "\n";
        }
    }
} 