<?php

declare(strict_types=1);

class CreatePinboardTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the pinboard table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `pinboard_temp` (
                `pinboard_temp_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `contact_number` JSON NULL,
                `uuid` CHAR(36) NULL,
                `reference_number` VARCHAR(255) NULL,
                `company_id` BIGINT(20) UNSIGNED NULL,
                `customer_id` INT UNSIGNED NULL,
                `job_id` INT UNSIGNED NULL,
                `dispatch_location_id` BIGINT(20) UNSIGNED NULL,
                `job_title` VARCHAR(255) NULL,
                `pinboard_name` VARCHAR(255) NULL,
                `pinboard_description` VARCHAR(500) NULL,
                `account_manager_id` BIGINT(20) UNSIGNED NULL,
                `project_manager_id` BIGINT(20) UNSIGNED NULL,
                `user_id` BIGINT(20) UNSIGNED NULL,
                `customer_email` VARCHAR(255) NULL,
                `customer_po_number` VARCHAR(255) NULL,
                `expiry_date` DATE NULL,
                `organisation_code` VARCHAR(50) NULL,
                `organisation_id` BIGINT(20) UNSIGNED NULL,
                `organisation_name` VARCHAR(255) NULL,
                `zoho_id` VARCHAR(100) NULL,
                `terms` VARCHAR(255) NULL,
                `deposit_percentage` DECIMAL(4,2) NULL DEFAULT 0.00,
                `gst` VARCHAR(20) NULL,
                `bill_to` VARCHAR(255) NULL,
                `ship_to` VARCHAR(255) NULL,
                `site_contacts` VARCHAR(500) NULL,
                `customer_balance` DECIMAL(18,2) NULL,
                `sales_price_list` VARCHAR(255) NULL,
                `total_bp_ex_gst` DECIMAL(18,2) NULL DEFAULT 0.00,
                `total_bp_inc_gst` DECIMAL(18,2) NULL DEFAULT 0.00,
                `total_sp_ex_gst` DECIMAL(18,2) NULL DEFAULT 0.00,
                `total_sp_inc_gst` DECIMAL(18,2) NULL DEFAULT 0.00,
                `order_discount` DECIMAL(15,2) NULL DEFAULT 0.00,
                `discount_rate` DECIMAL(13,2) NULL DEFAULT 0.00,
                `discount_amount` DECIMAL(15,2) NULL DEFAULT 0.00,
                `grand_total_sp_ex_gst` DECIMAL(18,2) NULL DEFAULT 0.00,
                `grand_total_sp_inc_gst` DECIMAL(18,2) NULL DEFAULT 0.00,
                `pinboard_status_id` INT UNSIGNED NULL DEFAULT 0,
                `total` DECIMAL(18,2) NULL DEFAULT 0.00,

                -- Billing Address Fields
                `bill_instructions` VARCHAR(1000) NULL,
                `bill_address` VARCHAR(255) NULL,
                `bill_suburb` VARCHAR(30) NULL,
                `bill_state` VARCHAR(10) NULL,
                `bill_postcode` VARCHAR(10) NULL,
                `bill_country` VARCHAR(20) NULL,

                -- Shipping Address Fields
                `ship_building_name` VARCHAR(255) NULL,
                `ship_instructions` VARCHAR(1000) NULL,
                `ship_address` VARCHAR(255) NULL,
                `ship_address_two` VARCHAR(255) NULL,
                `ship_suburb` VARCHAR(30) NULL,
                `ship_state` VARCHAR(10) NULL,
                `ship_postcode` VARCHAR(10) NULL,
                `ship_country` VARCHAR(20) NULL,

                `created_at` TIMESTAMP NULL DEFAULT current_timestamp,
                `updated_at` TIMESTAMP NULL DEFAULT current_timestamp,
                PRIMARY KEY (`pinboard_temp_id`),
                UNIQUE KEY `uuid` (`uuid`),
                KEY `reference_number` (`reference_number`),
                KEY `company_id` (`company_id`),
                KEY `customer_id` (`customer_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'pinboard_temp' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'pinboard_temp': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the order table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `pinboard_temp`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'pinboard_temp' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'pinboard_temp': " . $e->getMessage() . "\n";
        }
    }
} 