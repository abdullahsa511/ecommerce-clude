<?php

declare(strict_types=1);

class CreateUserWishlistTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the user_wishlist table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `user_wishlist` (
                `user_id` INT UNSIGNED NOT NULL,
                `product_id` INT UNSIGNED NOT NULL,
                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`user_id`,`product_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'user_wishlist' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'user_wishlist': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the user_wishlist table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `user_wishlist`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'user_wishlist' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'user_wishlist': " . $e->getMessage() . "\n";
        }
    }
} 