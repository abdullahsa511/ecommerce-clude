<?php

declare(strict_types=1);

class CreateVoucherTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the voucher table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `voucher` (
                `voucher_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `order_id` INT UNSIGNED NOT NULL,
                `code` varchar(10) NOT NULL,
                `from_name` varchar(64) NOT NULL,
                `from_email` varchar(96) NOT NULL,
                `to_name` varchar(64) NOT NULL,
                `to_email` varchar(96) NOT NULL,
                `message` text NOT NULL,
                `credit` decimal(15,4) NOT NULL,
                `status` tinyint NOT NULL,
                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`voucher_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'voucher' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'voucher': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the voucher table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `voucher`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'voucher' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'voucher': " . $e->getMessage() . "\n";
        }
    }
} 