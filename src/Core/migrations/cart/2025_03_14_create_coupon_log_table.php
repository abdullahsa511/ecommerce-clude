<?php

declare(strict_types=1);

class CreateCouponLogTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the coupon_log table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `coupon_log` (
                `coupon_log_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `coupon_id` INT UNSIGNED NOT NULL,
                `order_id` INT UNSIGNED NOT NULL,
                `user_id` INT UNSIGNED NOT NULL,
                `discount` decimal(15,4) NOT NULL,
                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`coupon_log_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'coupon_log' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'coupon_log': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the coupon_log table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `coupon_log`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'coupon_log' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'coupon_log': " . $e->getMessage() . "\n";
        }
    }
} 