<?php

declare(strict_types=1);



class CreateDesignResourceToTaxonomyItemTable
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
            CREATE TABLE IF NOT EXISTS design_resource_to_taxonomy_item (
                design_resource_id INT UNSIGNED NOT NULL,
                taxonomy_item_id INT UNSIGNED NOT NULL,
                PRIMARY KEY (design_resource_id,taxonomy_item_id),
                KEY design_resource_id (design_resource_id),
                KEY taxonomy_item_id (taxonomy_item_id)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'design_resource_to_taxonomy_item' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'design_resource_to_taxonomy_item': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the product_to_taxonomy_item table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS design_resource_to_taxonomy_item;";

        try {
            $this->pdo->exec($query);
            echo "Table 'design_resource_to_taxonomy_item' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'design_resource_to_taxonomy_item': " . $e->getMessage() . "\n";
        }
    }
}

