<?php

declare(strict_types=1);

class CreateProductTable
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
            CREATE TABLE IF NOT EXISTS product (
                product_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                km_item_id INT UNSIGNED NOT NULL DEFAULT '0',
                product_type_id            bigint unsigned                 not null default 1,
                class_id                bigint unsigned                 null,
                company_id              bigint unsigned                 null,
                admin_id INT unsigned NOT NULL DEFAULT '0',
                parent_id INT UNSIGNED NULL DEFAULT NULL,
                media_id INT UNSIGNED NULL DEFAULT NULL,
                model varchar(64) NULL DEFAULT '',
                description             varchar(500)                    null,
                specifications          varchar(1000)                   null,
                warranty_period         varchar(10)                     null,
                product_code            varchar(750)                     not null,
                product_family_code varchar(191) DEFAULT NULL,
                factory_code            varchar(255)                    null,
                sku varchar(64) NOT NULL DEFAULT '',
                isbn varchar(17) NULL DEFAULT '',
                barcode varchar(13) NULL DEFAULT '',
                track_stock             tinyint(1)      default 0       not null,
                stock_quantity int(4) NOT NULL DEFAULT '0',
                stock_status_id INT UNSIGNED NOT NULL DEFAULT 1,
                lead_days               int             default 0       not null,
                melbourne_lead_days     int             default 0       not null,
                safety_stock            int             default 0       not null,
                qty_alert               int             default 0       not null,
                image JSON NULL,
                manufacturer_id INT UNSIGNED  NULL DEFAULT NULL,
                vendor_id INT UNSIGNED  NULL DEFAULT NULL,
                import_vendor_id        bigint unsigned                 null,
                factory_vendor_id       bigint unsigned                 null,
                product_range_id        bigint unsigned                 null,
                product_category_id     bigint unsigned default '1'     not null,
                edgetape_colour_id      bigint unsigned                 null,
                requires_shipping tinyint NOT NULL DEFAULT '1',
                tax_type_id INT UNSIGNED  NULL DEFAULT NULL,
                material varchar(64) NULL DEFAULT '',
                weight decimal(15,8) NOT NULL DEFAULT '0.00000000',
                weight_type_id INT UNSIGNED  NULL DEFAULT NULL,
                length decimal(15,8) NOT NULL DEFAULT '0.00000000',
                length_type_id INT UNSIGNED  NULL DEFAULT NULL,
                width                   varchar(191)                  null,
                height                  varchar(191)                  null,
                depth                   varchar(191)                  null,
                price                   decimal(10, 5)                  null,
                old_price               decimal(10, 5)                  null,
                min_order_quantity      int                             not null default 1,
                out_of_stock_status     varchar(100)                    null,
                carton_qm               decimal(10, 5)                  null,
                size                    int                             null,
                carton_width            decimal(10, 5)  default 0.00000 not null,
                carton_depth            decimal(10, 5)  default 0.00000 not null,
                carton_height           decimal(10, 5)  default 0.00000 not null,
                gross_weight            decimal(14, 5)                  null,
                date_available date,
                template varchar(191) NULL DEFAULT '',
                views INT(5) unsigned NOT NULL DEFAULT '0',
                subtract_stock tinyint NOT NULL DEFAULT '1',
                status tinyint NOT NULL DEFAULT '0',
                is_featured tinyint NOT NULL DEFAULT '0',
                sort_order INT UNSIGNED NOT NULL DEFAULT '0',
                project_price_qty       int                             null,
                project_price_discount  decimal(10, 5)  default 0.00000 null,
                active                  tinyint(1)      default 1       not null,
                archive                 tinyint(1)      default 0       not null,

                specifications_image json NULL,
                banner_image json NULL,
                banner_way_points json NULL,
                video_link varchar(191) NULL,
                video_url json NULL,
                image_thumb json NULL,
                main_image_one json NULL,
                main_image_one_title varchar(191) NULL,
                main_image_one_description text NULL,
                main_image_two json NULL,
                main_image_two_title varchar(191) NULL,
                main_image_two_description text NULL,
                feature_description text NULL,
                feature_image_one json NULL,
                feature_image_one_title varchar(191) NULL,
                feature_image_one_description text NULL,
                feature_image_two json NULL,
                feature_image_two_title varchar(191) NULL,
                feature_image_two_description text NULL,
                feature_image_three json NULL,
                dimension_image json NULL,
                feature_image_three_title varchar(191) NULL,
                feature_image_three_description text NULL,
                created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                banner_way_points json NULL DEFAULT NULL,
                ocean_plastic_used tinyint(1) NOT NULL DEFAULT 0,
                show_configurator tinyint(1) NOT NULL DEFAULT 0,
                store_link varchar(255) default null,
                catalogue_link varchar(255) default null,
                delete_tags varchar(191) default null,
                PRIMARY KEY (product_id),
                KEY stock_status_id (stock_status_id),
                KEY vendor_id (vendor_id),
                KEY manufacturer_id (stock_status_id),
                KEY barcode (barcode),
                KEY is_featured (is_featured),
                UNIQUE KEY sku (sku),
                UNIQUE KEY uq_product_product_code (product_code),
                KEY media_id (media_id),
                FOREIGN KEY (media_id) REFERENCES media (media_id) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

            create index ix_products_company_id
                on product (company_id);

            create index ix_products_default_package_size
                on product (size);

            create index ix_products_description
                on product (description);

            create index ix_products_edgetape_carton_qm
                on product (carton_qm);

            create index ix_products_edgetape_colour_id
                on product (edgetape_colour_id);

            create index ix_products_factory_vendor_id
                on product (factory_vendor_id);

            create index ix_products_import_vendor_id
                on product (import_vendor_id);

            create index ix_products_product_code
                on product (product_code);

            create index ix_products_product_range_id
                on product (product_range_id);
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'product' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'product': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the product table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS product;";

        try {
            $this->pdo->exec($query);
            echo "Table 'product' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'product': " . $e->getMessage() . "\n";
        }
    }
}

