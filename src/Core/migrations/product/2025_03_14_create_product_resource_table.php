<?php

declare(strict_types=1);

class CreateProductResourceTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the product table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS product_resource (
                product_resource_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                product_id INT UNSIGNED NOT NULL,
                design_resource_id INT UNSIGNED NOT NULL,
                resource_type VARCHAR(191) NOT NULL,
                sort_order int NOT NULL DEFAULT 0,
                active_status tinyint(1) NOT NULL DEFAULT 1,
                created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                deleted_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,

                PRIMARY KEY (product_resource_id),
                FOREIGN KEY (product_id) REFERENCES product (product_id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (design_resource_id) REFERENCES design_resource (design_resource_id) ON DELETE CASCADE ON UPDATE CASCADE,
                UNIQUE KEY uq_product_id_design_resource_id (product_id, design_resource_id),
                INDEX idx_resource_type (resource_type),
                INDEX idx_sort_order (sort_order),
                INDEX idx_active_status (active_status)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_resource' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'product_resource': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the product table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS product_resource;";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_resource' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'product_resource': " . $e->getMessage() . "\n";
        }
    }
}

