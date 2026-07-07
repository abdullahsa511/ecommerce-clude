<?php

declare(strict_types=1);

class CreateOrderShipmentTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the order_shipment table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `order_shipment` (
                `order_shipment_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `order_id` INT UNSIGNED NOT NULL,
                `shipping_method` varchar(191) NOT NULL,
                `tracking_number` varchar(191) NOT NULL,
                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`order_shipment_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'order_shipment' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'order_shipment': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the order_shipment table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `order_shipment`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'order_shipment' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'order_shipment': " . $e->getMessage() . "\n";
        }
    }
} 