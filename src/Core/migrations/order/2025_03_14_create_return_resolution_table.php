<?php

declare(strict_types=1);

class CreateReturnResolutionTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the return_resolution table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `return_resolution` (
                `return_resolution_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `language_id` INT UNSIGNED NOT NULL DEFAULT '0',
                `name` varchar(64) NOT NULL,
                PRIMARY KEY (`return_resolution_id`,`language_id`),
                KEY `return_resolution_id` (`return_resolution_id`),
                KEY `language_id` (`language_id`),
                UNIQUE KEY `return_resolution_name_language_id` (`name`, `language_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'return_resolution' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'return_resolution': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the return_resolution table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `return_resolution`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'return_resolution' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'return_resolution': " . $e->getMessage() . "\n";
        }
    }
} 