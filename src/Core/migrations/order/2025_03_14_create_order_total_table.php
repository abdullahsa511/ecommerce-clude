<?php

declare(strict_types=1);

class CreateOrderTotalTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the order_total table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `order_total` (
                `order_total_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `order_id` INT UNSIGNED NOT NULL,
                `key` varchar(32) NOT NULL DEFAULT '',
                `title` varchar(191) NOT NULL,
                `value` decimal(15,4) NOT NULL DEFAULT '0.0000',
                `sort_order` int(3) NOT NULL DEFAULT 0,
                PRIMARY KEY (`order_total_id`),
                KEY `order_id` (`order_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'order_total' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'order_total': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the order_total table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `order_total`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'order_total' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'order_total': " . $e->getMessage() . "\n";
        }
    }
} 