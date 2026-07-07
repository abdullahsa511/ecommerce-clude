<?php

declare(strict_types=1);

class CreateFieldValueContentTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the field_value_content table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `field_value_content` (
                `field_value_id` int(10) UNSIGNED NOT NULL,
                `language_id` int(10) UNSIGNED NOT NULL,
                `field_id` int(10) UNSIGNED NOT NULL,
                `name` varchar(128) NOT NULL,
                PRIMARY KEY (`field_value_id`,`language_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'field_value_content' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'field_value_content': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the field_value_content table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `field_value_content`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'field_value_content' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'field_value_content': " . $e->getMessage() . "\n";
        }
    }
} 