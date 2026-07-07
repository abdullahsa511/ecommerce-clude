<?php

declare(strict_types=1);

class CreateUserGroupTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the user_group table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `user_group` (
                `user_group_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `status` int(1) NOT NULL,
                `sort_order` int(3) NOT NULL DEFAULT 0,
                PRIMARY KEY (`user_group_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'user_group' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'user_group': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the user_group table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `user_group`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'user_group' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'user_group': " . $e->getMessage() . "\n";
        }
    }
} 