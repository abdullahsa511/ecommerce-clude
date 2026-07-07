<?php

declare(strict_types=1);

class CreatePostTagTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the post_tag table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `post_tag` (
                `post_tag_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` varchar(191) NOT NULL DEFAULT '',
                `slug` varchar(191) NOT NULL DEFAULT '',
                `description` text NOT NULL,
                `image` varchar(191) NOT NULL DEFAULT '',
                `status` tinyint NOT NULL DEFAULT 1,
                `created_at` datetime NULL DEFAULT NULL CURRENT_TIMESTAMP,
                `deleted_at` datetime NULL DEFAULT NULL CURRENT_TIMESTAMP,
                `post_id` INT UNSIGNED NULL,
                PRIMARY KEY (`post_tag_id`),
                CONSTRAINT `fk_post_tag_post` FOREIGN KEY (`post_id`) REFERENCES `post` (`post_id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'post_tag' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'post_tag': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the post_tag table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `post_tag`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'post_tag' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'post_tag': " . $e->getMessage() . "\n";
        }
    }
}