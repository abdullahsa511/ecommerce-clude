<?php

declare(strict_types=1);

class CreateCouponProductTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the coupon_product table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `coupon_product` (
                `coupon_product_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `coupon_id` INT UNSIGNED NOT NULL,
                `product_id` INT UNSIGNED NOT NULL,
                PRIMARY KEY (`coupon_product_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'coupon_product' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'coupon_product': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the coupon_product table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `coupon_product`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'coupon_product' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'coupon_product': " . $e->getMessage() . "\n";
        }
    }
} 