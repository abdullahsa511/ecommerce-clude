<?php

declare(strict_types=1);


class CreateProductAttributeTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the product_attribute table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS product_attribute (
                product_id int(10) UNSIGNED NOT NULL,
                attribute_id int(10) UNSIGNED NOT NULL,
                language_id int(10) UNSIGNED NOT NULL,
                value text NOT NULL,
                PRIMARY KEY (product_id,attribute_id,language_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_attribute' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'product_attribute': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the product_attribute table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS product_attribute;";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_attribute' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'product_attribute': " . $e->getMessage() . "\n";
        }
    }
} 