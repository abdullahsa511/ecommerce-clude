<?php

declare(strict_types=1);

class CreateUserPointsTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the user_points table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `user_points` (
                `user_points_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `user_id` INT UNSIGNED NOT NULL DEFAULT '0',
                `order_id` INT UNSIGNED NOT NULL DEFAULT '0',
                `content` text NOT NULL,
                `points` int(8) NOT NULL DEFAULT '0',
                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`user_points_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'user_points' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'user_points': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the user_points table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `user_points`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'user_points' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'user_points': " . $e->getMessage() . "\n";
        }
    }
} 