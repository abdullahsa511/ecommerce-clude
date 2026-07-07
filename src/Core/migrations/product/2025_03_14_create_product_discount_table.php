<?php

declare(strict_types=1);


class CreateProductDiscountTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the product_discount table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS product_discount (
                product_discount_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                product_id INT UNSIGNED NOT NULL,
                user_group_id INT UNSIGNED NOT NULL,
                quantity int(4) NOT NULL DEFAULT '0',
                priority int NOT NULL DEFAULT '1',
                price decimal(15,4) NOT NULL DEFAULT '0.0000',
                from_date date,
                to_date date,
                deleted_at DATETIME DEFAULT NULL,
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (product_discount_id),
                KEY product_id (product_id)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_discount' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'product_discount': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the product_discount table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS product_discount;";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_discount' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'product_discount': " . $e->getMessage() . "\n";
        }
    }
}