<?php

declare(strict_types=1);

class CreateJobTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the job table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `job` (
                `job_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `language_id` INT UNSIGNED NOT NULL,
                `type` VARCHAR(100) NOT NULL,
                `reference` VARCHAR(100) NOT NULL,
                `job_title` VARCHAR(255) NOT NULL,
                `description` TEXT NULL,
                `company` VARCHAR(255) NOT NULL,
                `account_manager_id` INT UNSIGNED NULL,
                `account_manager_name` VARCHAR(255) NULL,
                `status` VARCHAR(50) NOT NULL DEFAULT 'active',
                `value` DECIMAL(13,2) NOT NULL DEFAULT 0,
                `created_at` TIMESTAMP NULL DEFAULT current_timestamp,
                `updated_at` TIMESTAMP NULL DEFAULT current_timestamp,
                `deleted_at` DATETIME DEFAULT NULL,
                PRIMARY KEY (`job_id`,`language_id`),
                KEY `type` (`type`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'job' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'job': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the job table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `job`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'job' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'job': " . $e->getMessage() . "\n";
        }
    }
} 