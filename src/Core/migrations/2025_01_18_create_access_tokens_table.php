<?php

declare(strict_types=1);


class CreateAccessTokensTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the access_tokens table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `access_tokens` (
                `id` VARCHAR(128) NOT NULL,
                `client_id` INT  DEFAULT NULL,
                `user_id` INT DEFAULT NULL,
                `token` VARCHAR(500) DEFAULT NULL,
                `revoked` TINYINT(1) NOT NULL DEFAULT 0,
                `expires_at` DATETIME NOT NULL,
                `scopes` TEXT DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'access_tokens' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'access_tokens': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the access_tokens table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `access_tokens`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'access_tokens' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'access_tokens': " . $e->getMessage() . "\n";
        }
    }
}
