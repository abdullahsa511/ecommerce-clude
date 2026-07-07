<?php

declare(strict_types=1);

class CreateAdminTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the admin table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `admin` (
                `admin_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `user_id` INT UNSIGNED NOT NULL,
                `uuid` binary(16) NOT NULL,
                `role_id` INT UNSIGNED DEFAULT NULL,
                `site_access` TEXT NOT NULL,
                `status` INT UNSIGNED NOT NULL DEFAULT '0',
                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`admin_id`),
                KEY `username` (`username`),
                UNIQUE KEY `uuid` (`uuid`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'admin' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'admin': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the admin table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `admin`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'admin' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'admin': " . $e->getMessage() . "\n";
        }
    }
} 