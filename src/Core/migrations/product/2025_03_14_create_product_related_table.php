<?php

declare(strict_types=1);


class CreateProductRelatedTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the product_related table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS product_related (
                product_id INT UNSIGNED NOT NULL,
                product_related_id INT UNSIGNED NOT NULL,
                PRIMARY KEY (product_id,product_related_id)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_related' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'product_related': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the product_related table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS product_related;";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_related' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'product_related': " . $e->getMessage() . "\n";
        }
    }
}