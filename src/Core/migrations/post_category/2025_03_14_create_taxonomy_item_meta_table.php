<?php

declare(strict_types=1);

class CreateTaxonomyItemMetaTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the taxonomy_item_meta table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `taxonomy_item_meta` (
                `meta_id` INT unsigned NOT NULL AUTO_INCREMENT,
                `taxonomy_item_id` INT unsigned NOT NULL DEFAULT '0',
                `key` varchar(191) DEFAULT NULL,
                `value` longtext,
                PRIMARY KEY (`meta_id`),
                KEY `taxonomy_item_id` (`taxonomy_item_id`),
                KEY `key` (`key`(191))
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'taxonomy_item_meta' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'taxonomy_item_meta': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the taxonomy_item_meta table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `taxonomy_item_meta`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'taxonomy_item_meta' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'taxonomy_item_meta': " . $e->getMessage() . "\n";
        }
    }
} 