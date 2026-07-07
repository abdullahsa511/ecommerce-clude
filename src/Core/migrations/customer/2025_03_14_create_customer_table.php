<?php

declare(strict_types=1);

class CreateCustomerTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the admin table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `customer` (
                `customer_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `company_id` INT UNSIGNED NOT NULL,
                `user_id` INT UNSIGNED NOT NULL,
                organisation_id      bigint unsigned              not null,
                uuid                 varchar(255)                 not null,
                org_code             varchar(255)                 not null,
                name                 varchar(255)                 not null,
                customer_name        varchar(255)                 not null,
                rating               double(8, 2)    default 0.00 not null,
                abn                  varchar(255)                 null,
                segment_id           bigint unsigned default '1'  not null,
                term_id              bigint unsigned default '1'  not null,
                credit_limit         decimal(14, 2)  default 0.00 not null,
                caution_bad_payer    tinyint(1)      default 0    not null,
                is_active            tinyint(1)      default 1    not null,
                is_verified          tinyint(1)      default 0    not null,
                date_last_invoice    date                         null,
                website              varchar(255)                 null,
                event_group          varchar(255)                 null,
                default_price_list   bigint unsigned default '1'  not null,
                deposit_percentage   decimal(4, 2)   default 0.00 not null,
                gst                  decimal(4, 2)   default 0.00 not null,
                is_gmail_lead        tinyint(1)      default 0    not null,
                gmail_Id             varchar(255)                 null,
                phone                varchar(50)                  default null,
                address              varchar(255)                 default null,
                bpay_ref             varchar(10)                  null,
                last_updated_on      timestamp                    null,
                created_by           bigint unsigned              null,
                deleted_at           timestamp                    null,
                created_at           timestamp                    null,
                updated_at           timestamp                    null,
                `company_name` varchar(255) DEFAULT NULL,
                `billing_first_name` varchar(32) NOT NULL,
                `billing_last_name` varchar(32) NOT NULL,
                `billing_company` varchar(60) NOT NULL DEFAULT '',
                `billing_address_1` varchar(191) NOT NULL,
                `billing_address_2` varchar(191) NOT NULL DEFAULT '',
                `billing_city` varchar(128) NOT NULL DEFAULT '',
                `billing_post_code` varchar(10) NOT NULL DEFAULT '',
                `billing_country_id` INT UNSIGNED NOT NULL,
                `billing_region` varchar(128) NOT NULL DEFAULT '',
                `billing_region_id` INT UNSIGNED NOT NULL,
                `payment_method` varchar(128) NOT NULL DEFAULT '',
                `payment_data` text,
                `payment_status_id` INT UNSIGNED NOT NULL DEFAULT '1',
                `shipping_first_name` varchar(32) NOT NULL DEFAULT '',
                `shipping_last_name` varchar(32) NOT NULL DEFAULT '',
                `shipping_company` varchar(60) NOT NULL DEFAULT '',
                `shipping_address_1` varchar(191) NOT NULL DEFAULT '',
                `shipping_address_2` varchar(191) NOT NULL DEFAULT '',
                `shipping_city` varchar(128) NOT NULL DEFAULT '',
                `shipping_post_code` varchar(10) NOT NULL DEFAULT '',
                `shipping_country` varchar(128) NOT NULL DEFAULT '',
                `shipping_country_id` INT UNSIGNED NOT NULL DEFAULT 0,
                `shipping_region` varchar(128) NOT NULL  DEFAULT '',
                `shipping_region_id` INT UNSIGNED NOT NULL DEFAULT 0,
                `shipping_method` varchar(128) NOT NULL DEFAULT '',
                `shipping_data` text,
                `shipping_status_id` INT UNSIGNED NOT NULL DEFAULT '1',
                PRIMARY KEY (`customer_id`),
                UNIQUE KEY `uuid` (`uuid`),
                KEY `customer_name` (`customer_name`),
                KEY `is_verified` (`is_verified`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'customer' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'customer': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the admin table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `customer`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'customer' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'customer': " . $e->getMessage() . "\n";
        }
    }
}
