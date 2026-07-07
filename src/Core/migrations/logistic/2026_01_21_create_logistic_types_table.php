<?php

declare(strict_types=1);

class CreateLogisticTypesTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the logistic_types table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `logistic_types` (
            `logistic_types_id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            `uuid` CHAR(36) NOT NULL,
            `name` VARCHAR(50) NOT NULL,
            `short` VARCHAR(5) NOT NULL,
            `type` VARCHAR(20) NOT NULL,
            `track_resource` TINYINT(1) NOT NULL DEFAULT 0,
            `forecasted_rate` FLOAT(11,4) NOT NULL DEFAULT 0.0000,
            `is_active` TINYINT(1) NOT NULL DEFAULT 1,
            `deleted_at` TIMESTAMP NULL DEFAULT NULL,
            `created_at` TIMESTAMP NULL DEFAULT NULL,
            `updated_at` TIMESTAMP NULL DEFAULT NULL,

            PRIMARY KEY (`logistic_types_id`),
            UNIQUE KEY `uk_logistic_types_uuid` (`uuid`),
            KEY `ix_logistic_types_name` (`name`),
            KEY `ix_logistic_types_short` (`short`),
            KEY `ix_logistic_is_active` (`is_active`),
            KEY `ix_logistic_type` (`type`),
            KEY `ix_logistic_types_forecasted_rate` (`forecasted_rate`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'logistic_types' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'logistic_types': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the logistic_types table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `logistic_types`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'logistic_types' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'logistic_types': " . $e->getMessage() . "\n";
        }
    }
} 