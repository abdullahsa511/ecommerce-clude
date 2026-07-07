<?php

declare(strict_types=1);

class CreatePostImageTable
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
            CREATE TABLE IF NOT EXISTS post_image (
                post_image_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                post_id INT UNSIGNED NOT NULL,
                 media_id INT UNSIGNED NULL,
                image_link varchar(191) NOT NULL,
                image json NOT NULL,
                sort_order int(3) NOT NULL DEFAULT '0',
                status json NOT NULL,
                way_points json NULL,
                PRIMARY KEY (post_image_id),
                KEY post_id (post_id),
                 KEY media_id (media_id),
                CONSTRAINT `fk_post_image_post` FOREIGN KEY (`post_id`) 
                    REFERENCES `post`(`post_id`) ON DELETE CASCADE,
                CONSTRAINT `fk_project_image_media` FOREIGN KEY (`media_id`)
                    REFERENCES `media`(`media_id`) ON DELETE CASCADE,
                UNIQUE KEY `uk_post_image` (`post_id`, `image_link`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'post_image' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'post_image': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the project_image table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS post_image;";

        try {
            $this->pdo->exec($query);
            echo "Table 'post_image' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'post_image': " . $e->getMessage() . "\n";
        }
    }
}