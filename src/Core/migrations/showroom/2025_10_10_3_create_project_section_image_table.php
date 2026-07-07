<?php

declare(strict_types=1);


class CreateProjectSectionImagesTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the project_section_images table.
     */
    public function up(): void
    {
        $query = "
           CREATE TABLE `project_section_images` (
            `project_section_images_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `section_id` INT UNSIGNED NOT NULL,
            `image_link` VARCHAR(191) NOT NULL,
            `image` JSON NOT NULL,
            `status` JSON NOT NULL,
            `sort_order` INT NOT NULL DEFAULT 0,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
            `deleted_at` DATETIME DEFAULT NULL,
            PRIMARY KEY (`project_section_images_id`),
            KEY `idx_section_id` (`section_id`),
            CONSTRAINT `fk_sections_products_image_section`
                FOREIGN KEY (`section_id`)
                REFERENCES `project_sections` (`project_sections_id`)
                ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'project_section_images' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'project_section_images': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the project_section_images table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS project_section_images;";

        try {
            $this->pdo->exec($query);
            echo "Table 'project_section_images' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'project_section_images': " . $e->getMessage() . "\n";
        }
    }
}