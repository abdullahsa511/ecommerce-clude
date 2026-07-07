<?php

declare(strict_types=1);

class CreateAttributeGroupContentTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the attribute_group_content table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `attribute_group_content` (
                `attribute_group_id` int(10) UNSIGNED NOT NULL,
                `language_id` int(10) UNSIGNED NOT NULL,
                `name` varchar(64) NOT NULL,
                PRIMARY KEY (`attribute_group_id`,`language_id`),
                UNIQUE KEY `uq_attribute_group_content_language_id_name` (`language_id`,`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'attribute_group_content' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'attribute_group_content': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the attribute_group_content table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `attribute_group_content`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'attribute_group_content' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'attribute_group_content': " . $e->getMessage() . "\n";
        }
    }
} 