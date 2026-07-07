<?php

declare(strict_types=1);

class CreatePinboardItemComponentTable
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
        $query = "
            CREATE TABLE IF NOT EXISTS `pinboard_item_component` (
                `pinboard_item_component_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `uuid` CHAR(36) NOT NULL,
                `pinboard_item_id` INT UNSIGNED NOT NULL,
                `component_id` INT UNSIGNED NULL,
                `post_id` INT UNSIGNED NULL,
                `pinboard_kit_item_id` INT UNSIGNED NULL,
                `created_at` TIMESTAMP NULL DEFAULT current_timestamp,
                `updated_at` TIMESTAMP NULL DEFAULT current_timestamp,
                PRIMARY KEY (`pinboard_item_component_id`),
                UNIQUE KEY `uuid` (`uuid`),
                KEY `pinboard_item_id` (`pinboard_item_id`),
                KEY `component_id` (`component_id`),
                KEY `pinboard_kit_item_id` (`pinboard_kit_item_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'pinboard_items_component' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'pinboard_item_component': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the order table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `pinboard_item_component`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'pinboard_item_component' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'pinboard_item_component': " . $e->getMessage() . "\n";
        }
    }
} 