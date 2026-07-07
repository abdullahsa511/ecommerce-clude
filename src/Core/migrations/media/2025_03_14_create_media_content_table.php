<?php

declare(strict_types=1);

class CreateMediaContentTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the media_content table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `media_content` (
                `media_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `language_id` INT UNSIGNED NOT NULL,
                `name` varchar(191) NOT NULL DEFAULT '',
                `caption` varchar(191) NOT NULL DEFAULT '',
                `description` varchar(191) NOT NULL DEFAULT '',
                `way_points` JSON NULL DEFAULT NULL,
                PRIMARY KEY (`media_id`,`language_id`),
                FULLTEXT `search` (`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'media_content' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'media_content': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the media_content table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `media_content`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'media_content' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'media_content': " . $e->getMessage() . "\n";
        }
    }
}