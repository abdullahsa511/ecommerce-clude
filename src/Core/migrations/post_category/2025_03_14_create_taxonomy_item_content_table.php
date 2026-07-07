<?php

declare(strict_types=1);

class CreateTaxonomyItemContentTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the taxonomy_item_content table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `taxonomy_item_content` (
                `taxonomy_item_id` INT UNSIGNED NOT NULL,
                `language_id` INT UNSIGNED NOT NULL,
                `name` varchar(191) NOT NULL,
                `slug` varchar(191) NOT NULL DEFAULT '',
                `content` text NOT NULL,
                `meta_title` varchar(191) NOT NULL DEFAULT '',
                `meta_description` varchar(191) NOT NULL DEFAULT '',
                `meta_keywords` varchar(500) NOT NULL DEFAULT '',
                `link` varchar(191) NOT NULL DEFAULT '',
                PRIMARY KEY (`taxonomy_item_id`,`language_id`),
                KEY `taxonomy_item_id` (`taxonomy_item_id`),
                KEY `language_id` (`language_id`),
                KEY `name` (`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'taxonomy_item_content' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'taxonomy_item_content': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the taxonomy_item_content table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `taxonomy_item_content`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'taxonomy_item_content' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'taxonomy_item_content': " . $e->getMessage() . "\n";
        }
    }
} 