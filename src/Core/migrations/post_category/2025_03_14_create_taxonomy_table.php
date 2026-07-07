<?php

declare(strict_types=1);

class CreateTaxonomyTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the taxonomy table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `taxonomy` (
                `taxonomy_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` varchar(191) NOT NULL DEFAULT '',
                `post_type` varchar(191) NOT NULL DEFAULT '',
                `type` varchar(50) NOT NULL DEFAULT 'categories',  
                `site_id` INT UNSIGNED NOT NULL DEFAULT 0,
                PRIMARY KEY (`taxonomy_id`),
                KEY `taxonomy_id` (`taxonomy_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'taxonomy' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'taxonomy': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the taxonomy table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `taxonomy`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'taxonomy' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'taxonomy': " . $e->getMessage() . "\n";
        }
    }
} 