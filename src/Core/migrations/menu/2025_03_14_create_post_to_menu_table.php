<?php

declare(strict_types=1);

class CreatePostToMenuTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the post_to_menu table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `post_to_menu` (
                `post_id` INT UNSIGNED NOT NULL,
                `menu_id` INT UNSIGNED NOT NULL,
                PRIMARY KEY (`post_id`,`menu_id`),
                KEY `menu_id` (`menu_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'post_to_menu' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'post_to_menu': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the post_to_menu table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `post_to_menu`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'post_to_menu' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'post_to_menu': " . $e->getMessage() . "\n";
        }
    }
} 