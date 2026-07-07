<?php

declare(strict_types=1);

class CreateMenuTypeContentTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the menu_type_content table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `menu_type_content` (
                `menu_type_id` INT UNSIGNED NOT NULL,
                `language_id` INT UNSIGNED NOT NULL,
                `name` varchar(191) NOT NULL,
                `slug` varchar(191) NOT NULL DEFAULT '',
                `content` text NOT NULL,
                PRIMARY KEY (`menu_type_id`,`language_id`),
                KEY `name` (`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'menu_type_content' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'menu_type_content': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the menu_type_content table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `menu_type_content`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'menu_type_content' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'menu_type_content': " . $e->getMessage() . "\n";
        }
    }
} 