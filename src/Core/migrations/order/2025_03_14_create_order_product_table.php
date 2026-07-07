<?php

declare(strict_types=1);

class CreateOrderProductTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the order_product table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `order_product` (
                `order_product_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `order_id` INT UNSIGNED NOT NULL,
                `product_id` INT UNSIGNED NOT NULL,
                `name` varchar(191) NOT NULL,
                `model` varchar(64) NOT NULL,
                `quantity` int(4) NOT NULL,
                `price` decimal(15,4) NOT NULL DEFAULT '0.0000',
                `total` decimal(15,4) NOT NULL DEFAULT '0.0000',
                `tax` decimal(15,4) NOT NULL DEFAULT '0.0000',
                `points` int(8) NOT NULL DEFAULT 0,
                PRIMARY KEY (`order_product_id`),
                KEY `order_id` (`order_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'order_product' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'order_product': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the order_product table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `order_product`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'order_product' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'order_product': " . $e->getMessage() . "\n";
        }
    }
} 