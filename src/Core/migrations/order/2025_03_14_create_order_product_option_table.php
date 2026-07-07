<?php

declare(strict_types=1);

class CreateOrderProductOptionTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the order_product_option table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `order_product_option` (
                `order_product_option_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `order_id` INT UNSIGNED NOT NULL,
                `order_product_id` INT UNSIGNED NOT NULL,
                `product_option_id` INT UNSIGNED NOT NULL,
                `product_option_value_id` INT UNSIGNED NOT NULL DEFAULT '0',
                `option` varchar(191) NOT NULL,
                `name` varchar(191) NOT NULL,
                `price` decimal(15,4) NOT NULL DEFAULT '0.0000',
                `type` varchar(32) NOT NULL DEFAULT '',
                PRIMARY KEY (`order_product_option_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'order_product_option' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'order_product_option': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the order_product_option table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `order_product_option`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'order_product_option' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'order_product_option': " . $e->getMessage() . "\n";
        }
    }
} 