<?php

declare(strict_types=1);



class CreateProductToTaxonomyItemTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the product_to_taxonomy_item table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS product_to_taxonomy_item (
                product_id INT UNSIGNED NOT NULL,
                taxonomy_item_id INT UNSIGNED NOT NULL,
                sort_order int(5) NOT NULL DEFAULT '0',
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (product_id,taxonomy_item_id),
                KEY product_id (product_id),
                KEY taxonomy_item_id (taxonomy_item_id)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_to_taxonomy_item' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'product_to_taxonomy_item': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the product_to_taxonomy_item table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS product_to_taxonomy_item;";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_to_taxonomy_item' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'product_to_taxonomy_item': " . $e->getMessage() . "\n";
        }
    }
}

