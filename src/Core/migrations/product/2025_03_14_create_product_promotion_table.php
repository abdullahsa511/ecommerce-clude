<?php

declare(strict_types=1);

class CreateProductPromotionTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the product_promotion table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS product_promotion (
                product_promotion_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                product_id INT UNSIGNED NOT NULL,
                user_group_id INT UNSIGNED NOT NULL,
                priority int NOT NULL DEFAULT '1',
                price decimal(15,4) NOT NULL DEFAULT '0.0000',
                from_date date,
                to_date date,
                user_group_name VARCHAR(255) NOT NULL,
                PRIMARY KEY (product_promotion_id),
                KEY product_id (product_id)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_promotion' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'product_promotion': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the product_promotion table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS product_promotion;";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_promotion' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'product_promotion': " . $e->getMessage() . "\n";
        }
    }
}
