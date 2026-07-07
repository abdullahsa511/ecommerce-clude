<?php

declare(strict_types=1);

class CreatePostContentTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the post_content table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `post_content` (
                `post_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `language_id` INT UNSIGNED NOT NULL,
                `name` varchar(191) NOT NULL DEFAULT '',
                `slug` varchar(191) NOT NULL DEFAULT '',
                `content` longtext,
                `excerpt` text,
                `label` varchar(191) NULL DEFAULT '',
                `link_text` varchar(191) NULL DEFAULT '',
                `meta_title` varchar(191) NULL DEFAULT '',
                `meta_keywords` varchar(191) NULL DEFAULT '',
                `meta_description` varchar(191) NULL DEFAULT '',
                PRIMARY KEY (`post_id`,`language_id`),
                KEY `slug` (`slug`),
                UNIQUE KEY `uq_post_content_slug_language_id` (`slug`,`language_id`),
                FULLTEXT `search` (`name`,`content`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'post_content' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'post_content': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the post_content table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `post_content`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'post_content' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'post_content': " . $e->getMessage() . "\n";
        }
    }
} 