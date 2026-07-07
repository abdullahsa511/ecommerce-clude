<?php

declare(strict_types=1);

class CreateReturnReasonTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the return_reason table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `return_reason` (
                `return_reason_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `language_id` INT UNSIGNED NOT NULL DEFAULT '0',
                `name` varchar(128) NOT NULL,
                PRIMARY KEY (`return_reason_id`,`language_id`),
                KEY `return_reason_id` (`return_reason_id`),
                KEY `language_id` (`language_id`),
                UNIQUE KEY `return_reason_name_language_id` (`name`, `language_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'return_reason' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'return_reason': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the return_reason table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `return_reason`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'return_reason' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'return_reason': " . $e->getMessage() . "\n";
        }
    }
} 