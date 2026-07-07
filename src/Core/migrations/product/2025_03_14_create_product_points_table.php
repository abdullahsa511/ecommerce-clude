<?php

declare(strict_types=1);

class CreateProductPointsTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the product_points table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS product_points (
                product_points_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                product_id INT UNSIGNED NOT NULL DEFAULT '0',
                user_group_id INT UNSIGNED NOT NULL DEFAULT '0',
                points int(8) NOT NULL DEFAULT '0',
                PRIMARY KEY (product_points_id)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_points' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'product_points': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the product_points table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS product_points;";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_points' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'product_points': " . $e->getMessage() . "\n";
        }
    }
}