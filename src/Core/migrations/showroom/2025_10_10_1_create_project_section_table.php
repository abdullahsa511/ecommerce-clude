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
            CREATE TABLE `project_sections` (
            `project_sections_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `project_id` INT UNSIGNED NOT NULL,
            `section_code` VARCHAR(191) DEFAULT NULL,
            `title` VARCHAR(191) NOT NULL,
            `slug` VARCHAR(191) DEFAULT NULL,
            `description` TEXT DEFAULT NULL,
            `image` JSON  DEFAULT NULL,
            `status` VARCHAR(191) NULL,
            `sort_order` INT NOT NULL DEFAULT 0,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            `deleted_at` DATETIME DEFAULT NULL,
            PRIMARY KEY (`project_sections_id`),
            UNIQUE KEY `uk_project_section` (`project_id`, `project_sections_id`),
            KEY `idx_project_id` (`project_id`),
            CONSTRAINT `fk_project_sections_project` FOREIGN KEY (`project_id`) REFERENCES `project` (`project_id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'project_sections' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'project_sections': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the project_sections table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS project_sections;";

        try {
            $this->pdo->exec($query);
            echo "Table 'project_sections' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'project_sections': " . $e->getMessage() . "\n";
        }
    }
}
