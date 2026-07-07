<?php

declare(strict_types=1);

class CreatePopularSearchTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the popular_search table.
     */
    public function up(): void
    {
        $query = "CREATE TABLE IF NOT EXISTS `popular_search` (
                popular_search_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                search_key VARCHAR(255) NOT NULL,
                search_count INT UNSIGNED NOT NULL DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at TIMESTAMP DEFAULT NULL,
                UNIQUE KEY unique_search_key (search_key)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;";

        try {
            $this->pdo->exec($query);
            echo "Table 'popular_search' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'popular_search': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the popular_search table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `popular_search`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'popular_search' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'popular_search': " . $e->getMessage() . "\n";
        }
    }
} 