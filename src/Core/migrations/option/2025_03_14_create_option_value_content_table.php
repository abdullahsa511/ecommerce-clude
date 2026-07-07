<?php

declare(strict_types=1);

class CreateOptionValueContentTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the option_value_content table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `option_value_content` (
                `option_value_id` int(10) UNSIGNED NOT NULL,
                `language_id` int(10) UNSIGNED NOT NULL,
                `option_id` int(10) UNSIGNED NOT NULL,
                `name` varchar(128) NOT NULL,
                PRIMARY KEY (`option_value_id`,`language_id`),
                KEY `option_value_id` (`option_value_id`),
                KEY `language_id` (`language_id`),
                KEY `option_id` (`option_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'option_value_content' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'option_value_content': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the option_value_content table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `option_value_content`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'option_value_content' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'option_value_content': " . $e->getMessage() . "\n";
        }
    }
} 