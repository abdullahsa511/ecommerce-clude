<?php

declare(strict_types=1);

class CreateCouponTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the coupon table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `coupon` (
                `coupon_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `name` varchar(128) NOT NULL,
                `code` varchar(20) NOT NULL,
                `discount` decimal(15,4) NOT NULL,
                `type` char(1) NOT NULL,
                `free_shipping` tinyint NOT NULL,
                `status` tinyint NOT NULL,
                `registered_user_only` tinyint NOT NULL,
                `cart_total_min` decimal(15,4) NOT NULL,
                `date_start` date,
                `date_end` date,
                `coupon_limit` INT UNSIGNED NOT NULL,
                `user_limit` INT UNSIGNED NOT NULL,
                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `deleted_at` datetime DEFAULT NULL,
                PRIMARY KEY (`coupon_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'coupon' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'coupon': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the coupon table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `coupon`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'coupon' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'coupon': " . $e->getMessage() . "\n";
        }
    }
} 