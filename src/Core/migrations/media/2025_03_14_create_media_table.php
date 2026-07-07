<?php

declare(strict_types=1);

class CreateMediaTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the media table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `media` (
                `media_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `file` JSON,
                `type` varchar(30) NOT NULL default 'image/png',
                `meta` TEXT,
                `parent_id` INT UNSIGNED NULL DEFAULT NULL,
                `folder_id` INT UNSIGNED NULL DEFAULT NULL,
                `name` varchar(191) NULL DEFAULT NULL,
                `way_points` JSON NULL DEFAULT NULL,
                `path` varchar(500) NOT NULL,
                `created_at` TIMESTAMP NULL DEFAULT current_timestamp,
                `updated_at` TIMESTAMP NULL DEFAULT current_timestamp,
                PRIMARY KEY (`media_id`),
                KEY `file_media_id` (`media_id`),
                UNIQUE KEY `media_path` (`path`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'media' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'media': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the media table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `media`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'media' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'media': " . $e->getMessage() . "\n";
        }
    }
} 