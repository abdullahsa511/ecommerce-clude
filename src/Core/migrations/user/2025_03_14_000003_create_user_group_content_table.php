<?php

declare(strict_types=1);

class CreateUserGroupContentTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the user_group_content table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `user_group_content` (
                `user_group_id` INT UNSIGNED NOT NULL,
                `language_id` INT UNSIGNED NOT NULL,
                `name` varchar(32) NOT NULL,
                `content` text NOT NULL,
                PRIMARY KEY (`user_group_id`,`language_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'user_group_content' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'user_group_content': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the user_group_content table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `user_group_content`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'user_group_content' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'user_group_content': " . $e->getMessage() . "\n";
        }
    }
} 