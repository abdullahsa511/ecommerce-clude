<?php

declare(strict_types=1);

class CreateVariantContentTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the variant_content table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `variant_content` (
                `variant_id` int(10) UNSIGNED NOT NULL,
                `language_id` int(10) UNSIGNED NOT NULL,
                `name` varchar(64) NOT NULL,
                `description` text NULL,
                PRIMARY KEY (`variant_id`,`language_id`),
                UNIQUE KEY `uq_variant_content_language_id_name` (`language_id`,`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'variant_content' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'variant_content': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the variant_content table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `variant_content`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'variant_content' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'variant_content': " . $e->getMessage() . "\n";
        }
    }
} 