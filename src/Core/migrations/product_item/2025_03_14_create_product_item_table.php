<?php

declare(strict_types=1);

class CreateProductItemTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the product_image table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS product_item (
                product_item_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                product_id INT UNSIGNED NOT NULL,
                item_id BIGINT UNSIGNED NOT NULL,
                product_variant_id INT UNSIGNED NOT NULL,
                km_item_id INT UNSIGNED NOT NULL DEFAULT '0',
                item_code VARCHAR(255) NOT NULL,
                sort_order int(3) NOT NULL DEFAULT '0',
                active_status tinyint(1) NOT NULL DEFAULT '1',
                created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at datetime DEFAULT NULL,
                PRIMARY KEY (product_item_id),
                FOREIGN KEY (product_id) REFERENCES product (product_id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (item_id) REFERENCES item (item_id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (product_variant_id) REFERENCES product_variant (product_variant_id) ON DELETE CASCADE ON UPDATE CASCADE,
                UNIQUE KEY uq_product_id_item_id_product_variant_id (product_id, item_id),
                INDEX idx_product_item_sort_order (sort_order)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_item' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'product_item': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the product_item table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS product_item;";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_item' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'product_item': " . $e->getMessage() . "\n";
        }
    }
}