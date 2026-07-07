<?php

declare(strict_types=1);
class CreateClientsTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the clients table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `clients` (
                `id` INT UNSIGNED AUTO_INCREMENT,
                `secret` VARCHAR(255) NOT NULL,
                `name` VARCHAR(255) NOT NULL,
                `scopes` VARCHAR(255) NOT NULL,
                `redirect_uri` TEXT NOT NULL,
                `revoked` TINYINT(1) NOT NULL DEFAULT 0,
                `is_confidential` TINYINT(1) NOT NULL DEFAULT 1,
                `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'clients' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'clients': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the clients table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `clients`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'clients' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'clients': " . $e->getMessage() . "\n";
        }
    }
}