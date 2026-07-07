<?php

declare(strict_types=1);

class CreateFieldGroupContentTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the field_group_content table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `field_group_content` (
                `field_group_id` int NOT NULL,
                `language_id` int NOT NULL,
                `name` varchar(128) NOT NULL,
                PRIMARY KEY (`field_group_id`,`language_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'field_group_content' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'field_group_content': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the field_group_content table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `field_group_content`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'field_group_content' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'field_group_content': " . $e->getMessage() . "\n";
        }
    }
} 