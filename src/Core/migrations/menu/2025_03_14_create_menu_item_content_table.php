<?php

declare(strict_types=1);

class CreateMenuItemContentTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the menu_item_content table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `menu_item_content` (
                `menu_item_id` INT UNSIGNED NOT NULL,
                `language_id` INT UNSIGNED NOT NULL,
                `name` varchar(191) NOT NULL DEFAULT '',
                `slug` varchar(191) NOT NULL DEFAULT '',
                `content` text NOT NULL,
                PRIMARY KEY (`menu_item_id`,`language_id`),
                KEY `name` (`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'menu_item_content' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'menu_item_content': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the menu_item_content table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `menu_item_content`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'menu_item_content' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'menu_item_content': " . $e->getMessage() . "\n";
        }
    }
} 