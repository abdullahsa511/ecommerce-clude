<?php

declare(strict_types=1);


class CreateProjectSectionsTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the project_sections table.
     */
    public function up(): void
    {
        $query = "
           CREATE TABLE `showrooms` (
            `showrooms_id` int UNSIGNED NOT NULL AUTO_INCREMENT,
            `title` varchar(191) NOT NULL,
            `slug` varchar(191) DEFAULT NULL,
            `description` text,
            `address` VARCHAR(300) DEFAULT NULL,
            `phone` VARCHAR(20) DEFAULT NULL,
            `email` VARCHAR(100) DEFAULT NULL,
            `mobile` VARCHAR(20) DEFAULT NULL,
            `opening_hours` VARCHAR(255) DEFAULT NULL,
            `image` json DEFAULT NULL,
            `banner_image` json DEFAULT NULL,
            `overview_image` json DEFAULT NULL,
            `banner_way_points` json NULL DEFAULT NULL,
            `is_section_active` tinyint(1) NOT NULL DEFAULT 0,
            `google_map_link` text DEFAULT NULL,
            `status` varchar(191) DEFAULT NULL,
            `sort_order` int NOT NULL DEFAULT '0',
            `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
            `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            `deleted_at` datetime DEFAULT NULL,
            PRIMARY KEY (`showrooms_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'showrooms' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'showrooms': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the showrooms table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS showrooms;";

        try {
            $this->pdo->exec($query);
            echo "Table 'showrooms' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'showrooms': " . $e->getMessage() . "\n";
        }
    }
}
