<?php

declare(strict_types=1);

use Dotenv\Dotenv;

class CreateUsersAuthScopesTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the users_auth_scopes table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `users_auth_scopes` (
                `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT UNSIGNED NOT NULL,
                `scopes` VARCHAR(1000) NOT NULL,
                `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT `fk_user_scopes_user` FOREIGN KEY (`user_id`) 
                    REFERENCES `user`(`user_id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'users_auth_scopes' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'users_auth_scopes': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the users_auth_scopes table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `users_auth_scopes`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'users_auth_scopes' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'users_auth_scopes': " . $e->getMessage() . "\n";
        }
    }
}
