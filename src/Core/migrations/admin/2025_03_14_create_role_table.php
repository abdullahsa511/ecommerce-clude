<?php

declare(strict_types=1);

class CreateRoleTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the role table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `role` (
                `role_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` varchar(191) NOT NULL,
                `display_name` varchar(191) NOT NULL,
                `permissions` TEXT NOT NULL,
                PRIMARY KEY (`role_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'role' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'role': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the role table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `role`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'role' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'role': " . $e->getMessage() . "\n";
        }
    }
} 