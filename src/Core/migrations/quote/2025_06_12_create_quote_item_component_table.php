<?php

declare(strict_types=1);

class CreateQuoteItemComponentTable
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
            CREATE TABLE IF NOT EXISTS `quote_item_component` (
                `quote_item_component_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `uuid` CHAR(36) NOT NULL,
                `quote_item_id` INT UNSIGNED NOT NULL,
                `component_id` INT UNSIGNED NULL,
                `quote_kit_item_id` INT UNSIGNED NULL,
                `created_at` TIMESTAMP NULL DEFAULT current_timestamp,
                `updated_at` TIMESTAMP NULL DEFAULT current_timestamp,
                PRIMARY KEY (`quote_item_component_id`),
                UNIQUE KEY `uuid` (`uuid`),
                KEY `quotes_item_id` (`quotes_item_id`),
                KEY `component_id` (`component_id`),
                KEY `quote_kit_item_id` (`quote_kit_item_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'quote_item_component' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'quote_item_component': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the order table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `quote_item_component`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'quote_item_component' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'quote_item_component': " . $e->getMessage() . "\n";
        }
    }
} 