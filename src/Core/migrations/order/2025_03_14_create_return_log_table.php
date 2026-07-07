<?php

declare(strict_types=1);

class CreateReturnLogTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the return_log table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `return_log` (
                `return_log_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `return_id` INT UNSIGNED NOT NULL,
                `return_status_id` INT UNSIGNED NOT NULL,
                `notify` tinyint NOT NULL,
                `note` text NOT NULL,
                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`return_log_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'return_log' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'return_log': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the return_log table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `return_log`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'return_log' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'return_log': " . $e->getMessage() . "\n";
        }
    }
} 