<?php

declare(strict_types=1);

class CreatePostContentMetaTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the post_content_meta table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `post_content_meta` (
                `post_id` INT unsigned NOT NULL DEFAULT '0',
                `language_id` INT unsigned NOT NULL DEFAULT '0',
                `namespace` varchar(32)  NOT NULL,
                `key` varchar(191) NOT NULL,
                `value` longtext,
                PRIMARY KEY (`post_id`, `language_id`, `namespace`, `key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'post_content_meta' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'post_content_meta': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the post_content_meta table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `post_content_meta`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'post_content_meta' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'post_content_meta': " . $e->getMessage() . "\n";
        }
    }
} 