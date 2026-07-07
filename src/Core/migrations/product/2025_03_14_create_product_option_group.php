<?php

declare(strict_types=1);



class CreateProductOptionGroupTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the vendor table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS product_option_group (
                product_option_group_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                product_id INT UNSIGNED NOT NULL,
                product_variant_id INT UNSIGNED NOT NULL,
                option_group_name VARCHAR(191) NOT NULL,
                group_type varchar(191) NOT NULL DEFAULT 'thumbnail',
                sort_order int(3) NOT NULL DEFAULT 0,
                `description` text NULL DEFAULT NULL,
                active_status tinyint(1) NOT NULL DEFAULT 1,
                created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                deleted_at datetime DEFAULT NULL,
                PRIMARY KEY (product_option_group_id),
                UNIQUE KEY uq_product_variant_id_option_group_name (product_id, product_variant_id, option_group_name),
                FOREIGN KEY (product_variant_id) REFERENCES product_variant (product_variant_id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (product_id) REFERENCES product (product_id) ON DELETE CASCADE ON UPDATE CASCADE,
                INDEX idx_product_option_group_sort_order (sort_order),
                INDEX idx_product_option_group_group_type (group_type),
                INDEX idx_product_option_group_active_status (active_status),
                INDEX idx_product_option_group_deleted_at (deleted_at),
                INDEX idx_product_option_group_created_at (created_at),
                INDEX idx_product_option_group_updated_at (updated_at)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_option_group' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'product_option_group': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the vendor table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS product_option_group;";

        try {
            $this->pdo->exec($query);
            echo "Table 'product_option_group' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'product_option_group': " . $e->getMessage() . "\n";
        }
    }
}

