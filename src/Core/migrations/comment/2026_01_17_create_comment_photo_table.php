<?php

declare(strict_types=1);

class CreateCommentPhotoTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the comment_photo table.
     */
    public function up(): void
    {
        $query = "CREATE TABLE comment_photo (
            comment_photo_id INT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            comment_id INT(20) UNSIGNED NOT NULL,
            media_id INT(20) UNSIGNED NOT NULL,
            image JSON NOT NULL,
            sort_order INT(10) UNSIGNED NOT NULL DEFAULT 0,
            active_status TINYINT(1) UNSIGNED NOT NULL DEFAULT 1,
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (comment_photo_id),
            KEY idx_comment_photo_sort_order (sort_order),
            KEY idx_comment_photo_active_status (active_status),
            CONSTRAINT fk_comment_photo_comment FOREIGN KEY (comment_id) REFERENCES comment (comment_id) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT fk_comment_photo_media FOREIGN KEY (media_id) REFERENCES media (media_id) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB
        DEFAULT CHARSET=utf8mb4;";

        try {
            $this->pdo->exec($query);
            echo "Table 'comment_photo' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'comment_photo': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the comment_photo table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `comment_photo`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'comment_photo' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'comment_photo': " . $e->getMessage() . "\n";
        }
    }
} 