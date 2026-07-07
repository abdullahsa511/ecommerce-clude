<?php

declare(strict_types=1);

class CreatePostMetaTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the post_meta table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `post_meta` (
                `post_id` INT unsigned NOT NULL DEFAULT '0',
                `namespace` varchar(32) NOT NULL,
                `key` varchar(191) NOT NULL,
                `value` longtext,
                PRIMARY KEY (`post_id`,`namespace`,`key`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'post_meta' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'post_meta': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the post_meta table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `post_meta`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'post_meta' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'post_meta': " . $e->getMessage() . "\n";
        }
    }
} 