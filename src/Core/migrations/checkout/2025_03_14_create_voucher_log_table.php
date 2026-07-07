<?php

declare(strict_types=1);

class CreateVoucherLogTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the voucher_log table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `voucher_log` (
                `voucher_log_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `voucher_id` INT UNSIGNED NOT NULL,
                `order_id` INT UNSIGNED NOT NULL,
                `credit` decimal(15,4) NOT NULL,
                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`voucher_log_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'voucher_log' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'voucher_log': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the voucher_log table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `voucher_log`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'voucher_log' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'voucher_log': " . $e->getMessage() . "\n";
        }
    }
} 