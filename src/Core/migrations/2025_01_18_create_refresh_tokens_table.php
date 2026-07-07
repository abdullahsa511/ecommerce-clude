<?php

declare(strict_types=1);

use Dotenv\Dotenv;

class CreateRefreshTokensTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the refresh_tokens table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS refresh_tokens (
                `id` VARCHAR(128) NOT NULL,
                access_token_id VARCHAR(100) NOT NULL,
                revoked TINYINT(1) NOT NULL DEFAULT 0,
                expires_at DATETIME NOT NULL,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'refresh_tokens' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'refresh_tokens': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the refresh_tokens table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS refresh_tokens;";

        try {
            $this->pdo->exec($query);
            echo "Table 'refresh_tokens' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'refresh_tokens': " . $e->getMessage() . "\n";
        }
    }
}
