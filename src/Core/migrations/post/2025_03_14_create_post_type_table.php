<?php

declare(strict_types=1);

class CreatePostTypeTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the post_type table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `post_type` (
                `post_type_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` varchar(191) NOT NULL DEFAULT '',
                `type` varchar(191) NOT NULL DEFAULT '',
                `plural` varchar(191) NOT NULL DEFAULT '',
                `icon` varchar(191) NOT NULL DEFAULT '',
                `image` varchar(191) NOT NULL DEFAULT '',
                `source` varchar(191) NOT NULL DEFAULT '',
                `site_id` tinyint unsigned NOT NULL,
                PRIMARY KEY (`post_type_id`),
                CONSTRAINT `fk_post_type_site` FOREIGN KEY (`site_id`) REFERENCES `site` (`site_id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'post_type' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'post_type': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the post_type table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `post_type`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'post_type' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'post_type': " . $e->getMessage() . "\n";
        }
    }
} 