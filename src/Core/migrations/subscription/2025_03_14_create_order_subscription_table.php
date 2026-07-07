<?php

declare(strict_types=1);

class CreateOrderSubscriptionTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the order_subscription table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `order_subscription` (
                `order_subscription_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `order_product_id` int(10) UNSIGNED NOT NULL,
                `order_id` int(10) UNSIGNED NOT NULL,
                `product_id` int(10) UNSIGNED NOT NULL,
                `subscription_plan_id` int(10) UNSIGNED NOT NULL,
                `trial_price` decimal(10,4) NOT NULL,
                `trial_tax` decimal(15,4) NOT NULL,
                `trial_period` enum('day','week','month','year') NOT NULL,
                `trial_cycle` smallint(6) NOT NULL,
                `trial_length` smallint(6) NOT NULL,
                `trial_left` smallint(6) NOT NULL,
                `trial_status` tinyint NOT NULL,
                `price` decimal(10,4) NOT NULL,
                `tax` decimal(15,4) NOT NULL,
                `period` enum('day','week','month','year') NOT NULL,
                `cycle` smallint(6) NOT NULL,
                `length` smallint(6) NOT NULL,
                PRIMARY KEY (`order_subscription_id`),
                KEY `order_id` (`order_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'order_subscription' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'order_subscription': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the order_subscription table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `order_subscription`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'order_subscription' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'order_subscription': " . $e->getMessage() . "\n";
        }
    }
} 