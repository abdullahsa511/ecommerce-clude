<?php

declare(strict_types=1);

class CreatePostToTaxonomyItemTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the post_to_taxonomy_item table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `post_to_taxonomy_item` (
                `post_id` INT UNSIGNED NOT NULL,
                `taxonomy_item_id` INT UNSIGNED NOT NULL,
                PRIMARY KEY (`post_id`,`taxonomy_item_id`),
                KEY `taxonomy_item_id` (`taxonomy_item_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'post_to_taxonomy_item' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'post_to_taxonomy_item': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the post_to_taxonomy_item table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `post_to_taxonomy_item`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'post_to_taxonomy_item' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'post_to_taxonomy_item': " . $e->getMessage() . "\n";
        }
    }
} 