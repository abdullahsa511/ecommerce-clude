<?php

declare(strict_types=1);

class CreateSubscriptionTable
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Run the migration to create the subscription table.
     */
    public function up(): void
    {
        $query = "
            CREATE TABLE IF NOT EXISTS `subscription` (
                `subscription_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
                `order_id` int(10) UNSIGNED NOT NULL,
                `email` VARCHAR(191) NULL DEFAULT NULL,
                `order_product_id` int(10) UNSIGNED NOT NULL,
                `site_id` tinyint(6) NOT NULL,
                `user_id` int(10) UNSIGNED NOT NULL,
                `payment_address_id` int(10) UNSIGNED NOT NULL,
                `payment_method` text NOT NULL,
                `shipping_address_id` int(10) UNSIGNED NOT NULL,
                `shipping_method` text NOT NULL,
                `product_id` int(10) UNSIGNED NOT NULL,
                `quantity` int(4) NOT NULL,
                `subscription_plan_id` int(10) UNSIGNED NOT NULL,
                `price` decimal(10,4) NOT NULL,
                `period` enum('day','week','month','year') NOT NULL,
                `cycle` smallint(6) NOT NULL,
                `length` smallint(6) NOT NULL,
                `left` smallint(6) NOT NULL,
                `trial_price` decimal(10,4) NOT NULL,
                `trial_period` enum('day','week','month','year') NOT NULL,
                `trial_cycle` smallint(6) NOT NULL,
                `trial_length` smallint(6) NOT NULL,
                `trial_left` smallint(6) NOT NULL,
                `trial_status` tinyint NOT NULL,
                `date_next` datetime NOT NULL,
                `subscription_status_id` int(10) UNSIGNED NOT NULL,
                `notes` text NOT NULL,
                `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`subscription_id`),
                KEY `order_id` (`order_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;
        ";

        try {
            $this->pdo->exec($query);
            echo "Table 'subscription' created successfully.\n";
        } catch (PDOException $e) {
            echo "Error creating table 'subscription': " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback the migration by dropping the subscription table.
     */
    public function down(): void
    {
        $query = "DROP TABLE IF EXISTS `subscription`;";

        try {
            $this->pdo->exec($query);
            echo "Table 'subscription' dropped successfully.\n";
        } catch (PDOException $e) {
            echo "Error dropping table 'subscription': " . $e->getMessage() . "\n";
        }
    }
} 