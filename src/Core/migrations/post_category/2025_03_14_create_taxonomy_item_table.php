<?php

declare(strict_types=1);

class CreateTaxonomyItemTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the taxonomy_item table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `taxonomy_item` (
                `taxonomy_item_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `taxonomy_item_code` VARCHAR(191) DEFAULT NULL,
                `taxonomy_id` INT UNSIGNED NOT NULL,
                `name` varchar(191) NOT NULL DEFAULT '',
                `link` varchar(255) NOT NULL DEFAULT '',
                `products_link` varchar(255) NOT NULL DEFAULT '',
                `image` JSON NULL DEFAULT NULL,
                `slider_image` JSON NULL DEFAULT NULL,
                `label_name` varchar(191) NULL DEFAULT NULL,
                `template` varchar(191) NOT NULL DEFAULT '',
                `parent_id` INT UNSIGNED NULL DEFAULT null,
                `item_id` INT UNSIGNED DEFAULT NULL,
                `sort_order` int(3) NOT NULL DEFAULT 0,
                `color` varchar(191) NULL DEFAULT NULL,
                `status` tinyint NOT NULL DEFAULT 0,
                `banner_way_points` json NULL DEFAULT NULL,
                PRIMARY KEY (`taxonomy_item_id`),
                UNIQUE KEY `name` (`name`),
                KEY `parent_id` (`parent_id`),
                UNIQUE KEY `unique_taxonomy_id_name` (`taxonomy_id`, `name`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'taxonomy_item' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'taxonomy_item': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the taxonomy_item table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `taxonomy_item`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'taxonomy_item' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'taxonomy_item': " . $e->getMessage() . "\n";
        }
    }
} 