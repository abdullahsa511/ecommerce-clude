<?php

declare(strict_types=1);

class CreateCouponTaxonomyTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the coupon_taxonomy table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `coupon_taxonomy` (
                `coupon_id` INT UNSIGNED NOT NULL,
                `taxonomy_item_id` INT UNSIGNED NOT NULL,
                PRIMARY KEY (`coupon_id`,`taxonomy_item_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'coupon_taxonomy' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'coupon_taxonomy': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the coupon_taxonomy table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `coupon_taxonomy`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'coupon_taxonomy' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'coupon_taxonomy': " . $e->getMessage() . "\n";
        }
    }
} 