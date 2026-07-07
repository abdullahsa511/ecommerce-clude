<?php

declare(strict_types=1);

class CreateReturnStatusTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the return_status table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `return_status` (
                `return_status_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `language_id` INT UNSIGNED NOT NULL DEFAULT '0',
                `name` varchar(32) NOT NULL,
                PRIMARY KEY (`return_status_id`,`language_id`),
                KEY `return_status_id` (`return_status_id`),
                KEY `language_id` (`language_id`),
                UNIQUE KEY `return_status_name_language_id` (`name`, `language_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'return_status' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'return_status': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the return_status table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `return_status`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'return_status' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'return_status': " . $e->getMessage() . "\n";
        }
    }
} 