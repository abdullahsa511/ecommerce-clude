<?php

declare(strict_types=1);



class CreateProductAccessoriesTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the product_accessories table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS product_accessories (
                product_accessories_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                parent_product_id INT UNSIGNED NOT NULL,
                product_id INT UNSIGNED NOT NULL,
                item_id BIGINT UNSIGNED NOT NULL,
                price DECIMAL(10,5) NOT NULL DEFAULT '0.00000',
                active_status tinyint(1) NOT NULL DEFAULT 1,
                created_at datetime DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at datetime DEFAULT NULL,
                PRIMARY KEY (product_accessories_id),
                FOREIGN KEY (parent_product_id) REFERENCES product (product_id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (product_id) REFERENCES product (product_id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (item_id) REFERENCES item (item_id) ON DELETE CASCADE ON UPDATE CASCADE,
                UNIQUE KEY uq_parent_product_id_product_id_item_id (parent_product_id, product_id, item_id),
                INDEX idx_product_accessories_active_status (active_status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_accessories' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'product_accessories': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the product_accessories table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS product_accessories;";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_accessories' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'product_accessories': " . $e->getMessage() . "\n";
        }
    }
}