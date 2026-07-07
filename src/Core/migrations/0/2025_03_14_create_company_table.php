<?php

declare(strict_types=1);

class CreateCompanyTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the company table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `company` (
                `company_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `company_name` varchar(255) NOT NULL,
                `company_entity` varchar(255) NULL,
                `company_short` varchar(50) NULL,
                `sort_order` int(11) NOT NULL DEFAULT 0,
                `company_code` varchar(255) NOT NULL,
                `company_prefix` varchar(255) NOT NULL,
                `company_trade_name` varchar(255) NULL,
                `company_entity_name` varchar(255) NULL,
                `phone_main` varchar(255) NULL,
                `krost_org_id` varchar(255) NULL,
                `krost_qld_org_id` varchar(255) NULL,
                `klein_org_id` varchar(255) NULL,
                `meloz_org_id` varchar(255) NULL,
                `gregbar_org_id` varchar(255) NULL,
                `vendor_id` varchar(255) NOT NULL,
                `ship_building` varchar(255) NULL,
                `ship_street` varchar(255) NULL,
                `ship_suburb` varchar(255) NULL,
                `ship_state` varchar(255) NULL,
                `ship_postcode` varchar(255) NULL,
                `ship_country` varchar(255) NULL,
                `bill_building` varchar(255) NULL,
                `bill_street` varchar(255) NULL,
                `bill_suburb` varchar(255) NULL,
                `bill_state` varchar(255) NULL,
                `bill_postcode` varchar(255) NULL,
                `bill_country` varchar(255) NULL,
                `po_box` varchar(255) NULL,
                `po_box_suburb` varchar(255) NULL,
                `po_box_state` varchar(255) NULL,
                `abn` varchar(255) NULL,
                `bsb` varchar(255) NULL,
                `account_number` varchar(255) NULL,
                `bpay_biller_code` int(11) NULL,
                `deleted_at` timestamp NULL,
                `created_at` timestamp NULL,
                `updated_at` timestamp NULL,
                PRIMARY KEY (`company_id`),
                INDEX `ix_companies_krost_org_id` (`krost_org_id`),
                INDEX `ix_companies_klein_org_id` (`klein_org_id`),
                INDEX `ix_companies_meloz_org_id` (`meloz_org_id`),
                INDEX `ix_companies_gregbar_org_id` (`gregbar_org_id`),
                INDEX `ix_companies_krost_qld_org_id` (`krost_qld_org_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'company' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'company': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the company table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `company`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'company' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'company': " . $e->getMessage() . "\n";
        }
    }
} 