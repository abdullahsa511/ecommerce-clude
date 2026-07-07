<?php

declare(strict_types=1);

class CreateOptionContentTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the option_content table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `option_content` (
                `option_id` int(10) UNSIGNED NOT NULL,
                `language_id` int(10) UNSIGNED NOT NULL,
                `name` varchar(128) NOT NULL,
                `deleted_at` TIMESTAMP NULL DEFAULT NULL,
                PRIMARY KEY (`option_id`,`language_id`),
                KEY `option_id` (`option_id`),
                KEY `language_id` (`language_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'option_content' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'option_content': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the option_content table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `option_content`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'option_content' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'option_content': " . $e->getMessage() . "\n";
        }
    }
} 