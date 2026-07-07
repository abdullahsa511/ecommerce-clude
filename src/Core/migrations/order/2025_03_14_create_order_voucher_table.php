<?php

declare(strict_types=1);

class CreateOrderVoucherTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the order_voucher table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `order_voucher` (
                `order_voucher_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `order_id` INT UNSIGNED NOT NULL,
                `voucher_id` INT UNSIGNED NOT NULL,
                `content` varchar(191) NOT NULL,
                `voucher` varchar(10) NOT NULL,
                `from_name` varchar(64) NOT NULL,
                `from_email` varchar(96) NOT NULL,
                `to_name` varchar(64) NOT NULL,
                `to_email` varchar(96) NOT NULL,
                `message` text NOT NULL,
                `amount` decimal(15,4) NOT NULL,
                PRIMARY KEY (`order_voucher_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'order_voucher' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'order_voucher': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the order_voucher table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `order_voucher`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'order_voucher' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'order_voucher': " . $e->getMessage() . "\n";
        }
    }
} 