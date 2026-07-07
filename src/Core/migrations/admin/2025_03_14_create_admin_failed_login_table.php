<?php

declare(strict_types=1);

class CreateAdminFailedLoginTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the admin_failed_login table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `admin_failed_login` (
                `admin_id` INT UNSIGNED NOT NULL,
                `count` INT UNSIGNED DEFAULT 0,
                `last_ip` VARCHAR(16) NOT NULL DEFAULT '',
                `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`admin_id`, `updated_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'admin_failed_login' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'admin_failed_login': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the admin_failed_login table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `admin_failed_login`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'admin_failed_login' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'admin_failed_login': " . $e->getMessage() . "\n";
        }
    }
} 