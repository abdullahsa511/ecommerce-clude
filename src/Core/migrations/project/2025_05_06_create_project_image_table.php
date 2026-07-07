<?php

declare(strict_types=1);

class CreateProjectImageTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the project_image table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS project_image (
                project_image_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                project_id INT UNSIGNED NOT NULL,
                media_id INT UNSIGNED NULL,
                image_link varchar(191) NOT NULL,
                image json NOT NULL,
                sort_order int(3) NOT NULL DEFAULT '0',
                status json NOT NULL,
                way_points json NULL,
                created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (project_image_id),
                KEY project_id (project_id),
                KEY media_id (media_id),
                CONSTRAINT `fk_project_image_project` FOREIGN KEY (`project_id`) 
                    REFERENCES `project`(`project_id`) ON DELETE CASCADE,
                CONSTRAINT `fk_project_image_media` FOREIGN KEY (`media_id`)
                    REFERENCES `media`(`media_id`) ON DELETE CASCADE,
                UNIQUE KEY `uk_project_image` (`project_id`, `image_link`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'project_image' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'project_image': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the project_image table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS project_image;";

        try {
            $this->pdo->exec($query);
            echo "Table 'project_image' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'project_image': " . $e->getMessage() . "\n";
        }
    }
}