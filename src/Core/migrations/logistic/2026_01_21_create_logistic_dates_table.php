<?php

declare(strict_types=1);

class CreateLogisticDatesTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the logistic_dates table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `logistic_dates` (
                `logistic_dates_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `orders_install_date_id` BIGINT UNSIGNED NULL,
                `uuid` CHAR(36) NOT NULL UNIQUE,
                `order_id` BIGINT UNSIGNED NULL,
                `logistic_types_id` BIGINT UNSIGNED NULL,
                `user_id` BIGINT UNSIGNED NULL,
                `logistic_statuses_id` BIGINT UNSIGNED NOT NULL DEFAULT 1,
                `date` DATE NOT NULL,
                `sort_order` INT NOT NULL DEFAULT 0,
                `mins` INT NOT NULL DEFAULT 0,
                `drive_mins` INT NOT NULL DEFAULT 0,
                `drive_kms` INT NOT NULL DEFAULT 0,
                `time_pref` VARCHAR(2) NULL,
                `calc` TINYINT(1) NOT NULL DEFAULT 0,
                `expected_start` TIME NULL,
                `expected_end` TIME NULL,
                `actual_start` TIME NULL,
                `actual_end` TIME NULL,
                `actual_mins` INT NOT NULL DEFAULT 0,
                `customer_name` VARCHAR(100) NULL,
                `time_block` TINYINT(1) NOT NULL DEFAULT 0,
                `address` TEXT NULL,
                `latitude` DECIMAL(8,4) NULL,
                `longitude` DECIMAL(8,4) NULL,
                `send_email` TINYINT(1) NOT NULL DEFAULT 1,
                `email_confirmed` TINYINT(1) NOT NULL DEFAULT 0,
                `email_alerted` TINYINT(1) NOT NULL DEFAULT 0,
                `load_up` TINYINT(1) NOT NULL DEFAULT 0,
                `actual_cost` FLOAT(11,4) NULL DEFAULT 0.0000,
                `actual_cost_updated` TINYINT(1) NOT NULL DEFAULT 0,
                `notes` VARCHAR(700) NULL,
                `deleted_at` TIMESTAMP NULL DEFAULT NULL,
                `created_at` TIMESTAMP NULL DEFAULT NULL,
                `updated_at` TIMESTAMP NULL DEFAULT NULL,

                PRIMARY KEY (`logistic_dates_id`),
                UNIQUE KEY `uk_logistic_dates_uuid` (`uuid`),
                KEY `ix_logistic_dates_order_id` (`order_id`),
                KEY `ix_logistic_dates_logistic_types_id` (`logistic_types_id`),
                KEY `ix_logistic_dates_user_id` (`user_id`),
                KEY `ix_logistic_dates_logistic_statuses_id` (`logistic_statuses_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'logistic_dates' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'logistic_dates': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the logistic_dates table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `logistic_dates`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'logistic_dates' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'logistic_dates': " . $e->getMessage() . "\n";
        }
    }
} 