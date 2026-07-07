<?php

declare(strict_types=1);

class CreateTaxonomyContentTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the taxonomy_content table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `taxonomy_content` (
                `taxonomy_id` INT UNSIGNED NOT NULL,
                `language_id` INT UNSIGNED NOT NULL,
                `name` varchar(191) NOT NULL,
                `slug` varchar(191) NOT NULL DEFAULT '',
                `link` varchar(191) NOT NULL DEFAULT '',
                `content` text NOT NULL,
                PRIMARY KEY (`taxonomy_id`,`language_id`),
                KEY `name` (`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'taxonomy_content' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'taxonomy_content': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the taxonomy_content table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `taxonomy_content`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'taxonomy_content' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'taxonomy_content': " . $e->getMessage() . "\n";
        }
    }
}