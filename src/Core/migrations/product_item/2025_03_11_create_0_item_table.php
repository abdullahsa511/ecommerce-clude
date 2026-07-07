<?php

declare(strict_types=1);

class CreateItemTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the item table.
     */
    public function up(): void
    {
        $query =
            "CREATE TABLE IF NOT EXISTS item (
                item_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                product_id INT UNSIGNED NOT NULL,
                product_variant_id INT UNSIGNED NOT NULL,
                km_item_id INT UNSIGNED NOT NULL DEFAULT '0',
                vendor_id BIGINT UNSIGNED NULL,
                import_vendor_id BIGINT UNSIGNED NULL,
                factory_vendor_id BIGINT UNSIGNED NULL,
                item_category_id BIGINT UNSIGNED NOT NULL DEFAULT 1,
                item_type_id BIGINT UNSIGNED NOT NULL DEFAULT 1,
                sort_order INT NOT NULL DEFAULT 0,
                item_code VARCHAR(50) NOT NULL,
                web_sku VARCHAR(25) NULL,
                class VARCHAR(50) NULL,
                description VARCHAR(500) NULL,
                specifications VARCHAR(1000) NULL,
                warranty_period VARCHAR(10) NULL,
                active TINYINT(1) NOT NULL DEFAULT 1,
                width DECIMAL(10, 5) NULL,
                height DECIMAL(10, 5) NULL,
                depth DECIMAL(10, 5) NULL,
                display_width VARCHAR(255) DEFAULT NULL,
                display_height VARCHAR(255) DEFAULT NULL,
                display_depth VARCHAR(255) DEFAULT NULL,
                carton_qm DECIMAL(8, 5) NULL,
                carton_width DECIMAL(10,5) NOT NULL DEFAULT '0.00000',
                carton_depth DECIMAL(10,5) NOT NULL DEFAULT '0.00000',
                carton_height DECIMAL(10,5) NOT NULL DEFAULT '0.00000',
                gross_weight DECIMAL(14, 5) NULL,
                boradusages_sixteen DECIMAL(10, 5) NOT NULL DEFAULT 0,
                boardusages_eighteen DECIMAL(10, 5) NOT NULL DEFAULT 0,
                boardusages_twentyfive DECIMAL(10, 5) NOT NULL DEFAULT 0,
                boardusages_thirtythree DECIMAL(10, 5) NOT NULL DEFAULT 0,
                krost_zoho_id VARCHAR(255) NULL,
                krost_qld_zoho_id VARCHAR(255) NULL,
                meloz_zoho_id VARCHAR(255) NULL,
                gregbar_zoho_id VARCHAR(255) NULL,
                klein_zoho_id VARCHAR(255) NULL,
                lead_days INT NOT NULL DEFAULT 0,
                melbourne_lead_days INT NOT NULL DEFAULT 0,
                brisbane_lead_days INT NOT NULL DEFAULT 0,
                safety_stock INT NOT NULL DEFAULT 0,
                quote_image VARCHAR(255) NULL,
                dimensions_image JSON NULL,
                delay_until DATE NULL,
                delay_until_reason VARCHAR(500) NULL,
                web_link VARCHAR(250) NULL,
                products_per_cartoon INT NULL,
                track_stock TINYINT(1) NOT NULL DEFAULT 0,
                user_note VARCHAR(500) NULL,
                archive TINYINT(1) NOT NULL DEFAULT 0,
                project_price_qty INT NULL,
                project_price_discount DECIMAL(10, 5) NULL DEFAULT 0,
                deleted_at DATETIME NULL,
                created_at DATETIME NULL,
                updated_at DATETIME NULL,
                is_default TINYINT(1) NOT NULL DEFAULT 0,
                PRIMARY KEY (item_id),
                UNIQUE KEY uq_item_item_code (item_code),
                FOREIGN KEY (product_id) REFERENCES product (product_id) ON DELETE CASCADE ON UPDATE CASCADE,
                FOREIGN KEY (product_variant_id) REFERENCES product_variant (product_variant_id) ON DELETE CASCADE ON UPDATE CASCADE,
                INDEX idx_item_sort_order (sort_order),
                INDEX idx_item_km_item_id (km_item_id),
                INDEX idx_item_vendor_id (vendor_id),
                INDEX idx_item_import_vendor_id (import_vendor_id),
                INDEX idx_item_factory_vendor_id (factory_vendor_id),
                INDEX idx_item_item_category_id (item_category_id),
                INDEX idx_item_item_type_id (item_type_id),
                INDEX idx_item_quote_image (quote_image),
                INDEX idx_item_delay_until (delay_until),
                INDEX idx_item_delay_until_reason (delay_until_reason),
                INDEX idx_item_web_link (web_link),
                INDEX idx_item_products_per_cartoon (products_per_cartoon),
                INDEX idx_item_track_stock (track_stock),
                INDEX idx_item_user_note (user_note),
                INDEX idx_item_archive (archive),
                INDEX idx_item_project_price_qty (project_price_qty),
                INDEX idx_item_project_price_discount (project_price_discount),
                INDEX idx_item_deleted_at (deleted_at),
                INDEX idx_item_created_at (created_at),
                INDEX idx_item_updated_at (updated_at),
                INDEX idx_item_active (active)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
            ";

        try {
            $this->pdo->exec($query);
            echo "Table 'item' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'item': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the item table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS item;";

        try {
            $this->pdo->exec($query);
            echo "Table 'item' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'item': " . $e->getMessage() . "\n";
        }
    }
}
