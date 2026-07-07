<?php

declare(strict_types=1);

class CreateVariantItemTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the variant table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE variant_item (
                variant_item_id int(20) unsigned NOT NULL AUTO_INCREMENT,
                product_id int unsigned NOT NULL,
                product_variant_id int unsigned NOT NULL comment 'from varainat table',
                item_id BIGINT UNSIGNED NOT NULL comment 'from item table',
                km_item_id int DEFAULT NULL DEFAULT '0' comment 'use id column in csv',
                sort_order int NOT NULL DEFAULT '0',
                active_status tinyint(1) NOT NULL DEFAULT '1',
                created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at datetime DEFAULT NULL,
                PRIMARY KEY (variant_item_id),
                FOREIGN KEY (product_id) REFERENCES product (product_id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (product_variant_id) REFERENCES product_variant (product_variant_id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (item_id) REFERENCES item (item_id) ON DELETE CASCADE ON UPDATE CASCADE,
                UNIQUE KEY uq_product_id_product_variant_id_item_id (product_id, product_variant_id, item_id),
                INDEX idx_variants_item_sort_order (sort_order),
                INDEX idx_variants_item_active_status (active_status),
                INDEX idx_variants_item_deleted_at (deleted_at),
                INDEX idx_variants_item_created_at (created_at),
                INDEX idx_variants_item_updated_at (updated_at)
            ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'variant_item' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'variant_item': " . $e->getMessage() . "\n";
        }
    }

    /**
    * Rollback the migration by dropping the variant table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `variant_item`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'variant_item' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'variant_item': " . $e->getMessage() . "\n";
        }
    }
} 