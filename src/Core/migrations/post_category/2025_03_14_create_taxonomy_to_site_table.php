<?php

declare(strict_types=1);

class CreateTaxonomyToSiteTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the taxonomy_to_site table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `taxonomy_to_site` (
                `taxonomy_item_id` INT UNSIGNED NOT NULL,
                `site_id` tinyint(6) UNSIGNED NOT NULL,
                PRIMARY KEY (`taxonomy_item_id`,`site_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'taxonomy_to_site' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'taxonomy_to_site': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the taxonomy_to_site table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `taxonomy_to_site`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'taxonomy_to_site' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'taxonomy_to_site': " . $e->getMessage() . "\n";
        }
    }
} 